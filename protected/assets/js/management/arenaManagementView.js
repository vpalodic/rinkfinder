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
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint"
        },
        location: {
            viewRecord: "/server/endpoint",
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint"
        },
        contact: {
            viewRecord: "/server/endpoint",
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint"
        },
        event: {
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint"
        },
        manager: {
            updateRecord: "/server/endpoint",
            newRecord: "/server/endpoint"
        }
    };
    
    arenaManagementView.arena = {};
    arenaManagementView.locations = {};
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
        $('#assignContactButton').attr("disabled", "disabled");
        $('#unassignContactButton').attr("disabled", "disabled");
        $('#deleteContactButton').attr("disabled", "disabled");
        $('#saveContactButton').attr("disabled", "disabled").hide();
        $('#cancelContactButton').attr("disabled", "disabled").hide();
        
        // Setup the select lists!
        
    };
    
    arenaManagementView.setupContactEditables = function () {
        $('#Contact_first_name').editable({
            params: arenaManagementView.params
        });
        
        $('#Contact_last_name').editable({
            params: arenaManagementView.params
        });
        
        $('#Contact_active').editable({
            params: arenaManagementView.params,
            showbuttons: false,
            source: [{
                    vale: 0,
                    text: 'Inactive'
            }, {
                value: 1,
                text: 'Active'
            }]
        });
        
        $('#Contact_primary').editable({
            params: arenaManagementView.params,
            showbuttons: false,
            source: [{
                    vale: 0,
                    text: 'No'
            }, {
                value: 1,
                text: 'Yes'
            }]
        });
        
        $('#Contact_email').editable({
            params: arenaManagementView.params
        });
        
        $('#Contact_phone').editable({
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
            params: arenaManagementView.params
        });
        
        $('#Contact_fax').editable({
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
            params: arenaManagementView.params
        });        
    };
    
    arenaManagementView.setupContactView = function (contact) {
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
        
        // 
        var contactView = '<strong>Details</strong><br /><table class="table ' +
                'table-condensed table-information"><tbody>';
        
        // Add one row at a time. 
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
        
        if(contact.active === 1)
        {
            contactView += 'Yes</a></td></tr>';
        }
        else
        {
            contactView += 'No</a></td></tr>';
        }
        
        contactView += '<tr><td style="width:33%">Primary</td><td>' +
                '<a href="#" id="Contact_primary" data-name="primary" ' +
                'data-type="select" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + contact.primary + '" ' +
                'title="Primary Status" class="contact-editable">';
        
        if(contact.primary === 1)
        {
            contactView += 'Yes</a></td></tr>';
        }
        else
        {
            contactView += 'No</a></td></tr>';
        }
        
        contactView += '</a></td></tr></tbody></table>';
        
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
                'data-pk="' + contact.id + '" data-value="' + contact.ext + '" ' +
                'title="Phone extension" class="contact-editable">' +
                contact.ext + '</a></td></tr>';
        
        contactView += '<tr><td style="width:33%">Fax</td><td>' +
                '<a href="#" id="Contact_fax" data-name="fax" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + contact.fax + '" ' +
                'title="Ten digit fax number" class="contact-editable">' +
                contact.fax + '</a></td></tr>';
       
        contactView += '<tr><td style="width:33%">Fax Extension</td><td>' +
                '<a href="#" id="Contact_fax_ext" data-name="fax_ext" ' +
                'data-type="text" data-mode="inline" data-url="' + 
                arenaManagementView.endpoints.contact.updateRecord + '" ' +
                'data-pk="' + contact.id + '" data-value="' + contact.fax_ext + '" ' +
                'title="Fax extension" class="contact-editable">' +
                contact.fax_ext + '</a></td></tr></tbody></table>';
        
        // Ok, now we can fade-out the current view, and then fade in our new
        // view!
        var $cv = $(contactView);
        
        $cv.hide();
        
        $contactDetails.fadeOut(500, function () {
            // Current view is hidden so let's remove it
            $contactDetails.empty();
            
            $contactDetails.append($cv);
            
            // Setup the contact editables before we fade in!
            arenaManagementView.setupContactEditables();
            
            // Now fade us in!
            $contactDetails.fadeIn(500);
        });

    };
    
    arenaManagementView.setupButtons = function () {
        
    };
    
    arenaManagementView.loadContact = function (contactId) {
        
    };
    
    arenaManagementView.createNewContact = function () {
        
    };
}( window.arenaManagementView = window.arenaManagementView || {}, jQuery ));