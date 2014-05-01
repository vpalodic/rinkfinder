/* 
 * This is the jQuery plugin for the user view / update / create actions
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( eventManagementView, $, undefined ) {
    "use strict";
    // public properties
    eventManagementView.endpoints = {
        event: {
            viewRecord: "/server/endpoint",
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint",
            deleteRecord: "/server/endpoint"
        }
    };
    
    eventManagementView.event = {};
    eventManagementView.eventTypes = [];
    eventManagementView.eventStatuses = [];
    eventManagementView.params = {};
    eventManagementView.isArenaManager = false;
    eventManagementView.Id = 0;
    eventManagementView.Name = '';
    
    eventManagementView.onReady = function () {
        if (typeof $.fn.editable === "undefined")
        { 
            eventManagementView.loadEditable();
        }
        else
        {
            eventManagementView.setupInitialEventView();
        }
        
        var $panel = $("#eventManagementView.panel.panel-primary");
        
        if ($panel.length > 0)
        {
            $panel.on('destroyed', function () {
                // We have been closed, so clean everything up!!!
                var $editables = $("#eventManagementView.panel.panel-primary .editable");
                
                $editables.editable('destroy');
            });
        }
        
        $('[data-toggle="tooltip"]').tooltip();
    };
    
    eventManagementView.loadEditable = function () {
        
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
                eventManagementView.setupInitialEventView();
            } else if (console && console.log) {
                console.log("Loading... " + scriptName);
            }
        }, 500);
        
    };
    
    eventManagementView.createDeleteModal = function () {
        var modalBody = '<div id="deleteModal" class="well"><h3>Are you sure you want to ' +
            'permanently delete this item?</h3><p class="lead text-error">This operation ' +
            'cannot be undone and will also delete this item from any related ' +
            'items. For example, deleting a event / event will also delete ' +
            'any events associated with that event / event.</p></div>';
    
        var modalFooter = '<button class="btn btn-large btn-danger" data-dismiss="' +
                'modal" type="button" aria-hidden="true" id="yes"><i class="' +
                'fa fa-fw fa-lg fa-check"></i> Yes</button>' +
                '<button class="btn btn-large" data-dismiss="' +
                'modal" type="button" aria-hidden="true" id="close"><i class="' +
                'fa fa-fw fa-lg fa-times"></i> No</button>';

        return utilities.modal.add('Confirm Action', modalBody, modalFooter, false, false);
    };
    
    eventManagementView.setupInitialEventView = function () {
        // Disable all buttons except the New button 
        // and hide the save / cancel buttons
        var that = this;
        var $newBtn = $('#newEventButton');
        var $deleteBtn = $('#deleteEventButton');
        var $saveBtn = $('#saveEventButton');
        var $cancelBtn = $('#cancelEventButton');
        var $eventS = $('#eventsSelect');
        
        $deleteBtn.attr("disabled", "disabled");
        $saveBtn.attr("disabled", "disabled").hide();
        $cancelBtn.attr("disabled", "disabled").hide();
        
        // Setup the select change handlers!
        $eventS.on('change', function (e) {
            // Get the selected options from the available select list
            var $selected = $eventS.find('option:selected');
            
            if($selected.length > 0)
            {
                // we have our selection, so clear the edit screen and
                // load in the new contact
                var eventId = $(this).val();

                that.resetEventView();
                
                if(eventId == 'none')
                {
                    return;
                }
                
                that.loadEvent(eventId);
            }
            else
            {
                // Nothing selected, so clear the edit screen
                that.resetEventView();
            }
        });
        
        // Setup the button click handlers!
        $newBtn.on('click', function (e) {
            e.preventDefault();
            
            $eventS.attr("disabled", "disabled");
            $eventS.val('none');
            
            var myParams = {
                aid: eventManagementView.params.aid,
                output: 'html'
            };

            eventManagementView.setupNewEventView(myParams);
            $newBtn.attr("disabled", "disabled");
            $deleteBtn.attr("disabled", "disabled");
        });
        
        $deleteBtn.on('click', function (e) {
            e.preventDefault();
            
            var $modal = eventManagementView.createDeleteModal();
            
            $modal.modal({
                loading: false,
                replace: false,
                modalOverflow: false
            });
            
            // The modal is now in the DOM so we can hook in to the button
            // clicks. Specifically, we only care about the 'yes' button.
            $('button#yes').on('click', function (e) {
                // They clicked yes and so now we must delete the contact!!!
                var eventId = $eventS.val();
                
                // We must disable everything and put up our spinner...
                var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
            
                // Prepare to delete the event.
                $eventS.attr("disabled", "disabled");
                $newBtn.attr("disabled", "disabled");
                $deleteBtn.attr("disabled", "disabled");
                $saveBtn.attr("disabled", "disabled").hide();
                $cancelBtn.attr("disabled", "disabled").hide();
            
                // Show we are busy by appending the spinner to the assign button
                $deleteBtn.parent().prepend(spinner);
            
                // Now let's delete the event!!!
                var myParams = {
                    aid: eventManagementView.params.id,
                    id: eventId,
                    pk: eventId,
                    output: 'html'
                };
                
                $.ajax({
                    url: eventManagementView.endpoints.event.deleteRecord,
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
                                $eventS.removeAttr("disabled");
                                $newBtn.removeAttr("disabled");
                                $deleteBtn.removeAttr("disabled");
                                utilities.ajaxError.show(
                                        "Error",
                                        "Failed to delete the event",
                                        xhr,
                                        "error",
                                        "Login Required"
                                );
                            }, 1000);

                            return;
                        }
                    
                        // Remove the event from the list
                        $eventS.find('option[value="' + eventId + '"]').remove();
                        $eventS.val('none').trigger('change');
                        
                        $eventS.removeAttr("disabled");
                        $newBtn.removeAttr("disabled");
                        $deleteBtn.parent().find('#loading').remove();
                    },
                    error: function(xhr, status, errorThrown) {
                        $eventS.removeAttr("disabled");
                        $newBtn.removeAttr("disabled");
                        $deleteBtn.parent().find('#loading').remove();
                    
                        utilities.ajaxError.show(
                            "Error",
                            "Failed to delete the event",
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
            
            // Prepare to create the new event
            $eventS.attr("disabled", "disabled");
            $newBtn.attr("disabled", "disabled");
            $deleteBtn.attr("disabled", "disabled");
            $saveBtn.attr("disabled", "disabled");
            $cancelBtn.attr("disabled", "disabled");
            
            // Show we are busy by appending the spinner to the assign button
            $saveBtn.parent().prepend(spinner);
            
            // Ok, we are going for a ride here as we will send all of the
            // values to the server at once to create the event.
            // I hope all goes well ;-)
            $('.event-editable').editable('submit', { 
                url: eventManagementView.endpoints.event.newRecord, 
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
                       
                       $('#Event_tags').editable('setValue', data.tags);
                       
                       // remove unsaved class
                       $(this).removeClass('editable-unsaved');
                       
                       // update the select lists!
                       var vals = $('#Event_name').editable('getValue');
                       var strStatus = $('#Event_status_id').text();
                       var strType = $('#Event_type_id').text();
                
                       var newText = vals.name + ' - ' + strType + ' (' + strStatus + ')';
                       var newOption = '<option value="' + data.id + '">' + newText + '</option>';
                       $eventS.append(newOption);
                       
                       // Now select the newly appended option
                       $eventS.val(data.id); // We don't trigger a change!
                       
                       $saveBtn.attr("disabled", "disabled").hide(250);
                       $cancelBtn.attr("disabled", "disabled").hide(250);
                       $eventS.removeAttr("disabled");
                       $newBtn.removeAttr("disabled");
                       $deleteBtn.removeAttr("disabled");
                       
                       //show messages
                       var msgArea = '<div class="alert alert-success">' +
                           '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                           '<h3><span class="badge badge-success">New event added!</span></h3></div>';
                       
                       $('#eventDetails').prepend(msgArea);
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
                            "Failed to add the new event",
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
                   
                   $('#eventDetails').prepend(msgArea);
               }
           });
        });
        
        $cancelBtn.on('click', function (e) {
            e.preventDefault();
            
            eventManagementView.resetEventView();
        });
        
        if(eventManagementView.event.id !== "undefined")
        {
            eventManagementView.setupEventView(eventManagementView.event, eventManagementView.params);
        }
    };
    
    eventManagementView.setupEventEditables = function (params) {
        $('#Event_name').editable({
            params: params,
            success: function(response, newValue) {
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
            
                var strStatus = $('#Event_status_id').text();
                var strType = $('#Event_type_id').text();
                
                var newText = newValue + ' - ' + strType + ' (' + strStatus + ')';
                
                // This is a bit risky as the user may select a different
                // event by the time we get to this part even
                // though we have disabled the buttons and the select list.
                var $eventS = $('#eventsSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $eventS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                $eventS.find('option[value="' + id + '"]').text(newText);
            }
        });
        
        $('#Event_external_id').editable({
            params: params
        });
        
        $('#Event_status_id').editable({
            params: params,
            showbuttons: false,
            source: eventManagementView.eventStatuses,
            success: function(response, newValue) {
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
            
                var vals = $('#Event_name').editable('getValue');
                var strStatus = $('#Event_status_id').text();
                var strType = $('#Event_type_id').text();
                
                var newText = vals.name + ' - ' + strType + ' (' + strStatus + ')';
                
                // This is a bit risky as the user may select a different
                // event by the time we get to this part even
                // though we have disabled the buttons and the select list.
                var $eventS = $('#eventsSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $eventS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                $eventS.find('option[value="' + id + '"]').text(newText);
            }
        });
        
        $('#Event_type_id').editable({
            params: params,
            showbuttons: false,
            source: eventManagementView.eventTypes,
            success: function(response, newValue) {
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
            
                var vals = $('#Event_name').editable('getValue');
                var strStatus = $('#Event_status_id').text();
                var strType = $('#Event_type_id').text();
                
                var newText = vals.name + ' - ' + strType + ' (' + strStatus + ')';
                
                // This is a bit risky as the user may select a different
                // event by the time we get to this part even
                // though we have disabled the buttons and the select list.
                var $eventS = $('#eventsSelect');
                
                // Assume the currectly selected option is the one we want
                // to update
                var id = $eventS.val();
                
                if(id == 'none')
                {
                    return;
                }
                
                $eventS.find('option[value="' + id + '"]').text(newText);
            }
        });
        
        $('#Event_notes').editable({
            params: params,
            toggle: 'manual'
        });
        
        $('#Event_notes_edit').off('click');
        
        $('#Event_notes_edit').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            
            $('#Event_notes').editable('toggle');
        });
        
        $('#Event_description').editable({
            params: params,
            toggle: 'manual'
        });
        
        $('#Event_description_edit').off('click');
        
        $('#Event_description_edit').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            
            $('#Event_description').editable('toggle');
        });
        
        $('#Event_tags').editable({
            params: params
        });

        $('#Event_length').editable({
            params: params
        });
        
        $('#Event_width').editable({
            params: params
        });

        $('#Event_radius').editable({
            params: params
        });
        
        $('#Event_seating').editable({
            params: params
        });        
    };
    
    eventManagementView.setupEventView = function (event, params) {
        var $eventDetails = $('#eventDetails');
        var $eventEditables = $('.event-editable');
        
        // Destroy any existing editables!
        $('#Event_notes_edit').off('click');
        $('#Event_description_edit').off('click');
        
        if ($eventEditables.length > 0)
        {
            $eventEditables.each(function () {
                $(this).editable('destroy');
            });
        }
        
        // Add one row at a time. 
        var eventView = '<strong>Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';
        
        eventView += '<tr><td style="width:33%">Name</td><td>' +
                '<a href="#" id="Event_name" data-name="name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + event.name + '" ' +
                'title="Event Name" class="event-editable">' + 
                event.name + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">External ID</td><td>' +
                '<a href="#" id="Event_external_id" data-name="external_id" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.external_id ? event.external_id : '') + '" ' +
                'title="Your Event ID" class="event-editable">' +
                (event.external_id ? event.external_id : '') + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Tags</td><td>' +
                '<a href="#" id="Event_tags" data-name="tags" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + event.tags + '" ' +
                'title="Event Tags" class="event-editable">' +
                event.tags + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Type</td><td>' +
                '<a href="#" id="Event_type_id" data-name="type_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + event.type_id + '" ' +
                'title="Event Type" class="event-editable">' +
                event.type + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Status</td><td>' +
                '<a href="#" id="Event_status_id" data-name="status_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + event.status_id + '" ' +
                'title="Event Status" class="event-editable">' +
                event.status + '</a></td></tr>';
        
        eventView += '</tbody></table>';
        
        // Now the next table of info!
        eventView += '<strong>Additional Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        eventView += '<tr><td style="width:33%">Length (ft)</td><td>' +
                '<a href="#" id="Event_length" data-name="length" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.length ? event.length : '') + '" ' +
                'title="Event length in feet" class="event-editable">' +
                (event.length ? event.length : '') + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Width (ft)</td><td>' +
                '<a href="#" id="Event_width" data-name="width" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.width ? event.width : '') + '" ' +
                'title="Event width in feet" class="event-editable">' +
                (event.width ? event.width : '') + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Radius (ft)</td><td>' +
                '<a href="#" id="Event_radius" data-name="radius" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.radius ? event.radius : '') + '" ' +
                'title="Event readius in feet" class="event-editable">' +
                (event.radius ? event.radius : '') + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Seating Capacity</td><td>' +
                '<a href="#" id="Event_seating" data-name="seating" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.seating ? event.seating : '') + '" ' +
                'title="Event seating capacity" class="event-editable">' +
                (event.seating ? event.seating : '') + '</a></td></tr>';

        eventView += '</tbody></table>';
        
        // Now the next table of info!
        eventView += '<strong>Description & Notes</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        eventView += '<tr><td style="width:33%">Description<i class="fa fa-lg fa-fw ' +
                'fa-pencil" style="padding-right: 5px"></i> <a href="#" id="Event_description_edit">' +
                '<span>[edit]</span></a></td><td><div id="Event_description" data-name="description" ' +
                'data-type="wysihtml5" data-mode="inline" data-toggle="manual" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.description ? event.description : '') + '" ' +
                'title="Event Description" class="event-editable">' +
                (event.description ? event.description : '') + '</div></td></tr>';
        
        eventView += '<tr><td style="width:33%">Notes<i class="fa fa-lg fa-fw ' +
                'fa-pencil" style="padding-right: 5px"></i> <a href="#" id="Event_notes_edit">' +
                '<span>[edit]</span></a></td><td><div id="Event_notes" data-name="notes" ' +
                'data-type="wysihtml5" data-mode="inline" data-toggle="manual" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.notes ? event.notes : '') + '" ' +
                'title="Event Notes" class="event-editable">' +
                (event.notes ? event.notes : '') + '</div></td></tr>';
        
        eventView += '</tbody></table>';
        
        // Ok, now we can fade-out the current view, and then fade in our new
        // view!
        var $cv = $('<div id="eventDetails">' + eventView + '</div>');
        
        $cv.hide();
        
        $eventDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $eventDetails.parent().prepend($cv);
            
            // Setup the contact editables before we fade in!
            eventManagementView.setupEventEditables(params);
            
            // Now fade us in!
            $cv.fadeIn(500, function() {
                var $newBtn = $('#newEventButton');
                var $deleteBtn = $('#deleteEventButton');
                var $eventS = $('#eventsSelect');

                $eventS.removeAttr("disabled");
                $newBtn.removeAttr("disabled");
                $deleteBtn.removeAttr("disabled");

                $eventS.parent().find('#loading').remove();
                $eventDetails.remove();
            });            
        });
    };
    
    eventManagementView.setupNewEventView = function (params) {
        var $eventDetails = $('#eventDetails');
        var $eventEditables = $('.event-editable');
        
        // Destroy any existing editables!
        $('#Event_notes_edit').off('click');
        $('#Event_description_edit').off('click');
        
        if ($eventEditables.length > 0)
        {
            $eventEditables.each(function () {
                $(this).editable('destroy');
            });
        }
        
        // Add one row at a time. 
        var eventView = '<strong>Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';
        
        eventView += '<tr><td style="width:33%">Name</td><td>' +
                '<a href="#" id="Event_name" data-name="name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Name" class="event-editable"></a></td></tr>';
        
        eventView += '<tr><td style="width:33%">External ID</td><td>' +
                '<a href="#" id="Event_external_id" data-name="external_id" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Your Event ID" class="event-editable"></a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Tags</td><td>' +
                '<a href="#" id="Event_tags" data-name="tags" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Tags" class="event-editable"></a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Type</td><td>' +
                '<a href="#" id="Event_type_id" data-name="type_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-value="1" title="Event Type" class="event-editable"></a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Status</td><td>' +
                '<a href="#" id="Event_status_id" data-name="status_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-value="1" title="Event Status" class="event-editable"></a></td></tr>';
        
        eventView += '</tbody></table>';
        
        // Now the next table of info!
        eventView += '<strong>Email & Phone</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        eventView += '<tr><td style="width:33%">Length (ft)</td><td>' +
                '<a href="#" id="Event_length" data-name="length" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event length in feet" class="event-editable"></a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Width (ft)</td><td>' +
                '<a href="#" id="Event_width" data-name="width" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event width in feet" class="event-editable"></a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Radius (ft)</td><td>' +
                '<a href="#" id="Event_radius" data-name="radius" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event readius in feet" class="event-editable"></a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Seating Capacity</td><td>' +
                '<a href="#" id="Event_seating" data-name="seating" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event seating capacity" class="event-editable"></a></td></tr>';
                
        eventView += '<tr style="display:none;"><td>Arena ID</td><td>' +
                '<a href="#" id="Event_arena_id" data-name="aid" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-value="' + eventManagementView.params.aid + '" ' +
                'title="Arena ID" class="event-editable">' +
                eventManagementView.params.aid + '</a></td></tr>';
                
        eventView += '<tr style="display:none;"><td>Output</td><td>' +
                '<a href="#" id="Event_output" data-name="output" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-value="json" title="Output" class="event-editable">' +
                'json</a></td></tr>';
                
        eventView += '</tbody></table>';
        
        // Now the next table of info!
        eventView += '<strong>Description & Notes</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        eventView += '<tr><td style="width:33%">Description<i class="fa fa-lg fa-fw ' +
                'fa-pencil" style="padding-right: 5px"></i> <a href="#" id="Event_description_edit">' +
                '<span>[edit]</span></a></td><td><div id="Event_description" data-name="description" ' +
                'data-type="wysihtml5" data-toggle="manual" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Description" class="event-editable"></div></td></tr>';
        
        eventView += '<tr><td style="width:33%">Notes<i class="fa fa-lg fa-fw ' +
                'fa-pencil" style="padding-right: 5px"></i> <a href="#" id="Event_notes_edit">' +
                '<span>[edit]</span></a></td><td><div id="Event_notes" data-name="notes" ' +
                'data-type="wysihtml5" data-toggle="manual" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Notes" class="event-editable"></div></td></tr>';
        
        eventView += '</tbody></table>';
        
        // Ok, now we can fade-out the current view, and then fade in our new
        // view!
        var $cv = $('<div id="eventDetails">' + eventView + '</div>');
        
        $cv.hide();
        
        $eventDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $eventDetails.parent().prepend($cv);
            
            // Setup the contact editables before we fade in!
            eventManagementView.setupEventEditables(params);
            
            $('#Event_arena_id').editable({
                params: params
            });
            
            $('#Event_output').editable({
                params: params
            });
            
            // Now fade us in!
            $cv.fadeIn(500, function() {
                var $saveBtn = $('#saveEventButton');
                var $cancelBtn = $('#cancelEventButton');

                $saveBtn.removeAttr("disabled").show(250);
                $cancelBtn.removeAttr("disabled").show(250);
                
                $eventDetails.remove();
            });            
        });
    };
    
    eventManagementView.resetEventView = function (){
        var $eventDetails = $('#eventDetails');
        var $eventEditables = $('.event-editable');
        var $newBtn = $('#newEventButton');
        var $deleteBtn = $('#deleteEventButton');
        var $saveBtn = $('#saveEventButton');
        var $cancelBtn = $('#cancelEventButton');
        var $eventS = $('#eventsSelect');
        
        $newBtn.attr("disabled", "disabled");
        $deleteBtn.attr("disabled", "disabled");
        $saveBtn.attr("disabled", "disabled").hide();
        $cancelBtn.attr("disabled", "disabled").hide();
        
        // Destroy any existing editables!
        $('#Event_notes_edit').off('click');
        $('#Event_description_edit').off('click');
        
        if ($eventEditables.length > 0)
        {
            $eventEditables.each(function () {
                $(this).editable('destroy');
            });
        }
        
        $eventDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $eventDetails.empty();
            
            // Now fade us in!
            $eventDetails.fadeIn(500, function () {
                $newBtn.removeAttr("disabled");
                $eventS.removeAttr("disabled");
            });
        });
    };
    
    eventManagementView.loadEvent = function (eventId) {
        var $newBtn = $('#newEventButton');
        var $deleteBtn = $('#deleteEventButton');
        var $saveBtn = $('#saveEventButton');
        var $cancelBtn = $('#cancelEventButton');
        var $eventS = $('#eventsSelect');
        
        var spinner = '<div id="loading"' +
                '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                'alt="Loading..." /></div>';
            
        // Prepare to load the event!
        $newBtn.attr("disabled", "disabled");
        $deleteBtn.attr("disabled", "disabled");
        $saveBtn.attr("disabled", "disabled").hide();
        $cancelBtn.attr("disabled", "disabled").hide();
        $eventS.attr("disabled", "disabled");

        $eventS.parent().append(spinner);
        
        var myParams = {
            id: eventId,
            aid: eventManagementView.params.aid,
            output: 'json'
        };
        
        $.ajax({
            url: eventManagementView.endpoints.event.viewRecord,
            data: myParams,
            type: 'GET',
            dataType: 'json',
            success: function(result, status, xhr) {
                // Its possible we will get a session timeout so check for it!
                if (result.error && result.error === "LOGIN_REQUIRED")
                {
                    $eventS.removeAttr("disabled");
                    $newBtn.removeAttr("disabled");
                    $deleteBtn.removeAttr("disabled");
                
                    $eventS.parent().find('#loading').remove();
                    
                    window.setTimeout(function () {
                        $eventS.removeAttr("disabled");
                        $newBtn.removeAttr("disabled");
                        $deleteBtn.removeAttr("disabled");
                        utilities.ajaxError.show(
                                "Error",
                                "Failed to load the event",
                                xhr,
                                "error",
                                "Login Required"
                        );
                    }, 1000);

                    return;
                }
                    
                // Event has been loaded!
                myParams.output = 'html';
                
                if(result.data && result.data.id !== "undefined")
                {
                    eventManagementView.setupEventView(result.data, myParams);
                }
            },
            error: function(xhr, status, errorThrown) {
                $eventS.removeAttr("disabled");
                $newBtn.removeAttr("disabled");
                $deleteBtn.removeAttr("disabled");
                
                $eventS.parent().find('#loading').remove();
                
                utilities.ajaxError.show(
                        "Error",
                        "Failed to load the event",
                        xhr,
                        status,
                        errorThrown
                );
            }
        });            
    };
    
}( window.eventManagementView = window.eventManagementView || {}, jQuery ));