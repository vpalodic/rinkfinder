/* 
 * This is the jQuery plugin for the management action
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( management, $, undefined ) {
    "use strict";
    // public properties
    management.endpoints = {
        counts: "/server/endpoint",
        details: "/server/endpoint",
    };
    
    management.dialogBox = "";
    management.mainContainer = "";
    
    management.processArenaCounts = function (name, data) {
        // First the header and then the well body
        var header = $("#" + name + "Header");
        var well = $("#" + name + "Well");
        
        var htmlOutput = '<span class="badge badge-important">' + 
                data.total + '</span> Arenas <button id="refreshArenaCounts" ' +
                'class="btn btn-success btn-small pull-right">' +
                '<i class="icon-refresh icon-white"></i></button>';
        
        header.html(htmlOutput);
        
        // Now the well data!
        // We will use lists!
        var i = 0;
        var objStatus = null;
        var odd = true;
        var className = "odd";

        htmlOutput = '<ul id="' + name + 'UnorderedList" class="unstyled">'

        for (i = 0; i < data.status.length; i++) {
            objStatus = data.status[i];
            
            htmlOutput += '<li data-name="' + objStatus.name + '" data-id=' +
                    objStatus.id + ' data-count=' + objStatus.count +
                    ' data-display_name="' + objStatus.display_name + '" ' +
                    'data-display_order=' + objStatus.display_order +
                    'rel="tooltip" title="' + objStatus.description + '" ' +
                    'style="word-break:break-all;word-wrap:break-word;">' +
                    ' <span class="badge badge-info">' + 
                    objStatus.count + '</span> <strong>' + objStatus.display_name + '</strong></li>';
        }
        
        htmlOutput += "</ul>";
        
        well.html(htmlOutput);
    };
    
    management.processEventCounts = function (name, data) {
        // First the header and then the well body
        var header = $("#" + name + "Header");
        var well = $("#" + name + "Well");
        
        var htmlOutput = '<span class="badge badge-important">' + 
                data.total + '</span> Events <button id="refreshEventCounts" ' +
                'class="btn btn-success btn-small pull-right">' +
                '<i class="icon-refresh icon-white"></i></button>';
        
        header.html(htmlOutput);
        
        // Now the well data!
        // We will use lists!
        var i = 0;
        var j = 0;
        var objType = null;
        var objStatus = null;
        var odd = true;
        var className = "odd";

        htmlOutput = '<ul id="' + name + 'UnorderedList" class="unstyled inline">'

        for (i = 0; i < data.type.length; i++) {
            objType = data.type[i];
            
            htmlOutput += '<li data-name="' + objType.name + '" data-id=' +
                    objType.id + ' data-count=' + objType.count +
                    ' data-display_name="' + objType.display_name + '" ' +
                    'data-display_order=' + objType.display_order +
                    'rel="tooltip" title="' + objType.description + '" ' +
                    'style="word-break:break-all;word-wrap:break-word;">' +
                    ' <span class="badge badge-info">' + objType.count + 
                    '</span> <strong>' + objType.display_name + '</strong>' +
                    '<ul>';
            
            for (j = 0; j < objType.status.length; j++) {
                objStatus = objType.status[j];
            
                htmlOutput += '<li data-name="' + objStatus.name + '" data-id=' +
                        objStatus.id + ' data-count=' + objStatus.count +
                        ' data-display_name="' + objStatus.display_name + '" ' +
                        'data-display_order=' + objStatus.display_order +
                        'rel="tooltip" title="' + objStatus.description + '" ' +
                        'style="word-break:break-all;word-wrap:break-word;">' +
                        ' <span class="badge">' + 
                        objStatus.count + '</span> <strong>' + objStatus.display_name + '</strong></li>';
            }
            htmlOutput += "</ul></li>";
        }
        
        htmlOutput += "</ul>";
        
        well.html(htmlOutput);
    };
    
    management.processRequestCounts = function (name, data) {
        // First the header and then the well body
        var header = $("#" + name + "Header");
        var well = $("#" + name + "Well");
        
        var htmlOutput = '<span class="badge badge-important">' + 
                data.total + '</span> Requests <button id="refreshRequestCounts" ' +
                'class="btn btn-success btn-small pull-right">' +
                '<i class="icon-refresh icon-white"></i></button>';
        
        header.html(htmlOutput);
        
        // Now the well data!
        // We will use lists!
        var i = 0;
        var j = 0;
        var objType = null;
        var objStatus = null;
        var odd = true;
        var className = "odd";

        htmlOutput = '<ul id="' + name + 'UnorderedList" class="unstyled inline">'

        for (i = 0; i < data.type.length; i++) {
            objType = data.type[i];
            
            htmlOutput += '<li data-name="' + objType.name + '" data-id=' +
                    objType.id + ' data-count=' + objType.count +
                    ' data-display_name="' + objType.display_name + '" ' +
                    'data-display_order=' + objType.display_order +
                    'rel="tooltip" title="' + objType.description + '" ' +
                    'style="word-break:break-all;word-wrap:break-word;">' +
                    ' <span class="badge badge-info">' + objType.count + 
                    '</span> <strong>' + objType.display_name + '</strong>' +
                    '<ul>';
            
            for (j = 0; j < objType.status.length; j++) {
                objStatus = objType.status[j];
            
                htmlOutput += '<li data-name="' + objStatus.name + '" data-id=' +
                        objStatus.id + ' data-count=' + objStatus.count +
                        ' data-display_name="' + objStatus.display_name + '" ' +
                        'data-display_order=' + objStatus.display_order +
                        'rel="tooltip" title="' + objStatus.description + '" ' +
                        'style="word-break:break-all;word-wrap:break-word;">' +
                        ' <span class="badge">' + 
                        objStatus.count + '</span> <strong>' + objStatus.display_name + '</strong></li>';
            }
            htmlOutput += "</ul></li>";
        }
        
        htmlOutput += "</ul>";
        
        well.html(htmlOutput);
    };
    
    management.processReservationCounts = function (name, data) {
        // First the header and then the well body
        var header = $("#" + name + "Header");
        var well = $("#" + name + "Well");
        
        var htmlOutput = '<span class="badge badge-important">' + 
                data.total + '</span> Reservations <button id="refreshReservationCounts" ' +
                'class="btn btn-success btn-small pull-right">' +
                '<i class="icon-refresh icon-white"></i></button>';
        
        header.html(htmlOutput);
        
        // Now the well data!
        // We will use lists!
        var i = 0;
        var objStatus = null;
        var odd = true;
        var className = "odd";

        htmlOutput = '<ul id="' + name + 'UnorderedList" class="unstyled">'

        for (i = 0; i < data.status.length; i++) {
            objStatus = data.status[i];
            
            htmlOutput += '<li data-name="' + objStatus.name + '" data-id=' +
                    objStatus.id + ' data-count=' + objStatus.count +
                    ' data-display_name="' + objStatus.display_name + '" ' +
                    'data-display_order=' + objStatus.display_order +
                    'rel="tooltip" title="' + objStatus.description + '" ' +
                    'style="word-break:break-all;word-wrap:break-word;">' +
                    ' <span class="badge badge-info">' + 
                    objStatus.count + '</span> <strong>' + objStatus.display_name + '</strong></li>';
        }
        
        htmlOutput += "</ul>";
        
        well.html(htmlOutput);
    };
    
    management.processCounts = function (result, status, xhr) {
        if (status != "success" || result.success != true)
        {
            return false;
        }
        
        var countSection;
        var countSections = result.for;
        
        for (countSection in countSections)
        {
            switch (countSection)
            {
                case "arenas":
                    this.processArenaCounts(countSection, countSections.arenas);
                    break;
                case "events":
                    this.processEventCounts(countSection, countSections.events);
                    break;
                case "requests":
                    this.processRequestCounts(countSection, countSections.requests);
                    break;
                case "reservations":
                    this.processReservationCounts(countSection, countSections.reservations);
                    break;
            }
        }
    };
    
    management.getInitialCounts = function () {
        var that = this;
        
        utilities.loadingScreen.show();

        $.ajax({                        
            url: that.endpoints.counts,
            type: "GET",
            dataType: "json",
            data: {
                for: ["arenas", "events", "requests", "reservations"],
            },
            success: function(result, status, xhr) {
                that.processCounts(result, status, xhr);
                
                $("#" + that.mainContainer).show();
                
                utilities.loadingScreen.hide();
            },
            error: function(xhr, status, errorThrown) {
                utilities.ajaxError.show(
                        "Management Dashboard",
                        "Failed to retrieve dashboard counts",
                        xhr,
                        status,
                        errorThrown
                        );
                $("#" + that.mainContainer).show();
                utilities.loadingScreen.hide();
            }
        });

        return true;
    };
    
}( window.management = window.management || {}, jQuery ));