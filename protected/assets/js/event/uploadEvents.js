/* 
 * This is the jQuery plugin for the uploadEvents action
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( uploadEvents, $, undefined ) {
    "use strict";
    // public properties
    uploadEvents.endpoints = {
        uploadFile: "/server/endpoint",
        deleteFile: "/server/endpoint",
        processFile: "/server/endpoint"
    };
    
    uploadEvents.uploadType = "";
    uploadEvents.userFullName = "";
    uploadEvents.fileUpload = {};
    uploadEvents.tableFields = [{}];
    uploadEvents.csvFields = [{}];
    uploadEvents.csvRows = [{}];
    uploadEvents.csvOptions = {};
    uploadEvents.mappings = [{}];
    uploadEvents.loginUrl = "";
    uploadEvents.baseUrl = "";
    uploadEvents.arenaId = 0;
    uploadEvents.step = 0;
    uploadEvents.eventType = 0;
    
    // public methods
    uploadEvents.addUploadAndDeleteButtons = function () {
        $('<span>   </span><button id="uploadButton" class="btn btn-success ' +
          'btn-large disabled" name="yt0" type="button"><i class="icon-upload' +
          ' icon-white"></i> Begin Upload</button> ' +
          '<span>   </span><button id="deleteButton" style="display: none;" ' +
          'class="btn btn-danger btn-large disabled" name="yt1" type="button">' +
          '<i class="icon-trash icon-white"></i> Delete File</button>').insertAfter($(".qq-upload-button"));
    };
    
    uploadEvents.onUploadCancel = function (event, id, name) {
        $("#uploadButton").prop("disabled", true);
        $("#uploadButton").addClass("disabled");
        
        if ($("#deleteButton").css("display") !== "none") {
            $("#deleteButton").animate({opacity: 1.0}, 0).fadeOut("fast");
        }
        return true;
    };
    
    uploadEvents.onUploadComplete = function (event, id, name, response, xhr) {
        if (response.success !== true) {
            return false;
        }
        
        this.goStep2(response);
        
        return true;
    };
    
    uploadEvents.onUploadError = function (event, id, name, errorReason, xhr) {
        if (id === null || xhr === null) {
            return false;
        }
        
        $("#uploadButton").prop("disabled", true);
        $("#uploadButton").addClass("disabled");
        
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
        
        $("#eventModalLabel").html("Upload File");
        
        var htmlOutput = "<p class=\"text-error\"><strong>Failed to upload the data file.</strong></p>";
        
        htmlOutput += "<h4>Web Server Response</h4>";
        htmlOutput += "<pre>Message: <strong>" + errorReason + "</strong>\n</pre>";

        if (response !== false && response.existingFile) {
            if($("#deleteButton").css("display") === "none") {
                $("#deleteButton").prop("disabled", false);
                $("#deleteButton").removeClass("disabled");
                $("#deleteButton").animate({opacity: 1.0}, 0).fadeIn("fast");
                
                // register a click handler on the delete button!
                $("#deleteButton").on("click", function () {
                    uploadEvents.onDeleteButtonClick(response);
                });
            }
        }
        else if (response !== false)
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
            $("#eventModalBody").html(htmlOutput);
            $("#eventModal").modal('show');
        }
        else if (xhr && xhr.responseText)
        {
            htmlOutput += "<h4>Error Details</h4>";
            htmlOutput += "<pre>Error: <strong>" + xhr.responseText + "</strong></pre>";
            $("#eventModalBody").html(htmlOutput);
            $("#eventModal").modal('show');
        } else {
            $("#eventModalBody").html(htmlOutput);
            $("#eventModal").modal('show');
        }
        
        $("#loadingScreen").html("");
        
        return true;
    };

    uploadEvents.onUploadSubmit = function (event, id, name) {
        $("#uploadButton").prop("disabled", false);
        $("#uploadButton").removeClass("disabled");
        
        if ($("#uploadButton").css("display") === "none") {
            $("#uploadButton").animate({opacity: 1.0}, 0).fadeIn("fast");
        }
        return true;
    };
    
    uploadEvents.onUploadRetry = function (event, id, name) {
        uploadEvents.setLoadingScreen("#loadingScreen");
        
        return true;
    };
    
    uploadEvents.onUploadButtonClick = function () {
        uploadEvents.setLoadingScreen("#loadingScreen");
        $("#uploadButton").prop("disabled", true);
        $("#uploadButton").addClass("disabled");
        
        if ($("#deleteButton").css("display") !== "none") {
            $("#deleteButton").animate({opacity: 1.0}, 100).fadeOut("fast");
        }
        
        $("#EventUploadForm_fileName").fineUploader("uploadStoredFiles");

        return true;
    };
    
    uploadEvents.onDeleteButtonClick = function (response) {
        uploadEvents.setLoadingScreen("#loadingScreen");
        
        $.ajax({                        
            url: response.deleteFile.endpoint,
            type: "DELETE",
            dataType: "json",
            data: { fid: response.fileUpload.id, name: response.fileUpload.name },
            success: function(result, status, xhr) {
                $("#deleteButton").off("click");
                $("#deleteButton").prop("disabled", true);
                $("#deleteButton").addClass("disabled");
                $("#deleteButton").animate({opacity: 1.0}, 0).fadeOut("fast");
                $("#eventModalLabel").html("Delete Request");
                $("#eventModalBody").html("<p>File has been deleted, please retry your upload.</p>");
                $("#eventModal").modal('show');
                $("#loadingScreen").html("");
            },
            error: function(xhr, status, errorThrown) {
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
                
                $("#eventModalLabel").html("Delete Request");

                var htmlOutput = "<p class=\"text-error\"><strong>Failed to delete the file.</strong></p>";
                
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
                    htmlOutput += "<pre>Error: <strong>" + xhr.responseText + "</strong></pre>";
                }
                
                $("#eventModalBody").html(htmlOutput);
                $("#eventModal").modal('show');
                
                $("#loadingScreen").html("");
            }
        });
        return true;
    };
    
    uploadEvents.goStep2 = function(param) {
        this.step = 2;
        
        $("#uploadEventsStep1").hide();
        $("#step2Continue").prop("disabled", false);
        $("#step2Continue").removeClass("disabled");
        $("#uploadEventsStep2").show();

        this.uploadType = param.uploadType;
        this.userFullName = param.userFullName;
        this.fileUpload = param.fileUpload;
        this.arenaId = param.fileUpload.arena_id;
        this.endpoints.deleteFile = param.deleteFile.endpoint;
        this.endpoints.processFile = param.processFile.endpoint;
        
        $("#loadingScreen").html("");        
    };
    
    uploadEvents.goStep3 = function(param) {
        this.step = 3;
        
        this.csvFields = param.csvFields;
        this.csvRows = param.csvRows;
        this.tableFields = param.tableFields;
        this.setupMappingTable("#mappingTable");
                
        $("#step3Previous").prop("disabled", false);
        $("#step3Previous").removeClass("disabled");

        $("#uploadEventsStep3").show();
        $("#loadingScreen").html("");
    };
    
    uploadEvents.onContinueStep2ButtonClick = function () {
        this.csvOptions = {
            delimiter: $("#delimiter").val(),
            enclosure: $("#enclosure").val(),
            escapeChar: $("#escape-char").val(),
            skipRows: $("#header-row").val() - 1,
            updateExisting: $("#update-existing").is(':checked') ? 1 : 0
        };

        this.eventType = $("#eventType").val();
        
        var that = this;
        
        uploadEvents.setLoadingScreen("#loadingScreen");
        
        $("#step2Continue").prop("disabled", true);
        $("#step2Continue").addClass("disabled");
        $("#uploadEventsStep2").hide();

        $.ajax({                        
            url: this.endpoints.processFile,
            type: "GET",
            dataType: "json",
            data: {
                step: 2,
                fileUpload: {
                    id: this.fileUpload.id,
                    name: this.fileUpload.name,
                    upload_type_id: this.fileUpload.upload_type_id
                },
                arenaId: this.fileUpload.arena_id,
                eventType: this.eventType,
                csvOptions: this.csvOptions
            },
            success: function(result, status, xhr) {
                that.goStep3(result);
                return true;
            },
            error: function(xhr, status, errorThrown) {
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
                
                $("#eventModalLabel").html("Import Options");
                
                var htmlOutput = "<p class=\"text-error\"><strong>Failed to apply Import Options.</strong></p>";
                
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
                    htmlOutput += "<pre>Error: <strong>" + xhr.responseText + "</strong></pre>";
                }
                
                $("#eventModalBody").html(htmlOutput);
                $("#eventModal").modal('show');
                
                $("#step2Continue").prop("disabled", false);
                $("#step2Continue").removeClass("disabled");
                $("#uploadEventsStep2").show();
                $("#loadingScreen").html("");
            }
        });

        return true;
    };
    
    uploadEvents.onPreviousStep3ButtonClick = function () {
        this.step = 2;
        
        uploadEvents.setLoadingScreen("#loadingScreen");
        $("#step3Continue").prop("disabled", true);
        $("#step3Continue").addClass("disabled");
        $("#step3Previous").prop("disabled", true);
        $("#step3Previous").addClass("disabled");
        $("#uploadEventsStep3").hide();
        $("#mappingTable").html("");
        
        $("#step2Continue").prop("disabled", false);
        $("#step2Continue").removeClass("disabled");
        $("#uploadEventsStep2").show();
        $("#loadingScreen").html("");        
    };
    
    uploadEvents.onContinueStep3ButtonClick = function () {
        var that = this;
        
        uploadEvents.setLoadingScreen("#loadingScreen");
        uploadEvents.getMappings();
        
        $("#step3Continue").prop("disabled", true);
        $("#step3Continue").addClass("disabled");
        $("#uploadEventsStep3").hide();
        
        
        $.ajax({                        
            url: this.endpoints.processFile + "?step=3",
            type: "POST",
            dataType: "json",
            data: {
                step: 3,
                fileUpload: {
                    id: this.fileUpload.id,
                    name: this.fileUpload.name,
                    upload_type_id: this.fileUpload.upload_type_id
                },
                arenaId: this.fileUpload.arena_id,
                eventType: this.eventType,
                csvOptions: this.csvOptions,
                mappings: this.mappings
            },
            success: function(result, status, xhr) {
                $("#step4Continue").prop("disabled", false);
                $("#step4Continue").removeClass("disabled");
                that.showSummary(result.importSummary);
                $("#uploadEventsStep4").show();
                $("#loadingScreen").html("");
            },
            error: function(xhr, status, errorThrown) {
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
                
                $("#eventModalLabel").html("Import Data");
                
                var htmlOutput = "<p class=\"text-error\"><strong>Failed to import the the data file.</strong></p>";
                
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
                    htmlOutput += "<pre>Error: <strong>" + xhr.responseText + "</strong></pre>";
                }
                
                $("#eventModalBody").html(htmlOutput);
                $("#eventModal").modal('show');
                
                $("#step2Continue").prop("disabled", false);
                $("#step2Continue").removeClass("disabled");
                $("#mappingTable").html("");
                $("#uploadEventsStep2").show();
                $("#loadingScreen").html("");
            }
        });

        return true;
    };
    
    uploadEvents.onContinueStep4ButtonClick = function () {
        $("#step4Continue").prop("disabled", true);
        $("#step4Continue").addClass("disabled");
        $("#ArenaUploadForm_fileName").fineUploader("clearStoredFiles");
        $("#mappingTable").html("");
        $("#uploadEventsStep4").hide();
        this.clearSummary();
        $("#uploadEventsStep1").show();
        return true;
    };
    
    uploadEvents.setLoadingScreen = function (elementID) {
        var strOutput = "<div id=\"loading\"><img src=\"" + this.baseUrl +
                "/images/ajax-loader-roller-bg_red-fg_blue.gif\"" + 
                "alt=\"Loading...\" /><br />Please wait...</div>";
	$(elementID).html(strOutput);
	return strOutput;
    };
    
    uploadEvents.setupMappingTable = function (elementID) {
        // Now we get to build our mapping table as we have all of the information
        // that we need about the CSV file and the database table.
        var that = this;
        // First add the table headers
        var strOutput = "<thead><tr><th>Table Column</th><th>Data File Column</th>" +
                "<th>Data File Example</th></tr></thead><tbody></tbody>";
        
        $(elementID).append(strOutput);
        
        var tableBody = $(elementID).find("tbody");
        var odd = true;
        
        for (var i = 0; i < this.tableFields.length; i++) {
            if(odd === true) {
                strOutput = '<tr class="odd" rel="tooltip" title="' + this.tableFields[i].tooltip + '">';
                odd = false;
            } else {
                strOutput = '<tr class="even" rel="tooltip" title="' + this.tableFields[i].tooltip + '">';
                odd = true;
            }
            
            // Add the table field, we will use the
            strOutput += '<td id="' + this.tableFields[i].name + '">';
            strOutput += this.tableFields[i].name + " ";
            
            if(this.tableFields[i].required === true) {
                strOutput += '<span class="required">*</span>';
            }
            
            strOutput += "</td>";
            
            // Build the select list!!
            strOutput += '<td id="' + this.tableFields[i].name + 'SelectField">';
            strOutput += '<select class="span5" id="' + this.tableFields[i].name + 'SelectList" data-index="' + i + '">';
            strOutput += '<option value="Not Mapped">Not Mapped</option>';
            
            // Loop through all of the CSV fields
            var match = false;
            
            for (var j = 0; j < this.csvFields.length; j++) {
                strOutput += '<option value="' + this.csvFields[j] + '"';
                
                if(this.csvFields[j] === this.tableFields[i].name) {
                    strOutput += ' selected>';
                    match = true;
                } else {
                    strOutput += '>';
                }
                
                strOutput += this.csvFields[j] + '</option>';
            }
            
            strOutput += '</select>';
            strOutput += '</td>';
            
            // Build the preview field
            strOutput += '<td id="' + this.tableFields[i].name + 'PreviewField">';
            
            if(match === true) {
                strOutput += (this.csvRows[0])[this.tableFields[i].name];
            }
            strOutput += '</td>';
            
            strOutput += "</tr>";
            
            tableBody.append(strOutput);
            
            // Setup the onChange handler for the select lists!
            $('#' + this.tableFields[i].name + 'SelectList').on("change", function () {
                if (this.value === "Not Mapped") {
                    $('#' + that.tableFields[$(this).data('index')].name + 'PreviewField').html("");
                } else {
                    $('#' + that.tableFields[$(this).data('index')].name + 'PreviewField').html((that.csvRows[0])[this.value]);
                }
                
                that.checkStep3Button();
            });
        }
        
        this.checkStep3Button();
    };
    
    uploadEvents.checkStep3Button = function() {
        // If all of the required fields are mapped
        // Then we can enable the button if it is disabled.
        // If they are not all mapped, then we disable the
        // button if it is enabled.
        
        var isDisabled = $("#step3Continue").prop("disabled");
        
        if(typeof isDisabled === "undefined") {
            isDisabled = false;
        }
        
        // Let's go through each table item
        var canEnable = true;
        
        for (var i = 0; i < this.tableFields.length; i++) {
            // We are only interested in required items!!!
            if (this.tableFields[i].required === true) {
                // If we find at least one required item
                // that hasn't been mapped, we set canEnable to false
                if ($("#" + this.tableFields[i].name + "SelectList").val() === "Not Mapped") {
                    canEnable = false;
                }
            }
        }
        
        if (isDisabled === false && canEnable === false) {
            // We need to disable the button
            $("#step3Continue").prop("disabled", true);
            $("#step3Continue").addClass("disabled");
        } else if(isDisabled === true && canEnable === true) {
            // We need to enable the button
            $("#step3Continue").prop("disabled", false);
            $("#step3Continue").removeClass("disabled");
        }
    };
    
    uploadEvents.getMappings = function() {
        // Build the mappings object!!!
        
        // Let's go through each table item
        this.mappings = new Array();
        
        for (var i = 0; i < this.tableFields.length; i++) {
            if ($("#" + this.tableFields[i].name + "SelectList").val() !== "Not Mapped") {
                this.mappings.push({
                    fieldName: this.tableFields[i].name,
                    headerName: $("#" + this.tableFields[i].name + "SelectList").val(),
                    fieldType: this.tableFields[i].type,
                    fieldSize: this.tableFields[i].size,
                    fieldRequired: this.tableFields[i].required
                });
            }
        }
    };
    
    uploadEvents.showSummary = function(importSummary) {
        $("#uploadEventsSummaryUpdated").html("<strong>" + importSummary.totalUpdated + "</strong>");
        $("#uploadEventsSummaryCreated").html("<strong>" + importSummary.totalInserted + "</strong>");
        $("#uploadEventsSummaryTotal").html("<strong>" + importSummary.totalRecords + "</strong>");
        if (importSummary.autoTagged)
        {
            $("#uploadEventsSummaryAutoTagged").addClass("text-success");
            $("#uploadEventsSummaryAutoTagged").html("<strong>Yes</strong>");
        }
        else
        {
            $("#uploadEventsSummaryAutoTagged").addClass("text-error");
            $("#uploadEventsSummaryAutoTagged").html("<strong>No</strong>");
        }
    };
    
    uploadEvents.clearSummary = function() {
        $("#uploadEventsSummaryUpdated").html("");
        $("#uploadEventsSummaryCreated").html("");
        $("#uploadEventsSummaryTotal").html("");
        $("#uploadEventsSummaryAutoTagged").html("");
        $("#uploadEventsSummaryAutoTagged").removeClass("text-success");
        $("#uploadEventsSummaryAutoTagged").removeClass("text-error");
    };
    
    uploadEvents.reset = function() {
        // Reset the steps 1 at a time!
        $("#step4Continue").prop("disabled", true);
        $("#step4Continue").addClass("disabled");
        $("#uploadEventsStep4").hide();
        this.clearSummary();
        
        $("#step3Previous").prop("disabled", true);
        $("#step3Previous").addClass("disabled");
        $("#step3Continue").prop("disabled", true);
        $("#step3Continue").addClass("disabled");
        $("#uploadEventsStep3").hide();
        $("#mappingTable").html("");
        
        $("#step2Continue").prop("disabled", true);
        $("#step2Continue").addClass("disabled");
        $("#uploadEventsStep2").hide();
        
        $("#EventUploadForm_fileName").fineUploader("clearStoredFiles");
        $("#uploadButton").prop("disabled", true);
        $("#uploadButton").addClass("disabled");
        $("#deleteButton").prop("disabled", true);
        $("#deleteButton").addClass("disabled");
        $("#uploadEventsStep1").show();
    };
    
    uploadEvents.onResetButtonClick = function () {
        this.reset();
        $("#eventModal").modal('hide');
        return true;
    };
    
}( window.uploadEvents = window.uploadEvents || {}, jQuery ));