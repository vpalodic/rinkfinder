/* 
 * This is the jQuery plugin for the _eventRequest view / update action
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( _eventRequest, $, undefined ) {
    "use strict";
    // public properties
    _eventRequest.endpoints = {
        updateRecord: "/server/endpoint",
        acknowledgeRecord: "/server/endpoint",
        acceptRecord: "/server/endpoint",
        rejectRecord: "/server/endpoint",
    };
    
    _eventRequest.data = {};
    _eventRequest.userId = 0;
    _eventRequest.userName = '';
    
    _eventRequest.onReady = function () {
        if (typeof $.fn.editable === "undefined")
        { 
            _eventRequest.loadEditable();
        }
        else
        {
            _eventRequest.enableEditable();
        }

    };
    
    _eventRequest.loadEditable = function () {
            $.ajax({
                url: utilities.urls.assets + (utilities.debug ? "/bootstrap-editable/js/bootstrap-editable.js" : "/bootstrap-editable/js/bootstrap-editable.min.js"),
                dataType: "script",
                cache: true,
                success: function() {
                    window.setTimeout(function () {
                        _eventRequest.enableEditable();
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
    
    _eventRequest.enableEditable = function () {
        $('#requester_phone').editable({
            params: _eventRequest.data.parms,
            display: function(value, sourceData) {
                // display the supplied digits as a phone number!
                var html = '';

                if (typeof value === 'undefined' || value.length <= 0)
                {
                    return;
                }
            
                html = value.replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
                $(this).html(html);
            }
        });

        $("#requester_phone").on('shown', function(e, editable) {
            // ensure that we only get the unmasked value
            if (editable) {
                $(this).data('editable').input.$input.inputmask(
                {
                    "mask": _eventRequest.data.item.fields.requester_phone.inputmask.mask,
                    "clearIncomplete": false,
                    'autoUnmask' : true
                });
            }
        });
        
        if(_eventRequest.data.parms.rejected == false) {
            $(".rejected_reason").editable({
                params: _eventRequest.data.parms,
                success: function(response, newValue) {
                    if (typeof response === "undefined")
                    {
                        return;
                    }
                    
                    _eventRequest.data.parms.action = null;
                    _eventRequest.data.parms.rejected = true;
                    _eventRequest.data.parms.acknowledged = true;
                    
                    $("#notes").editable('option', 'params', _eventRequest.data.parms);
                        
                    if (typeof response !== 'undefined' && response.length > 0)
                    {
                        return "Data not saved. Please refresh the page as it appears" +
                                " the session has expired."
                    }
                    
                    $("#message").prop('disabled', false);
                
                    utilities.loadingScreen.hide();
                        
                    $('.alert').remove();
                    $("#acknowledger_id").remove();
                    $("#accepter_id").remove();
                    $("#rejector_id").remove();
                    utilities.addAlert("alerts", "alert alert-success",
                    "Request successfully rejected!",
                    "The request has been rejected.<br />The requester has " +
                            "been notified via e-mail that their request " +
                            "has been rejected for the reason you " +
                            "provided.<br />You may use the " +
                            "Message button to contact the requester if " +
                            "you need to.");
                },
                error: function (response, newValue) {
                    _eventRequest.data.parms.action = null;
                    $("#notes").editable('option', 'params', _eventRequest.data.parms);
                    utilities.loadingScreen.hide();
                    var $element = $(".rejected_reason");
                    $element.editable('setValue', response.responseText);
                    
                    utilities.addAlert("alerts", "alert alert-danger",
                    "Failed to reject the request!",
                    "The request has not been rejected.<br />The requester has " +
                            "not been notified via e-mail that their request " +
                            "has been rejected for the reason you " +
                            "provided.<br />You may use the " +
                            "Message button to contact the requester if " +
                            "you need to. The error has been set to the " +
                            "rejection text. Please close and re-open this " +
                            "record before clicking the Reject " +
                            "button to try again.");
                    
                    $("#acknowledger_id").prop('disabled', false);
                    $("#accepter_id").prop('disabled', false);
                    $("#rejector_id").prop('disabled', false);
                    $("#message").prop('disabled', false);
                
                    var responseText = response.responseText;
                    response = undefined;
                    $element.editable('setValue', responseText);
                    $element.editable('option', 'params', null);
                    $element.editable('option', 'pk', null);
                    $element.editable('option', 'url', null);
                    return responseText;
               }
            });
    
            $(".rejected_reason").on('hidden', function (e, reason) {
                var $element = $(".rejected_reason");
                $element.hide();
            });
            
            $(".rejected_reason").on('save', function (e, params) {
                if (arguments.length != 2)
                {
                    return;
                }
        
                var $element = $(".rejected_reason");
                $element.hide();
                $element.editable('setValue', params.newValue);

                var newParms = _eventRequest.data.parms;
                newParms.action = 'reject';
                newParms.pk = _eventRequest.data.pk.value;
                newParms.requester_name = _eventRequest.data.item.fields.requester_name.value;
                newParms.requester_email = _eventRequest.data.item.fields.requester_email.value;
            
                $("#acknowledger_id").prop('disabled', true);
                $("#accepter_id").prop('disabled', true);
                $("#rejector_id").prop('disabled', true);
                $("#message").prop('disabled', true);
                
                // Ok, we will submit the data to the server
                utilities.loadingScreen.parentId = "rejector_id";
                utilities.loadingScreen.image.enabled = true;
                utilities.loadingScreen.show();
                
                $(".rejected_reason").editable('option', 'params', newParms);
                $(".rejected_reason").editable('option', 'pk', newParms.pk);
                $(".rejected_reason").editable('option', 'url', _eventRequest.endpoints.updateRecord);
                $(".rejected_reason").editable('submit');            
            });
    
            $("#rejector_id").click(function (e) {
                e.preventDefault();

                e.stopPropagation();

                // Show the editable
                var $element = $(".rejected_reason");

                $element.show();
                $element.editable('show');
            });
        }
        
        if(_eventRequest.data.parms.acknowledged == false) {
            $("#acknowledger_id").click(function (e) {
                e.preventDefault();
                e.stopPropagation();

                var newParms = _eventRequest.data.parms;
                newParms.action = 'acknowledge';
                newParms.pk = _eventRequest.data.pk.value;
                newParms.requester_name = _eventRequest.data.item.fields.requester_name.value;
                newParms.requester_email = _eventRequest.data.item.fields.requester_email.value;
            
                $("#acknowledger_id").prop('disabled', true);
                $("#accepter_id").prop('disabled', true);
                $("#rejector_id").prop('disabled', true);
                $("#message").prop('disabled', true);
                
                // Ok, we will submit the data to the server
                utilities.loadingScreen.parentId = "acknowledger_id";
                utilities.loadingScreen.image.enabled = true;
                utilities.loadingScreen.show();
                
                $.ajax({
                    url: _eventRequest.endpoints.updateRecord,
                    type: "POST",
                    dataType: "html",
                    data: newParms,
                    success: function(result, status, xhr) {
                        // Its possible we will get a session timeout so check for it!
                        var myjsonObj = false;
                        try
                        {
                            myjsonObj = JSON.parse(result);
                        }
                        catch (err)
                        {
                            myjsonObj = false;
                        }

                        if (myjsonObj !== false)
                        {
                            $("#acknowledger_id").prop('disabled', false);
                            $("#accepter_id").prop('disabled', false);
                            $("#rejector_id").prop('disabled', false);
                            $("#message").prop('disabled', false);
                            window.setTimeout(function () {
                                utilities.ajaxError.show(
                                    "Management Dashboard",
                                    "Failed to acknowledge request",
                                    xhr,
                                    "error",
                                    "Login Required"
                                );
                            }, 1000);

                            return;
                        }
                        _eventRequest.data.parms.action = null;
                        _eventRequest.data.parms.acknowledged = true;
                    
                        $("#notes").editable('option', 'params', _eventRequest.data.parms);
                        
                        $("#acknowledger_id").remove();
                        $("#accepter_id").prop('disabled', false);
                        $("#rejector_id").prop('disabled', false);
                        $("#message").prop('disabled', false);
                
                        utilities.loadingScreen.hide();
                        
                        $('.alert.alert-danger').remove();
                        
                        utilities.addAlert("alerts", "alert alert-success",
                        "Request successfully acknowledged!",
                        "The request has been acknowledged.<br />The requester has " +
                                "been notified via e-mail that their request " +
                                "has been acknowledged.<br />You may use the " +
                                "Message button to contact the requester if " +
                                "you need to.<br />Please remember that the " +
                                "request still needs to be either accepted " +
                                "or rejected.");

                    },
                    error: function(xhr, status, errorThrown) {
                        _eventRequest.data.parms.action = null;
                        _eventRequest.data.parms.acknowledged = false;
                    
                        $("#notes").editable('option', 'params', _eventRequest.data.parms);
                        $("#acknowledger_id").prop('disabled', false);
                        $("#accepter_id").prop('disabled', false);
                        $("#rejector_id").prop('disabled', false);
                        $("#message").prop('disabled', false);
                        
                        utilities.loadingScreen.hide();
                        
                        utilities.ajaxError.show(
                            "Error",
                            "Acknowledge Request",
                            xhr,
                            status,
                            errorThrown
                        );
                    }
                });
            });
        }
        
        if(_eventRequest.data.parms.accepted == false) {
            $("#accepter_id").click(function (e) {
                e.preventDefault();
                e.stopPropagation();

                var newParms = _eventRequest.data.parms;
                newParms.action = 'accept';
                newParms.pk = _eventRequest.data.pk.value;
                newParms.requester_name = _eventRequest.data.item.fields.requester_name.value;
                newParms.requester_email = _eventRequest.data.item.fields.requester_email.value;
            
                $("#acknowledger_id").prop('disabled', true);
                $("#accepter_id").prop('disabled', true);
                $("#rejector_id").prop('disabled', true);
                $("#message").prop('disabled', true);
                
                // Ok, we will submit the data to the server
                utilities.loadingScreen.parentId = "accepter_id";
                utilities.loadingScreen.image.enabled = true;
                utilities.loadingScreen.show();
                
                $.ajax({
                    url: _eventRequest.endpoints.updateRecord,
                    type: "POST",
                    dataType: "html",
                    data: newParms,
                    success: function(result, status, xhr) {
                        // Its possible we will get a session timeout so check for it!
                        var myjsonObj = false;
                        try
                        {
                            myjsonObj = JSON.parse(result);
                        }
                        catch (err)
                        {
                            myjsonObj = false;
                        }

                        if (myjsonObj !== false)
                        {
                            $("#acknowledger_id").prop('disabled', false);
                            $("#accepter_id").prop('disabled', false);
                            $("#rejector_id").prop('disabled', false);
                            $("#message").prop('disabled', false);
                            window.setTimeout(function () {
                                utilities.ajaxError.show(
                                    "Management Dashboard",
                                    "Failed to accept the request",
                                    xhr,
                                    "error",
                                    "Login Required"
                                );
                            }, 1000);

                            return;
                        }
                        _eventRequest.data.parms.action = null;
                        _eventRequest.data.parms.acknowledged = true;
                        _eventRequest.data.parms.accepted = true;
                    
                        $("#notes").editable('option', 'params', _eventRequest.data.parms);
                        
                        $("#acknowledger_id").remove();
                        $("#accepter_id").remove();
                        $("#rejector_id").remove();
                        $("#message").prop('disabled', false);
                
                        utilities.loadingScreen.hide();
                        
                        $('.alert').remove();
                        
                        utilities.addAlert("alerts", "alert alert-success",
                        "Request successfully accepted!",
                        "The request has been accepted.<br />The requester has " +
                            "been notified via e-mail that their request " +
                            "has been accepted.<br />Please be sure to respond " +
                            "to any other pending requests.<br />To remove this " +
                            "event from the search results, you may either create a " +
                            "reservation for this event or update the status to closed." +
                            "<br />You may use the " +
                            "Message button to contact the requester if " +
                            "you need to.<br />");

                    },
                    error: function(xhr, status, errorThrown) {
                        _eventRequest.data.parms.action = null;
                        _eventRequest.data.parms.accepted = false;
                    
                        $("#notes").editable('option', 'params', _eventRequest.data.parms);
                        $("#acknowledger_id").prop('disabled', false);
                        $("#accepter_id").prop('disabled', false);
                        $("#rejector_id").prop('disabled', false);
                        $("#message").prop('disabled', false);
                        
                        utilities.loadingScreen.hide();
                        
                        utilities.ajaxError.show(
                            "Error",
                            "Accept Request",
                            xhr,
                            status,
                            errorThrown
                        );
                    }
                });
            });
        }
        
            $(".message_box").editable({
                params: _eventRequest.data.parms
            });
    
            $(".message_box").on('hidden', function (e, reason) {
                var $element = $(".message_box");
                $element.hide();
                
                _eventRequest.data.parms.action = 'message';
            });
            
            $(".message_box").on('save', function (e, params) {
                if (arguments.length != 2)
                {
                    return;
                }
        
                var $element = $(".message_box");
                $element.hide();
                $element.editable('setValue', params.newValue);

                _eventRequest.data.parms.action = 'message';
                
                var newParms = _eventRequest.data.parms;
                newParms.action = 'message';
                newParms.pk = _eventRequest.data.pk.value;
                newParms.requester_name = _eventRequest.data.item.fields.requester_name.value;
                newParms.requester_email = _eventRequest.data.item.fields.requester_email.value;
                newParms.message = params.newValue;
            
                $("#acknowledger_id").prop('disabled', true);
                $("#accepter_id").prop('disabled', true);
                $("#rejector_id").prop('disabled', true);
                $("#message").prop('disabled', true);
                
                // Ok, we will submit the data to the server
                utilities.loadingScreen.parentId = "message";
                utilities.loadingScreen.image.enabled = true;
                utilities.loadingScreen.show();
                
//                $(".message_box").editable('option', 'params', newParms);
//                $(".message_box").editable('option', 'pk', newParms.pk);
                $(".message_box").editable('option', 'url', _eventRequest.endpoints.updateRecord);
                
                $.ajax({
                    url: _eventRequest.endpoints.updateRecord,
                    type: "POST",
                    dataType: "html",
                    data: newParms,
                    success: function(result, status, xhr) {
                        // Its possible we will get a session timeout so check for it!
                        var myjsonObj = false;
                        try
                        {
                            myjsonObj = JSON.parse(result);
                        }
                        catch (err)
                        {
                            myjsonObj = false;
                        }

                        if (myjsonObj !== false)
                        {
                            $("#acknowledger_id").prop('disabled', false);
                            $("#accepter_id").prop('disabled', false);
                            $("#rejector_id").prop('disabled', false);
                            $("#message").prop('disabled', false);
                            window.setTimeout(function () {
                                utilities.ajaxError.show(
                                    "Management Dashboard",
                                    "Failed to send message",
                                    xhr,
                                    "error",
                                    "Login Required"
                                );
                            }, 1000);

                            return;
                        }
                        _eventRequest.data.parms.action = null;
                    
                        $("#notes").editable('option', 'params', _eventRequest.data.parms);
                        
                        $("#acknowledger_id").prop('disabled', false);
                        $("#accepter_id").prop('disabled', false);
                        $("#rejector_id").prop('disabled', false);
                        $("#message").prop('disabled', false);
                
                        utilities.loadingScreen.hide();
                        
                        utilities.addAlert("alerts", "alert alert-success",
                        "Messsage successfully sent!",
                        "The message has been sent as you requested.");

                    },
                    error: function(xhr, status, errorThrown) {
                        _eventRequest.data.parms.action = null;
                    
                        $("#notes").editable('option', 'params', _eventRequest.data.parms);

                        $("#acknowledger_id").prop('disabled', false);
                        $("#accepter_id").prop('disabled', false);
                        $("#rejector_id").prop('disabled', false);
                        $("#message").prop('disabled', false);
                        
                        utilities.loadingScreen.hide();
                        
                        utilities.ajaxError.show(
                            "Error",
                            "Message Request",
                            xhr,
                            status,
                            errorThrown
                        );
                    }
                });
            });
    
            $("#message").click(function (e) {
                e.preventDefault();

                e.stopPropagation();

                // Show the editable
                var $element = $(".message_box");

                $element.show();
                $element.editable('show');
            });
        
        $("#notes").editable({
            emptytext: "Add Note",
            params: _eventRequest.data.parms,
            success: function(response, newValue) {
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
            
                // We hide the editable, set the history, and then clear the value
                // of the editable!
                $(this).data('editable').hide();
                $("#notesHistory").text(newValue);
                $(this).data('editable').input.$input.val('');
                newValue = '';
            
                // What we return gets added to the editable window.
                return "Note added";
            },
            validate: function(value) {
                // Here we add our timestamp information to the note.
                var oldNotes = $("#notesHistory").text();
                oldNotes += moment().format("MM/DD/YYYY h:mm:ss A") + " by " + _eventRequest.userName + ":\r\n\r\n";
                oldNotes += value + "\r\n\r\n";
                return {newValue: oldNotes};
            }
        });
    
//      $('[data-toggle="tooltip"]').tooltip();
        var maxHeight = 0;
        var maxWidth = 0;
        $('button').each(function () {
            var $this = $(this);
            maxHeight = Math.max($this.height(), maxHeight);
            maxWidth = Math.max($this.width(), maxWidth);
        });
        
        $('button').height(maxHeight);
        //$('button').width(maxWidth);
    };
    
}( window._eventRequest = window._eventRequest || {}, jQuery ));