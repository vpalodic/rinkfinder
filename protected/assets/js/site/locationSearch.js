/* 
 * This is the jQuery plugin for the locationSearch action
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( locationSearch, $, undefined ) {
    "use strict";
    // public properties
    locationSearch.endpoints = {
        markers: ''
    };
    
    locationSearch.useGeoLocation = false;
    locationSearch.position = null;
    locationSearch.searchResults = [];
    locationSearch.map = null;
    locationSearch.markers = [];
    locationSearch.infoWindow = null;
    locationSearch.centerpoint = null;
    locationSearch.geocodedAddr = "";
    locationSearch.isSearching = false;
    locationSearch.isGeolocating = false;
    locationSearch.searchParams = {};
    
    locationSearch.getUserPosition = function (search) {
        if (this.isGeolocating === true)
        {
            return;
        }
        
        var that = this;
        if (navigator.geolocation)
        {
            that.isGeolocating = true;
            
            navigator.geolocation.getCurrentPosition(function (pos) {
                that.position = pos;
                
                that.useGeoLocation = true;
                
                $("input[name=saddr]").attr("placeholder", "Your current location");
                
                that.centerpoint = new google.maps.LatLng(that.position.coords.latitude, that.position.coords.longitude);
                
                that.isGeolocating = false;
                
                if (search === true)
                {
                    that.searchLocationsNear();
                }
            },
            function (error) {
                that.useGeoLocation = false;
                that.isGeolocating = false;
                
                switch(error.code)
                {
                    case error.PERMISSION_DENIED:
                        alert('Geolocation failed: You denied the request for Geolocation.'); 
                        break;
                    case error.POSITION_UNAVAILABLE:
                        alert('Geolocation failed: Location information is unavailable.');
                        break;
                    case error.TIMEOUT:
                        alert('Geolocation failed: The request to get your location timed out.');
                        break;
                    case error.UNKNOWN_ERROR:
                        alert('Geolocation failed: An unknown error occurred.');
                        break;
                }
            });
        }
        else
        {
            this.useGeoLocation = false;
            this.isGeolocating = false;
        }
    };
    
    locationSearch.initMap = function () {
        if (typeof this.centerpoint === "undefined" || this.centerpoint === null) {
            this.centerpoint = new google.maps.LatLng(44.9833, -93.2667);
        }

        var $well = $("#searchResultsWell");
        
        if($well.hasClass("hidden")) {
            $well.removeClass("hidden");
        }
        
        if (typeof this.map === "undefined" || this.map === null) {
            this.map = new google.maps.Map(document.getElementById("map-canvas"), {
                center: this.centerpoint,
                zoom: 12,
                mapTypeId: google.maps.MapTypeId.HYBRID
            });
        }
        
        if (typeof this.infoWindow === "undefined" || this.infoWindow === null) {
            this.infoWindow = new google.maps.InfoWindow();
        }
        
        this.clearLocations();
    };
    
    locationSearch.setupMarkers = function () {
        var bounds = new google.maps.LatLngBounds();
        var that = this;
        
        for (var i = 0; i < this.searchResults.length; i++) {
            var latlng = new google.maps.LatLng(
                    parseFloat(this.searchResults[i].lat),
                    parseFloat(this.searchResults[i].lng)
            );

            this.createMarker(latlng, this.searchResults[i], i);
            bounds.extend(latlng);
        }
        
        if (this.searchResults.length > 0)
        {
            this.map.fitBounds(bounds);
        
            $("#locationSelect").off('change');
            $("#locationSelect").on('change', function (e) {
                var markerNum = $("#locationSelect").val();
            
                if (markerNum !== "none")
                {
                    google.maps.event.trigger(that.markers[markerNum], 'click');
                }
            });
        
            $("#locationList").off('click', 'li');
            $("#locationList").on('click', 'li', function (e) {
                var markerNum = $(this).data('markerIndex');

                if (typeof markerNum !== "undefined" && markerNum !== "none")
                {
                    google.maps.event.trigger(that.markers[markerNum], 'click');
                }
            });
        }
        else
        {
            this.map.panTo(this.centerpoint);
            this.map.setZoom(8);
            
            $('#locationSelect').empty();
            $('#locationList').empty();
        
            var li = "<li data-marker-index='none'><h3>No Facilities Found</h3>" +
                    "<p>There were no facilities found that matched your search" +
                    " filter.</p></li>";
            var option = "<option value='none'>No Facilities Found</option>";
        
            $('#locationSelect').append(option);
            $('#locationList').append(li);
        }
    };

    locationSearch.searchLocations = function () {
        var $address = $("#addressInput");
        var address = $address.val();
        
        if (typeof address.trim === "function")
        {
            address = address.trim();
        }
        
        var that = this;
        
        if (address.length <= 0 && this.useGeoLocation === true && this.isGeolocating === false)
        {
            this.getUserPosition(true);
        }
        else if (address === this.geocodedAddr)
        {
            this.searchLocationsNear();
        }
        else
        {
            var geocoder = new google.maps.Geocoder();
            
            geocoder.geocode({address: address}, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK)
                {
                    that.centerpoint = results[0].geometry.location;
                    that.geocodedAddr = address;
                    that.searchLocationsNear();
                } else
                {
                    alert(address + ' not found');
                }
            });
        }
    };
    
    locationSearch.searchLocationsNear = function () {
        if (this.isSearching === true)
        {
            return;
        }
        
        var that = this;
        
        this.clearLocations();
        var doSearch = this.getFilterOptions();

        if (doSearch === false) {
            return;
        }
        
        this.isSearching = true;
        
        $.ajax({
            url: this.endpoints.markers,
            type: "GET",
            dataType: "json",
            data: that.searchParams,
            success: function (result, status, xhr) {
                // Clear previous results!
                that.searchResults = [];
                that.searchResults = result.data;
                
                // Now we have an indexed array of the main results
                
                that.setupMarkers();
                that.isSearching = false;
                that.showButtons();
                
                if ($(window).width() > 767)
                {
                    $('#searchResultsWell').ScrollTo({
                        offsetTop: 60
                    });
                }
                else
                {
                    $('#searchResultsWell').ScrollTo();
                }
            },
            error: function(xhr, status, errorThrown) {
                window.setTimeout(function () {
                    that.isSearching = false;
                    that.showButtons();
                    
                    utilities.ajaxError.show(
                            "Location Search",
                            "Failed to retrieve data",
                            xhr,
                            status,
                            errorThrown
                    );
                }, 1000);
            }
        });
    };

    locationSearch.clearLocations = function() {        
        if (this.infoWindow !== null) {
            this.infoWindow.close();
        }
        
        for (var i = 0; i < this.markers.length; i++) {
            this.markers[i].setMap(null);
        }
        
        this.markers.length = 0;
        this.searchResults.length = 0;
        
        $('#locationSelect').empty();
        $('#locationList').empty();
        
        var option = "<option value='none'>See all results</option>";
        
        $('#locationSelect').append(option);
    };

    locationSearch.createMarker = function (latlng, marker, index) {
        var html = this.createInfoWindow(marker);        
        var that = this;
        this.addLocationListItem(marker, index);
        this.addLocationSelectItem(marker, index);
        
        html = "<div class='infowindow'>" + html + "</div>";
        
        var mapMarker = new google.maps.Marker({
            map: that.map,
            position: latlng
        });
        
        google.maps.event.addListener(mapMarker, 'click', function() {
            that.infoWindow.setContent(html);
            that.infoWindow.open(that.map, mapMarker);
        });
        
        this.markers.push(mapMarker);
    };
    
    locationSearch.createInfoWindow = function (marker) {
        // Start with the general arena info
        var output = '<div class="accordion" id="accordionInfoWindow">';

        // Now do the events!
        if (typeof marker.events !== 'undefined' && marker.events !== null && marker.events.length > 0)
        {
            output += '<div class="accordion-group"><div class="accordion-heading">' +
                    '<a class="accordion-toggle" data-toggle="collapse" ' +
                    'data-parent="#accordionInfoWindow" href="#collapseInfoWindowThree">' +
                    'Facility Events</a></div>';

            output += '<div id="collapseInfoWindowThree" class="accordion-body collapse in">' +
                    '<div class="accordion-inner">';

            output += "<h5><a href='" + marker.events_url + "'>Event Calendar</a></h5>";
            
            for (var i = 0; i < marker.events.length; i++)
            {
                output += "<address><strong><a href='" + marker.events[i].event_view_url + "'>" + marker.events[i].event_type_name + "</a></strong><br />";
                
                output += "Found: " + marker.events[i].event_count + "<br />";
                output += "Earliest: " + marker.events[i].start_date_time + "</address>";
            }
            output += '</div></div></div>';
        }
        else if (typeof marker.events_url !== 'undefined' && marker.events_url !== null && marker.events_url.length > 0)
        {
            output += "<h5><a href='" + marker.events_url + "'>Event Calendar</a></h5>";
        }
        
        output += '<div class="accordion-group"><div class="accordion-heading">' +
                '<a class="accordion-toggle" data-toggle="collapse" ' +
                'data-parent="#accordionInfoWindow" href="#collapseInfoWindowOne">' +
                'Facility Details</a></div>';
        
        output += '<div id="collapseInfoWindowOne" class="accordion-body collapse in">' +
                '<div class="accordion-inner">';

        output += "<h5><a href='" + marker.view_url + "'>" + marker.arena_name + "</a></h5><small>" + 
                "<strong>" + parseFloat(marker.distance).toFixed(2) + "</strong> miles</small><br />";

        if(this.geocodedAddr === '')
        {
            if (typeof marker.address_line2 !== 'undefined' && marker.address_line2 !== null && marker.address_line2.length > 0)
            {
                output += '<a target="_blank" href="http://maps.google.com/?q=' + escape(this.centerpoint.lat() + ',' + this.centerpoint.lng()) + 
                    "&daddr=" + escape(marker.address_line1 + ', ' + marker.address_line2 + ',' + marker.city_state_zip) + '&saddr=' + 
                    escape(this.centerpoint.lat() + ',' + this.centerpoint.lng()) + '">Driving Directions</a>';
            }
            else
            {
                output += '<a target="_blank" href="http://maps.google.com/?q=' + escape(this.centerpoint.lat() + ',' + this.centerpoint.lng()) + 
                    "&daddr=" + escape(marker.address_line1 + ', ' + marker.city_state_zip) + '&saddr=' + 
                    escape(this.centerpoint.lat() + ',' + this.centerpoint.lng()) + '">Driving Directions</a>';
            }
            
        }
        else
        {
            if (typeof marker.address_line2 !== 'undefined' && marker.address_line2 !== null && marker.address_line2.length > 0)
            {
                output += '<a target="_blank" href="http://maps.google.com/?q=' + escape(this.centerpoint.lat() + ',' + this.centerpoint.lng()) + 
                    "&daddr=" + escape(marker.address_line1 + ', ' + marker.address_line2 + ',' + marker.city_state_zip) + '&saddr=' + 
                    escape(this.geocodedAddr) + '">Driving Directions</a>';
            }
            else
            {
                output += '<a target="_blank" href="http://maps.google.com/?q=' + escape(this.centerpoint.lat() + ',' + this.centerpoint.lng()) + 
                    "&daddr=" + escape(marker.address_line1 + ', ' + marker.city_state_zip) + '&saddr=' + 
                    escape(this.geocodedAddr) + '">Driving Directions</a>';
            }            
        }
        
        output += "<address>" + marker.address_line1 + "<br />";
        
        if (typeof marker.address_line2 !== 'undefined' && marker.address_line2 !== null && marker.address_line2.length > 0)
        {
            output += marker.address_line2 + "<br />";
        }
        
        output += marker.city_state_zip + "<br />";

        if (typeof marker.phone !== 'undefined' && marker.phone !== null && marker.phone.length > 0)
        {
            output += '<abbr title="Phone">P:</abbr> ' + marker.phone.replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");

            if (typeof marker.ext !== 'undefined' && marker.ext !== null && marker.ext.length > 0)
            {
                output += ' <abbr title="Extension">E:</abbr> ' + marker.ext + "<br />";
            }
            else
            {
                output += "<br />";
            }
        }
        
        if (typeof marker.fax !== 'undefined' && marker.fax !== null && marker.fax.length > 0)
        {
            output += '<abbr title="Fax">F:</abbr> ' + marker.fax.replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");

            if (typeof marker.fax_ext !== 'undefined' && marker.fax_ext !== null && marker.fax_ext.length > 0)
            {
                output += ' <abbr title="Fax Extension">E:</abbr> ' + marker.fax_ext + "<br />";
            }
            else
            {
                output += "<br />";
            }
        }
        
        if (typeof marker.home_url !== 'undefined' && marker.home_url !== null && marker.home_url.length > 0)
        {
            output += '<abbr title="Home Page">H:</abbr> <a target="_blank" href="' + marker.home_url + '">' + 'Home Page' + '</a><br />';
        }
        
        output += '</address></div></div></div>';
        
        // Now do the contacts!
        if (typeof marker.contacts !== 'undefined' && marker.contacts !== null && marker.contacts.length > 0)
        {
            output += '<div class="accordion-group"><div class="accordion-heading">' +
                    '<a class="accordion-toggle" data-toggle="collapse" ' +
                    'data-parent="#accordionInfoWindow" href="#collapseInfoWindowTwo">' +
                    'Facility Contacts</a></div>';

            output += '<div id="collapseInfoWindowTwo" class="accordion-body collapse">' +
                    '<div class="accordion-inner">';

            for (var i = 0; i < marker.contacts.length; i++)
            {
                if (marker.contacts[i].contact_type === "Primary")
                {
                    output += "<address><abbr title='Primary Contact'>*:</abbr> <strong>" + marker.contacts[i].contact_name + "</strong><br />";
                }
                else
                {
                    output += "<address>" + marker.contacts[i].contact_name + "<br />";
                }
                
                if (typeof marker.contacts[i].contact_phone !== 'undefined' && marker.contacts[i].contact_phone !== null && marker.contacts[i].contact_phone.length > 0)
                {
                    output += '<abbr title="Phone">P:</abbr> ' + marker.contacts[i].contact_phone.replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
            
                    if (typeof marker.contacts[i].contact_ext !== 'undefined' && marker.contacts[i].contact_ext !== null && marker.contacts[i].contact_ext.length > 0)
                    {
                        output += ' <abbr title="Extension">E:</abbr> ' + marker.contacts[i].contact_ext + "<br />";
                    }
                    else
                    {
                        output += "<br />";
                    }
                }

                if (typeof marker.contacts[i].contact_fax !== 'undefined' && marker.contacts[i].contact_fax !== null && marker.contacts[i].contact_fax.length > 0)
                {
                    output += '<abbr title="Fax">F:</abbr> ' + marker.contacts[i].contact_fax.replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
                        
                    if (typeof marker.contacts[i].contact_fax_ext !== 'undefined' && marker.contacts[i].contact_fax_ext !== null && marker.contacts[i].contact_fax_ext.length > 0)
                    {
                        output += ' <abbr title="Fax Extension">E:</abbr> ' + marker.contacts[i].contact_fax_ext + "<br />";
                    }
                    else
                    {
                        output += "<br />";
                    }
                }
        
                if (typeof marker.contacts[i].contact_email !== 'undefined' && marker.contacts[i].contact_email !== null && marker.contacts[i].contact_email.length > 0)
                {
                    output += '<abbr title="Email Address">M:</abbr> <a href="mailto:' + marker.contacts[i].contact_email + '">' + marker.contacts[i].contact_email + '</a><br />';
                }
                
                output += "</address>";
            }
            output += '</div></div></div>';
        }
        
        output += '</div>';
        return output;
    };
    
    locationSearch.addLocationListItem = function (marker, index) {
        // Start with the general arena info
        var $list = $("#locationList");
        
        var output = "<li data-marker-index='" + index + "'><h5><a href='" + marker.view_url + "'>" + marker.arena_name + "</a></h5><small>" + 
                "<strong>" + parseFloat(marker.distance).toFixed(2) + "</strong> miles</small><p>";
        
        if(this.geocodedAddr === '')
        {
            if (typeof marker.address_line2 !== 'undefined' && marker.address_line2 !== null && marker.address_line2.length > 0)
            {
                output += '<a target="_blank" href="http://maps.google.com/?q=' + escape(this.centerpoint.lat() + ',' + this.centerpoint.lng()) + 
                    "&daddr=" + escape(marker.address_line1 + ', ' + marker.address_line2 + ',' + marker.city_state_zip) + '&saddr=' + 
                    escape(this.centerpoint.lat() + ',' + this.centerpoint.lng()) + '">Driving Directions</a>';
            }
            else
            {
                output += '<a target="_blank" href="http://maps.google.com/?q=' + escape(this.centerpoint.lat() + ',' + this.centerpoint.lng()) + 
                    "&daddr=" + escape(marker.address_line1 + ', ' + marker.city_state_zip) + '&saddr=' + 
                    escape(this.centerpoint.lat() + ',' + this.centerpoint.lng()) + '">Driving Directions</a>';
            }
        }
        else
        {
            if (typeof marker.address_line2 !== 'undefined' && marker.address_line2 !== null && marker.address_line2.length > 0)
            {
                output += '<a target="_blank" href="http://maps.google.com/?q=' + escape(this.centerpoint.lat() + ',' + this.centerpoint.lng()) + 
                    "&daddr=" + escape(marker.address_line1 + ', ' + marker.address_line2 + ',' + marker.city_state_zip) + '&saddr=' + 
                    escape(this.geocodedAddr) + '">Driving Directions</a>';
            }
            else
            {
                output += '<a target="_blank" href="http://maps.google.com/?q=' + escape(this.centerpoint.lat() + ',' + this.centerpoint.lng()) + 
                    "&daddr=" + escape(marker.address_line1 + ', ' + marker.city_state_zip) + '&saddr=' + 
                    escape(this.geocodedAddr) + '">Driving Directions</a>';
            }            
        }
        
        output += "<address>";
        
        output += marker.address_line1 + "<br />";
        
        if (typeof marker.address_line2 !== 'undefined' && marker.address_line2 !== null && marker.address_line2.length > 0)
        {
            output += marker.address_line2 + "<br />";
        }
        
        output += marker.city_state_zip + "<br />";

        if (typeof marker.phone !== 'undefined' && marker.phone !== null && marker.phone.length > 0)
        {
            output += '<abbr title="Phone">P:</abbr> ' + marker.phone.replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
            
            if (typeof marker.ext !== 'undefined' && marker.ext !== null && marker.ext.length > 0)
            {
                output += ' <abbr title="Extension">E:</abbr> ' + marker.ext + "<br />";
            }
            else
            {
                output += "<br />";
            }
        }

        if (typeof marker.fax !== 'undefined' && marker.fax !== null && marker.fax.length > 0)
        {
            output += '<abbr title="Fax">F:</abbr> ' + marker.fax.replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
            
            if (typeof marker.fax_ext !== 'undefined' && marker.fax_ext !== null && marker.fax_ext.length > 0)
            {
                output += ' <abbr title="Fax Extension">E:</abbr> ' + marker.fax_ext + "<br />";
            }
            else
            {
                output += "<br />";
            }
        }
        
        if (typeof marker.home_url !== 'undefined' && marker.home_url !== null && marker.home_url.length > 0)
        {
            output += '<abbr title="Home Page">H:</abbr> <a target="_blank" href="' + marker.home_url + '">' + 'Home Page' + '</a><br />';
        }
        
        output += "</address>";
        
        // Now do the contacts!
        if (typeof marker.contacts !== 'undefined' && marker.contacts !== null && marker.contacts.length > 0)
        {
            output += "<h5>Contacts</h5>";
            
            for (var i = 0; i < marker.contacts.length; i++)
            {
                if (marker.contacts[i].contact_type === "Primary")
                {
                    output += "<address><abbr title='Primary Contact'>*:</abbr> <strong>" + marker.contacts[i].contact_name + "</strong><br />";
                }
                else
                {
                    output += "<address>" + marker.contacts[i].contact_name + "<br />";
                }
                
                if (typeof marker.contacts[i].contact_phone !== 'undefined' && marker.contacts[i].contact_phone !== null && marker.contacts[i].contact_phone.length > 0)
                {
                    output += '<abbr title="Phone">P:</abbr> ' + marker.contacts[i].contact_phone.replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
            
                    if (typeof marker.contacts[i].contact_ext !== 'undefined' && marker.contacts[i].contact_ext !== null && marker.contacts[i].contact_ext.length > 0)
                    {
                        output += ' <abbr title="Extension">E:</abbr> ' + marker.contacts[i].contact_ext + "<br />";
                    }
                    else
                    {
                        output += "<br />";
                    }
                }

                if (typeof marker.contacts[i].contact_fax !== 'undefined' && marker.contacts[i].contact_fax !== null && marker.contacts[i].contact_fax.length > 0)
                {
                    output += '<abbr title="Fax">F:</abbr> ' + marker.contacts[i].contact_fax.replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
                        
                    if (typeof marker.contacts[i].contact_fax_ext !== 'undefined' && marker.contacts[i].contact_fax_ext !== null && marker.contacts[i].contact_fax_ext.length > 0)
                    {
                        output += ' <abbr title="Fax Extension">E:</abbr> ' + marker.contacts[i].contact_fax_ext + "<br />";
                    }
                    else
                    {
                        output += "<br />";
                    }
                }
        
                if (typeof marker.contacts[i].contact_email !== 'undefined' && marker.contacts[i].contact_email !== null && marker.contacts[i].contact_email.length > 0)
                {
                    output += '<abbr title="Email Address">M:</abbr> <a href="mailto:' + marker.contacts[i].contact_email + '">' + marker.contacts[i].contact_email + '</a><br />';
                }
                
                output += "</address>";
            }
        }

        // Now do the events!
        if (typeof marker.events !== 'undefined' && marker.events !== null && marker.events.length > 0)
        {
            output += "<h5><a href='" + marker.events_url + "'>Event Calendar</a></h5>";
            
            for (var i = 0; i < marker.events.length; i++)
            {
                output += "<address><strong><a href='" + marker.events[i].event_view_url + "'>" + marker.events[i].event_type_name + "</a></strong><br />";
                
                output += "Available: " + marker.events[i].start_date_time + "<br />";
                output += "Total: " + marker.events[i].event_count + "<br /></address>";
            }
        }
        else if (typeof marker.events_url !== 'undefined' && marker.events_url !== null && marker.events_url.length > 0)
        {
            output += "<h5><a href='" + marker.events_url + "'>Event Calendar</a></h5>";
        }

        output += "</p></li>";
        
        $list.append(output);
    };
    
    locationSearch.addLocationSelectItem = function(marker, index) {
        var $list = $("#locationSelect");
        
        var output = "<option value='" + index +"'>" + marker.arena_name + " (" + 
                parseFloat(marker.distance).toFixed(2) + " mi)</option>";
        
        $list.append(output);
    };
    
    locationSearch.getFilterOptions = function () {
        // get all the inputs into an array.
        var $inputs = $('#searchForm :input');
        var that = this;

        // not sure if you wanted this, but I thought I'd add it.
        // get an associative array of just the values.
        var values = {};
        $inputs.each(function() {
            if (this.name !== "" && $(this).val() !== null && $(this).val() !== "")
            {
                if (typeof $(this).val() === "string")
                {
                    var value = $(this).val();
                    
                    if (typeof value.trim === "function")
                    {
                        value = value.trim();
                    }
                    
                    if (this.name === "start_date" || this.name === "end_date")
                    {
                        var myDate = moment(value, "MM/DD/YYYY");
                        
                        if (myDate.isValid() !== false)
                        {
                            values[this.name] = myDate.format("YYYY-MM-DD");
                        }
                    }
                    else if (this.name === "start_time" || this.name === "end_time")
                    {
                        var myDate = moment(value, "hh:mm A");
                        
                        if (myDate.isValid() !== false)
                        {
                            values[this.name] = myDate.format("HH:mm:ss");
                        }
                    }
                    else if (this.name === "radius")
                    {
                        var myInt = parseInt(value, 10);
                        
                        if (myInt !== "NaN")
                        {
                            values[this.name] = myInt;
                        }
                    }
                    else if (this.name === "price")
                    {
                        var myFloat = parseFloat(value, 10);
                        
                        if (myFloat !== "NaN")
                        {
                            values[this.name] = myFloat.toFixed(2);
                        }
                    }
                    else
                    {
                        values[this.name] = value;
                    }
                }
                else
                {
                    values[this.name] = $(this).val();
                }
            }
        });
        
        that.searchParams = values;
        that.searchParams.lat = (that.centerpoint) ? that.centerpoint.lat() : 0;
        that.searchParams.lng = (that.centerpoint) ? that.centerpoint.lng() : 0;

        return values !== {};
    };

    locationSearch.onReady = function () {
        var that = this;
        var $swell = $('#searchResultsWell');
        var $body = $('body');
        var $sresults = $('#searchResults');
        var $mapc = $('#map-canvas');
        
        $sresults.height($(window).height() * .90);
        $('#locationList').parent().height($(window).height() * .85);
        $mapc.parent().height($(window).height() * .85);
        
        if ($(window).width() <= 767)
        {
            if($swell.css('padding-top') !== '0px')
            {
                $swell.css('padding-top', '0px');
                $swell.css('padding-right', '0px');
                
                $swell.css('padding-bottom', '0px');
                $swell.css('padding-left', '0px');
            }
            
            if($body.css('padding-right') !== '0px')
            {
                $body.css('padding-left', '0px');
                $body.css('padding-right', '0px');
                $('.navbar-fixed-top').css('margin-left', '0px');
                $('.navbar-fixed-top').css('margin-right', '0px');
            }
            
            if($swell.css('magin-bottom') !== '0px')
            {
                $swell.css('margin-bottom', '0px');
            }
            
            $sresults.height($(window).height());
            
            $mapc.parent().height($sresults.height() - 52);
        }
        else
        {
            if($swell.css('padding-top') == '0px')
            {
                $swell.css('padding-top', '9px');
                $swell.css('padding-right', '9px');
                $swell.css('padding-bottom', '9px');
                $swell.css('padding-left', '9px');
            }
            
            if($body.css('padding-right') == '0px')
            {
                $body.css('padding-left', '20px');
                $body.css('padding-right', '20px');
            }
            
            if($swell.css('magin-bottom') == '0px')
            {
                $swell.css('margin-bottom', '20px');
            }
            
            $sresults.height($(window).height() * .90);
            $('#locationList').parent().height($(window).height() - 60);
            $mapc.parent().height($(window).height() - 60);
        }
        
        $(window).on('resize', function (e) {
            if ($(window).width() <= 767)
            {
                if($swell.css('padding-top') !== '0px')
                {
                    $swell.css('padding-top', '0px');
                    $swell.css('padding-right', '0px');
                    $swell.css('padding-bottom', '0px');
                    $swell.css('padding-left', '0px');
                }
                
                if($body.css('padding-right') !== '0px')
                {
                    $body.css('padding-left', '0px');
                    $body.css('padding-right', '0px');
                    $('.navbar-fixed-top').css('margin-left', '0px');
                    $('.navbar-fixed-top').css('margin-right', '0px');
                }
                                
                if($swell.css('magin-bottom') !== '0px')
                {
                    $swell.css('margin-bottom', '0px');
                }
                
                $sresults.height($(window).height());
                
                $mapc.parent().height($sresults.height() - 52);
            }
            else
            {
                if($swell.css('padding-top') == '0px')
                {
                    $swell.css('padding-top', '9px');
                    $swell.css('padding-right', '9px');
                    $swell.css('padding-bottom', '9px');
                    $swell.css('padding-left', '9px');
                }
                
                if($body.css('padding-right') == '0px')
                {
                    $body.css('padding-left', '20px');
                    $body.css('padding-right', '20px');
                }
                
                if($swell.css('magin-bottom') == '0px')
                {
                    $swell.css('margin-bottom', '20px');
                }
                
                $sresults.height($(window).height() * .90);
                $('#locationList').parent().height($(window).height() - 60);
                $mapc.parent().height($(window).height() - 60);
            }
        });
        
        $("#eventPrice").inputmask("decimal", {
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
        
        $.fn.datetimepicker.defaults = {
            maskInput: true,           // disables the text input mask
            pick12HourFormat: true,   // enables the 12-hour format time picker
            pickSeconds: false,         // disables seconds in the time picker
            startDate: moment().startOf('day').toDate(),      // set a minimum date
            endDate: moment().add('days', 365).endOf('day').toDate()  // set a maximum date
        };
        
        $('#searchDate').datetimepicker({
            pickDate: true,
            pickTime: false
        });

        $('#searchDateEnd').datetimepicker({
            pickDate: true,
            pickTime: false
        });
        
        $('#searchTime').datetimepicker({
            pickDate: false,
            pickTime: true,
            maskInput: true,
            pick12HourFormat: true,
            pickSeconds: false
        });
        
        $('#searchTimeEnd').datetimepicker({
            pickDate: false,
            pickTime: true,
            maskInput: true,
            pick12HourFormat: true,
            pickSeconds: false
        });
        
        $('[data-toggle="tooltip"]').tooltip();
        
        $('#map-canvas').on('destroyed', function () {
            // We have been closed, so clean everything up!!!
            locationSearch.clearLocations();
            locationSearch.map = null;
            locationSearch.infoWindow = null;
            locationSearch.centerpoint = null;
            
            $(".bootstrap-datetimepicker-widget").each(function () {
                $(this).remove();
            });
        });
        
        $('#searchFilterButton').on('click', function (e) {
            e.preventDefault();
            $('#searchFilterDiv').slideToggle(400);
        });
        
        $('#searchForm').on('submit', function (e) {
            e.preventDefault();
            
            if(that.isSearching === true)
            {
                return false;
            }
            
            $("input[name=submit]").val(true);
            
            that.hideButtons();

            that.searchLocations();            
        });
        
        var params = utilities.getUrlVars();
        
        this.initMap();

        this.getUserPosition();
    };
    
    locationSearch.hideButtons = function ()
    {
        var $bDiv = $("#searchButtons > button");
        var $sDiv = $("#searchButtons");
        
        var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
       
        $sDiv.append(spinner);
        $bDiv.filter(":visible").hide();        
    };
    
    locationSearch.showButtons = function ()
    {
        var $bDiv = $("#searchButtons > button");
        var $spinner = $("#searchButtons > #loading");
        
        $bDiv.filter(":hidden").show();
        $spinner.remove();
    };
    
}( window.locationSearch = window.locationSearch || {}, jQuery ));