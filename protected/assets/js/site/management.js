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
    
    management.urls = {
        base: "/server/url",
        login: "/server/url",
    };
    
    management.dialogBox = "";
    management.mainContainer = "";
    
    management.setLoadingScreen = function (elementID) {
        var strOutput = "<div id=\"loading\"><img src=\"" + this.urls.base +
                "/images/ajax-loader-roller-bg_red-fg_blue.gif\"" + 
                "alt=\"Loading...\" /><br />Please wait...</div>";
	$(elementID).html(strOutput);
	return strOutput;
    };
    
    management.resetLoadingScreen = function (elementID) {
        var strOutput = "";
	$(elementID).html(strOutput);
	return strOutput;
    };
    
    management.handleAjaxError = function (label, message, xhr, status, errorThrown) {
        var response = false;
                
        try
        {
            if (xhr && xhr.responseText)
            {
                response = JSON.parse(xhr.responseText);
            }
        }
        catch (err)
        {
            response = false;
        }
                
        $("#" + this.dialogBox + "Label").html(label);
                
        var htmlOutput = "<p class=\"text-error\"><strong>" + message + "</strong></p>";
                
        htmlOutput += "<h4>Web Server Response</h4>";
        htmlOutput += "<pre>Status: <strong>" + status + "</strong>\n";
        htmlOutput += "Message: <strong>" + errorThrown + "</strong>\n</pre>";
                
        if (response !== false)
        {
            htmlOutput += "<h4>Error Details</h4>";

            if(response.error == 'LOGIN_REQUIRED') {
                htmlOutput += "<pre>Error: <strong>" + "Session has expired. Please <a href='#' " +
                    "onClick='document.location.reload(true);return false;'>" +
                    "<i class='icon-user'></i> login</a> again.</strong>\n";
            } else {
                htmlOutput += "<pre>Error: <strong>" + response.error + "</strong>\n";
            }

            if (response.exception == true)
            {
                htmlOutput += "Exception Code: <strong>" + response.errorCode + "</strong>\n";
                htmlOutput += "Exception File: <strong>" + response.errorFile + "</strong>\n";
                htmlOutput += "Exception Line: <strong>" + response.errorLine + "</strong>\n";
                        
                if (response.errorInfo != null)
                {
                    htmlOutput += "</pre><h4>Database Server Response</h4>";
                    htmlOutput += "<pre>SQLSTATE Code: <strong>" + response.errorInfo.sqlState + "</strong>\n";
                    htmlOutput += "Driver Code: <strong>" + response.errorInfo.mysqlError + "</strong>\n";
                    htmlOutput += "Driver Message: <strong>" + response.errorInfo.message + "</strong>\n";
                }
            }

            htmlOutput += "</pre>";
        }
        else if (xhr && xhr.responseText)
        {
            htmlOutput += "<h4>Error Details</h4>";
            htmlOutput += "<pre>Error: <strong>" + xhr.responseText + "</pre></strong>";
        }
        
        $("#" + this.dialogBox + "Body").html(htmlOutput);
        $("#" + this.dialogBox).modal('show');
    };
    
    management.getInitialCounts = function () {
        var that = this;
        
        that.setLoadingScreen("#loadingScreen");
        
        $.ajax({                        
            url: that.endpoints.counts,
            type: "GET",
            dataType: "json",
            data: {
                for: ["arenas", "events", "requests", "reservations"],
            },
            success: function(result, status, xhr) {
                $("#" + that.mainContainer).show();
                
                that.resetLoadingScreen("#loadingScreen");
            },
            error: function(xhr, status, errorThrown) {
                that.handleAjaxError(
                        "Management Dashboard",
                        "Failed to retrieve dashboard counts",
                        xhr,
                        status,
                        errorThrown
                );
                $("#" + that.mainContainer).show();
                that.resetLoadingScreen("#loadingScreen");
            }
        });

        return true;
    };
    
}( window.management = window.management || {}, jQuery ));