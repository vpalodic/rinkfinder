/* 
 * This is the jQuery plugin for the eventSearch action
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( eventSearch, $, undefined ) {
    "use strict";
    // public properties
    eventSearch.endpoints = {
        events: ''
    };
    
    eventSearch.useGeoLocation = false;
    eventSearch.position = null;
    eventSearch.centerpoint = null;
    eventSearch.geocodedAddr = "";
    eventSearch.isSearching = false;
    eventSearch.isGeolocating = false;
    eventSearch.searchParams = {};
    eventSearch.$search = null;
    
    eventSearch.getUserPosition = function (search) {
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
    
    eventSearch.searchLocations = function () {
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
    
    eventSearch.searchLocationsNear = function () {
        if (this.isSearching === true)
        {
            return;
        }
        
        var that = this;
        
        var doSearch = this.getFilterOptions();

        if (doSearch === false) {
            return;
        }
        
        this.isSearching = true;
        
        $.ajax({
            url: this.endpoints.events,
            type: "GET",
            dataType: "html",
            data: that.searchParams,
            success: function (result, status, xhr) {
                // Now we have the results so show them!!
                var $data = $(result);
                $data.hide();
                var $parent = that.$search.parent();
                
                that.$search.fadeOut(333, function () {
                    that.$search.remove();
                    $parent.append($data);
                    $data.fadeIn(666, function() {
                        that.$search = $data;
                    });
                });
                
                that.isSearching = false;
                that.showButtons();
                
                if ($(window).width() > 767)
                {
                    $parent.ScrollTo({
                        offsetTop: 60
                    });
                }
                else
                {
                    $parent.ScrollTo();
                }
            },
            error: function(xhr, status, errorThrown) {
                window.setTimeout(function () {
                    that.isSearching = false;
                    that.showButtons();
                    
                    utilities.ajaxError.show(
                            "Event Search",
                            "Failed to retrieve data",
                            xhr,
                            status,
                            errorThrown
                    );
                }, 1000);
            }
        });
    };

    eventSearch.getFilterOptions = function () {
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
                    else if (this.name === "limit")
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
        that.searchParams.lat = that.centerpoint.lat();
        that.searchParams.lng = that.centerpoint.lng();

        return values !== {};
    };

    eventSearch.onReady = function () {
        var that = this;
        
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
            
            that.hideButtons();

            that.searchLocations();            
        });
        
        this.getUserPosition();
    };
    
    eventSearch.hideButtons = function ()
    {
        var $bDiv = $("#searchButtons > button");
        var $sDiv = $("#searchButtons");
        
        var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
       
        $sDiv.append(spinner);
        $bDiv.filter(":visible").hide();        
    };
    
    eventSearch.showButtons = function ()
    {
        var $bDiv = $("#searchButtons > button");
        var $spinner = $("#searchButtons > #loading");
        
        $bDiv.filter(":hidden").show();
        $spinner.remove();
    };
    
}( window.eventSearch = window.eventSearch || {}, jQuery ));