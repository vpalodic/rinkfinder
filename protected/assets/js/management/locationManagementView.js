/* 
 * This is the jQuery plugin for the user view / update / create actions
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( locationManagementView, $, undefined ) {
    "use strict";
    // public properties
    locationManagementView.endpoints = {
        location: {
            viewRecord: "/server/endpoint",
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint",
            deleteRecord: "/server/endpoint"
        }
    };
    
    locationManagementView.location = {};
    locationManagementView.locationTypes = [];
    locationManagementView.locationStatuses = [];
    locationManagementView.params = {};
    locationManagementView.isArenaManager = false;
    locationManagementView.Id = 0;
    locationManagementView.Name = '';
    
    locationManagementView.onReady = function () {
        if (typeof $.fn.editable === "undefined")
        { 
            locationManagementView.loadEditable();
        }
        else
        {
            locationManagementView.setupInitialLocationView();
        }
        
        var $panel = $("#locationManagementView.panel.panel-primary");
        
        if ($panel.length > 0)
        {
            $panel.on('destroyed', function () {
                // We have been closed, so clean everything up!!!
                var $editables = $("#locationManagementView.panel.panel-primary .editable");
                
                $editables.editable('destroy');
            });
        }
        
        $('[data-toggle="tooltip"]').tooltip();
    };
    
    locationManagementView.loadEditable = function () {
        
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
                locationManagementView.setupInitialLocationView();
            } else if (console && console.log) {
                console.log("Loading... " + scriptName);
            }
        }, 500);
        
    };
    
    locationManagementView.createDeleteModal = function () {
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
    
    locationManagementView.setupInitialLocationView = function () {
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
                aid: locationManagementView.params.aid,
                output: 'html'
            };

            locationManagementView.setupNewLocationView(myParams);
            $newBtn.attr("disabled", "disabled");
            $deleteBtn.attr("disabled", "disabled");
        });
        
        $deleteBtn.on('click', function (e) {
            e.preventDefault();
            
            var $modal = locationManagementView.createDeleteModal();
            
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
                    aid: locationManagementView.params.id,
                    id: locationId,
                    pk: locationId,
                    output: 'html'
                };
                
                $.ajax({
                    url: locationManagementView.endpoints.location.deleteRecord,
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
                url: locationManagementView.endpoints.location.newRecord, 
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
            
            locationManagementView.resetLocationView();
        });
        
        if(locationManagementView.location.id !== "undefined")
        {
            locationManagementView.setupLocationView(locationManagementView.location, locationManagementView.params);
        }
    };
    
    locationManagementView.setupLocationEditables = function (params) {
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
            source: locationManagementView.locationStatuses,
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
            source: locationManagementView.locationTypes,
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
    
    locationManagementView.setupLocationView = function (location, params) {
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
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + location.name + '" ' +
                'title="Venue Name" class="location-editable">' + 
                location.name + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">External ID</td><td>' +
                '<a href="#" id="Location_external_id" data-name="external_id" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + (location.external_id ? location.external_id : '') + '" ' +
                'title="Your Venue ID" class="location-editable">' +
                (location.external_id ? location.external_id : '') + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Tags</td><td>' +
                '<a href="#" id="Location_tags" data-name="tags" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + location.tags + '" ' +
                'title="Venue Tags" class="location-editable">' +
                location.tags + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Type</td><td>' +
                '<a href="#" id="Location_type_id" data-name="type_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + location.type_id + '" ' +
                'title="Venue Type" class="location-editable">' +
                location.type + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Status</td><td>' +
                '<a href="#" id="Location_status_id" data-name="status_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
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
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + (location.length ? location.length : '') + '" ' +
                'title="Venue length in feet" class="location-editable">' +
                (location.length ? location.length : '') + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Width (ft)</td><td>' +
                '<a href="#" id="Location_width" data-name="width" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + (location.width ? location.width : '') + '" ' +
                'title="Venue width in feet" class="location-editable">' +
                (location.width ? location.width : '') + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Radius (ft)</td><td>' +
                '<a href="#" id="Location_radius" data-name="radius" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + (location.radius ? location.radius : '') + '" ' +
                'title="Venue readius in feet" class="location-editable">' +
                (location.radius ? location.radius : '') + '</a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Seating Capacity</td><td>' +
                '<a href="#" id="Location_seating" data-name="seating" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
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
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'data-pk="' + location.id + '" data-value="' + (location.description ? location.description : '') + '" ' +
                'title="Venue Description" class="location-editable">' +
                (location.description ? location.description : '') + '</div></td></tr>';
        
        locationView += '<tr><td style="width:33%">Notes<i class="fa fa-lg fa-fw ' +
                'fa-pencil" style="padding-right: 5px"></i> <a href="#" id="Location_notes_edit">' +
                '<span>[edit]</span></a></td><td><div id="Location_notes" data-name="notes" ' +
                'data-type="wysihtml5" data-mode="inline" data-toggle="manual" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
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
            locationManagementView.setupLocationEditables(params);
            
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
    
    locationManagementView.setupNewLocationView = function (params) {
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
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue Name" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">External ID</td><td>' +
                '<a href="#" id="Location_external_id" data-name="external_id" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'title="Your Venue ID" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Tags</td><td>' +
                '<a href="#" id="Location_tags" data-name="tags" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue Tags" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Type</td><td>' +
                '<a href="#" id="Location_type_id" data-name="type_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'data-value="1" title="Venue Type" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Status</td><td>' +
                '<a href="#" id="Location_status_id" data-name="status_id" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'data-value="1" title="Venue Status" class="location-editable"></a></td></tr>';
        
        locationView += '</tbody></table>';
        
        // Now the next table of info!
        locationView += '<strong>Email & Phone</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';

        locationView += '<tr><td style="width:33%">Length (ft)</td><td>' +
                '<a href="#" id="Location_length" data-name="length" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue length in feet" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Width (ft)</td><td>' +
                '<a href="#" id="Location_width" data-name="width" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue width in feet" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Radius (ft)</td><td>' +
                '<a href="#" id="Location_radius" data-name="radius" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue readius in feet" class="location-editable"></a></td></tr>';
        
        locationView += '<tr><td style="width:33%">Seating Capacity</td><td>' +
                '<a href="#" id="Location_seating" data-name="seating" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue seating capacity" class="location-editable"></a></td></tr>';
                
        locationView += '<tr style="display:none;"><td>Arena ID</td><td>' +
                '<a href="#" id="Location_arena_id" data-name="aid" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'data-value="' + locationManagementView.params.aid + '" ' +
                'title="Arena ID" class="location-editable">' +
                locationManagementView.params.aid + '</a></td></tr>';
                
        locationView += '<tr style="display:none;"><td>Output</td><td>' +
                '<a href="#" id="Location_output" data-name="output" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
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
                locationManagementView.endpoints.location.updateRecord + '" ' +
                'title="Venue Description" class="location-editable"></div></td></tr>';
        
        locationView += '<tr><td style="width:33%">Notes<i class="fa fa-lg fa-fw ' +
                'fa-pencil" style="padding-right: 5px"></i> <a href="#" id="Location_notes_edit">' +
                '<span>[edit]</span></a></td><td><div id="Location_notes" data-name="notes" ' +
                'data-type="wysihtml5" data-toggle="manual" data-mode="inline" data-url="' + 
                locationManagementView.endpoints.location.updateRecord + '" ' +
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
            locationManagementView.setupLocationEditables(params);
            
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
    
    locationManagementView.resetLocationView = function (){
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
    
    locationManagementView.loadLocation = function (locationId) {
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
            aid: locationManagementView.params.aid,
            output: 'json'
        };
        
        $.ajax({
            url: locationManagementView.endpoints.location.viewRecord,
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
                
                locationManagementView.setupLocationView(result.data, myParams);
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
    
}( window.locationManagementView = window.locationManagementView || {}, jQuery ));