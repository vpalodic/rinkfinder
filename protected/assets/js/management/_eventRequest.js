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
        addReservation: "/server/endpoint",
        acknowledgeRecord: "/server/endpoint",
        acceptRecord: "/server/endpoint",
        rejectRecord: "/server/endpoint",
    };
    
    _eventRequest.data = {};
    _eventRequest.attribute = {
        name: "",
        oldVal: "",
        newVal: ""
    };
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
            url: utilities.urls.assets + (utilities.debug ? 
                "/bootstrap-editable/js/bootstrap-editable.js" : 
                        "/bootstrap-editable/js/bootstrap-editable.min.js"),
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
        _eventRequest.setupRequesterPhone();
        _eventRequest.setupNotes();
        _eventRequest.setupMessageButton();
        
        if(_eventRequest.data.parms.acknowledged == false) {
            _eventRequest.setupAcknowledgeButton();
        }
        
        if(_eventRequest.data.parms.accepted == false && _eventRequest.data.parms.rejected == false) {
            _eventRequest.setupRejectButton();
            _eventRequest.setupAcceptButton();
        } else if(_eventRequest.data.parms.accepted == true && _eventRequest.data.parms.rejected == false) {
            _eventRequest.setupReservationButton();
        }
        
        _eventRequest.makeButtonsEqualHeight();
