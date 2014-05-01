/* 
 * This is the jQuery plugin for the user view / update / create actions
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( contactManagementView, $, undefined ) {
    "use strict";
    // public properties
    contactManagementView.endpoints = {
        contact: {
            viewRecord: "/server/endpoint",
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint",
            deleteRecord: "/server/endpoint"
        }
    };
    
    contactManagementView.contact = {};
    contactManagementView.params = {};
    contactManagementView.isArenaManager = false;
    contactManagementView.statusList = [];
    contactManagementView.stateList = [];
    contactManagementView.Id = 0;
    contactManagementView.Name = '';
    
    contactManagementView.onReady = function () {
        if (typeof $.fn.editable === "undefined")
        { 
            contactManagementView.loadEditable();
        }
        else
        {
            contactManagementView.setupInitialContactView();
        }
        
        var $panel = $("#contactManagementView.panel.panel-primary");
        
        if ($panel.length > 0)
        {
            $panel.on('destroyed', function () {
                // We have been closed, so clean everything up!!!
                var $editables = $("#contactManagementView.panel.panel-primary .editable");
                
                $editables.editable('destroy');
            });
        }
        
        $('[data-toggle="tooltip"]').tooltip();
    };
    
    contactManagementView.loadEditable = function () {
        
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
                contactManagementView.setupInitialContactView();
            } else if (console && console.log) {
                console.log("Loading... " + scriptName);
            }
        }, 500);
        
    };

    contactManagementView.setupInitialContactView = function () {
        // Disable all buttons except the New button 
        // and hide the save / cancel buttons
        var that = this;
        var $assignBtn = $('#assignContactButton');
        var $unassignBtn = $('#unassignContactButton');
        var $newBtn = $('#newContactButton');
        var $deleteBtn = $('#deleteContactButton');
        var $saveBtn = $('#saveContactButton');
        var $cancelBtn = $('#cancelContactButton');
        var $availableMS = $('#availableContactsMSelect');
        var $assignedMS = $('#assignedContactsMSelect');
        var $assignedS = $('#assignedContactsSelect');
        
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
                // load in the new contact
                var contactId = $(this).val();

                that.resetContactView();
                
                if(contactId == 'none')
                {
                    contactManagementView.resetArenaLists();
                    
                    return;
                }
                
                that.loadContact(contactId);
            }
            else
            {
                // Nothing selected, so clear the edit screen
                that.resetContactView();
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
            var contactId = $assignedS.val();
            
            if(contactId === 'none' || contactId <= 0)
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
            
            // Prepare to assign the contacts by disabling the assignment
            // buttons and select lists!
            $assignBtn.attr("disabled", "disabled");
            $unassignBtn.attr("disabled", "disabled");
            $availableMS.attr("disabled", "disabled");
            $assignedMS.attr("disabled", "disabled");
            
            // Show we are busy by appending the spinner to the assign button
            $assignBtn.parent().prepend(spinner);
            
            // Now let's assign the contacts!!!
            var myParams = {
                name: 'assign',
                aids: values,
                id: contactId,
                pk: contactId,
                output: 'html'
            };
            
            $.ajax({
                url: contactManagementView.endpoints.contact.updateRecord,
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
                                    "Failed to assign the contact",
                                    xhr,
                                    "error",
                                    "Login Required"
                            );
                        }, 1000);

                        return;
                    }
                    
                    // Move the contacts to the assigned list
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
                        "Failed to assign the contact",
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
            var contactId = $assignedS.val();
            
            if(contactId === 'none' || contactId <= 0)
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
            
            // Prepare to unassign the contacts by disabling the assignment
            // buttons and select lists!
            $assignBtn.attr("disabled", "disabled");
            $unassignBtn.attr("disabled", "disabled");
            $availableMS.attr("disabled", "disabled");
            $assignedMS.attr("disabled", "disabled");
            
            // Show we are busy by appending the spinner to the assign button
            $unassignBtn.parent().prepend(spinner);
            
            // Now let's unassign the contacts!!!
            var myParams = {
                name: 'unassign',
                aids: values,
                id: contactId,
                pk: contactId,
                output: 'html'
            };
            
            $.ajax({
                url: contactManagementView.endpoints.contact.updateRecord,
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
                                    "Failed to unassign the contact",
                                    xhr,
                                    "error",
                                    "Login Required"
                            );
                        }, 1000);

                        return;
                    }
                    
                    // Move the contacts to the available list
                    $selected.each(function () {
                        $availableMS.append('<option value="' + $(this).val() + '">' + $(this).text() + '</option>');
                        var $s = $assignedS.find('option[value="' + $(this).val() + '"]');
                        
                        if($assignedS.val() == $s.val())
                        {
                            // We just unassigned the contact we are editing
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
                        "Failed to unassign the contact",
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
            contactManagementView.resetArenaLists();
            
            var myParams = {
                get_available: 1,
                get_assigned: 1,
                output: 'html'
            };

            contactManagementView.setupNewContactView(myParams);
            $newBtn.attr("disabled", "disabled");
            $deleteBtn.attr("disabled", "disabled");
        });
        
        $deleteBtn.on('click', function (e) {
            e.preventDefault();
            
            var $modal = contactManagementView.createDeleteModal();
            
            $modal.modal({
                loading: false,
                replace: false,
                modalOverflow: false
            });
            
            // The modal is now in the DOM so we can hook in to the button
            // clicks. Specifically, we only care about the 'yes' button.
            $('button#yes').on('click', function (e) {
                // They clicked yes and so now we must delete the contact!!!
                var contactId = $assignedS.val();
                
                // We must disable everything and put up our spinner...
                var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
            
                // Prepare to assign the contacts by disabling the assignment
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
            
                // Now let's delete the contact!!!
                var myParams = {
                    id: contactId,
                    pk: contactId,
                    output: 'html'
                };
                
                $.ajax({
                    url: contactManagementView.endpoints.contact.deleteRecord,
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
                                    "Failed to delete the contact",
                                    xhr,
                                    "error",
                                    "Login Required"
                                );
                            }, 1000);

                            return;
                        }
                    
                        // Remove the contact from the assigned list
                        $assignedMS.find('option[value="' + contactId + '"]').remove();
                        $assignedS.find('option[value="' + contactId + '"]').remove();
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
                            "Failed to delete the contact",
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
            
            // Prepare to assign the contacts by disabling the assignment
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
            // values to the server at once to create the contact.
            // I hope all goes well ;-)
            $('.contact-editable').editable('submit', { 
                url: contactManagementView.endpoints.contact.newRecord, 
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
                       var vals = $('#Contact_active, #Contact_last_name, #Contact_first_name, #Contact_email').editable('getValue');
                
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
                           '<h3><span class="badge badge-success">New contact added!</span></h3></div>';
                       
                       $('#contactDetails').prepend(msgArea);
                       
                       contactManagementView.resetArenaLists();
                            
                       if (data.availableArenas !== "undefined")
                       {
                           contactManagementView.loadArenaList($availableMS, data.availableArenas);
                       }

                       if (data.assignedArenas !== "undefined")
                       {
                           contactManagementView.loadArenaList($assignedMS, data.assignedArenas);
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
                            "Failed to add the new contact",
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
                   
                   $('#contactDetails').prepend(msgArea);
               }
           });
        });
        
        $cancelBtn.on('click', function (e) {
            e.preventDefault();
            
            contactManagementView.resetContactView();
            contactManagementView.resetArenaLists();
        });
        
        if(contactManagementView.contact.id !== "undefined")
        {
            contactManagementView.setupContactView(contactManagementView.contact, contactManagementView.params);
        }
        else
        {
            $newBtn.trigger('click');
        }
    };
    
    contactManagementView.setupContactEditables = function (params) {
        $('#Contact_first_name').editable({
            params: params,
            success: function(response, newValue) {
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
            
                var vals = $('#Contact_active, #Contact_last_name, #Contact_email').editable('getValue');
                
                // This is a bit risky as the user may select a different
                // a different contact by the time we get to this part even
                // though we have disabled the buttons and the select lists.
                var $assignedS = $('#assignedContactsSelect');
                
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
        
        $('#Contact_last_name').editable({
            params: params,
            success: function(response, newValue) {
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
            
                var vals = $('#Contact_active, #Contact_first_name, #Contact_email').editable('getValue');
                
                // This is a bit risky as the user may select a different
                // a different contact by the time we get to this part even
                // though we have disabled the buttons and the select lists.
                var $assignedS = $('#assignedContactsSelect');
                
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
        
        $('#Contact_active').editable({
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
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
            
                var vals = $('#Contact_first_name, #Contact_last_name, #Contact_email').editable('getValue');
                
                // This is a bit risky as the user may select a different
                // a different contact by the time we get to this part even
                // though we have disabled the buttons and the select lists.
                var $assignedS = $('#assignedContactsSelect');
                
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
        
        $('#Contact_email').editable({
            params: params,
            success: function(response, newValue) {
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
            
                var vals = $('#Contact_active, #Contact_first_name, #Contact_last_name').editable('getValue');
                
                // This is a bit risky as the user may select a different
                // a different contact by the time we get to this part even
                // though we have disabled the buttons and the select lists.
                var $assignedS = $('#assignedContactsSelect');
                
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
        
        $('#Contact_phone').editable({
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

        $("#Contact_phone").on('shown', function(e, editable) {
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
        
        $('#Contact_ext').editable({
            params: params
        });
        
        $('#Contact_fax').editable({
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

        $("#Contact_fax").on('shown', function(e, editable) {
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
        
        $('#Contact_fax_ext').editable({
            params: params
        });        
    };
    
    contactManagementView.setupContactView = function (contact, params) {
        var $contactDetails = $('#contactDetails');
        var $contactEditables = $('.contact-editable');
        
        // Destroy any existing editables!
        $("#Contact_phone").off('shown');
        $("#Contact_fax").off('shown');
        
        if ($contactEditables.length > 0)
        {
            $contactEditables.each(function () {
                $(this).editable('destroy');
            });
        }
        
        // Add one row at a time. 
        var contactView = '<strong>Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';
        
        contactView += '<tr><td style="width:33%">First Name</td><td>' +
                '<a href="#" id="Contact_first_name" data-name="first_name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + contact.first_name + '" ' +
                'title="First Name" class="contact-editable">' + 
                contact.first_name + '</a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Last Name</td><td>' +
                '<a href="#" id="Contact_last_name" data-name="last_name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + contact.last_name + '" ' +
                'title="Last Name" class="contact-editable">' +
                contact.last_name + '</a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Active</td><td>' +
                '<a href="#" id="Contact_active" data-name="active" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + contact.active + '" ' +
                'title="Contact Status" class="contact-editable">';
        
        if(contact.active == 1)
        {
            contactView += 'Active</a></td></tr>';
        }
        else
        {
            contactView += 'Inactive</a></td></tr>';
        }
        
        contactView += '</tbody></table>';
        
        // Now the next table of info!
        contactView += '<strong>Email & Phone</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        contactView += '<tr><td style="width:33%">E-mail</td><td>' +
                '<a href="#" id="Contact_email" data-name="email" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + contact.email + '" ' +
                'title="E-mail Address" class="contact-editable">' +
                contact.email + '</a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Phone</td><td>' +
                '<a href="#" id="Contact_phone" data-name="phone" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + contact.phone + '" ' +
                'title="Ten digit phone number" class="contact-editable">' +
                contact.phone + '</a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Extension</td><td>' +
                '<a href="#" id="Contact_ext" data-name="ext" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + (contact.ext ? contact.ext : '') + '" ' +
                'title="Phone extension" class="contact-editable">' +
                (contact.ext ? contact.ext : '') + '</a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Fax</td><td>' +
                '<a href="#" id="Contact_fax" data-name="fax" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + (contact.fax ? contact.fax : '') + '" ' +
                'title="Ten digit fax number" class="contact-editable">' +
                (contact.fax ? contact.fax : '') + '</a></td></tr>';
       
        contactView += '<tr><td style="width:33%">Fax Extension</td><td>' +
                '<a href="#" id="Contact_fax_ext" data-name="fax_ext" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + (contact.fax_ext ? contact.fax_ext : '') + '" ' +
                'title="Fax extension" class="contact-editable">' +
                (contact.fax_ext ? contact.fax_ext : '') + '</a></td></tr></tbody></table>';
        
        // Ok, now we can fade-out the current view, and then fade in our new
        // view!
        var $cv = $('<div id="contactDetails">' + contactView + '</div>');
        
        $cv.hide();
        
        $contactDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $contactDetails.parent().prepend($cv);
            
            // Setup the contact editables before we fade in!
            contactManagementView.setupContactEditables(params);
            
            // Now fade us in!
            $cv.fadeIn(500, function() {
                var $assignBtn = $('#assignContactButton');
                var $unassignBtn = $('#unassignContactButton');
                var $newBtn = $('#newContactButton');
                var $deleteBtn = $('#deleteContactButton');
                var $availableMS = $('#availableContactsMSelect');
                var $assignedMS = $('#assignedContactsMSelect');
                var $assignedS = $('#assignedContactsSelect');

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
                $contactDetails.remove();
            });            
        });
    };
    
    contactManagementView.setupNewContactView = function (params) {
        var $contactDetails = $('#contactDetails');
        var $contactEditables = $('.contact-editable');
        
        // Destroy any existing editables!
        $("#Contact_phone").off('shown');
        $("#Contact_fax").off('shown');
        
        if ($contactEditables.length > 0)
        {
            $contactEditables.each(function () {
                $(this).editable('destroy');
            });
        }
        
        // Add one row at a time. 
        var contactView = '<strong>Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';
        
        contactView += '<tr><td style="width:33%">First Name</td><td>' +
                '<a href="#" id="Contact_first_name" data-name="first_name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'title="First Name" class="contact-editable"></a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Last Name</td><td>' +
                '<a href="#" id="Contact_last_name" data-name="last_name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'title="Last Name" class="contact-editable"></a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Active</td><td>' +
                '<a href="#" id="Contact_active" data-name="active" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'data-value="1"' +
                'title="Contact Status" class="contact-editable">Active</a></td></tr>';
        
        contactView += '</tbody></table>';
        
        // Now the next table of info!
        contactView += '<strong>Email & Phone</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        contactView += '<tr><td style="width:33%">E-mail</td><td>' +
                '<a href="#" id="Contact_email" data-name="email" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'title="E-mail Address" class="contact-editable"></a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Phone</td><td>' +
                '<a href="#" id="Contact_phone" data-name="phone" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'title="Ten digit phone number" class="contact-editable"></a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Extension</td><td>' +
                '<a href="#" id="Contact_ext" data-name="ext" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'title="Phone extension" class="contact-editable"></a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Fax</td><td>' +
                '<a href="#" id="Contact_fax" data-name="fax" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'title="Ten digit fax number" class="contact-editable"></a></td></tr>';
       
        contactView += '<tr><td style="width:33%">Fax Extension</td><td>' +
                '<a href="#" id="Contact_fax_ext" data-name="fax_ext" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'title="Fax extension" class="contact-editable"></a></td></tr>';
                
        contactView += '<tr style="display:none;"><td>Output</td><td>' +
                '<a href="#" id="Contact_output" data-name="output" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'data-value="json" title="Output" class="contact-editable">' +
                'json</a></td></tr>';
                
        contactView += '<tr style="display:none;"><td>Get Available</td><td>' +
                '<a href="#" id="Contact_get_available" data-name="get_available" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                contactManagementView.endpoints.contact.updateRecord + '" ' +
                'data-value="1" title="Get Available" class="contact-editable">' +
                'json</a></td></tr>';
                
                
        contactView += '</tbody></table>';
        
        // Ok, now we can fade-out the current view, and then fade in our new
        // view!
        var $cv = $('<div id="contactDetails">' + contactView + '</div>');
        
        $cv.hide();
        
        $contactDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $contactDetails.parent().prepend($cv);
            
            // Setup the contact editables before we fade in!
            contactManagementView.setupContactEditables(params);
            
            $('#Contact_get_available').editable({
                params: params
            });
            
            $('#Contact_output').editable({
                params: params
            });
            
            // Now fade us in!
            $cv.fadeIn(500, function() {
                var $assignBtn = $('#assignContactButton');
                var $unassignBtn = $('#unassignContactButton');
                var $saveBtn = $('#saveContactButton');
                var $cancelBtn = $('#cancelContactButton');
                var $availableMS = $('#availableContactsMSelect');
                var $assignedMS = $('#assignedContactsMSelect');

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
                
                $contactDetails.remove();
            });            
        });
    };
    
    contactManagementView.loadArenaList = function ($element, list){
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
    
    contactManagementView.resetArenaLists = function (){
        var $assignBtn = $('#assignContactButton');
        var $unassignBtn = $('#unassignContactButton');
        var $availableMS = $('#availableContactsMSelect');
        var $assignedMS = $('#assignedContactsMSelect');
        
        $availableMS.val('');
        $availableMS.empty();
        $assignBtn.attr("disabled", "disabled");
        
        $assignedMS.val('');
        $assignedMS.empty();
        $unassignBtn.attr("disabled", "disabled");        
    };
    
    contactManagementView.resetContactView = function (){
        var $contactDetails = $('#contactDetails');
        var $contactEditables = $('.contact-editable');
        var $newBtn = $('#newContactButton');
        var $deleteBtn = $('#deleteContactButton');
        var $saveBtn = $('#saveContactButton');
        var $cancelBtn = $('#cancelContactButton');
        var $assignedS = $('#assignedContactsSelect');
        
        $newBtn.attr("disabled", "disabled");
        $deleteBtn.attr("disabled", "disabled");
        $saveBtn.attr("disabled", "disabled").hide();
        $cancelBtn.attr("disabled", "disabled").hide();
        
        // Destroy any existing editables!
        $("#Contact_phone").off('shown');
        $("#Contact_fax").off('shown');
        
        if ($contactEditables.length > 0)
        {
            $contactEditables.each(function () {
                $(this).editable('destroy');
            });
        }
        
        $contactDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $contactDetails.empty();
            
            // Now fade us in!
            $contactDetails.fadeIn(500, function () {
                $newBtn.removeAttr("disabled");
                $assignedS.removeAttr("disabled");
            });
        });
    };
    
    contactManagementView.loadContact = function (contactId) {
        var $assignBtn = $('#assignContactButton');
        var $unassignBtn = $('#unassignContactButton');
        var $newBtn = $('#newContactButton');
        var $deleteBtn = $('#deleteContactButton');
        var $saveBtn = $('#saveContactButton');
        var $cancelBtn = $('#cancelContactButton');
        var $availableMS = $('#availableContactsMSelect');
        var $assignedMS = $('#assignedContactsMSelect');
        var $assignedS = $('#assignedContactsSelect');
        
        var spinner = '<div id="loading"' +
                '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                'alt="Loading..." /></div>';
            
        // Prepare to assign the contacts by disabling the assignment
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
            id: contactId,
            get_available: 1,
            get_assigned: 1,
            output: 'json'
        };
        
        $.ajax({
            url: contactManagementView.endpoints.contact.viewRecord,
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
                                "Failed to load the contact",
                                xhr,
                                "error",
                                "Login Required"
                        );
                    }, 1000);

                    return;
                }
                    
                // Contact has been loaded!
                myParams.output = 'html';
                
                if(result.data && result.data.id !== "undefined")
                {
                    contactManagementView.setupContactView(result.data, myParams);
                    contactManagementView.resetArenaLists();
                    
                    if (result.availableArenas !== "undefined")
                    {
                        contactManagementView.loadArenaList($availableMS, result.availableArenas);
                    }
                    
                    if (result.assignedArenas !== "undefined")
                    {
                        contactManagementView.loadArenaList($assignedMS, result.assignedArenas);
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
                        "Failed to load the contact",
                        xhr,
                        status,
                        errorThrown
                );
            }
        });            
    };
    
    contactManagementView.createDeleteModal = function () {
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
    
}( window.contactManagementView = window.contactManagementView || {}, jQuery ));