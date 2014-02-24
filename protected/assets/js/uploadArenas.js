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
    uploadArenas.baseUrl = "";
    
    // public methods
    uploadArenas.addUploadAndDeleteButtons = function () {
        $('<span>   </span><button id="uploadButton" class="btn btn-success ' +
          'btn-large disabled" name="yt0" type="button">Begin Upload</button> ' +
          '<span>   </span><button id="deleteButton" style="display: none;" ' +
          'class="btn btn-danger btn-large disabled" name="yt1" type="button">' +
          'Delete File</button>').insertAfter($(".qq-upload-button"));
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

        $("#arenaUploadStep1").animate({opacity: 1.0}, 100).fadeOut("slow");
        $("#step2Continue").prop("disabled", false);
        $("#step2Continue").removeClass("disabled");
        $("#arenaUploadStep2").animate({opacity: 1.0}, 100).fadeIn("slow");

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
        
        var response = JSON.parse(xhr.responseText);
        
        if (response.existingFile) {
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
        
        $("#loadingScreen").html("");
        
        return true;
    };

    uploadArenas.onUploadSubmit = function (event, id, name) {
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
                alert("File has been deleted, please retry your upload.");
                $("#loadingScreen").html("");
            },
            error: function(xhr, status, errorThrown) {
                alert("Failed to delete the file.\nStatus: " + status + "\nError Thrown: " + errorThrown);
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
            skipRows: $("#header-row").val() - 1
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
                
                $("#arenaUploadStep3").show();
                $("#loadingScreen").html("");
            },
            error: function(xhr, status, errorThrown) {
                alert("Failed to apply CSV Options.\nStatus: " + status + "\nError Thrown: " + errorThrown);
                
                $("#step2Continue").prop("disabled", false);
                $("#step2Continue").removeClass("disabled");
                $("#arenaUploadStep2").show();
                $("#loadingScreen").html("");
            }
        });

        return true;
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
            type: "PUT",
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
                $("#arenaUploadStep4").show();
                $("#loadingScreen").html("");
            },
            error: function(xhr, status, errorThrown) {
                alert("Failed to process the CSV file.\nStatus: " + status + "\nError Thrown: " + errorThrown);
                
                $("#step3Continue").prop("disabled", false);
                $("#step3Continue").removeClass("disabled");
                $("#arenaUploadStep3").hide();
                $("#loadingScreen").html("");
            }
        });

        return true;
    };
    
    uploadArenas.onContinueStep4ButtonClick = function () {
        $("#step4Continue").prop("disabled", true);
        $("#step4Continue").addClass("disabled");
        $("#arenaUploadStep4").hide();
        $("#arenaUploadStep5").show();
        return true;
    };
    
    uploadArenas.setLoadingScreen = function (elementID) {
        var strOutput = "<div id=\"loading\"><img src=\"" + this.baseUrl + "/images/ajax-loader-roller-bg_red-fg_blue.gif\" alt=\"Loading...\" /></div>";
	$(elementID).html(strOutput);
	return strOutput;
    };
    
    uploadArenas.setupMappingTable = function (elementID) {
        // Now we get to build our mapping table as we have all of the information
        // that we need about the CSV file and the database table.
        var that = this;
        // First add the table headers
        var strOutput = "<thead><tr><th>Table Column</th><th>CSV Column</th>" +
                "<th>CSV Example</th></tr></thead><tbody></tbody>";
        
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
            strOutput += '<select id="' + this.tableFields[i].name + 'SelectList" data-index="' + i + '">';
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
                    headerName: $("#" + this.tableFields[i].name + "SelectList").val()
                });
            }
        }
    };
    
}( window.uploadArenas = window.uploadArenas || {}, jQuery ));