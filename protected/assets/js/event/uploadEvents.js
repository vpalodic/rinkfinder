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
    uploadEvents.arenaName = "";
    uploadEvents.step = 0;
    uploadEvents.eventType = 0;
    
    // public methods
    uploadEvents.addUploadButton = function () {
        $('<span>   </span><button id="uploadButton" class="btn btn-success ' +
          'btn-large disabled" name="yt0" type="button"><i class="icon-upload' +
          ' icon-white"></i> Begin Upload</button>').insertAfter($(".qq-upload-button"));
    };
    
    uploadEvents.onUploadCancel = function (event, id, name) {
        $("#uploadButton").prop("disabled", true);
        $("#uploadButton").addClass("disabled");
        
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
        
        var label = "Upload File";
        
        var message = "<strong>Failed to upload the data file.</strong>";
        
        utilities.ajaxError.show(label, message, xhr, "error", errorReason);
        
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
                $("#eventModalLabel").html("Delete Request");
                $("#eventModalBody").html("<p>File has been deleted, please retry your upload.</p>");
                $("#eventModal").modal('show');
                $("#loadingScreen").html("");
            },
            error: function(xhr, status, errorThrown) {
                var label = "Delete Request";
        
                var message = "<strong>Failed to delete the file.</strong>";
        
                utilities.ajaxError.show(label, message, xhr, status, errorThrown);
                
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
                var label = "Import Options";
        
                var message = "<strong>Failed to apply Import Options.</strong>";
        
                utilities.ajaxError.show(label, message, xhr, status, errorThrown);
                
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
                var label = "Import Data";
        
                var message = "<strong>Failed to import the the data file.</strong>";
        
                utilities.ajaxError.show(label, message, xhr, status, errorThrown);
                
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
        var strOutput = "<div id=\"loading\"><img src=\"" + utilities.urls.base +
                "/images/spinners/ajax-loader-roller-bg_red-fg_blue.gif\"" + 
                "alt=\"Loading...\" /><br />Please wait...</div>";
	$(elementID).html(strOutput);
	return strOutput;
    };
    
    uploadEvents.setupMappingTable = function (elementID) {
        // Now we get to build our mapping table as we have all of the information
        // that we need about the CSV file and the database table.
        var that = this;
        // First add the table headers
        var strOutput = "<thead><tr><th>Table Column</th><th>Data File Column" +
                "</th><th data-hide='phone'>Data File Example</th></tr>" +
                "</thead><tbody></tbody><tfoot><tr>" +
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
        
        var footable = $('#mappingTable').data('footable');

        if (typeof footable === "object")
        {
            footable.redraw();
        }
        else
        {
            $("#mappingTable").footable();
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
        $("#uploadEventsStep1").show();
    };
    
    uploadEvents.onResetButtonClick = function () {
        this.reset();
        $("#eventModal").modal('hide');
        return true;
    };
    
    uploadEvents.onReady = function () {
        if (typeof $.fn.fineUploader === "undefined")
        { 
            $.ajax({
                url: utilities.urls.assets + (utilities.debug ? "/js/jquery.fineuploader-3.2.js" : "/js/jquery.fineuploader-3.2.min.js"),
                dataType: "script",
                cache: true,
                success: function() {
                    window.setTimeout(function () {
                        uploadEvents.enableFineUploader();
                    }, 1000);
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
        }
        else
        {
            uploadEvents.enableFineUploader();
        }
        
        if (typeof $.fn['bootstrapSwitch'] === "undefined")
        {
            $.ajax({
                url: utilities.urls.assets + (utilities.debug ? "/js/bootstrap-switch.js" : "/js/bootstrap-switch.min.js"),
                dataType: "script",
                cache: true,
                success: function() {
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
        }
        
        if (typeof $.fn.footable === "undefined") { 
            uploadEvents.loadFootable();
        }

        uploadEvents.addUploadButton();
        $("#step2Continue").on("click", function () {
            return uploadEvents.onContinueStep2ButtonClick();
        });
        $("#step3Previous").on("click", function () {
            return uploadEvents.onPreviousStep3ButtonClick();
        });
        $("#step3Continue").on("click", function () {
            return uploadEvents.onContinueStep3ButtonClick();
        });
        $("#step4Continue").on("click", function () {
            return uploadEvents.onContinueStep4ButtonClick();
        });
        $("#uploadButton").on("click", function () {
            return uploadEvents.onUploadButtonClick();
        });
        $("#resetButton1").on("click", function () {
            return uploadEvents.onResetButtonClick();
        });
        $("#resetButton2").on("click", function () {
            return uploadEvents.onResetButtonClick();
        });
        $("#resetButton3").on("click", function () {
            return uploadEvents.onResetButtonClick();
        });
            
        $("#EventUploadForm_fileName").on("complete", function (event, id, name, response, xhr) {
            return uploadEvents.onUploadComplete(event, id, name, response, xhr);
        });
        $("#EventUploadForm_fileName").on("submit", function (event, id, name) {
            return uploadEvents.onUploadSubmit(event, id, name);
        });
        $("#EventUploadForm_fileName").on("cancel", function (event, id, name) {
            return uploadEvents.onUploadCancel(event, id, name);
        });
        $("#EventUploadForm_fileName").on("manualRetry", function (event, id, name) {
            return uploadEvents.onUploadRetry(event, id, name);
        });
        $("#EventUploadForm_fileName").on("error", function (event, id, name, errorReason, xhr) {
            return uploadEvents.onUploadError(event, id, name, errorReason, xhr);
        });
    };
    
    uploadEvents.loadFootable = function () {
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? "/js/footable.js" : "/js/footable.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
                uploadEvents.loadPaginate();
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
    };
    
    uploadEvents.loadPaginate = function () {
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? "/js/footable.paginate.js" : "/js/footable.paginate.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
                uploadEvents.loadSort();
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
    };
    
    uploadEvents.loadSort = function () {
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? "/js/footable.sort.js" : "/js/footable.sort.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
                uploadEvents.loadFilter();
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
    };
    
    uploadEvents.loadFilter = function () {
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? "/js/footable.filter.js" : "/js/footable.filter.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
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
    };
    
    uploadEvents.enableFineUploader = function () {
        $('#EventUploadForm_fileName').fineUploader( {
            'request': {
                'endpoint': utilities.urls.base + '/event/uploadEventsFile?aid=' + uploadEvents.arenaId,
                'inputName': 'EventUploadForm[fileName]'
            },
            'validation': {
                'allowedExtensions': ['csv','tsv','txt'],
                'sizeLimit': null,
                'minSizeLimit': null
            },
            'messages': {
                'typeError': '{file} has an invalid extension. Valid extension(s): {extensions}.',
                'sizeError': '{file} is too large, maximum file size is {sizeLimit}.',
                'minSizeError': '{file} is too small, minimum file size is {minSizeLimit}.',
                'emptyError:': '{file} is empty, please select files again without it.',
                'noFilesError': 'No files to upload.', 
                'onLeave': 'The files are being uploaded, if you leave now the upload will be cancelled.'
            },
            'debug': false,
            'multiple': false,
            'autoUpload': false,
            'deleteFile': {
                'enabled': true,
                'endpoint': utilities.urls.base + '/event/uploadEventsFileDelete?aid=' + uploadEvents.arenaId
            },
            'dragAndDrop': {
                'disableDefaultDropzone': true,
                'enable': false
            },
            'text': {
                'uploadButton': '<i class=\"icon-file icon-white\"><\/i> Select File'
            },
            'failedUploadTextDisplay': {
                'mode': 'custom',
                'responseProperty': 'error'
            },
            'retry': {
                'showButton': true
            },
            'template': '<div class=\"qq-uploader\"><div class=\"qq-upload-button ' +
                    'btn btn-warning btn-large\"><div>{uploadButtonText}<\/div>' +
                    '<\/div><span class=\"qq-drop-processing\"><span>{dropProces' +
                    'singText}<\/span><span class=\"qq-drop-processing-spinner\">' +
                    '<\/span><\/span><ul class=\"qq-upload-list\"><\/ul><\/div>',
            'classes': {
                'button': 'qq-upload-button.btn.btn-warning.btn-large',
                'success': 'alert alert-success',
                'buttonHover': '',
                'buttonFocus': ''
            }
        });
    };
    
    uploadEvents.enableBootstrapSwitches = function () {
        $('.make-switch')['bootstrapSwitch'](); // attach bootstrapswitch
    };
}( window.uploadEvents = window.uploadEvents || {}, jQuery ));