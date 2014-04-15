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
    uploadArenas.assetsUrl = "";
    uploadArenas.step = 1;
    
    // public methods
    uploadArenas.addUploadButton = function () {
        $('<span>   </span><button id="uploadButton" class="btn btn-success ' +
          'btn-large disabled" disabled name="yt0" type="button"><i class="icon-upload' +
          ' icon-white"></i> Begin Upload</button>').insertAfter($(".qq-upload-button"));
    };
    
    uploadArenas.onUploadCancel = function (event, id, name) {
        $("#uploadButton").prop("disabled", true);
        $("#uploadButton").addClass("disabled");
        
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
        
        var label = "Upload File";
        
        var message = "<strong>Failed to upload the data file.</strong>";
        
        utilities.ajaxError.show(label, message, xhr, "error", errorReason);
        
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
                $("#arenaModalLabel").html("Delete Request");
                $("#arenaModalBody").html("<p>File has been deleted, please retry your upload.</p>");
                $("#arenaModal").modal('show');
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
                var label = "Import Options";
        
                var message = "<strong>Failed to apply Import Options.</strong>";
        
                utilities.ajaxError.show(label, message, xhr, status, errorThrown);
                
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
                var label = "Import Data";
        
                var message = "<strong>Failed to import the the data file.</strong>";
        
                utilities.ajaxError.show(label, message, xhr, status, errorThrown);
                
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
        var strOutput = "<div id=\"loading\"><img src=\"" + utilities.urls.base +
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
        $("#arenaUploadStep1").show();
    };
    
    uploadArenas.onResetButtonClick = function () {
        this.reset();
        $("#arenaModal").modal('hide');
        return true;
    };
    
    uploadArenas.onReady = function () {
        if (typeof $.fn.fineUploader === "undefined")
        { 
            $.ajax({
                url: utilities.urls.assets + (utilities.debug ? "/js/jquery.fineuploader-3.2.js" : "/js/jquery.fineuploader-3.2.min.js"),
                dataType: "script",
                cache: true,
                success: function() {
                    window.setTimeout(function () {
                        uploadArenas.enableFineUploader();
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
            uploadArenas.enableFineUploader();
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
            $.ajax({
                url: utilities.urls.assets + (utilities.debug ? "/js/footable.js" : "/js/footable.min.js"),
                dataType: "script",
                cache: true,
                success: function() {
                    $.ajax({
                        url: utilities.urls.assets + (utilities.debug ? "/js/footable.paginate.js" : "/js/footable.paginate.min.js"),
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

        uploadArenas.addUploadButton();
        $("#step2Continue").on("click", function () {
            return uploadArenas.onContinueStep2ButtonClick();
        });
        $("#step3Previous").on("click", function () {
            return uploadArenas.onPreviousStep3ButtonClick();
        });
        $("#step3Continue").on("click", function () {
            return uploadArenas.onContinueStep3ButtonClick();
        });
        $("#step4Continue").on("click", function () {
            return uploadArenas.onContinueStep4ButtonClick();
        });
        $("#uploadButton").on("click", function () {
            return uploadArenas.onUploadButtonClick();
        });
        $("#resetButton1").on("click", function () {
            return uploadArenas.onResetButtonClick();
        });
        $("#resetButton2").on("click", function () {
            return uploadArenas.onResetButtonClick();
        });
        $("#resetButton3").on("click", function () {
            return uploadArenas.onResetButtonClick();
        });
            
        $("#ArenaUploadForm_fileName").on("complete", function (event, id, name, response, xhr) {
            return uploadArenas.onUploadComplete(event, id, name, response, xhr);
        });
        $("#ArenaUploadForm_fileName").on("submit", function (event, id, name) {
            return uploadArenas.onUploadSubmit(event, id, name);
        });
        $("#ArenaUploadForm_fileName").on("cancel", function (event, id, name) {
            return uploadArenas.onUploadCancel(event, id, name);
        });
        $("#ArenaUploadForm_fileName").on("manualRetry", function (event, id, name) {
            return uploadArenas.onUploadRetry(event, id, name);
        });
        $("#ArenaUploadForm_fileName").on("error", function (event, id, name, errorReason, xhr) {
            return uploadArenas.onUploadError(event, id, name, errorReason, xhr);
        });
    };
    
    uploadArenas.loadFootable = function () {
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? "/js/footable.js" : "/js/footable.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
                uploadArenas.loadPaginate();
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
    
    uploadArenas.loadPaginate = function () {
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? "/js/footable.paginate.js" : "/js/footable.paginate.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
                uploadArenas.loadSort();
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
    
    uploadArenas.loadSort = function () {
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? "/js/footable.sort.js" : "/js/footable.sort.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
                uploadArenas.loadFilter();
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
    
    uploadArenas.loadFilter = function () {
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? "/js/footable.filter.js" : "/js/footable.filter.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
                window.setTimeout(function () {
                    uploadArenas.enableFootable();
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
    };
    
    uploadArenas.enableFineUploader = function () {
        $('#ArenaUploadForm_fileName').fineUploader( {
            'request': {
                'endpoint': utilities.urls.base + '/arena/uploadArenasFile',
                'inputName': 'ArenaUploadForm[fileName]'
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
                'endpoint': utilities.urls.base + '/arena/uploadArenasFileDelete'
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
    
    uploadArenas.enableBootstrapSwitches = function () {
        $('.make-switch')['bootstrapSwitch'](); // attach bootstrapswitch
    };
}( window.uploadArenas = window.uploadArenas || {}, jQuery ));