/* 
 * This is the jQuery plugin for the uploadArenas action
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( uploadArenas, $, undefined ) {
    "use strict";
    // public properties
    uploadArenas.endpoints = {
        uploadFile: "/server/endpoint",
        deleteFile: "/server/endpoint",
        processFile: "/server/endpoint"
    };
    
    uploadArenas.uploadType = "";
    uploadArenas.userFullName = "";
    uploadArenas.fileUpload = {};
    uploadArenas.tableFields = [{}];
    uploadArenas.csvFields = [{}];
    uploadArenas.csvRows = [{}];
    uploadArenas.csvOptions = {};
    uploadArenas.mappings = [{}];
    uploadArenas.loginUrl = "";
    uploadArenas.baseUrl = "";
    uploadArenas.step = 1;
    
    // public methods
    uploadArenas.addUploadAndDeleteButtons = function () {
        $('<span>   </span><button id="uploadButton" class="btn btn-success ' +
          'btn-large disabled" name="yt0" type="button"><i class="icon-upload' +
          ' icon-white"></i> Begin Upload</button> ' +
          '<span>   </span><button id="deleteButton" style="display: none;" ' +
          'class="btn btn-danger btn-large disabled" name="yt1" type="button">' +
          '<i class="icon-trash icon-white"></i> Delete File</button>').insertAfter($(".qq-upload-button"));
    };
    
    uploadArenas.onUploadCancel = function (event, id, name) {
        $("#uploadButton").prop("disabled", true);
        $("#uploadButton").addClass("disabled");
        
        if ($("#deleteButton").css("display") !== "none") {
            $("#deleteButton").animate({opacity: 1.0}, 0).fadeOut("fast");
        }
        return true;
    };
    
    uploadArenas.onUploadComplete = function (event, id, name, response, xhr) {
        if (response.success !== true) {
            return false;
        }

        $("#arenaUploadStep1").hide();
        $("#step2Continue").prop("disabled", false);
        $("#step2Continue").removeClass("disabled");
        $("#arenaUploadStep2").show();

        this.uploadType = response.uploadType;
        this.userFullName = response.userFullName;
        this.fileUpload = response.fileUpload;
        this.endpoints.deleteFile = response.deleteFile.endpoint;
        this.endpoints.processFile = response.processFile.endpoint;
        $("#loadingScreen").html("");
        
        return true;
    };
    
    uploadArenas.onUploadError = function (event, id, name, errorReason, xhr) {
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
        
        $("#arenaModalLabel").html("Upload File");
        
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
                    uploadArenas.onDeleteButtonClick(response);
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
            $("#arenaModalBody").html(htmlOutput);
            $("#arenaModal").modal('show');
        }
        else if (xhr && xhr.responseText)
        {
            htmlOutput += "<h4>Error Details</h4>";
            htmlOutput += "<pre>Error: <strong>" + xhr.responseText + "</strong></pre>";
            $("#arenaModalBody").html(htmlOutput);
            $("#arenaModal").modal('show');
        } else {
            $("#arenaModalBody").html(htmlOutput);
            $("#arenaModal").modal('show');
        }
        
        $("#loadingScreen").html("");
        
        return true;
    };

    uploadArenas.onUploadSubmit = function (event, id, name) {
        if($("#uploadButton").length == 0) {
            this.addUploadAndDeleteButtons();
        }
        
        $("#uploadButton").prop("disabled", false);
        $("#uploadButton").removeClass("disabled");
        
        if ($("#uploadButton").css("display") === "none") {
            $("#uploadButton").animate({opacity: 1.0}, 0).fadeIn("fast");
        }
        return true;
    };
    
    uploadArenas.onUploadRetry = function (event, id, name) {
        uploadArenas.setLoadingScreen("#loadingScreen");
        
        return true;
    };
    
    uploadArenas.onUploadButtonClick = function () {
        uploadArenas.setLoadingScreen("#loadingScreen");
        $("#uploadButton").prop("disabled", true);
        $("#uploadButton").addClass("disabled");
        
        if ($("#deleteButton").css("display") !== "none") {
            $("#deleteButton").animate({opacity: 1.0}, 100).fadeOut("fast");
        }
        
        $("#ArenaUploadForm_fileName").fineUploader("uploadStoredFiles");

        return true;
    };
    
    uploadArenas.onDeleteButtonClick = function (response) {
        uploadArenas.setLoadingScreen("#loadingScreen");
        
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
                $("#arenaModalLabel").html("Delete Request");
                $("#arenaModalBody").html("<p>File has been deleted, please retry your upload.</p>");
                $("#arenaModal").modal('show');
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
                
                $("#arenaModalLabel").html("Delete Request");

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
                
                $("#arenaModalBody").html(htmlOutput);
                $("#arenaModal").modal('show');
                
                $("#loadingScreen").html("");
            }
        });
        return true;
    };
    
    uploadArenas.onContinueStep2ButtonClick = function () {
        this.csvOptions = {
            delimiter: $("#delimiter").val(),
            enclosure: $("#enclosure").val(),
            escapeChar: $("#escape-char").val(),
            skipRows: $("#header-row").val() - 1,
            updateExisting: $("#update-existing").is(':checked') ? 1 : 0
        };

        var that = this;
        
        uploadArenas.setLoadingScreen("#loadingScreen");
        
        $("#step2Continue").prop("disabled", true);
        $("#step2Continue").addClass("disabled");
        $("#arenaUploadStep2").hide();

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
                csvOptions: this.csvOptions
            },
            success: function(result, status, xhr) {
                that.csvFields = result.csvFields;
                that.csvRows = result.csvRows;
                that.tableFields = result.tableFields;
                that.setupMappingTable("#mappingTable");
                
                $("#step3Previous").prop("disabled", false);
                $("#step3Previous").removeClass("disabled");

                $("#arenaUploadStep3").show();
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
                
                $("#arenaModalLabel").html("Import Options");
                
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
                    htmlOutput += "<pre>Error: <strong>" + xhr.responseText + "</pre></strong>";
                }
                
                $("#arenaModalBody").html(htmlOutput);
                $("#arenaModal").modal('show');
                
                $("#step2Continue").prop("disabled", false);
                $("#step2Continue").removeClass("disabled");
                $("#arenaUploadStep2").show();
                $("#loadingScreen").html("");
            }
        });

        return true;
    };
    
    uploadArenas.onPreviousStep3ButtonClick = function () {
        var that = this;
        
        uploadArenas.setLoadingScreen("#loadingScreen");
        $("#step3Continue").prop("disabled", true);
        $("#step3Continue").addClass("disabled");
        $("#step3Previous").prop("disabled", true);
        $("#step3Previous").addClass("disabled");
        $("#arenaUploadStep3").hide();
        $("#mappingTable").html("");
        
        $("#step2Continue").prop("disabled", false);
        $("#step2Continue").removeClass("disabled");
        $("#arenaUploadStep2").show();
        $("#loadingScreen").html("");        
    };
    
    uploadArenas.onContinueStep3ButtonClick = function () {
        var that = this;
        
        uploadArenas.setLoadingScreen("#loadingScreen");
        uploadArenas.getMappings();
        
        $("#step3Continue").prop("disabled", true);
        $("#step3Continue").addClass("disabled");
        $("#arenaUploadStep3").hide();
        
        
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
                csvOptions: this.csvOptions,
                mappings: this.mappings
            },
            success: function(result, status, xhr) {
                $("#step4Continue").prop("disabled", false);
                $("#step4Continue").removeClass("disabled");
                that.showSummary(result.importSummary);
                $("#arenaUploadStep4").show();
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
                
                $("#arenaModalLabel").html("Import Data");
                
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
                
                $("#arenaModalBody").html(htmlOutput);
                $("#arenaModal").modal('show');
                
                $("#step2Continue").prop("disabled", false);
                $("#step2Continue").removeClass("disabled");
                $("#mappingTable").html("");
                $("#arenaUploadStep2").show();
                $("#loadingScreen").html("");
            }
        });

        return true;
    };
    
    uploadArenas.onContinueStep4ButtonClick = function () {
        $("#step4Continue").prop("disabled", true);
        $("#step4Continue").addClass("disabled");
        $("#ArenaUploadForm_fileName").fineUploader("clearStoredFiles");
        $("#mappingTable").html("");
        $("#arenaUploadStep4").hide();
        this.clearSummary();
        $("#arenaUploadStep1").show();
        return true;
    };
    
    uploadArenas.setLoadingScreen = function (elementID) {
        var strOutput = "<div id=\"loading\"><img src=\"" + this.baseUrl +
                "/images/spinners/ajax-loader-roller-bg_red-fg_blue.gif\"" + 
                "alt=\"Loading...\" /><br />Please wait...</div>";
	$(elementID).html(strOutput);
	return strOutput;
    };
    
    uploadArenas.setupMappingTable = function (elementID) {
        // Now we get to build our mapping table as we have all of the information
        // that we need about the CSV file and the database table.
        var that = this;
        // First add the table headers
        var strOutput = "<thead><tr><th>Table Column</th><th>Data File Column" +
                "</th><th data-hide='phone'>Data File Example</th></tr>" +
                "</thead><tbody></tbody><tfoot class='hide-if-no-paging'><tr>" +
                "<td colspan='3'><div class='pagination pagination-centered'>" +
                "</div></td></tr></tfoot>";
        
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
        
        $("#mappingTable").footable();
        
        this.checkStep3Button();
    };
    
    uploadArenas.checkStep3Button = function() {
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
    
    uploadArenas.getMappings = function() {
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
    
    uploadArenas.showSummary = function(importSummary) {
        $("#arenaSummaryUpdated").html("<strong>" + importSummary.totalUpdated + "</strong>");
        $("#arenaSummaryCreated").html("<strong>" + importSummary.totalInserted + "</strong>");
        $("#arenaSummaryTotal").html("<strong>" + importSummary.totalRecords + "</strong>");
        if (importSummary.autoTagged)
        {
            $("#arenaSummaryAutoTagged").addClass("text-success");
            $("#arenaSummaryAutoTagged").html("<strong>Yes</strong>");
        }
        else
        {
            $("#arenaSummaryAutoTagged").addClass("text-error");
            $("#arenaSummaryAutoTagged").html("<strong>No</strong>");
        }
    };
    
    uploadArenas.clearSummary = function() {
        $("#arenaSummaryUpdated").html("");
        $("#arenaSummaryCreated").html("");
        $("#arenaSummaryTotal").html("");
        $("#arenaSummaryAutoTagged").html("");
        $("#arenaSummaryAutoTagged").removeClass("text-success");
        $("#arenaSummaryAutoTagged").removeClass("text-error");
    };
    
    uploadArenas.reset = function() {
        // Reset the steps 1 at a time!
        $("#step4Continue").prop("disabled", true);
        $("#step4Continue").addClass("disabled");
        $("#arenaUploadStep4").hide();
        this.clearSummary();
        
        $("#step3Previous").prop("disabled", true);
        $("#step3Previous").addClass("disabled");
        $("#step3Continue").prop("disabled", true);
        $("#step3Continue").addClass("disabled");
        $("#arenaUploadStep3").hide();
        $("#mappingTable").html("");
        
        $("#step2Continue").prop("disabled", true);
        $("#step2Continue").addClass("disabled");
        $("#arenaUploadStep2").hide();
        
        $("#ArenaUploadForm_fileName").fineUploader("clearStoredFiles");
        $("#uploadButton").prop("disabled", true);
        $("#uploadButton").addClass("disabled");
        $("#deleteButton").prop("disabled", true);
        $("#deleteButton").addClass("disabled");
        $("#arenaUploadStep1").show();
    };
    
    uploadArenas.onResetButtonClick = function () {
        this.reset();
        $("#arenaModal").modal('hide');
        return true;
    };
    
}( window.uploadArenas = window.uploadArenas || {}, jQuery ));