//      $('[data-toggle="tooltip"]').tooltip();
    };
    
    _eventRequest.makeButtonsEqualHeight = function () {
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
    
    _eventRequest.setupNotes = function () {
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
    };
    
    _eventRequest.setupRequesterPhone = function () {
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
    };
    
    _eventRequest.setupRejectButton = function () {
        $("#rejector_id").on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Show the editable
            var $element = $(".rejected_reason");

            $element.show();
            
            $(".rejected_reason").editable({
                placement: "top"
            });

            $element.editable('show');
        });
        
        $(".rejected_reason").on('hidden', function (e, reason) {
            // Ignore Bootstrap popover!
            if (arguments.length != 2)
            {
                return;
            }

            // Hide the faux link!
            var $element = $(".rejected_reason");
            $element.hide();
        });

        $(".rejected_reason").on('save', function (e, params) {
            // Hide the faux link!
            var $element = $(".rejected_reason");
            $element.hide();

            // Disable the buttons!
            $("button").prop('disabled', true);
            
            var newParms = {
                action: 'reject',
                pk: _eventRequest.data.pk.value,
                requester_name: _eventRequest.data.item.fields.requester_name.value,
                requester_email: _eventRequest.data.item.fields.requester_email.value,
                rejected_reason: params.newValue,
                id: _eventRequest.data.item.fields.id.value,
                eid: _eventRequest.data.parms.eid,
                aid: _eventRequest.data.parms.aid,
                lid: _eventRequest.data.parms.lid,
                rejected: _eventRequest.data.parms.rejected,
                accepted: _eventRequest.data.parms.accepted,
                acknowledged: _eventRequest.data.parms.acknowledged
            };

            // Destroy the editable!
            params.newValue = '';
            $(".rejected_reason").editable('destroy');
            
            // Ok, we will submit the data to the server
            utilities.loadingScreen.parentId = "rejector_id";
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
                        window.setTimeout(function () {
                            // Some type of error happened so enable the buttons.
                            $("button").prop('disabled', false);

                            utilities.ajaxError.show(
                                    "Event Request",
                                    "Failed to reject request",
                                    xhr,
                                    "error",
                                    "Login Required"
                            );
                        }, 1000);

                        return;
                    }

                    _eventRequest.data.parms.rejected = true;
                    _eventRequest.data.parms.acknowledged = true;
                    
                    // Enable the buttons
                    $("#message").prop('disabled', false);
                
                    utilities.loadingScreen.hide();
                        
                    $('.alert').remove();
                    
                    $("#acknowledger_id").off('click');
                    $("#acknowledger_id").remove();
                    $("#accepter_id").off('click');
                    $("#accepter_id").remove();
                    $(".rejected_reason").off('save');
                    $(".rejected_reason").off('hidden');
                    $(".rejected_reason").remove();
                    $("#rejector_id").off('click');
                    $("#rejector_id").remove();
                    
                    utilities.addAlert("alerts", "alert alert-success",
                        "Request successfully rejected!",
                        "The request has been rejected.<br />The requester has " +
                            "been notified via e-mail that their request " +
                            "has been rejected for the reason you " +
                            "provided.<br />You may use the " +
                            "Message button to contact the requester if " +
                            "you need to."
                    );
                },
                error: function(xhr, status, errorThrown) {
                    // Enable the buttons
                    $("button").prop('disabled', false);

                    utilities.loadingScreen.hide();

                    utilities.ajaxError.show(
                            "Error",
                            "Reject Request",
                            xhr,
                            status,
                            errorThrown
                    );
                }
            });
        });
    };
    
    _eventRequest.setupAcknowledgeButton = function () {
        $("#acknowledger_id").on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var newParms = {
                action: 'acknowledge',
                pk: _eventRequest.data.pk.value,
                requester_name: _eventRequest.data.item.fields.requester_name.value,
                requester_email: _eventRequest.data.item.fields.requester_email.value,
                id: _eventRequest.data.item.fields.id.value,
                eid: _eventRequest.data.parms.eid,
                aid: _eventRequest.data.parms.aid,
                lid: _eventRequest.data.parms.lid,
                rejected: _eventRequest.data.parms.rejected,
                accepted: _eventRequest.data.parms.accepted,
                acknowledged: _eventRequest.data.parms.acknowledged
            };
            
            $("button").prop('disabled', true);

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
                        window.setTimeout(function () {
                            $("#button").prop('disabled', false);
                            utilities.ajaxError.show(
                                    "Event Request",
                                    "Failed to acknowledge request",
                                    xhr,
                                    "error",
                                    "Login Required"
                            );
                        }, 1000);

                        return;
                    }

                    _eventRequest.data.parms.acknowledged = true;
                    
                    $("#acknowledger_id").remove();
                    $("button").prop('disabled', false);
                
                    utilities.loadingScreen.hide();

                    $('.alert.alert-danger').remove();

                    utilities.addAlert("alerts", "alert alert-success",
                        "Request successfully acknowledged!",
                        "The request has been acknowledged.<br />The requester has " +
                            "been notified via e-mail that their request " +
                            "has been acknowledged.<br />You may use the " +
                            "Message button to contact the requester if " +
                            "you need to.<br /><strong>Please remember that the " +
                            "request still needs to be either accepted " +
                            "or rejected.</strong>"
                    );
                },
                error: function(xhr, status, errorThrown) {
                    $("button").prop('disabled', false);

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
    };
    
    _eventRequest.setupAcceptButton = function () {
        $("#accepter_id").on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var newParms = {
                action: 'accept',
                pk: _eventRequest.data.pk.value,
                requester_name: _eventRequest.data.item.fields.requester_name.value,
                requester_email: _eventRequest.data.item.fields.requester_email.value,
                id: _eventRequest.data.item.fields.id.value,
                eid: _eventRequest.data.parms.eid,
                aid: _eventRequest.data.parms.aid,
                lid: _eventRequest.data.parms.lid,
                rejected: _eventRequest.data.parms.rejected,
                accepted: _eventRequest.data.parms.accepted,
                acknowledged: _eventRequest.data.parms.acknowledged
            };
            
            $("button").prop('disabled', true);

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
                        window.setTimeout(function () {
                            $("button").prop('disabled', false);
                            utilities.ajaxError.show(
                                    "Event Request",
                                    "Failed to accept the request",
                                    xhr,
                                    "error",
                                    "Login Required"
                            );
                        }, 1000);

                        return;
                    }
                    
                    _eventRequest.data.parms.acknowledged = true;
                    _eventRequest.data.parms.accepted = true;
                    
                    var $parent = $("#accepter_id").parent();
                    
                    utilities.loadingScreen.hide();

                    $('.alert').remove();
                        
                    $("#acknowledger_id").off('click');
                    $("#acknowledger_id").remove();
                    $("#accepter_id").off('click');
                    $("#accepter_id").remove();
                    $(".rejected_reason").off('save');
                    $(".rejected_reason").off('hidden');
                    $(".rejected_reason").remove();
                    $("#rejector_id").off('click');
                    $("#rejector_id").remove();
                    
                    _eventRequest.addReservationButton($parent);
                    
                    utilities.addAlert("alerts",
                        "alert alert-success",
                        "Request successfully accepted!",
                        "The request has been accepted.<br />The requester has " +
                            "been notified via e-mail that their request " +
                            "has been accepted.<br />Please be sure to respond " +
                            "to any other pending requests.<br />To remove this " +
                            "event from the search results, you may either create a " +
                            "reservation for this event or update the status to closed." +
                            "<br />You may use the Message button to contact the requester if " +
                            "you need to.<br />"
                    );

                },
                error: function(xhr, status, errorThrown) {
                    $("button").prop('disabled', false);

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
    };
    
    _eventRequest.setupMessageButton = function () {
        $("#message").on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Show the editable
            var $element = $(".message_box");

            $element.show();

            $(".message_box").editable({
                placement: "right"
            });

            $element.editable('show');
        });
        
        $(".message_box").on('hidden', function (e, reason) {
            // Ignore Bootstrap popover!
            if (arguments.length != 2)
            {
                return;
            }

            // Hide the faux link!
            var $element = $(".message_box");
            $element.hide();
        });

        $(".message_box").on('save', function (e, params) {
            // Hide the faux link!
            var $element = $(".message_box");
            $element.hide();

            // Disable the buttons!
            $("button").prop('disabled', true);
            
            var newParms = {
                action: 'message',
                pk: _eventRequest.data.pk.value,
                requester_name: _eventRequest.data.item.fields.requester_name.value,
                requester_email: _eventRequest.data.item.fields.requester_email.value,
                message: params.newValue,
                id: _eventRequest.data.item.fields.id.value,
                eid: _eventRequest.data.parms.eid,
                aid: _eventRequest.data.parms.aid,
                lid: _eventRequest.data.parms.lid,
                rejected: _eventRequest.data.parms.rejected,
                accepted: _eventRequest.data.parms.accepted,
                acknowledged: _eventRequest.data.parms.acknowledged
            };

            // Destroy the editable!
            params.newValue = '';
            $(".message_box").editable('destroy');
            
            // Ok, we will submit the data to the server
            utilities.loadingScreen.parentId = "message";
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
                        window.setTimeout(function () {
                            // Some type of error happened so enable the buttons.
                            $("button").prop('disabled', false);

                            utilities.ajaxError.show(
                                    "Event Request",
                                    "Failed to send message",
                                    xhr,
                                    "error",
                                    "Login Required"
                            );
                        }, 1000);

                        return;
                    }

                    // Enable the buttons
                    $("button").prop('disabled', false);

                    utilities.loadingScreen.hide();

                    // Add a new alert to notify the user the message was
                    // sent
                    utilities.addAlert("alerts",
                        "alert alert-success",
                        "Messsage successfully sent!",
                        "The message has been sent as you requested."
                    );

                },
                error: function(xhr, status, errorThrown) {
                    // Enable the buttons
                    $("button").prop('disabled', false);

                    utilities.loadingScreen.hide();

                    utilities.ajaxError.show(
                            "Error",
                            "Send Message",
                            xhr,
                            status,
                            errorThrown
                    );
                }
            });
        });
    };
    
    _eventRequest.addReservationButton = function ($parent) {
        var button = '<button class="btn btn-block btn-large btn-success" ' +
                'type="button" data-toggle="tooltip" data-original-title="' +
                'Create a reservation for this request" id="createReservation' +
                '"><i class="fa fa-lg fa-plus-square"></i> <br /><span>' +
                'Reservation</span></button>';
        
        $parent.append(button);
        
        _eventRequest.setupReservationButton();
    };
    
    _eventRequest.setupReservationButton = function () {
        $("#createReservation").on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var newParms = {
                action: 'create',
                output: 'html',
                fid: _eventRequest.data.item.fields.requester_id.value,
                fname: _eventRequest.data.item.fields.requester_name.value,
                femail: _eventRequest.data.item.fields.requester_email.value,
                fphone: _eventRequest.data.item.fields.requester_phone.value,
                erid: _eventRequest.data.item.fields.id.value,
                eid: _eventRequest.data.parms.eid,
                aid: _eventRequest.data.parms.aid,
                lid: _eventRequest.data.parms.lid,
            };

            var $modal = utilities.modal.add('Create Reservation', '', false, false, true);

            $modal.modal({
                loading: true,
                replace: false,
                modalOverflow: true
            });

            var that = this;
            var $thatModal = $modal;

             $.ajax({                        
                url: _eventRequest.data.endpoint.addReservation,
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
                        window.setTimeout(function () {
                            $thatModal.modal('loading');
                            $thatModal.find('.modal-body').empty().append('<h1 class="text-error">Error</h1>');

                            utilities.ajaxError.show(
                                "Create Reservation",
                                "Failed to retrieve data",
                                xhr,
                                "error",
                                "Login Required"
                            );
                        }, 1000);

                        return;
                    }

                    window.setTimeout(function () {
                        $thatModal.find('.modal-body').empty().append(result);
                        $thatModal.modal('loading');
                    }, 1000);
                },
                error: function(xhr, status, errorThrown) {
                    window.setTimeout(function () {
                        $thatModal.modal('loading');
                        $thatModal.find('.modal-body').empty().append('<h1 class="text-error">Error</h1>');

                        utilities.ajaxError.show(
                            "Create Reservation",
                            "Failed to retrieve data",
                            xhr,
                            status,
                            errorThrown
                        );
                    }, 1000);
                }
            });
        });
    };
    
}( window._eventRequest = window._eventRequest || {}, jQuery ));