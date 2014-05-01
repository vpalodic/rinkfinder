/* 
 * This is the jQuery plugin for the user view / update / create actions
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( arenaManagementView, $, undefined ) {
    "use strict";
    // public properties
    arenaManagementView.endpoints = {
        arena: {
            viewRecord: "/server/endpoint",
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint"
        },
        location: {
            viewRecord: "/server/endpoint",
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint",
            deleteRecord: "/server/endpoint"
        },
        contact: {
            viewRecord: "/server/endpoint",
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint",
            deleteRecord: "/server/endpoint"
        },
        event: {
            viewRecord: "/server/endpoint",
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint",
            deleteRecord: "/server/endpoint"
        },
        manager: {
            viewRecord: "/server/endpoint",
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint",
            deleteRecord: "/server/endpoint"
        }
    };
    
    arenaManagementView.arena = {};
    arenaManagementView.locations = {};
    arenaManagementView.locationTypes = [];
    arenaManagementView.locationStatuses = [];
    arenaManagementView.contacts = {};
    arenaManagementView.events = {};
    arenaManagementView.managers = {};
    arenaManagementView.params = {};
    arenaManagementView.isArenaManager = false;
    arenaManagementView.statusList = [];
    arenaManagementView.stateList = [];
    
    arenaManagementView.attribute = {
        name: "",
        oldVal: "",
        newVal: ""
    };
    
    arenaManagementView.Id = 0;
    arenaManagementView.Name = '';
    
    arenaManagementView.onReady = function () {
        if (typeof $.fn.editable === "undefined")
        { 
            arenaManagementView.loadEditable();
        }
        else
        {
            arenaManagementView.enableEditable();
        }
        
        var $panel = $("#arenaManagementView.panel.panel-primary");
        
        if ($panel.length > 0)
        {
            $panel.on('destroyed', function () {
                // We have been closed, so clean everything up!!!
                var $editables = $("#arenaManagementView.panel.panel-primary .editable");
                
                $editables.editable('destroy');
            });
        }
        
        arenaManagementView.setupArenaGeocoding();
        arenaManagementView.setupInitialContactView();
        arenaManagementView.setupInitialLocationView();
        
        $('[data-toggle="tooltip"]').tooltip();
    };
    
    arenaManagementView.loadEditable = function () {
        
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
                arenaManagementView.enableEditable();
            } else if (console && console.log) {
                console.log("Loading... " + scriptName);
            }
        }, 500);
        
    };
    
    arenaManagementView.enableEditable = function () {
        arenaManagementView.setupArenaName();
        arenaManagementView.setupArenaLogo();
        arenaManagementView.setupArenaUrl();
        arenaManagementView.setupArenaExternalId();
        arenaManagementView.setupArenaTags();
        arenaManagementView.setupArenaStatus();
        arenaManagementView.setupArenaAddressLine1();
        arenaManagementView.setupArenaAddressLine2();
        arenaManagementView.setupArenaCity();
        arenaManagementView.setupArenaState();
        arenaManagementView.setupArenaZip();
        arenaManagementView.setupArenaPhone();
        arenaManagementView.setupArenaExtension();
        arenaManagementView.setupArenaFax();
        arenaManagementView.setupArenaFaxExtension();
        arenaManagementView.setupArenaDescription();
        arenaManagementView.setupArenaNotes();
    };
    
    arenaManagementView.setupArenaName = function () {
        $('#Arena_name').editable({
            params: arenaManagementView.params
        });
    };
    
    arenaManagementView.setupArenaLogo = function () {
        $('#Arena_logo').editable({
            params: arenaManagementView.params,
            success: function(response, newValue) {
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
            
                var $img = $('#Arena_logo_img');
                var $parent = $img.parent();
                var $newImg = $('<img id="Arena_logo_img" class="img-circle" src="' + newValue + '" alt="Facility Logo" />');
                $img.remove();
                
                $parent.append($newImg);
                $img = $newImg;
            }
        });
    };
    
    arenaManagementView.setupArenaUrl = function () {
        $('#Arena_url').editable({
            params: arenaManagementView.params
        });
    };
    
    arenaManagementView.setupArenaExternalId = function () {
        $('#Arena_external_id').editable({
            params: arenaManagementView.params
        });
    };
    
    arenaManagementView.setupArenaTags = function () {
        $('#Arena_tags').editable({
            params: arenaManagementView.params
        });
    };
    
    arenaManagementView.setupArenaStatus = function () {
        if(arenaManagementView.isArenaManager !== 1)
        {
            return;
        }
        
        $('#Arena_status_id').editable({
            params: arenaManagementView.params,
            showbuttons: false,
            source: arenaManagementView.statusList
        });
    };
    
    arenaManagementView.setupArenaAddressLine1 = function () {
        $('#Arena_address_line1').editable({
            params: arenaManagementView.params
        });
    };
    
    arenaManagementView.setupArenaAddressLine2 = function () {
        $('#Arena_address_line2').editable({
            params: arenaManagementView.params
        });
    };
    
    arenaManagementView.setupArenaCity = function () {
        $('#Arena_city').editable({
            params: arenaManagementView.params
        });
    };
    
    arenaManagementView.setupArenaState = function () {
        $('#Arena_state').editable({
            params: arenaManagementView.params,
            showbuttons: false,
            source: arenaManagementView.stateList
        });
    };
    
    arenaManagementView.setupArenaZip = function () {
        $('#Arena_zip').editable({
            params: arenaManagementView.params
        });
    };
    
    arenaManagementView.setupArenaGeocoding = function () {
        $('#Arena_geocode_btn').on('click', function (e) {
            e.preventDefault();
            
            var geocodedAddress = '';

            var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
            
            var $parent = $('#Arena_geocode_btn').parent();
            
            $parent.append(spinner);
            $('#Arena_geocode_btn').hide(300, function () {
                var geocoder = new google.maps.Geocoder();
                
                var addressObj = $('#Arena_address_line1, #Arena_address_line2, #Arena_city, #Arena_state, #Arena_zip').editable('getValue');
                var address1 = addressObj.address_line1;
                var address2 = addressObj.address_line2;
                var city = addressObj.city;
                var state = addressObj.state;
                var zip = addressObj.zip;
                
                if (typeof address1.trim === "function")
                {
                    address1 = address1.trim();
                    address2 = address2.trim();
                    city = city.trim();
                    state = state.trim();
                    zip = String(zip).trim();
                }
                
                var address = '';
                
                if(address2.length > 0)
                {
                    address = address1 + ', ' + address2 + ', ' + city + ', ' + state + ' ' + zip;
                }
                else
                {
                    address = address1 + ', ' + city + ', ' + state + ' ' + zip;
                }

                if (geocodedAddress !== address)
                {
                    geocoder.geocode({address: address}, function(results, status) {
                        if (status === google.maps.GeocoderStatus.OK)
                        {
                            var centerpoint = results[0].geometry.location;
                            geocodedAddress = address;
                            
                            // Save the updated geocoding!
                            var myParams = arenaManagementView.params;
                            myParams.name = 'geocoding';
                            myParams.value = [centerpoint.lat(), centerpoint.lng()];
                            myParams.pk = myParams.id;
                            myParams.lat = centerpoint.lat();
                            myParams.lng = centerpoint.lng();
                            
                            $.ajax({
                                url: arenaManagementView.endpoints.arena.updateRecord,
                                data: myParams,
                                type: 'POST',
                                dataType: "html",
                                success: function () {
                                            $('#Arena_geocode_btn').show(300, function () {
                                                var $link = $('#Arena_directions');
                                                
                                                var newHref = 'http://maps.google.com/maps?daddr=' + escape(address);
                                                
                                                $link.attr('href', newHref);
                                                
                                                $parent.find('#loading').remove();
                                                var alertSuccess = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Success!</strong></div>';
                                                $parent.prepend(alertSuccess);
                                            });
                                },
                                error: function(xhr, status, errorThrown) {
                                    $('#Arena_geocode_btn').show(300, function () {
                                        $parent.find('#loading').remove();
                                    });
                                    utilities.ajaxError.show(
                                            "Error",
                                            "Failed to update the lattitude and longitude",
                                            xhr,
                                            status,
                                            errorThrown
                                    );
                                }
                            });
                        } else
                        {
                            $('#Arena_geocode_btn').show(300, function () {
                                $parent.find('#loading').remove();
                                var alertSuccess = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Error!</strong> ' + address + ' not found</div>';
                                $parent.append(alertSuccess);
                            });
                        }
                    });
                }
            });
        });
    };
    
    arenaManagementView.setupArenaPhone = function () {
        $('#Arena_phone').editable({
            params: arenaManagementView.params,
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

        $("#Arena_phone").on('shown', function(e, editable) {
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
    };
    
    arenaManagementView.setupArenaExtension = function () {
        $('#Arena_ext').editable({
            params: arenaManagementView.params
        });
    };
    
    arenaManagementView.setupArenaFax = function () {
        $('#Arena_fax').editable({
            params: arenaManagementView.params,
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

        $("#Arena_fax").on('shown', function(e, editable) {
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
    };
    
    arenaManagementView.setupArenaFaxExtension = function () {
        $('#Arena_fax_ext').editable({
            params: arenaManagementView.params
        });
    };
    
    arenaManagementView.setupArenaDescription = function () {
        $('#Arena_description').editable({
            params: arenaManagementView.params
        });
        
        $('#Arena_description_edit').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            
            $('#Arena_description').editable('toggle');
        });
    };
    
    arenaManagementView.setupArenaNotes = function () {
        $('#Arena_notes').editable({
            params: arenaManagementView.params
        });
        
        $('#Arena_notes_edit').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            
            $('#Arena_notes').editable('toggle');
        });
    };

    arenaManagementView.setupInitialContactView = function () {
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
                value: values,
                aid: arenaManagementView.params.id,
                id: 1,
                pk: 1,
                output: 'html'
            };
            
            $.ajax({
                url: arenaManagementView.endpoints.contact.updateRecord,
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
                        "Failed to assign the contacts",
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
            
            // Now let's assign the contacts!!!
            var myParams = {
                name: 'unassign',
                value: values,
                aid: arenaManagementView.params.id,
                id: 1,
                pk: 1,
                output: 'html'
            };
            
            $.ajax({
                url: arenaManagementView.endpoints.contact.updateRecord,
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
                        "Failed to unassign the contacts",
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
            
            var myParams = {
                aid: arenaManagementView.params.aid,
                output: 'html'
            };

            arenaManagementView.setupNewContactView(myParams);
            $newBtn.attr("disabled", "disabled");
            $deleteBtn.attr("disabled", "disabled");
        });
        
        $deleteBtn.on('click', function (e) {
            e.preventDefault();
            
            var $modal = arenaManagementView.createDeleteModal();
            
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
                    aid: arenaManagementView.params.id,
                    id: contactId,
                    pk: contactId,
                    output: 'html'
                };
                
                $.ajax({
                    url: arenaManagementView.endpoints.contact.deleteRecord,
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
                url: arenaManagementView.endpoints.contact.newRecord, 
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
                       $assignedMS.append(newOption);
                       
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
            
            arenaManagementView.resetContactView();
        });
    };
    
    arenaManagementView.setupContactEditables = function (params) {
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
                var $assignedMS = $('#assignedContactsMSelect');
                var $assignedS = $('#assignedContactsSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $assignedS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                var $op1 = $assignedS.find('option[value="' + id + '"]');
                var $op2 = $assignedMS.find('option[value="' + id + '"]');
                
                var active = (vals.active == 0) ? 'Inactive' : 'Active';
                
                var newText = vals.last_name + ', ' + newValue + ' - ' + vals.email + ' (' + active + ')';
                
                $op1.text(newText);
                $op2.text(newText);
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
                var $assignedMS = $('#assignedContactsMSelect');
                var $assignedS = $('#assignedContactsSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $assignedS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                var $op1 = $assignedS.find('option[value="' + id + '"]');
                var $op2 = $assignedMS.find('option[value="' + id + '"]');
                
                var active = (vals.active == 0) ? 'Inactive' : 'Active';
                
                var newText = newValue + ', ' + vals.first_name + ' - ' + vals.email + ' (' + active + ')';
                
                $op1.text(newText);
                $op2.text(newText);
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
                var $assignedMS = $('#assignedContactsMSelect');
                var $assignedS = $('#assignedContactsSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $assignedS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                var $op1 = $assignedS.find('option[value="' + id + '"]');
                var $op2 = $assignedMS.find('option[value="' + id + '"]');
                
                var active = (newValue == 0) ? 'Inactive' : 'Active';
                
                var newText = vals.last_name + ', ' + vals.first_name + ' - ' + vals.email + ' (' + active + ')';
                
                $op1.text(newText);
                $op2.text(newText);
            }
        });
        
        $('#Contact_primary_contact').editable({
            params: params,
            showbuttons: false,
            source: [{
                    value: 0,
                    text: 'No'
            }, {
                value: 1,
                text: 'Yes'
            }]
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
                var $assignedMS = $('#assignedContactsMSelect');
                var $assignedS = $('#assignedContactsSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $assignedS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                var $op1 = $assignedS.find('option[value="' + id + '"]');
                var $op2 = $assignedMS.find('option[value="' + id + '"]');
                
                var active = (vals.active == 0) ? 'Inactive' : 'Active';
                
                var newText = vals.last_name + ', ' + vals.first_name + ' - ' + newValue + ' (' + active + ')';
                
                $op1.text(newText);
                $op2.text(newText);
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
    
    arenaManagementView.setupContactView = function (contact, params) {
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
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + contact.first_name + '" ' +
                'title="First Name" class="contact-editable">' + 
                contact.first_name + '</a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Last Name</td><td>' +
                '<a href="#" id="Contact_last_name" data-name="last_name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + contact.last_name + '" ' +
                'title="Last Name" class="contact-editable">' +
                contact.last_name + '</a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Active</td><td>' +
                '<a href="#" id="Contact_active" data-name="active" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
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
        
        contactView += '<tr><td style="width:33%">Primary</td><td>' +
                '<a href="#" id="Contact_primary_contact" data-name="primary_contact" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + contact.primary_contact + '" ' +
                'title="Primary Status" class="contact-editable">';
        
        if(contact.primary_contact == 1)
        {
            contactView += 'Yes</a></td></tr>';
        }
        else
        {
            contactView += 'No</a></td></tr>';
        }
        
        contactView += '</tbody></table>';
        
        // Now the next table of info!
        contactView += '<strong>Email & Phone</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        contactView += '<tr><td style="width:33%">E-mail</td><td>' +
                '<a href="#" id="Contact_email" data-name="email" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + contact.email + '" ' +
                'title="E-mail Address" class="contact-editable">' +
                contact.email + '</a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Phone</td><td>' +
                '<a href="#" id="Contact_phone" data-name="phone" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + contact.phone + '" ' +
                'title="Ten digit phone number" class="contact-editable">' +
                contact.phone + '</a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Extension</td><td>' +
                '<a href="#" id="Contact_ext" data-name="ext" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + (contact.ext ? contact.ext : '') + '" ' +
                'title="Phone extension" class="contact-editable">' +
                (contact.ext ? contact.ext : '') + '</a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Fax</td><td>' +
                '<a href="#" id="Contact_fax" data-name="fax" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + (contact.fax ? contact.fax : '') + '" ' +
                'title="Ten digit fax number" class="contact-editable">' +
                (contact.fax ? contact.fax : '') + '</a></td></tr>';
       
        contactView += '<tr><td style="width:33%">Fax Extension</td><td>' +
                '<a href="#" id="Contact_fax_ext" data-name="fax_ext" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
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
            arenaManagementView.setupContactEditables(params);
            
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
    
    arenaManagementView.setupNewContactView = function (params) {
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
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'title="First Name" class="contact-editable"></a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Last Name</td><td>' +
                '<a href="#" id="Contact_last_name" data-name="last_name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'title="Last Name" class="contact-editable"></a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Active</td><td>' +
                '<a href="#" id="Contact_active" data-name="active" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-value="1"' +
                'title="Contact Status" class="contact-editable">Active</a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Primary</td><td>' +
                '<a href="#" id="Contact_primary_contact" data-name="primary_contact" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-value="0"' +
                'title="Primary Status" class="contact-editable">No</a></td></tr>';
        
        contactView += '</tbody></table>';
        
        // Now the next table of info!
        contactView += '<strong>Email & Phone</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        contactView += '<tr><td style="width:33%">E-mail</td><td>' +
                '<a href="#" id="Contact_email" data-name="email" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'title="E-mail Address" class="contact-editable"></a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Phone</td><td>' +
                '<a href="#" id="Contact_phone" data-name="phone" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'title="Ten digit phone number" class="contact-editable"></a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Extension</td><td>' +
                '<a href="#" id="Contact_ext" data-name="ext" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'title="Phone extension" class="contact-editable"></a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Fax</td><td>' +
                '<a href="#" id="Contact_fax" data-name="fax" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'title="Ten digit fax number" class="contact-editable"></a></td></tr>';
       
        contactView += '<tr><td style="width:33%">Fax Extension</td><td>' +
                '<a href="#" id="Contact_fax_ext" data-name="fax_ext" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'title="Fax extension" class="contact-editable"></a></td></tr>';
                
        contactView += '<tr style="display:none;"><td>Arena ID</td><td>' +
                '<a href="#" id="Contact_arena_id" data-name="aid" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-value="' + arenaManagementView.params.aid + '" ' +
                'title="Arena ID" class="contact-editable">' +
                arenaManagementView.params.aid + '</a></td></tr>';
                
        contactView += '<tr style="display:none;"><td>Output</td><td>' +
                '<a href="#" id="Contact_output" data-name="output" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-value="json" title="Output" class="contact-editable">' +
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
            arenaManagementView.setupContactEditables(params);
            
            $('#Contact_arena_id').editable({
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
    
    arenaManagementView.resetContactView = function (){
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
    
    arenaManagementView.loadContact = function (contactId) {
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
            aid: arenaManagementView.params.aid,
            output: 'json'
        };
        
        $.ajax({
            url: arenaManagementView.endpoints.contact.viewRecord,
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
                    arenaManagementView.setupContactView(result.data, myParams);
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
    
    arenaManagementView.createDeleteModal = function () {
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
    
    arenaManagementView.setupInitialLocationView = function () {
        // Disable all buttons except the New button 
        // and hide the save / cancel buttons
        var that = this;
        var $newBtn = $('#newLocationButton');
        var $deleteBtn = $('#deleteLocationButton');
        var $saveBtn = $('#saveLocationButton');
        var $cancelBtn = $('#cancelLocationButton');
        var $locationS = $('#locationsSelect');
        
        $deleteBtn.attr("disabled", "disabled");
        $saveBtn.attr("disabled", "disabled").hide();
        $cancelBtn.attr("disabled", "disabled").hide();
        
        // Setup the select change handlers!
        $locationS.on('change', function (e) {
            // Get the selected options from the available select list
            var $selected = $locationS.find('option:selected');
            
            if($selected.length > 0)
            {
                // we have our selection, so clear the edit screen and
                // load in the new contact
                var locationId = $(this).val();

                that.resetLocationView();
                
                if(locationId == 'none')
                {
                    return;
                }
                
                that.loadLocation(locationId);
            }
            else
            {
                // Nothing selected, so clear the edit screen
                that.resetLocationView();
            }
        });
        
        // Setup the button click handlers!
        $newBtn.on('click', function (e) {
            e.preventDefault();
            
            $locationS.attr("disabled", "disabled");
            $locationS.val('none');
            
            var myParams = {
                aid: arenaManagementView.params.aid,
                output: 'html'
            };

            arenaManagementView.setupNewLocationView(myParams);
            $newBtn.attr("disabled", "disabled");
            $deleteBtn.attr("disabled", "disabled");
        });
        
        $deleteBtn.on('click', function (e) {
            e.preventDefault();
            
            var $modal = arenaManagementView.createDeleteModal();
            
            $modal.modal({
                loading: false,
                replace: false,
                modalOverflow: false
            });
            
            // The modal is now in the DOM so we can hook in to the button
            // clicks. Specifically, we only care about the 'yes' button.
            $('button#yes').on('click', function (e) {
                // They clicked yes and so now we must delete the contact!!!
                var locationId = $locationS.val();
                
                // We must disable everything and put up our spinner...
                var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
            
                // Prepare to delete the location.
                $locationS.attr("disabled", "disabled");
                $newBtn.attr("disabled", "disabled");
                $deleteBtn.attr("disabled", "disabled");
                $saveBtn.attr("disabled", "disabled").hide();
                $cancelBtn.attr("disabled", "disabled").hide();
            
                // Show we are busy by appending the spinner to the assign button
                $deleteBtn.parent().prepend(spinner);
            
                // Now let's delete the location!!!
                var myParams = {
                    aid: arenaManagementView.params.id,
                    id: locationId,
                    pk: locationId,
                    output: 'html'
                };
                
                $.ajax({
                    url: arenaManagementView.endpoints.location.deleteRecord,
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
                                $locationS.removeAttr("disabled");
                                $newBtn.removeAttr("disabled");
                                $deleteBtn.removeAttr("disabled");
                                utilities.ajaxError.show(
                                        "Error",
                                        "Failed to delete the venue",
                                        xhr,
                                        "error",
                                        "Login Required"
                                );
                            }, 1000);

                            return;
                        }
                    
                        // Remove the location from the list
                        $locationS.find('option[value="' + locationId + '"]').remove();
                        $locationS.val('none').trigger('change');
                        
                        $locationS.removeAttr("disabled");
                        $newBtn.removeAttr("disabled");
                        $deleteBtn.parent().find('#loading').remove();
                    },
                    error: function(xhr, status, errorThrown) {
                        $locationS.removeAttr("disabled");
                        $newBtn.removeAttr("disabled");
                        $deleteBtn.parent().find('#loading').remove();
                    
                        utilities.ajaxError.show(
                            "Error",
                            "Failed to delete the venue",
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
            
            // Prepare to create the new location
            $locationS.attr("disabled", "disabled");
            $newBtn.attr("disabled", "disabled");
            $deleteBtn.attr("disabled", "disabled");
            $saveBtn.attr("disabled", "disabled");
            $cancelBtn.attr("disabled", "disabled");
            
            // Show we are busy by appending the spinner to the assign button
            $saveBtn.parent().prepend(spinner);
            
            // Ok, we are going for a ride here as we will send all of the
            // values to the server at once to create the location.
            // I hope all goes well ;-)
            $('.location-editable').editable('submit', { 
                url: arenaManagementView.endpoints.location.newRecord, 
                ajaxOptions: {
                    dataType: 'json'
                },
                success: function(data, config) {
                   $saveBtn.removeAttr("disabled");
                   $cancelBtn.removeAttr("disabled");
                   
                   $saveBtn.parent().find("#loading").remove();
                   
                   if(data && data.id) {  //record created, response like {"id": 2}
                       // set pk
                       $(this).editable('option', 'pk', data.id);
                       
                       $('#Location_tags').editable('setValue', data.tags);
                       
                       // remove unsaved class
                       $(this).removeClass('editable-unsaved');
                       
                       // update the select lists!
                       var vals = $('#Location_name').editable('getValue');
                       var strStatus = $('#Location_status_id').text();
                       var strType = $('#Location_type_id').text();
                
                       var newText = vals.name + ' - ' + strType + ' (' + strStatus + ')';
                       var newOption = '<option value="' + data.id + '">' + newText + '</option>';
                       $locationS.append(newOption);
                       
                       // Now select the newly appended option
                       $locationS.val(data.id); // We don't trigger a change!
                       
                       $saveBtn.attr("disabled", "disabled").hide(250);
                       $cancelBtn.attr("disabled", "disabled").hide(250);
                       $locationS.removeAttr("disabled");
                       $newBtn.removeAttr("disabled");
                       $deleteBtn.removeAttr("disabled");
                       
                       //show messages
                       var msgArea = '<div class="alert alert-success">' +
                           '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                           '<h3><span class="badge badge-success">New location added!</span></h3></div>';
                       
                       $('#locationDetails').prepend(msgArea);
                   } else if(data && data.errors){ 
                       //server-side validation error, response like {"errors": {"username": "username already exist"} }
                       config.error.call(this, data.errors);
                   } else if(data && data.error){ 
                       //server-side validation error, response like {"errors": {"username": "username already exist"} }
                       config.error.call(this, data.error);
                   }
               },
               error: function(errors) {
                   $saveBtn.removeAttr("disabled");
                   $cancelBtn.removeAttr("disabled");
                   
                   $saveBtn.parent().find("#loading").remove();
                   
                   var msg = '';
                   if(errors && errors.responseText) { //ajax error, errors = xhr object
                       msg = errors.responseText;
                       utilities.ajaxError.show(
                            "Error",
                            "Failed to add the new location",
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
                   
                   $('#locationDetails').prepend(msgArea);
               }
           });
        });
        
        $cancelBtn.on('click', function (e) {
            e.preventDefault();
            
            arenaManagementView.resetLocationView();
        });
    };
    
    arenaManagementView.setupLocationEditables = function (params) {
        $('#Location_name').editable({
            params: params,
            success: function(response, newValue) {
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
            
                var strStatus = $('#Location_status_id').text();
                var strType = $('#Location_type_id').text();
                
                var newText = newValue + ' - ' + strType + ' (' + strStatus + ')';
                
                // This is a bit risky as the user may select a different
                // location by the time we get to this part even
                // though we have disabled the buttons and the select list.
                var $locationS = $('#locationsSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $locationS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                $locationS.find('option[value="' + id + '"]').text(newText);
            }
        });
        
        $('#Location_external_id').editable({
            params: params
        });
        
        $('#Location_status_id').editable({
            params: params,
            showbuttons: false,
            source: arenaManagementView.locationStatuses,
            success: function(response, newValue) {
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
            
                var vals = $('#Location_name').editable('getValue');
                var strStatus = $('#Location_status_id').text();
                var strType = $('#Location_type_id').text();
                
                var newText = vals.name + ' - ' + strType + ' (' + strStatus + ')';
                
                // This is a bit risky as the user may select a different
                // location by the time we get to this part even
                // though we have disabled the buttons and the select list.
                var $locationS = $('#locationsSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $locationS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                $locationS.find('option[value="' + id + '"]').text(newText);
            }
        });
        
        $('#Location_type_id').editable({
            params: params,
            showbuttons: false,
            source: arenaManagementView.locationTypes,
            success: function(response, newValue) {
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
            
                var vals = $('#Location_name').editable('getValue');
                var strStatus = $('#Location_status_id').text();
                var strType = $('#Location_type_id').text();
                
                var newText = vals.name + ' - ' + strType + ' (' + strStatus + ')';
                
                // This is a bit risky as the user may select a different
                // location by the time we get to this part even
                // though we have disabled the buttons and the select list.
                var $locationS = $('#locationsSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $locationS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                $locationS.find('option[value="' + id + '"]').text(newText);
            }
        });
        
        $('#Location_notes').editable({
            params: params,
            toggle: 'manual'
        });
        
        $('#Location_notes_edit').off('click');
        
        $('#Location_notes_edit').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            
            $('#Location_notes').editable('toggle');
        });
        
        $('#Location_description').editable({
            params: params,
            toggle: 'manual'
        });
        
        $('#Location_description_edit').off('click');
        
        $('#Location_description_edit').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            
            $('#Location_description').editable('toggle');
        });
        
        $('#Location_tags').editable({
            params: params
        });

        $('#Location_length').editable({
            params: params
        });
        
        $('#Location_width').editable({
            params: params
        });

        $('#Location_radius').editable({
            params: params
        });
        
        $('#Location_seating').editable({
            params: params
        });        
    };
    
    arenaManagementView.setupLocationView = function (location, params) {
        var $locationDetails = $('#locationDetails');
        var $locationEditables = $('.location-editable');
        
        // Destroy any existing editables!
        $('#Location_notes_edit').off('click');
        $('#Location_description_edit').off('click');
        
        if ($locationEditables.length > 0)
        {
            $locationEditables.each(function () {
                $(this).editable('destroy');
            });
        }
        
        // Add one row at a time. 
        var locationView = '<strong>Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';
        
        locationView += '<tr><td style="width:33%">Name</td><td>' +
                '<a href="#" id="Location_name" data-name="name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + location.name + '" ' +
                'title="Venue Name" class="location-editable">' + 
                location.name + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">External ID</td><td>' +
                '<a href="#" id="Location_external_id" data-name="external_id" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + (location.external_id ? location.external_id : '') + '" ' +
                'title="Your Venue ID" class="location-editable">' +
                (location.external_id ? location.external_id : '') + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Tags</td><td>' +
                '<a href="#" id="Location_tags" data-name="tags" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + location.tags + '" ' +
                'title="Venue Tags" class="location-editable">' +
                location.tags + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Type</td><td>' +
                '<a href="#" id="Location_type_id" data-name="type_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + location.type_id + '" ' +
                'title="Venue Type" class="location-editable">' +
                location.type + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Status</td><td>' +
                '<a href="#" id="Location_status_id" data-name="status_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + location.status_id + '" ' +
                'title="Venue Status" class="location-editable">' +
                location.status + '</a></td></tr>';
        
        locationView += '</tbody></table>';
        
        // Now the next table of info!
        locationView += '<strong>Additional Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        locationView += '<tr><td style="width:33%">Length (ft)</td><td>' +
                '<a href="#" id="Location_length" data-name="length" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + (location.length ? location.length : '') + '" ' +
                'title="Venue length in feet" class="location-editable">' +
                (location.length ? location.length : '') + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Width (ft)</td><td>' +
                '<a href="#" id="Location_width" data-name="width" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + (location.width ? location.width : '') + '" ' +
                'title="Venue width in feet" class="location-editable">' +
                (location.width ? location.width : '') + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Radius (ft)</td><td>' +
                '<a href="#" id="Location_radius" data-name="radius" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + (location.radius ? location.radius : '') + '" ' +
                'title="Venue readius in feet" class="location-editable">' +
                (location.radius ? location.radius : '') + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Seating Capacity</td><td>' +
                '<a href="#" id="Location_seating" data-name="seating" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + (location.seating ? location.seating : '') + '" ' +
                'title="Venue seating capacity" class="location-editable">' +
                (location.seating ? location.seating : '') + '</a></td></tr>';

        locationView += '</tbody></table>';
        
        // Now the next table of info!
        locationView += '<strong>Description & Notes</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        locationView += '<tr><td style="width:33%">Description<i class="fa fa-lg fa-fw ' +
                'fa-pencil" style="padding-right: 5px"></i> <a href="#" id="Location_description_edit">' +
                '<span>[edit]</span></a></td><td><div id="Location_description" data-name="description" ' +
                'data-type="wysihtml5" data-mode="inline" data-toggle="manual" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + (location.description ? location.description : '') + '" ' +
                'title="Venue Description" class="location-editable">' +
                (location.description ? location.description : '') + '</div></td></tr>';
        
        locationView += '<tr><td style="width:33%">Notes<i class="fa fa-lg fa-fw ' +
                'fa-pencil" style="padding-right: 5px"></i> <a href="#" id="Location_notes_edit">' +
                '<span>[edit]</span></a></td><td><div id="Location_notes" data-name="notes" ' +
                'data-type="wysihtml5" data-mode="inline" data-toggle="manual" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + (location.notes ? location.notes : '') + '" ' +
                'title="Venue Notes" class="location-editable">' +
                (location.notes ? location.notes : '') + '</div></td></tr>';
        
        locationView += '</tbody></table>';
        
        // Ok, now we can fade-out the current view, and then fade in our new
        // view!
        var $cv = $('<div id="locationDetails">' + locationView + '</div>');
        
        $cv.hide();
        
        $locationDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $locationDetails.parent().prepend($cv);
            
            // Setup the contact editables before we fade in!
            arenaManagementView.setupLocationEditables(params);
            
            // Now fade us in!
            $cv.fadeIn(500, function() {
                var $newBtn = $('#newLocationButton');
                var $deleteBtn = $('#deleteLocationButton');
                var $locationS = $('#locationsSelect');

                $locationS.removeAttr("disabled");
                $newBtn.removeAttr("disabled");
                $deleteBtn.removeAttr("disabled");

                $locationS.parent().find('#loading').remove();
                $locationDetails.remove();
            });            
        });
    };
    
    arenaManagementView.setupNewLocationView = function (params) {
        var $locationDetails = $('#locationDetails');
        var $locationEditables = $('.location-editable');
        
        // Destroy any existing editables!
        $('#Location_notes_edit').off('click');
        $('#Location_description_edit').off('click');
        
        if ($locationEditables.length > 0)
        {
            $locationEditables.each(function () {
                $(this).editable('destroy');
            });
        }
        
        // Add one row at a time. 
        var locationView = '<strong>Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';
        
        locationView += '<tr><td style="width:33%">Name</td><td>' +
                '<a href="#" id="Location_name" data-name="name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue Name" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">External ID</td><td>' +
                '<a href="#" id="Location_external_id" data-name="external_id" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'title="Your Venue ID" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Tags</td><td>' +
                '<a href="#" id="Location_tags" data-name="tags" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue Tags" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Type</td><td>' +
                '<a href="#" id="Location_type_id" data-name="type_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-value="1" title="Venue Type" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Status</td><td>' +
                '<a href="#" id="Location_status_id" data-name="status_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-value="1" title="Venue Status" class="location-editable"></a></td></tr>';
        
        locationView += '</tbody></table>';
        
        // Now the next table of info!
        locationView += '<strong>Email & Phone</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        locationView += '<tr><td style="width:33%">Length (ft)</td><td>' +
                '<a href="#" id="Location_length" data-name="length" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue length in feet" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Width (ft)</td><td>' +
                '<a href="#" id="Location_width" data-name="width" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue width in feet" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Radius (ft)</td><td>' +
                '<a href="#" id="Location_radius" data-name="radius" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue readius in feet" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Seating Capacity</td><td>' +
                '<a href="#" id="Location_seating" data-name="seating" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue seating capacity" class="location-editable"></a></td></tr>';
                
        locationView += '<tr style="display:none;"><td>Arena ID</td><td>' +
                '<a href="#" id="Location_arena_id" data-name="aid" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-value="' + arenaManagementView.params.aid + '" ' +
                'title="Arena ID" class="location-editable">' +
                arenaManagementView.params.aid + '</a></td></tr>';
                
        locationView += '<tr style="display:none;"><td>Output</td><td>' +
                '<a href="#" id="Location_output" data-name="output" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'data-value="json" title="Output" class="location-editable">' +
                'json</a></td></tr>';
                
        locationView += '</tbody></table>';
        
        // Now the next table of info!
        locationView += '<strong>Description & Notes</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        locationView += '<tr><td style="width:33%">Description<i class="fa fa-lg fa-fw ' +
                'fa-pencil" style="padding-right: 5px"></i> <a href="#" id="Location_description_edit">' +
                '<span>[edit]</span></a></td><td><div id="Location_description" data-name="description" ' +
                'data-type="wysihtml5" data-toggle="manual" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue Description" class="location-editable"></div></td></tr>';
        
        locationView += '<tr><td style="width:33%">Notes<i class="fa fa-lg fa-fw ' +
                'fa-pencil" style="padding-right: 5px"></i> <a href="#" id="Location_notes_edit">' +
                '<span>[edit]</span></a></td><td><div id="Location_notes" data-name="notes" ' +
                'data-type="wysihtml5" data-toggle="manual" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue Notes" class="location-editable"></div></td></tr>';
        
        locationView += '</tbody></table>';
        
        // Ok, now we can fade-out the current view, and then fade in our new
        // view!
        var $cv = $('<div id="locationDetails">' + locationView + '</div>');
        
        $cv.hide();
        
        $locationDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $locationDetails.parent().prepend($cv);
            
            // Setup the contact editables before we fade in!
            arenaManagementView.setupLocationEditables(params);
            
            $('#Location_arena_id').editable({
                params: params
            });
            
            $('#Location_output').editable({
                params: params
            });
            
            // Now fade us in!
            $cv.fadeIn(500, function() {
                var $saveBtn = $('#saveLocationButton');
                var $cancelBtn = $('#cancelLocationButton');

                $saveBtn.removeAttr("disabled").show(250);
                $cancelBtn.removeAttr("disabled").show(250);
                
                $locationDetails.remove();
            });            
        });
    };
    
    arenaManagementView.resetLocationView = function (){
        var $locationDetails = $('#locationDetails');
        var $locationEditables = $('.location-editable');
        var $newBtn = $('#newLocationButton');
        var $deleteBtn = $('#deleteLocationButton');
        var $saveBtn = $('#saveLocationButton');
        var $cancelBtn = $('#cancelLocationButton');
        var $locationS = $('#locationsSelect');
        
        $newBtn.attr("disabled", "disabled");
        $deleteBtn.attr("disabled", "disabled");
        $saveBtn.attr("disabled", "disabled").hide();
        $cancelBtn.attr("disabled", "disabled").hide();
        
        // Destroy any existing editables!
        $('#Location_notes_edit').off('click');
        $('#Location_description_edit').off('click');
        
        if ($locationEditables.length > 0)
        {
            $locationEditables.each(function () {
                $(this).editable('destroy');
            });
        }
        
        $locationDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $locationDetails.empty();
            
            // Now fade us in!
            $locationDetails.fadeIn(500, function () {
                $newBtn.removeAttr("disabled");
                $locationS.removeAttr("disabled");
            });
        });
    };
    
    arenaManagementView.loadLocation = function (locationId) {
        var $newBtn = $('#newLocationButton');
        var $deleteBtn = $('#deleteLocationButton');
        var $saveBtn = $('#saveLocationButton');
        var $cancelBtn = $('#cancelLocationButton');
        var $locationS = $('#locationsSelect');
        
        var spinner = '<div id="loading"' +
                '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                'alt="Loading..." /></div>';
            
        // Prepare to load the location!
        $newBtn.attr("disabled", "disabled");
        $deleteBtn.attr("disabled", "disabled");
        $saveBtn.attr("disabled", "disabled").hide();
        $cancelBtn.attr("disabled", "disabled").hide();
        $locationS.attr("disabled", "disabled");

        $locationS.parent().append(spinner);
        
        var myParams = {
            id: locationId,
            aid: arenaManagementView.params.aid,
            output: 'json'
        };
        
        $.ajax({
            url: arenaManagementView.endpoints.location.viewRecord,
            data: myParams,
            type: 'GET',
            dataType: 'json',
            success: function(result, status, xhr) {
                // Its possible we will get a session timeout so check for it!
                if (result.error && result.error === "LOGIN_REQUIRED")
                {
                    $locationS.removeAttr("disabled");
                    $newBtn.removeAttr("disabled");
                    $deleteBtn.removeAttr("disabled");
                
                    $locationS.parent().find('#loading').remove();
                    
                    window.setTimeout(function () {
                        $locationS.removeAttr("disabled");
                        $newBtn.removeAttr("disabled");
                        $deleteBtn.removeAttr("disabled");
                        utilities.ajaxError.show(
                                "Error",
                                "Failed to load the venue",
                                xhr,
                                "error",
                                "Login Required"
                        );
                    }, 1000);

                    return;
                }
                    
                // Location has been loaded!
                myParams.output = 'html';
                
                if(result.data && result.data.id !== "undefined")
                {
                    arenaManagementView.setupLocationView(result.data, myParams);
                }
            },
            error: function(xhr, status, errorThrown) {
                $locationS.removeAttr("disabled");
                $newBtn.removeAttr("disabled");
                $deleteBtn.removeAttr("disabled");
                
                $locationS.parent().find('#loading').remove();
                
                utilities.ajaxError.show(
                        "Error",
                        "Failed to load the venue",
                        xhr,
                        status,
                        errorThrown
                );
            }
        });            
    };
    
}( window.arenaManagementView = window.arenaManagementView || {}, jQuery ));