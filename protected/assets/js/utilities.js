/**
 * utility functions packaged in a jQuery plugin
 *
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package www.js
 */

(function ( utilities, $, undefined ) {
    "use strict";
    // public properties
    utilities.urls = {
        base: "",
        login: "",
        logout: "",
    };
    
    utilities.ajaxError = {
        /*
         * @property string The ID of the bootstrap 2.3.2 dialog box container
         */
        dialogBox: "",
        
        show: function (label, message, xhr, status, errorThrown) {
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
        },
    };

    utilities.loadingScreen = {
        containerId: "",
        text: "Please wait...",
        
        image: {
            enabled: false,
            src: "",
        },
        
        progress: {
            enabled: false,
            type: "progress",
            percent: 0,
            step: 40,
        },
        
        show: function () {
            var htmlOutput = "";
            
            // Start with the text container
            if (this.text != "")
            {
                htmlOutput += '<p class="loading-stuff">' + this.text + "</p>";
            }
            
            if (this.image.enabled == true) {
                htmlOutput += '<div class="loading-stuff">' +
                        '<img src="' + utilities.urls.base +
                        this.image.src + '" alt="Loading..." /></div>';
            }
            
            if (this.progress.enabled == true) {
                htmlOutput += '<div class="' + this.progress.type + ' loading-stuff">';
                
                htmlOutput += '<div id="myProgress" class="bar" style="width: ' +
                        this.progress.percent + '%;"></div></div>';
            }
            
            $("#" + this.containerId).html(htmlOutput);

            if (!$("#" + this.containerId).is(":visible"))
            {
                $("#" + this.containerId).show();
            }
        },
        
        hide: function () {
            if ($("#" + this.containerId).is(":visible"))
            {
                $("#" + this.containerId).hide();
            }
            
            $("#" + this.containerId).html("");
        },
        
        stepUp: function() {
            this.stepUpBy(this.progress.step);
        },
        
        stepDown: function() {
            this.stepDownBy(this.progress.step);
        },
        
        stepUpBy: function(upBy) {
            if (this.progress.enabled == false)
            {
                return;
            }
            
            if (this.progress.percent + upBy >= 100)
            {
                this.progress.percent = 100;
            }
            else
            {
                this.progress.percent += upBy;
            }
            
            $("#myProgress").width(this.progress.percent + "%");
        },
        
        stepDownBy: function(downBy) {
            if (this.progress.enabled == false)
            {
                return;
            }
            
            if (this.progress.percent - downBy <= 0)
            {
                this.progress.percent = 0;
            }
            else
            {
                this.progress.percent -= downBy;
            }
            
            $("#myProgress").width(this.progress.percent + "%");
        },
        
    };
    
    utilities.arrayHasOwnIndex = function (array, prop) {
        return array.hasOwnProperty(prop) && /^0$|^[1-9]\d*$/.test(prop) && prop <= 4294967294; // 2^32 - 2
    };
    
}( window.utilities = window.utilities || {}, jQuery ));