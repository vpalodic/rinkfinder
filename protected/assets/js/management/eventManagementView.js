/* 
 * This is the jQuery plugin for the event view / update / create actions
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
    
    eventManagementView.newRecord = false;
    eventManagementView.event = {};
    eventManagementView.arenas = [];
    eventManagementView.locations = [];
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
                output: 'html'
            };

            eventManagementView.newRecord = true;
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
                       $(this).editable('option', 'params', {id: data.id, aid: data.aid, output: 'html'});
                       
                       // update the various editables
                       $('#Event_tags').editable('setValue', data.tags);
                       $('#Event_all_day').editable('setValue', data.all_day);
                       $('#Event_duration').editable('setValue', data.duration);
                       $('#Event_start_date').editable('setValue', data.start_date, true);
                       $('#Event_start_time').editable('setValue', data.start_time, true);
                       $('#Event_end_date').editable('setValue', data.end_date, true);
                       $('#Event_end_time').editable('setValue', data.end_time, true);
                       
                       // remove unsaved class
                       $(this).removeClass('editable-unsaved');

                       // update the select lists!
                       var vals = $('#Event_start_date, #Event_start_time').editable('getValue');
                       var strArena = $('#Event_arena_id').text();
                       var strLocation = $('#Event_location_id').text();
                       var strStatus = $('#Event_status_id').text();
                       var strType = $('#Event_type_id').text();
                       var strDate = moment(vals.start_date, ['YYYY-MM-DD', 'MM/DD/YYYY']);
                       var strTime = moment(vals.start_time, ['HH:mm', 'hh:mm A']);
                       var newText = '';
                       
                       if (strLocation === '')
                       {
                           newText = strDate.format('M/D/YYYY') + ' ' + strTime.format('h:mm A') + ' @ ' + strArena + ' - ' + strType + ' (' + strStatus + ')';
                       }
                       else
                       {
                           newText = strDate.format('M/D/YYYY') + ' ' + strTime.format('h:mm A') + ' @ ' + strArena + ' ' + strLocation + ' - ' + strType + ' (' + strStatus + ')';
                       }
                       
                       var newOption = '<option value="' + data.id + '">' + newText + '</option>';
                       
                       $eventS.append(newOption);
                       
                       // Now select the newly appended option
                       $eventS.val(data.id); // We don't trigger a change!
                       
                       $saveBtn.attr("disabled", "disabled").hide(250);
                       $cancelBtn.attr("disabled", "disabled").hide(250);
                       $eventS.removeAttr("disabled");
                       $newBtn.removeAttr("disabled");
                       $deleteBtn.removeAttr("disabled");
                       eventManagementView.newRecord = false;
                       
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
                            if (errors && typeof errors === "string")
                            {
                                errors = JSON.parse(errors);
                            }
                            
                            if (typeof errors === "object")
                            {
                                $.each(errors, function(k, v) { msg += v+"<br>"; });
                            }
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
            
            eventManagementView.newRecord = false;
            eventManagementView.resetEventView();
        });
        
        if (eventManagementView.newRecord)
        {
            $newBtn.trigger('click');
        }
        else if (eventManagementView.event && eventManagementView.event.id)
        {
            eventManagementView.setupEventView(eventManagementView.event, eventManagementView.params);
        }
    };
    
    eventManagementView.setupEventEditables = function (params) {
        $('#Event_arena_id').editable({
            params: params,
            showbuttons: false,
            source: eventManagementView.arenas,
            validate: function (value) {
                if($.trim(value) == '') {
                    return 'Facility is required';
                }
            },
            success: function (response, newValue) {
                var newLocations = false;

                try
                {
                    if (response && response.length > 0)
                    {
                        newLocations = JSON.parse(response);
                        
                        if (newLocations.locations)
                        {
                            response = null;
                        }
                    }
                }
                catch (err)
                {
                    newLocations = false;
                }            
                
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }

                var vals = $('#Event_start_date, #Event_start_time').editable('getValue');
                var strArena = '';
                
                // Find the newly selected Arena name
                for (var i = 0; i < eventManagementView.arenas.length; i++)
                {
                    if (newValue == eventManagementView.arenas[i].value)
                    {
                        strArena = eventManagementView.arenas[i].text;
                        break;
                    }
                }
                
                var strStatus = $('#Event_status_id').text();
                var strType = $('#Event_type_id').text();
                var strDate = moment(vals.start_date, ['YYYY-MM-DD', 'MM/DD/YYYY']);
                var strTime = moment(vals.start_time, ['HH:mm', 'hh:mm A']);
                
                var newText = strDate.format('MM/DD/YYYY') + ' ' + strTime.format('hh:mm A') + ' @ ' + strArena + ' - ' + strType + ' (' + strStatus + ')';
                
                // Set the new location list for the editable!
                eventManagementView.locations = newLocations.locations;
                
                $('#Event_location_id').editable('option', 'source', newLocations.locations);
                $('#Event_location_id').editable('setValue', null);
                
                $('.event-editable').editable('option', 'params', {aid: newValue, output: 'json'});
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
        
        $("#Event_arena_id").on('save', function(e, params) {
            // ensure that we only get the unmasked value
            if (!params.response)
            {
                // First update the value of the #Event_aid field
                $('#Event_aid').editable('setValue', params.newValue);
                
                // A new record is being created and so we have to grab the
                // Arena ID and then get a list of locations for that Arena
                var myParams = {
                    output: 'json',
                    aid: params.newValue,
                    name: 'arena_id',
                    value: params.newValue,
                    newRecord: 1
                };
                
                $.ajax({
                    url: eventManagementView.endpoints.event.updateRecord,
                    data: myParams,
                    type: 'POST',
                    dataType: 'json',
                    success: function(result, status, xhr) {
                        // Its possible we will get a session timeout so check for it!
                        if (result.error && result.error === "LOGIN_REQUIRED")
                        {
                            window.setTimeout(function () {
                                utilities.ajaxError.show(
                                        "Error",
                                        "Failed to venue listt",
                                        xhr,
                                        "error",
                                        "Login Required"
                                );
                            }, 1000);

                            return;
                        }
                    
                        // list has been loaded!
                        // Set the new location list for the editable!
                        if (result.locations)
                        {
                            eventManagementView.locations = result.locations;

                            $('#Event_location_id').editable('option', 'source', result.locations);
                            $('#Event_location_id').editable('setValue', null);
                        }
                    },
                    error: function(xhr, status, errorThrown) {
                        utilities.ajaxError.show(
                                "Error",
                                "Failed to load the venue list",
                                xhr,
                                status,
                                errorThrown
                        );
                    }
                });
            }
        });
        
        $('#Event_location_id').editable({
            params: params,
            showbuttons: false,
            source: eventManagementView.locations,
            success: function(response, newValue) {
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }
                
                // Since we changed Arenas for this event, there is no location name!
                var vals = $('#Event_start_date, #Event_start_time, #Event_arena_id').editable('getValue');
                var strArena = $('#Event_arena_id').text();
                var strLocation = '';
                
                // Find the newly selected location name
                for (var i = 0; i < eventManagementView.locations.length; i++)
                {
                    if (newValue == eventManagementView.locations[i].value)
                    {
                        strLocation = eventManagementView.locations[i].text;
                        break;
                    }
                }
                
                var strStatus = $('#Event_status_id').text();
                var strType = $('#Event_type_id').text();
                var strDate = moment(vals.start_date, ['YYYY-MM-DD', 'MM/DD/YYYY']);
                var strTime = moment(vals.start_time, ['HH:mm', 'hh:mm A']);
                
                var newText = '';
                
                if (strLocation === '')
                {
                    newText = strDate.format('MM/DD/YYYY') + ' ' + strTime.format('hh:mm A') + ' @ ' + strArena + ' - ' + strType + ' (' + strStatus + ')';
                }
                else
                {
                    newText = strDate.format('MM/DD/YYYY') + ' ' + strTime.format('hh:mm A') + ' @ ' + strArena + ' ' + strLocation + ' - ' + strType + ' (' + strStatus + ')';
                }
                
                $('.event-editable').editable('option', 'params', {aid: vals.arena_id, lid: newValue, output: 'json'});
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
        
        $('#Event_type_id').editable({
            params: params,
            showbuttons: false,
            source: eventManagementView.eventTypes,
            success: function(response, newValue) {
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }
            
                var vals = $('#Event_start_date, #Event_start_time').editable('getValue');
                var strArena = $('#Event_arena_id').text();
                var strLocation = $('#Event_location_id').text();
                var strStatus = $('#Event_status_id').text();
                var strType = '';
                
                // Find the newly selected type name
                for (var i = 0; i < eventManagementView.eventTypes.length; i++)
                {
                    if (newValue == eventManagementView.eventTypes[i].value)
                    {
                        strType = eventManagementView.eventTypes[i].text;
                        break;
                    }
                }                
                
                var strDate = moment(vals.start_date, ['YYYY-MM-DD', 'MM/DD/YYYY']);
                var strTime = moment(vals.start_time, ['HH:mm', 'hh:mm A']);
                
                var newText = '';
                
                if (strLocation === '')
                {
                    newText = strDate.format('MM/DD/YYYY') + ' ' + strTime.format('hh:mm A') + ' @ ' + strArena + ' - ' + strType + ' (' + strStatus + ')';
                }
                else
                {
                    newText = strDate.format('MM/DD/YYYY') + ' ' + strTime.format('hh:mm A') + ' @ ' + strArena + ' ' + strLocation + ' - ' + strType + ' (' + strStatus + ')';
                }
                
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
        
        $('#Event_status_id').editable({
            params: params,
            showbuttons: false,
            source: eventManagementView.eventStatuses,
            success: function(response, newValue) {
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }
            
                var vals = $('#Event_start_date, #Event_start_time').editable('getValue');
                var strArena = $('#Event_arena_id').text();
                var strLocation = $('#Event_location_id').text();
                var strStatus = '';
                var strType = $('#Event_type_id').text();

                // Find the newly selected status name
                for (var i = 0; i < eventManagementView.eventStatuses.length; i++)
                {
                    if (newValue == eventManagementView.eventStatuses[i].value)
                    {
                        strStatus = eventManagementView.eventStatuses[i].text;
                        break;
                    }
                }
                
                var strDate = moment(vals.start_date, ['YYYY-MM-DD', 'MM/DD/YYYY']);
                var strTime = moment(vals.start_time, ['HH:mm', 'hh:mm A']);
                
                var newText = '';
                
                if (strLocation === '')
                {
                    newText = strDate.format('MM/DD/YYYY') + ' ' + strTime.format('hh:mm A') + ' @ ' + strArena + ' - ' + strType + ' (' + strStatus + ')';
                }
                else
                {
                    newText = strDate.format('MM/DD/YYYY') + ' ' + strTime.format('hh:mm A') + ' @ ' + strArena + ' ' + strLocation + ' - ' + strType + ' (' + strStatus + ')';
                }
                
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
        
        $('#Event_name').editable({
            params: params
        });
        
        $('#Event_tags').editable({
            params: params
        });
        
        $('#Event_all_day').editable({
            params: params,
            showbuttons: false,
            source: [{value: 0, text: 'No'}, {value: 1, text: 'Yes'}],
            success: function(response, newValue) {
                if (eventManagementView.newRecord)
                {
                    return;
                }
                
                var attributes = false;

                try
                {
                    if (response && response.length > 0)
                    {
                        var newResponse = JSON.parse(response);
                        if (newResponse.attributes)
                        {
                            attributes = newResponse.attributes;
                            response = null;
                        }
                    }
                }
                catch (err)
                {
                    attributes = false;
                }            
                
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }
                
                // Ok, we now need to update the values of our fine feathered
                // friends!
                $('#Event_all_day').editable('setValue', attributes.all_day);
                $('#Event_duration').editable('setValue', attributes.duration);
                $('#Event_start_date').editable('setValue', attributes.start_date, true);
                $('#Event_start_time').editable('setValue', attributes.start_time, true);
                $('#Event_end_date').editable('setValue', attributes.end_date, true);
                $('#Event_end_time').editable('setValue', attributes.end_time, true);
                return {newValue: attributes.all_day};
            }
        });
        
        $('#Event_duration').editable({
            params: params,
            display: function(value, sourceData) {
                // display the supplied digits as a phone number!
                var html = '';

                if (typeof value === 'undefined' || value.length <= 0)
                {
                    return;
                }
            
                $(this).html(value + " minutes");
            },
            success: function(response, newValue) {
                if (eventManagementView.newRecord)
                {
                    return;
                }
                
                var attributes = false;

                try
                {
                    if (response && response.length > 0)
                    {
                        var newResponse = JSON.parse(response);
                        if (newResponse.attributes)
                        {
                            attributes = newResponse.attributes;
                            response = null;
                        }
                    }
                }
                catch (err)
                {
                    attributes = false;
                }            
                
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }
                
                // Ok, we now need to update the values of our fine feathered
                // friends!
                $('#Event_all_day').editable('setValue', attributes.all_day);
                $('#Event_duration').editable('setValue', attributes.duration);
                $('#Event_start_date').editable('setValue', attributes.start_date, true);
                $('#Event_start_time').editable('setValue', attributes.start_time, true);
                $('#Event_end_date').editable('setValue', attributes.end_date, true);
                $('#Event_end_time').editable('setValue', attributes.end_time, true);
                return {newValue: attributes.duration};
            }
        });

        $('#Event_start_date').editable({
            params: params,
            datepicker: {
                autoclose: true,
                cleanBtn: true,
                endDate: moment().add('years', 2).endOf('day').toDate(),
                startDate: moment().subtract('days', 1).startOf('day').toDate(),
                startView: 'month',
                todayBtn: false,
                todayHighlight: false
            },
            success: function(response, newValue) {
                if (eventManagementView.newRecord)
                {
                    return;
                }
                
                var attributes = false;

                try
                {
                    if (response && response.length > 0)
                    {
                        var newResponse = JSON.parse(response);
                        if (newResponse.attributes)
                        {
                            attributes = newResponse.attributes;
                            response = null;
                        }
                    }
                }
                catch (err)
                {
                    attributes = false;
                }            
                
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }
                
                // Ok, we now need to update the values of our fine feathered
                // friends!
                $('#Event_all_day').editable('setValue', attributes.all_day);
                $('#Event_duration').editable('setValue', attributes.duration);
                $('#Event_start_date').editable('setValue', attributes.start_date, true);
                $('#Event_start_time').editable('setValue', attributes.start_time, true);
                $('#Event_end_date').editable('setValue', attributes.end_date, true);
                $('#Event_end_time').editable('setValue', attributes.end_time, true);
                return {newValue: moment(attributes.start_date, 'YYYY-MM-DD').toDate()};
            }
        });
        
        $('#Event_start_time').editable({
            params: params,
            combodate: {
                minuteStep: 1,
                firstItem: 'empty'
            },
            success: function(response, newValue) {
                if (eventManagementView.newRecord)
                {
                    return;
                }
                
                var attributes = false;

                try
                {
                    if (response && response.length > 0)
                    {
                        var newResponse = JSON.parse(response);
                        if (newResponse.attributes)
                        {
                            attributes = newResponse.attributes;
                            response = null;
                        }
                    }
                }
                catch (err)
                {
                    attributes = false;
                }            
                
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }
                
                // Ok, we now need to update the values of our fine feathered
                // friends!
                $('#Event_all_day').editable('setValue', attributes.all_day);
                $('#Event_duration').editable('setValue', attributes.duration);
                $('#Event_start_date').editable('setValue', attributes.start_date, true);
                $('#Event_start_time').editable('setValue', attributes.start_time, true);
                $('#Event_end_date').editable('setValue', attributes.end_date, true);
                $('#Event_end_time').editable('setValue', attributes.end_time, true);
                return {newValue: moment(attributes.start_time, 'HH:mm')};
            }
        });

        $('#Event_end_date').editable({
            params: params,
            datepicker: {
                autoclose: true,
                cleanBtn: true,
                endDate: moment().add('years', 2).endOf('day').toDate(),
                startDate: moment().subtract('days', 1).startOf('day').toDate(),
                startView: 'month',
                todayBtn: false,
                todayHighlight: false
            },
            success: function(response, newValue) {
                if (eventManagementView.newRecord)
                {
                    return;
                }
                
                var attributes = false;

                try
                {
                    if (response && response.length > 0)
                    {
                        var newResponse = JSON.parse(response);
                        if (newResponse.attributes)
                        {
                            attributes = newResponse.attributes;
                            response = null;
                        }
                    }
                }
                catch (err)
                {
                    attributes = false;
                }            
                
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }
                
                // Ok, we now need to update the values of our fine feathered
                // friends!
                $('#Event_all_day').editable('setValue', attributes.all_day);
                $('#Event_duration').editable('setValue', attributes.duration);
                $('#Event_start_date').editable('setValue', attributes.start_date, true);
                $('#Event_start_time').editable('setValue', attributes.start_time, true);
                $('#Event_end_date').editable('setValue', attributes.end_date, true);
                $('#Event_end_time').editable('setValue', attributes.end_time, true);
                return {newValue: moment(attributes.end_date, 'YYYY-MM-DD').toDate()};
            }
        });
        
        $('#Event_end_time').editable({
            params: params,
            combodate: {
                minuteStep: 1,
                firstItem: 'empty'
            },
            success: function(response, newValue) {
                if (eventManagementView.newRecord)
                {
                    return;
                }
                
                var attributes = false;

                try
                {
                    if (response && response.length > 0)
                    {
                        var newResponse = JSON.parse(response);
                        if (newResponse.attributes)
                        {
                            attributes = newResponse.attributes;
                            response = null;
                        }
                    }
                }
                catch (err)
                {
                    attributes = false;
                }            
                
                if (response && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired.";
                }
                
                // Ok, we now need to update the values of our fine feathered
                // friends!
                $('#Event_all_day').editable('setValue', attributes.all_day);
                $('#Event_duration').editable('setValue', attributes.duration);
                $('#Event_start_date').editable('setValue', attributes.start_date, true);
                $('#Event_start_time').editable('setValue', attributes.start_time, true);
                $('#Event_end_date').editable('setValue', attributes.end_date, true);
                $('#Event_end_time').editable('setValue', attributes.end_time, true);
                return {newValue: moment(attributes.end_time, 'HH:mm')};
            }
        });
        
        $('#Event_price').editable({
            params: params,
            display: function(value, sourceData) {
                // display the supplied digits as a phone number!
                var html = '';

                if (typeof value === 'undefined' || value.length <= 0)
                {
                    return;
                }
            
                $(this).html("$" + value);
            }
        });

        $("#Event_price").on('shown', function(e, editable) {
            // ensure that we only get the unmasked value
            if (editable) {
                $(this).data('editable').input.$input.inputmask({
                    alias: 'decimal',
                    placeholder: "$0.00",
                    autoUnmask: true,
                    digits: 2,
                    groupSeparator: ",",
                    radixPoint: ".",
                    autoGroup: true,
                    rightAlignNumerics: true,
                    clearMaskOnLostFocus: true,
                    clearIncomplete: true,
                    showTooltip: true
                });
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
        var eventView = '<strong>General Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';
        
        eventView += '<tr><td style="width:33%">Facility</td><td>' +
                '<a href="#" id="Event_arena_id" data-name="arena_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + event.arena_id + '" ' +
                'title="Facility" class="event-editable">' +
                event.arena_name + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Venue</td><td>' +
                '<a href="#" id="Event_location_id" data-name="location_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.location_id ? event.location_id : '') + '" ' +
                'title="Venue" class="event-editable">' +
                (event.location_id ? event.location_name : '') + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Name</td><td>' +
                '<a href="#" id="Event_name" data-name="name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.name ? event.name : '') + '" ' +
                'title="Event Name" class="event-editable">' + 
                (event.name ? event.name : '') + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">External ID</td><td>' +
                '<a href="#" id="Event_external_id" data-name="external_id" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.external_id ? event.external_id : '') + '" ' +
                'title="Your Event ID" class="event-editable">' +
                (event.external_id ? event.external_id : '') + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Tags</td><td>' +
                '<a href="#" id="Event_tags" data-name="tags" ' +
                'data-type="text" "data-mode="inline" data-url="' + 
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
        eventView += '<strong>Event Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        eventView += '<tr><td style="width:33%">All Day</td><td>' +
                '<a href="#" id="Event_all_day" data-name="all_day" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + event.all_day + '" ' +
                'title="All Day Event" class="event-editable">' +
                (event.all_day == 0 ? 'No' : 'Yes') + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Start Date</td><td>' +
                '<a href="#" id="Event_start_date" data-name="start_date" ' +
                'data-type="date" data-format="yyyy-mm-dd" data-mode="inline" data-viewFormat="mm/dd/yyyy" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + event.start_date + '" ' +
                'title="Event Start Date" class="event-editable">' +
                moment(event.start_date, 'YYYY-MM-DD').format('MM/DD/YYYY') + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Start Time</td><td>' +
                '<a href="#" id="Event_start_time" data-name="start_time" ' +
                'data-type="combodate" data-template="hh : mm A" data-format="HH:mm" data-mode="inline" data-viewFormat="hh:mm A" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + event.start_time + '" ' +
                'title="Event Start Time" class="event-editable">' +
                moment(event.start_time, 'HH:mm:ss').format('hh:mm A') + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">End Date</td><td>' +
                '<a href="#" id="Event_end_date" data-name="end_date" ' +
                'data-type="date" data-format="yyyy-mm-dd" data-mode="inline" data-viewFormat="mm/dd/yyyy" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.end_date ? event.end_date : '') + '" ' +
                'title="Event End Date" class="event-editable">' +
                (event.end_date ? moment(event.end_date, 'YYYY-MM-DD').format('MM/DD/YYYY') : '') + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">End Time</td><td>' +
                '<a href="#" id="Event_end_time" data-name="end_time" ' +
                'data-type="combodate" data-template="hh : mm A" data-format="HH:mm" data-mode="inline" data-viewFormat="hh:mm A" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.end_time ? event.end_time : '') + '" ' +
                'title="Event End Time" class="event-editable">' +
                (event.end_time ? moment(event.end_time, 'HH:mm:ss').format('hh:mm A') : '') + '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Event Duration</td><td>' +
                '<a href="#" id="Event_duration" data-name="duration" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.duration ? event.duration : '') + '" ' +
                'title="Event Duration" class="event-editable">' +
                (event.duration ? event.duration : '') + '</a></td></tr>';

        eventView += '<tr><td style="width:33%">Event Price</td><td>' +
                '<a href="#" id="Event_price" data-name="price" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-pk="' + event.id + '" data-value="' + (event.price ? event.price : '') + '" ' +
                'title="Event Price $" class="event-editable">' +
                (event.price ? event.price : '') + '</a></td></tr>';

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
        
        var eventView = '<strong>General Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';
        
        eventView += '<tr><td style="width:33%">Facility</td><td>' +
                '<a href="#" id="Event_arena_id" data-name="arena_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Facility" class="event-editable">' +
                '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Venue</td><td>' +
                '<a href="#" id="Event_location_id" data-name="location_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Venue" class="event-editable">' +
                '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Name</td><td>' +
                '<a href="#" id="Event_name" data-name="name" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Name" class="event-editable">' + 
                '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">External ID</td><td>' +
                '<a href="#" id="Event_external_id" data-name="external_id" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Your Event ID" class="event-editable">' +
                '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Tags</td><td>' +
                '<a href="#" id="Event_tags" data-name="tags" ' +
                'data-type="text" "data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Tags" class="event-editable">' +
                '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Type</td><td>' +
                '<a href="#" id="Event_type_id" data-name="type_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-value="1" ' +
                'title="Event Type" class="event-editable"></a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Status</td><td>' +
                '<a href="#" id="Event_status_id" data-name="status_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-value="1" ' +
                'title="Event Status" class="event-editable">' +
                '</a></td></tr>';
        
        eventView += '</tbody></table>';
        
        // Now the next table of info!
        eventView += '<strong>Event Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        eventView += '<tr><td style="width:33%">All Day</td><td>' +
                '<a href="#" id="Event_all_day" data-name="all_day" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'data-value="0" ' +
                'title="All Day Event" class="event-editable">' +
                '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Start Date</td><td>' +
                '<a href="#" id="Event_start_date" data-name="start_date" ' +
                'data-type="date" data-format="yyyy-mm-dd" data-mode="inline" data-viewFormat="mm/dd/yyyy" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Start Date" class="event-editable">' +
                '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Start Time</td><td>' +
                '<a href="#" id="Event_start_time" data-name="start_time" ' +
                'data-type="combodate" data-template="hh : mm A" data-format="HH:mm" data-mode="inline" data-viewFormat="hh:mm A" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Start Time" class="event-editable">' +
                '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">End Date</td><td>' +
                '<a href="#" id="Event_end_date" data-name="end_date" ' +
                'data-type="date" data-format="yyyy-mm-dd" data-mode="inline" data-viewFormat="mm/dd/yyyy" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event End Date" class="event-editable">' +
                '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">End Time</td><td>' +
                '<a href="#" id="Event_end_time" data-name="end_time" ' +
                'data-type="combodate" data-template="hh : mm A" data-format="HH:mm" data-mode="inline" data-viewFormat="hh:mm A" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event End Time" class="event-editable">' +
                '</a></td></tr>';
        
        eventView += '<tr><td style="width:33%">Event Duration</td><td>' +
                '<a href="#" id="Event_duration" data-name="duration" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Duration" class="event-editable">' +
                '</a></td></tr>';

        eventView += '<tr><td style="width:33%">Event Price</td><td>' +
                '<a href="#" id="Event_price" data-name="price" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Price $" class="event-editable">' +
                '</a></td></tr>';

        eventView += '</tbody></table>';
        
        // Now the next table of info!
        eventView += '<strong>Description & Notes</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        eventView += '<tr><td style="width:33%">Description<i class="fa fa-lg fa-fw ' +
                'fa-pencil" style="padding-right: 5px"></i> <a href="#" id="Event_description_edit">' +
                '<span>[edit]</span></a></td><td><div id="Event_description" data-name="description" ' +
                'data-type="wysihtml5" data-mode="inline" data-toggle="manual" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Description" class="event-editable">' +
                '</div></td></tr>';
        
        eventView += '<tr><td style="width:33%">Notes<i class="fa fa-lg fa-fw ' +
                'fa-pencil" style="padding-right: 5px"></i> <a href="#" id="Event_notes_edit">' +
                '<span>[edit]</span></a></td><td><div id="Event_notes" data-name="notes" ' +
                'data-type="wysihtml5" data-mode="inline" data-toggle="manual" data-url="' + 
                eventManagementView.endpoints.event.updateRecord + '" ' +
                'title="Event Notes" class="event-editable">' +
                '</div></td></tr>';
                
        eventView += '<tr style="display:none;"><td>Arena ID</td><td>' +
                '<a href="#" id="Event_aid" data-name="aid" ' +
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
        
        // Ok, now we can fade-out the current view, and then fade in our new
        // view!
        var $cv = $('<div id="eventDetails">' + eventView + '</div>');
        
        $cv.hide();
        
        $eventDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $eventDetails.parent().prepend($cv);
            
            // Setup the contact editables before we fade in!
            eventManagementView.setupEventEditables(params);
            
            $('#Event_aid').editable({
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