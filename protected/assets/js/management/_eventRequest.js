/* 
 * This is the jQuery plugin for the _eventRequest view / update action
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( _eventRequest, $, undefined ) {
    "use strict";
    // public properties
    _eventRequest.onReady = function () {
        if (typeof $.fn.editable === "undefined")
        { 
            $.ajax({
                url: utilities.urls.assets + (utilities.debug ? "/bootstrap-editable/js/bootstrap-editable.js" : "/bootstrap-editable/js/bootstrap-editable.min.js"),
                dateType: "script",
                cache: true,
                success: function() {
                    window.setTimeout(function () {
                        
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
                dateType: "script",
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
                dateType: "script",
                cache: true,
                success: function() {
                    $.ajax({
                        url: utilities.urls.assets + (utilities.debug ? "/js/footable.paginate.js" : "/js/footable.paginate.min.js"),
                        dateType: "script",
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
    
    _eventRequest.enableFineUploader = function () {
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
    
    _eventRequest.enableBootstrapSwitches = function () {
        $('.make-switch')['bootstrapSwitch'](); // attach bootstrapswitch
    };
}( window._eventRequest = window._eventRequest || {}, jQuery ));