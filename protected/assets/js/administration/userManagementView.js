/* 
 * This is the jQuery plugin for the user view / update / create actions
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( userAdministrationView, $, undefined ) {
    "use strict";
    // public properties
    userAdministrationView.endpoints = {
        manager: {
            viewRecord: "/server/endpoint",
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint",
            deleteRecord: "/server/endpoint",
            assignRecord: "/server/ednpoint"
        }
    };
    
    userAdministrationView.newRecord = false;
    userAdministrationView.manager = {};
    userAdministrationView.arenas = [];
    userAdministrationView.params = {};
    userAdministrationView.isArenaManager = false;
    userAdministrationView.isApplicationAdministrator = false;
    userAdministrationView.isSystemAdministrator = false;
    userAdministrationView.roleList = [];
    userAdministrationView.statusList = [];
    userAdministrationView.stateList = [];
    userAdministrationView.Id = 0;
    userAdministrationView.Name = '';
    
    userAdministrationView.onReady = function () {
        if (typeof $.fn.editable === "undefined")
        { 
            userAdministrationView.loadEditable();
        }
        else
        {
            userAdministrationView.setupInitialManagerView();
        }
        
        var $panel = $("#userAdministrationView.panel.panel-primary");
        
        if ($panel.length > 0)
        {
            $panel.on('destroyed', function () {
                // We have been closed, so clean everything up!!!
                var $editables = $("#userAdministrationView.panel.panel-primary .editable");
                
                $editables.editable('destroy');
            });
        }
        
        $('[data-toggle="tooltip"]').tooltip();
    };
    
    userAdministrationView.loadEditable = function () {
        
        var scriptName = utilities.debug ? 'bootstrap-editable.js' : 'bootstrap-editable.min.js';
        
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? 
                "/bootstrap-editable/js/bootstrap-editable.js" : 
                        "/bootstrap-editable/js/bootstrap-editable.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
                console.log("Loaded: " + scriptName);
            },
            error: function(xhr, status, errorThrown) {
                utilities.ajaxError.show(
                    "Error",
                    "Failed to retrieve javsScript file",
                    xhr,
                    status,
                    errorThrown
                );
            }
        });
        
        var interval = setInterval(function () {
            if (typeof typeof $.fn.editable !== "undefined") {
                clearInterval(interval);
                userAdministrationView.setupInitialManagerView();
            } else if (console && console.log) {
                console.log("Loading... " + scriptName);
            }
        }, 500);
        
    };

    userAdministrationView.setupInitialManagerView = function () {
        // Disable all buttons except the New button 
        // and hide the save / cancel buttons
        var that = this;
        var $assignBtn = $('#assignManagerButton');
        var $unassignBtn = $('#unassignManagerButton');
        var $newBtn = $('#newManagerButton');
        var $deleteBtn = $('#deleteManagerButton');
        var $saveBtn = $('#saveManagerButton');
        var $cancelBtn = $('#cancelManagerButton');
        var $availableMS = $('#availableManagersMSelect');
        var $assignedMS = $('#assignedManagersMSelect');
        var $assignedS = $('#assignedManagersSelect');
        
        $assignBtn.attr("disabled", "disabled");
        $unassignBtn.attr("disabled", "disabled");
        $deleteBtn.attr("disabled", "disabled");
        $saveBtn.attr("disabled", "disabled").hide();
        $cancelBtn.attr("disabled", "disabled").hide();
        
        // Setup the select change handlers!
        $availableMS.on('change', function (e) {
            // Get the selected options from the available select list
            var $selected = $availableMS.find('option:selected');
            
            if($selected.length > 0)
            {
                // we have at least one selection so enable the button
                $assignBtn.removeAttr("disabled");
            }
            else
            {
                // Nothing selected so disable the button
                $assignBtn.attr("disabled", "disabled");
            }
        });
        
        $assignedMS.on('change', function (e) {
            // Get the selected options from the available select list
            var $selected = $assignedMS.find('option:selected');
            
            if($selected.length > 0)
            {
                // we have at least one selection so enable the button
                $unassignBtn.removeAttr("disabled");
            }
            else
            {
                // Nothing selected so disable the button
                $unassignBtn.attr("disabled", "disabled");
            }
        });
        
        $assignedS.on('change', function (e) {
            // Get the selected options from the available select list
            var $selected = $assignedS.find('option:selected');
            
            if($selected.length > 0)
            {
                // we have our selection, so clear the edit screen and
                // load in the new manager
                var managerId = $(this).val();

                that.resetManagerView();
                
                if(managerId == 'none')
                {
                    userAdministrationView.resetArenaLists();
                    
                    return;
                }
                
                that.loadManager(managerId);
            }
            else
            {
                // Nothing selected, so clear the edit screen
                that.resetManagerView();
            }
        });
        
        // Setup the button click handlers!
        $assignBtn.on('click', function (e) {
            e.preventDefault();
            
            var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
            
            // Get the selected options from the available select list
            var $selected = $availableMS.find('option:selected');
            var managerId = $assignedS.val();
            
            if(managerId === 'none' || managerId <= 0)
            {
                return ;
            }

            var values = [];
            
            $selected.each(function () {
                values.push($(this).val());
            });
            
            if(values.length <= 0)
            {
                return;
            }
            
            // Prepare to assign the managers by disabling the assignment
            // buttons and select lists!
            $assignBtn.attr("disabled", "disabled");
            $unassignBtn.attr("disabled", "disabled");
            $availableMS.attr("disabled", "disabled");
            $assignedMS.attr("disabled", "disabled");
            
            // Show we are busy by appending the spinner to the assign button
            $assignBtn.parent().prepend(spinner);
            
            // Now let's assign the managers!!!
            var myParams = {
                name: 'assign',
                aids: values,
                id: managerId,
                pk: managerId,
                output: 'html'
            };
            
            $.ajax({
                url: userAdministrationView.endpoints.manager.updateRecord,
                data: myParams,
                type: 'POST',
                dataType: 'html',
                success: function(result, status, xhr) {
                    // Its possible we will get a session timeout so check for it!
                    var myjsonObj = false;
                    try
                    {
                        myjsonObj = JSON.parse(result);
                    }
                    catch (err)
                    {
                        myjsonObj = false;
                    }

                    if (myjsonObj !== false)
                    {
                        window.setTimeout(function () {
                            $availableMS.removeAttr("disabled");
                            $assignedMS.removeAttr("disabled");
                            $assignBtn.removeAttr("disabled");

                            $assignBtn.parent().find('#loading').remove();
                            utilities.ajaxError.show(
                                    "Error",
                                    "Failed to assign the manager",
                                    xhr,
                                    "error",
                                    "Login Required"
                            );
                        }, 1000);

                        return;
                    }
                    
                    // Move the managers to the assigned list
                    $selected.each(function () {
                        $assignedMS.append('<option value="' + $(this).val() + '">' + $(this).text() + '</option>');
                        $assignedS.append('<option value="' + $(this).val() + '">' + $(this).text() + '</option>');
                        $(this).remove();
                    });
                    
                    // Clear any selections
                    $availableMS.val('');
                    $assignedMS.val('');
                    
                    $availableMS.removeAttr("disabled");
                    $assignedMS.removeAttr("disabled");
                    $assignBtn.parent().find('#loading').remove();
                },
                error: function(xhr, status, errorThrown) {
                    $availableMS.removeAttr("disabled");
                    $assignedMS.removeAttr("disabled");
                    $assignBtn.removeAttr("disabled");
                    
                    $assignBtn.parent().find('#loading').remove();
                    
                    utilities.ajaxError.show(
                        "Error",
                        "Failed to assign the manager",
                        xhr,
                        status,
                        errorThrown
                    );
                }
            });            
        });
        
        $unassignBtn.on('click', function (e) {
            e.preventDefault();
            
            var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
            
            // Get the selected options from the available select list
            var $selected = $assignedMS.find('option:selected');
            var managerId = $assignedS.val();
            
            if(managerId === 'none' || managerId <= 0)
            {
                return ;
            }
            
            var values = [];
            
            $selected.each(function () {
                values.push($(this).val());
            });
            
            if(values.length <= 0)
            {
                return;
            }
            
            // Prepare to unassign the managers by disabling the assignment
            // buttons and select lists!
            $assignBtn.attr("disabled", "disabled");
            $unassignBtn.attr("disabled", "disabled");
            $availableMS.attr("disabled", "disabled");
            $assignedMS.attr("disabled", "disabled");
            
            // Show we are busy by appending the spinner to the assign button
            $unassignBtn.parent().prepend(spinner);
            
            // Now let's unassign the managers!!!
            var myParams = {
                name: 'unassign',
                aids: values,
                id: managerId,
                pk: managerId,
                output: 'html'
            };
            
            $.ajax({
                url: userAdministrationView.endpoints.manager.updateRecord,
                data: myParams,
                type: 'POST',
                dataType: 'html',
                success: function(result, status, xhr) {
                    // Its possible we will get a session timeout so check for it!
                    var myjsonObj = false;
                    try
                    {
                        myjsonObj = JSON.parse(result);
                    }
                    catch (err)
                    {
                        myjsonObj = false;
                    }

                    if (myjsonObj !== false)
                    {
                        window.setTimeout(function () {
                            $availableMS.removeAttr("disabled");
                            $assignedMS.removeAttr("disabled");
                            $unassignBtn.removeAttr("disabled");

                            $unassignBtn.parent().find('#loading').remove();
                            utilities.ajaxError.show(
                                    "Error",
                                    "Failed to unassign the manager",
                                    xhr,
                                    "error",
                                    "Login Required"
                            );
                        }, 1000);

                        return;
                    }
                    
                    // Move the managers to the available list
                    $selected.each(function () {
                        $availableMS.append('<option value="' + $(this).val() + '">' + $(this).text() + '</option>');
                        var $s = $assignedS.find('option[value="' + $(this).val() + '"]');
                        
                        if($assignedS.val() == $s.val())
                        {
                            // We just unassigned the manager we are editing
                            // and so we need to trigger a change event
                            $assignedS.val('none');
                            $s.remove();
                            $assignedS.trigger('change');                            
                        }
                        else
                        {
                            $s.remove();
                        }
                        
                        $(this).remove();
                    });
                    
                    // Clear any selections
                    $availableMS.val('');
                    $assignedMS.val('');
                    
                    $availableMS.removeAttr("disabled");
                    $assignedMS.removeAttr("disabled");
                    $unassignBtn.parent().find('#loading').remove();
                },
                error: function(xhr, status, errorThrown) {
                    $availableMS.removeAttr("disabled");
                    $assignedMS.removeAttr("disabled");
                    $unassignBtn.removeAttr("disabled");
                    
                    $unassignBtn.parent().find('#loading').remove();
                    
                    utilities.ajaxError.show(
                        "Error",
                        "Failed to unassign the manager",
                        xhr,
                        status,
                        errorThrown
                    );
                }
            });            
        });
        
        $newBtn.on('click', function (e) {
            e.preventDefault();
            
            $assignedS.attr("disabled", "disabled");
            $assignedS.val('none');
            userAdministrationView.resetArenaLists();
            
            var myParams = {
                get_available: 1,
                get_assigned: 1,
                output: 'html'
            };

            userAdministrationView.setupNewManagerView(myParams);
            $newBtn.attr("disabled", "disabled");
            $deleteBtn.attr("disabled", "disabled");
        });
        
        $deleteBtn.on('click', function (e) {
            e.preventDefault();
            
            var $modal = userAdministrationView.createDeleteModal();
            
            $modal.modal({
                loading: false,
                replace: false,
                modalOverflow: false
            });
            
            // The modal is now in the DOM so we can hook in to the button
            // clicks. Specifically, we only care about the 'yes' button.
            $('button#yes').on('click', function (e) {
                // They clicked yes and so now we must delete the manager!!!
                var managerId = $assignedS.val();
                
                // We must disable everything and put up our spinner...
                var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
            
                // Prepare to assign the managers by disabling the assignment
                // buttons and select lists!
                $assignBtn.attr("disabled", "disabled");
                $unassignBtn.attr("disabled", "disabled");
                $availableMS.attr("disabled", "disabled");
                $assignedMS.attr("disabled", "disabled");
                $assignedS.attr("disabled", "disabled");
                $newBtn.attr("disabled", "disabled");
                $deleteBtn.attr("disabled", "disabled");
                $saveBtn.attr("disabled", "disabled").hide();
                $cancelBtn.attr("disabled", "disabled").hide();
            
                // Show we are busy by appending the spinner to the assign button
                $deleteBtn.parent().prepend(spinner);
            
                // Now let's delete the manager!!!
                var myParams = {
                    id: managerId,
                    pk: managerId,
                    output: 'html'
                };
                
                $.ajax({
                    url: userAdministrationView.endpoints.manager.deleteRecord,
                    data: myParams,
                    type: 'POST',
                    dataType: 'html',
                    success: function(result, status, xhr) {
                        // Its possible we will get a session timeout so check for it!
                        var myjsonObj = false;
                        try
                        {
                            myjsonObj = JSON.parse(result);
                        }
                        catch (err)
                        {
                            myjsonObj = false;
                        }

                        if (myjsonObj !== false)
                        {
                            window.setTimeout(function () {
                                // Clear any selections
                                $availableMS.val('');
                                $assignedMS.val('');
                    
                                $availableMS.removeAttr("disabled");
                                $assignedMS.removeAttr("disabled");
                                $assignedS.removeAttr("disabled");
                                $newBtn.removeAttr("disabled");
                                $deleteBtn.parent().find('#loading').remove();
                                utilities.ajaxError.show(
                                    "Error",
                                    "Failed to delete the manager",
                                    xhr,
                                    "error",
                                    "Login Required"
                                );
                            }, 1000);

                            return;
                        }
                    
                        // Remove the manager from the assigned list
                        $assignedMS.find('option[value="' + managerId + '"]').remove();
                        $assignedS.find('option[value="' + managerId + '"]').remove();
                        $assignedS.val('none').trigger('change');
                        
                        // Clear any selections
                        $availableMS.val('');
                        $assignedMS.val('');
                    
                        $availableMS.removeAttr("disabled");
                        $assignedMS.removeAttr("disabled");
                        $assignedS.removeAttr("disabled");
                        $newBtn.removeAttr("disabled");
                        $deleteBtn.parent().find('#loading').remove();
                    },
                    error: function(xhr, status, errorThrown) {
                        // Clear any selections
                        $availableMS.val('');
                        $assignedMS.val('');
                    
                        $availableMS.removeAttr("disabled");
                        $assignedMS.removeAttr("disabled");
                        $assignedS.removeAttr("disabled");
                        $newBtn.removeAttr("disabled");
                        $deleteBtn.parent().find('#loading').remove();
                    
                        utilities.ajaxError.show(
                            "Error",
                            "Failed to delete the manager",
                            xhr,
                            status,
                            errorThrown
                        );
                    }
                });            
            });
        });
        
        $saveBtn.on('click', function (e) {
            e.preventDefault();
            
            // We must disable everything and put up our spinner...
            var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
            
            // Prepare to assign the managers by disabling the assignment
            // buttons and select lists!
            $assignBtn.attr("disabled", "disabled");
            $unassignBtn.attr("disabled", "disabled");
            $availableMS.attr("disabled", "disabled");
            $assignedMS.attr("disabled", "disabled");
            $assignedS.attr("disabled", "disabled");
            $newBtn.attr("disabled", "disabled");
            $deleteBtn.attr("disabled", "disabled");
            $saveBtn.attr("disabled", "disabled");
            $cancelBtn.attr("disabled", "disabled");
            
            // Show we are busy by appending the spinner to the assign button
            $saveBtn.parent().prepend(spinner);
            
            // Ok, we are going for a ride here as we will send all of the
            // values to the server at once to create the manager.
            // I hope all goes well ;-)
            $('.manager-editable').editable('submit', { 
                url: userAdministrationView.endpoints.manager.newRecord, 
                ajaxOptions: {
                    dataType: 'json'
                },
                success: function(data, config) {
                   $availableMS.removeAttr("disabled");
                   $assignedMS.removeAttr("disabled");
                   
                   if($availableMS.val() > 0)
                   {
                       $assignBtn.removeAttr("disabled");
                   }
                   
                   if($assignedMS.val() > 0)
                   {
                       $unassignBtn.removeAttr("disabled");
                   }
                   
                   $saveBtn.removeAttr("disabled");
                   $cancelBtn.removeAttr("disabled");
                   
                   $saveBtn.parent().find("#loading").remove();
                   
                   if(data && data.id) {  //record created, response like {"id": 2}
                       // set pk
                       $(this).editable('option', 'pk', data.id);
                       
                       // remove unsaved class
                       $(this).removeClass('editable-unsaved');
                       
                       // update the select lists!
                       var vals = $('#Manager_active, #Manager_last_name, #Manager_first_name, #Manager_email').editable('getValue');
                
                       var active = (vals.active == 0) ? 'Inactive' : 'Active';
                       var newText = vals.last_name + ', ' + vals.first_name + ' - ' + vals.email + ' (' + active + ')';
                       var newOption = '<option value="' + data.id + '">' + newText + '</option>';
                       $assignedS.append(newOption);
                       
                       // Now select the newly appended option
                       $assignedS.val(data.id); // We don't trigger a change!
                       
                       $saveBtn.attr("disabled", "disabled").hide(250);
                       $cancelBtn.attr("disabled", "disabled").hide(250);
                       $assignedS.removeAttr("disabled");
                       $newBtn.removeAttr("disabled");
                       $deleteBtn.removeAttr("disabled");
                       
                       //show messages
                       var msgArea = '<div class="alert alert-success">' +
                           '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                           '<h3><span class="badge badge-success">New manager added!</span></h3></div>';
                       
                       $('#managerDetails').prepend(msgArea);
                       
                       userAdministrationView.resetArenaLists();
                            
                       if (data.availableArenas !== "undefined")
                       {
                           userAdministrationView.loadArenaList($availableMS, data.availableArenas);
                       }

                       if (data.assignedArenas !== "undefined")
                       {
                           userAdministrationView.loadArenaList($assignedMS, data.assignedArenas);
                       }
                   } else if(data && data.errors){ 
                       //server-side validation error, response like {"errors": {"username": "username already exist"} }
                       config.error.call(this, data.errors);
                   } else if(data && data.error){ 
                       //server-side validation error, response like {"errors": {"username": "username already exist"} }
                       config.error.call(this, data.error);
                   }
               },
               error: function(errors) {
                   $availableMS.removeAttr("disabled");
                   $assignedMS.removeAttr("disabled");
                   
                   if($availableMS.val() > 0)
                   {
                       $assignBtn.removeAttr("disabled");
                   }
                   
                   if($assignedMS.val() > 0)
                   {
                       $unassignBtn.removeAttr("disabled");
                   }
                   
                   $saveBtn.removeAttr("disabled");
                   $cancelBtn.removeAttr("disabled");
                   
                   $saveBtn.parent().find("#loading").remove();
                   
                   var msg = '';
                   if(errors && errors.responseText) { //ajax error, errors = xhr object
                       msg = errors.responseText;
                       utilities.ajaxError.show(
                            "Error",
                            "Failed to add the new manager",
                            errors,
                            'error',
                            'Unknown'
                        );
                   } else { //validation error (client-side or server-side)
                        try
                        {
                            if (errors)
                            {
                                errors = JSON.parse(errors);
                            }
                            
                            $.each(errors, function(k, v) { msg += v+"<br>"; });
                        }
                        catch (err)
                        {
                            errors = false;
                        }
                   }
                   var msgArea = '<div class="alert alert-error">' +
                           '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                           '<h3><span class="badge badge-important">Please correct the following errors:</span></h3>' +
                           msg + '</div>';
                   
                   $('#managerDetails').prepend(msgArea);
               }
           });
        });
        
        $cancelBtn.on('click', function (e) {
            e.preventDefault();
            
            userAdministrationView.resetManagerView();
            userAdministrationView.resetArenaLists();
        });
        
        if(userAdministrationView.manager.id !== "undefined")
        {
            userAdministrationView.setupManagerView(userAdministrationView.manager, userAdministrationView.params);
        }
        else
        {
            $newBtn.trigger('click');
        }
    };
    
    userAdministrationView.setupManagerEditables = function (params) {
        $('#Manager_first_name').editable({
            params: params,
            success: function(response, newValue) {
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }
            
                var vals = $('#Manager_active, #Manager_last_name, #Manager_email').editable('getValue');
                
                // This is a bit risky as the user may select a different
                // a different manager by the time we get to this part even
                // though we have disabled the buttons and the select lists.
                var $assignedS = $('#assignedManagersSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $assignedS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                var $op1 = $assignedS.find('option[value="' + id + '"]');
                var active = (vals.active == 0) ? 'Inactive' : 'Active';
                var newText = vals.last_name + ', ' + newValue + ' - ' + vals.email + ' (' + active + ')';
                
                $op1.text(newText);
            }
        });
        
        $('#Manager_last_name').editable({
            params: params,
            success: function(response, newValue) {
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }
            
                var vals = $('#Manager_active, #Manager_first_name, #Manager_email').editable('getValue');
                
                // This is a bit risky as the user may select a different
                // a different manager by the time we get to this part even
                // though we have disabled the buttons and the select lists.
                var $assignedS = $('#assignedManagersSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $assignedS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                var $op1 = $assignedS.find('option[value="' + id + '"]');
                var active = (vals.active == 0) ? 'Inactive' : 'Active';
                var newText = newValue + ', ' + vals.first_name + ' - ' + vals.email + ' (' + active + ')';
                
                $op1.text(newText);
            }
        });
        
        $('#Manager_active').editable({
            params: params,
            showbuttons: false,
            source: [{
                    value: 0,
                    text: 'Inactive'
            }, {
                value: 1,
                text: 'Active'
            }],
            success: function(response, newValue) {
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }
            
                var vals = $('#Manager_first_name, #Manager_last_name, #Manager_email').editable('getValue');
                
                // This is a bit risky as the user may select a different
                // a different manager by the time we get to this part even
                // though we have disabled the buttons and the select lists.
                var $assignedS = $('#assignedManagersSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $assignedS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                var $op1 = $assignedS.find('option[value="' + id + '"]');
                var active = (newValue == 0) ? 'Inactive' : 'Active';
                var newText = vals.last_name + ', ' + vals.first_name + ' - ' + vals.email + ' (' + active + ')';
                
                $op1.text(newText);
            }
        });
        
        $('#Manager_email').editable({
            params: params,
            success: function(response, newValue) {
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }
            
                var vals = $('#Manager_active, #Manager_first_name, #Manager_last_name').editable('getValue');
                
                // This is a bit risky as the user may select a different
                // a different manager by the time we get to this part even
                // though we have disabled the buttons and the select lists.
                var $assignedS = $('#assignedManagersSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $assignedS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                var $op1 = $assignedS.find('option[value="' + id + '"]');
                var active = (vals.active == 0) ? 'Inactive' : 'Active';
                var newText = vals.last_name + ', ' + vals.first_name + ' - ' + newValue + ' (' + active + ')';
                
                $op1.text(newText);
            }
        });
        
        $('#Manager_phone').editable({
            params: params,
            display: function(value, sourceData) {
                // display the supplied digits as a phone number!
                var html = '';

                if (typeof value === 'undefined' || value.length <= 0)
                {
                    return;
                }
            
                html = String(value).replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
                $(this).html(html);
            }
        });

        $("#Manager_phone").on('shown', function(e, editable) {
            // ensure that we only get the unmasked value
            if (editable) {
                $(this).data('editable').input.$input.inputmask(
                {
                    "mask": "(999) 999-9999",
                    "clearIncomplete": false,
                    "autoUnmask" : true,
                    "showTooltip" : true
                });
            }
        });
        
        $('#Manager_ext').editable({
            params: params
        });
        
        $('#Manager_fax').editable({
            params: params,
            display: function(value, sourceData) {
                // display the supplied digits as a phone number!
                var html = '';

                if (typeof value === 'undefined' || value.length <= 0)
                {
                    return;
                }
            
                html = String(value).replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
                $(this).html(html);
            }
        });

        $("#Manager_fax").on('shown', function(e, editable) {
            // ensure that we only get the unmasked value
            if (editable) {
                $(this).data('editable').input.$input.inputmask(
                {
                    "mask": "(999) 999-9999",
                    "clearIncomplete": false,
                    "autoUnmask" : true,
                    "showTooltip" : true
                });
            }
        });
        
        $('#Manager_fax_ext').editable({
            params: params
        });        
    };
    
    userAdministrationView.setupManagerView = function (manager, params) {
        var $managerDetails = $('#managerDetails');
        var $managerEditables = $('.manager-editable');
        
        // Destroy any existing editables!
        $("#Manager_phone").off('shown');
        $("#Manager_fax").off('shown');
        
        if ($managerEditables.length > 0)
        {
            $managerEditables.each(function () {
                $(this).editable('destroy');
            });
        }
        
        // Add one row at a time. 
        var managerView = '<strong>Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';
        
        managerView += '<tr><td style="width:33%">First Name</td><td>' +
                '<a href="#" id="Manager_first_name" data-name="first_name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'data-pk="' + manager.id + '" data-value="' + manager.first_name + '" ' +
                'title="First Name" class="manager-editable">' + 
                manager.first_name + '</a></td></tr>';
        
        managerView += '<tr><td style="width:33%">Last Name</td><td>' +
                '<a href="#" id="Manager_last_name" data-name="last_name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'data-pk="' + manager.id + '" data-value="' + manager.last_name + '" ' +
                'title="Last Name" class="manager-editable">' +
                manager.last_name + '</a></td></tr>';
        
        managerView += '<tr><td style="width:33%">Active</td><td>' +
                '<a href="#" id="Manager_active" data-name="active" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'data-pk="' + manager.id + '" data-value="' + manager.active + '" ' +
                'title="Manager Status" class="manager-editable">';
        
        if(manager.active == 1)
        {
            managerView += 'Active</a></td></tr>';
        }
        else
        {
            managerView += 'Inactive</a></td></tr>';
        }
        
        managerView += '</tbody></table>';
        
        // Now the next table of info!
        managerView += '<strong>Email & Phone</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        managerView += '<tr><td style="width:33%">E-mail</td><td>' +
                '<a href="#" id="Manager_email" data-name="email" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'data-pk="' + manager.id + '" data-value="' + manager.email + '" ' +
                'title="E-mail Address" class="manager-editable">' +
                manager.email + '</a></td></tr>';
        
        managerView += '<tr><td style="width:33%">Phone</td><td>' +
                '<a href="#" id="Manager_phone" data-name="phone" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'data-pk="' + manager.id + '" data-value="' + manager.phone + '" ' +
                'title="Ten digit phone number" class="manager-editable">' +
                manager.phone + '</a></td></tr>';
        
        managerView += '<tr><td style="width:33%">Extension</td><td>' +
                '<a href="#" id="Manager_ext" data-name="ext" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'data-pk="' + manager.id + '" data-value="' + (manager.ext ? manager.ext : '') + '" ' +
                'title="Phone extension" class="manager-editable">' +
                (manager.ext ? manager.ext : '') + '</a></td></tr>';
        
        managerView += '<tr><td style="width:33%">Fax</td><td>' +
                '<a href="#" id="Manager_fax" data-name="fax" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'data-pk="' + manager.id + '" data-value="' + (manager.fax ? manager.fax : '') + '" ' +
                'title="Ten digit fax number" class="manager-editable">' +
                (manager.fax ? manager.fax : '') + '</a></td></tr>';
       
        managerView += '<tr><td style="width:33%">Fax Extension</td><td>' +
                '<a href="#" id="Manager_fax_ext" data-name="fax_ext" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'data-pk="' + manager.id + '" data-value="' + (manager.fax_ext ? manager.fax_ext : '') + '" ' +
                'title="Fax extension" class="manager-editable">' +
                (manager.fax_ext ? manager.fax_ext : '') + '</a></td></tr></tbody></table>';
        
        // Ok, now we can fade-out the current view, and then fade in our new
        // view!
        var $cv = $('<div id="managerDetails">' + managerView + '</div>');
        
        $cv.hide();
        
        $managerDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $managerDetails.parent().prepend($cv);
            
            // Setup the manager editables before we fade in!
            userAdministrationView.setupManagerEditables(params);
            
            // Now fade us in!
            $cv.fadeIn(500, function() {
                var $assignBtn = $('#assignManagerButton');
                var $unassignBtn = $('#unassignManagerButton');
                var $newBtn = $('#newManagerButton');
                var $deleteBtn = $('#deleteManagerButton');
                var $availableMS = $('#availableManagersMSelect');
                var $assignedMS = $('#assignedManagersMSelect');
                var $assignedS = $('#assignedManagersSelect');

                $availableMS.removeAttr("disabled");
                $assignedMS.removeAttr("disabled");
                $assignedS.removeAttr("disabled");
                $newBtn.removeAttr("disabled");
                $deleteBtn.removeAttr("disabled");

                if($availableMS.val() > 0)
                {
                    $assignBtn.removeAttr("disabled");
                }

                if($assignedMS.val() > 0)
                {
                    $unassignBtn.removeAttr("disabled");
                }

                $assignedS.parent().find('#loading').remove();
                $managerDetails.remove();
            });            
        });
    };
    
    userAdministrationView.setupNewManagerView = function (params) {
        var $managerDetails = $('#managerDetails');
        var $managerEditables = $('.manager-editable');
        
        // Destroy any existing editables!
        $("#Manager_phone").off('shown');
        $("#Manager_fax").off('shown');
        
        if ($managerEditables.length > 0)
        {
            $managerEditables.each(function () {
                $(this).editable('destroy');
            });
        }
        
        // Add one row at a time. 
        var managerView = '<strong>Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';
        
        managerView += '<tr><td style="width:33%">First Name</td><td>' +
                '<a href="#" id="Manager_first_name" data-name="first_name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'title="First Name" class="manager-editable"></a></td></tr>';
        
        managerView += '<tr><td style="width:33%">Last Name</td><td>' +
                '<a href="#" id="Manager_last_name" data-name="last_name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'title="Last Name" class="manager-editable"></a></td></tr>';
        
        managerView += '<tr><td style="width:33%">Active</td><td>' +
                '<a href="#" id="Manager_active" data-name="active" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'data-value="1"' +
                'title="Manager Status" class="manager-editable">Active</a></td></tr>';
        
        managerView += '</tbody></table>';
        
        // Now the next table of info!
        managerView += '<strong>Email & Phone</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        managerView += '<tr><td style="width:33%">E-mail</td><td>' +
                '<a href="#" id="Manager_email" data-name="email" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'title="E-mail Address" class="manager-editable"></a></td></tr>';
        
        managerView += '<tr><td style="width:33%">Phone</td><td>' +
                '<a href="#" id="Manager_phone" data-name="phone" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'title="Ten digit phone number" class="manager-editable"></a></td></tr>';
        
        managerView += '<tr><td style="width:33%">Extension</td><td>' +
                '<a href="#" id="Manager_ext" data-name="ext" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'title="Phone extension" class="manager-editable"></a></td></tr>';
        
        managerView += '<tr><td style="width:33%">Fax</td><td>' +
                '<a href="#" id="Manager_fax" data-name="fax" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'title="Ten digit fax number" class="manager-editable"></a></td></tr>';
       
        managerView += '<tr><td style="width:33%">Fax Extension</td><td>' +
                '<a href="#" id="Manager_fax_ext" data-name="fax_ext" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'title="Fax extension" class="manager-editable"></a></td></tr>';
                
        managerView += '<tr style="display:none;"><td>Output</td><td>' +
                '<a href="#" id="Manager_output" data-name="output" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'data-value="json" title="Output" class="manager-editable">' +
                'json</a></td></tr>';
                
        managerView += '<tr style="display:none;"><td>Get Available</td><td>' +
                '<a href="#" id="Manager_get_available" data-name="get_available" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                userAdministrationView.endpoints.manager.updateRecord + '" ' +
                'data-value="1" title="Get Available" class="manager-editable">' +
                'json</a></td></tr>';
                
                
        managerView += '</tbody></table>';
        
        // Ok, now we can fade-out the current view, and then fade in our new
        // view!
        var $cv = $('<div id="managerDetails">' + managerView + '</div>');
        
        $cv.hide();
        
        $managerDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $managerDetails.parent().prepend($cv);
            
            // Setup the manager editables before we fade in!
            userAdministrationView.setupManagerEditables(params);
            
            $('#Manager_get_available').editable({
                params: params
            });
            
            $('#Manager_output').editable({
                params: params
            });
            
            // Now fade us in!
            $cv.fadeIn(500, function() {
                var $assignBtn = $('#assignManagerButton');
                var $unassignBtn = $('#unassignManagerButton');
                var $saveBtn = $('#saveManagerButton');
                var $cancelBtn = $('#cancelManagerButton');
                var $availableMS = $('#availableManagersMSelect');
                var $assignedMS = $('#assignedManagersMSelect');

                $availableMS.removeAttr("disabled");
                $assignedMS.removeAttr("disabled");
                $saveBtn.removeAttr("disabled").show(250);
                $cancelBtn.removeAttr("disabled").show(250);

                if($availableMS.val() > 0)
                {
                    $assignBtn.removeAttr("disabled");
                }

                if($assignedMS.val() > 0)
                {
                    $unassignBtn.removeAttr("disabled");
                }
                
                $managerDetails.remove();
            });            
        });
    };
    
    userAdministrationView.loadArenaList = function ($element, list){
        if (list === "undefined" || list.length === "undefined" || list.length <= 0)
        {
            return;
        }
        
        for (var i = 0; i < list.length; i++)
        {
            var val = list[i].id;
            var text = list[i].name + ', ' + list[i].city + ', ' + list[i].state + ' ' + list[i].zip + ' (' + list[i].status + ')';
            
            $element.append('<option value="' + val + '">' + text + '</option>');
        }
    };
    
    userAdministrationView.resetArenaLists = function (){
        var $assignBtn = $('#assignManagerButton');
        var $unassignBtn = $('#unassignManagerButton');
        var $availableMS = $('#availableManagersMSelect');
        var $assignedMS = $('#assignedManagersMSelect');
        
        $availableMS.val('');
        $availableMS.empty();
        $assignBtn.attr("disabled", "disabled");
        
        $assignedMS.val('');
        $assignedMS.empty();
        $unassignBtn.attr("disabled", "disabled");        
    };
    
    userAdministrationView.resetManagerView = function (){
        var $managerDetails = $('#managerDetails');
        var $managerEditables = $('.manager-editable');
        var $newBtn = $('#newManagerButton');
        var $deleteBtn = $('#deleteManagerButton');
        var $saveBtn = $('#saveManagerButton');
        var $cancelBtn = $('#cancelManagerButton');
        var $assignedS = $('#assignedManagersSelect');
        
        $newBtn.attr("disabled", "disabled");
        $deleteBtn.attr("disabled", "disabled");
        $saveBtn.attr("disabled", "disabled").hide();
        $cancelBtn.attr("disabled", "disabled").hide();
        
        // Destroy any existing editables!
        $("#Manager_phone").off('shown');
        $("#Manager_fax").off('shown');
        
        if ($managerEditables.length > 0)
        {
            $managerEditables.each(function () {
                $(this).editable('destroy');
            });
        }
        
        $managerDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $managerDetails.empty();
            
            // Now fade us in!
            $managerDetails.fadeIn(500, function () {
                $newBtn.removeAttr("disabled");
                $assignedS.removeAttr("disabled");
            });
        });
    };
    
    userAdministrationView.loadManager = function (managerId) {
        var $assignBtn = $('#assignManagerButton');
        var $unassignBtn = $('#unassignManagerButton');
        var $newBtn = $('#newManagerButton');
        var $deleteBtn = $('#deleteManagerButton');
        var $saveBtn = $('#saveManagerButton');
        var $cancelBtn = $('#cancelManagerButton');
        var $availableMS = $('#availableManagersMSelect');
        var $assignedMS = $('#assignedManagersMSelect');
        var $assignedS = $('#assignedManagersSelect');
        
        var spinner = '<div id="loading"' +
                '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                'alt="Loading..." /></div>';
            
        // Prepare to assign the managers by disabling the assignment
        // buttons and select lists!
        $assignBtn.attr("disabled", "disabled");
        $unassignBtn.attr("disabled", "disabled");
        $newBtn.attr("disabled", "disabled");
        $deleteBtn.attr("disabled", "disabled");
        $saveBtn.attr("disabled", "disabled").hide();
        $cancelBtn.attr("disabled", "disabled").hide();
        $availableMS.attr("disabled", "disabled");
        $assignedMS.attr("disabled", "disabled");
        $assignedS.attr("disabled", "disabled");

        $assignedS.parent().append(spinner);
        
        var myParams = {
            id: managerId,
            get_available: 1,
            get_assigned: 1,
            output: 'json'
        };
        
        $.ajax({
            url: userAdministrationView.endpoints.manager.viewRecord,
            data: myParams,
            type: 'GET',
            dataType: 'json',
            success: function(result, status, xhr) {
                // Its possible we will get a session timeout so check for it!
                if (result.error && result.error === "LOGIN_REQUIRED")
                {
                    window.setTimeout(function () {
                        $availableMS.removeAttr("disabled");
                        $assignedMS.removeAttr("disabled");
                        $assignedS.removeAttr("disabled");
                        $newBtn.removeAttr("disabled");
                        $deleteBtn.removeAttr("disabled");

                        if($availableMS.val() > 0)
                        {
                            $assignBtn.removeAttr("disabled");
                        }

                        if($assignedMS.val() > 0)
                        {
                            $unassignBtn.removeAttr("disabled");
                        }

                        $assignedS.parent().find('#loading').remove();
                        utilities.ajaxError.show(
                                "Error",
                                "Failed to load the manager",
                                xhr,
                                "error",
                                "Login Required"
                        );
                    }, 1000);

                    return;
                }
                    
                // Manager has been loaded!
                myParams.output = 'html';
                
                if(result.data && result.data.id !== "undefined")
                {
                    userAdministrationView.setupManagerView(result.data, myParams);
                    userAdministrationView.resetArenaLists();
                    
                    if (result.availableArenas !== "undefined")
                    {
                        userAdministrationView.loadArenaList($availableMS, result.availableArenas);
                    }
                    
                    if (result.assignedArenas !== "undefined")
                    {
                        userAdministrationView.loadArenaList($assignedMS, result.assignedArenas);
                    }
                }
            },
            error: function(xhr, status, errorThrown) {
                $availableMS.removeAttr("disabled");
                $assignedMS.removeAttr("disabled");
                $assignedS.removeAttr("disabled");
                $newBtn.removeAttr("disabled");
                $deleteBtn.removeAttr("disabled");
                
                if($availableMS.val() > 0)
                {
                    $assignBtn.removeAttr("disabled");
                }
                
                if($assignedMS.val() > 0)
                {
                    $unassignBtn.removeAttr("disabled");
                }
                
                $assignedS.parent().find('#loading').remove();
                
                utilities.ajaxError.show(
                        "Error",
                        "Failed to load the manager",
                        xhr,
                        status,
                        errorThrown
                );
            }
        });            
    };
    
    userAdministrationView.createDeleteModal = function () {
        var modalBody = '<div id="deleteModal" class="well"><h3>Are you sure you want to ' +
            'permanently delete this item?</h3><p class="lead text-error">This operation ' +
            'cannot be undone and will also delete this item from any related ' +
            'items. For example, deleting a location / venue will also delete ' +
            'any events associated with that location / venue.</p></div>';
    
        var modalFooter = '<button class="btn btn-large btn-danger" data-dismiss="' +
                'modal" type="button" aria-hidden="true" id="yes"><i class="' +
                'fa fa-fw fa-lg fa-check"></i> Yes</button>' +
                '<button class="btn btn-large" data-dismiss="' +
                'modal" type="button" aria-hidden="true" id="close"><i class="' +
                'fa fa-fw fa-lg fa-times"></i> No</button>';

        return utilities.modal.add('Confirm Action', modalBody, modalFooter, false, false);
    };
    
}( window.userAdministrationView = window.userAdministrationView || {}, jQuery ));