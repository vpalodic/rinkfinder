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
        deleteRecord: "/server/endpoint"
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
        
        var $panel = $("#eventRequestView.panel.panel-primary");
        
        if ($panel.length > 0)
        {
            $panel.on('destroyed', function () {
                // We have been closed, so clean everything up!!!
                var $editables = $("#eventRequestView.panel.panel-primary .editable");
                
                $editables.editable('destroy');
            });
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
        _eventRequest.setupDeleteButton();
        
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
        var $buttons = $("#eventRequestView.panel.panel-primary button.btn-block");
        $buttons.each(function () {
            var $this = $(this);
            maxHeight = Math.max($this.height(), maxHeight);
            maxWidth = Math.max($this.width(), maxWidth);
        });
        
        $buttons.height(maxHeight);
        //$('button').width(maxWidth);
    };
    
    _eventRequest.setupNotes = function () {
        $("#notes").editable({
            emptytext: "Add Note",
            params: _eventRequest.data.parms,
            showbuttons: 'bottom',
            success: function(response, newValue) {
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
            
                // We hide the editable, set the history, and then clear the value
                // of the editable!
                $(this).data('editable').hide();
                $("#notesHistory").text($("#notesHistory").text() + newValue);
                $(this).data('editable').input.$input.val('');
                newValue = '';
            
                // What we return gets added to the editable window.
                return "Note added";
            },
            validate: function(value) {
                // Here we add our timestamp information to the note.
                if($.trim(value) == '') {
                   return 'This field is required';
                }
                
                var oldNotes = ''; //$("#notesHistory").text();
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
                showbuttons: 'bottom',
                placement: "top",
                validate: function(value) {
                    if($.trim(value) == '') {
                    return 'This field is required';
                    }
                }
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
            var $buttons = $("#eventRequestView.panel.panel-primary button.btn-block");
            $buttons.prop('disabled', true);
            // We must disable everything and put up our spinner...
            var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
            
            // Prepare to delete the request.
            $('#deleteRequest').attr("disabled", "disabled");
            
            // Show we are busy by appending the spinner to the assign button
            $('#deleteRequest').parent().prepend(spinner);
            
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
            $("#rejector_id").toggleClass('active');

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
                            $("#rejector_id").toggleClass('active');
                            $buttons.prop('disabled', false);
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
                    
                    $buttons.prop('disabled', false);
                    $('#deleteRequest').prop('disabled', false);
                    $('#deleteRequest').parent().find("#loading").remove();
                    
                    $("#rejector_id").toggleClass('active');
                        
                    $('.alert').remove();
                    
                    $("#acknowledger_id").off('click');
                    $("#acknowledger_id").parent().remove();
                    $("#accepter_id").off('click');
                    $("#accepter_id").parent().remove();
                    $(".rejected_reason").off('save');
                    $(".rejected_reason").off('hidden');
                    $("#rejector_id").off('click');
                    $("#rejector_id").parent().remove();

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
                    $buttons.prop('disabled', false);
                    $('#deleteRequest').prop('disabled', false);
                    $('#deleteRequest').parent().find("#loading").remove();

                    $("#rejector_id").toggleClass('active');

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
            
            // Disable the buttons!
            var $buttons = $("#eventRequestView.panel.panel-primary button.btn-block");
            $buttons.prop('disabled', true);
            // We must disable everything and put up our spinner...
            var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
            
            // Prepare to delete the request.
            $('#deleteRequest').attr("disabled", "disabled");
            
            // Show we are busy by appending the spinner to the assign button
            $('#deleteRequest').parent().prepend(spinner);

            // Ok, we will submit the data to the server
            $("#acknowledger_id").toggleClass('active');

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
                            $("#acknowledger_id").toggleClass('active');
                            $buttons.prop('disabled', false);
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
                    
                    // Enable the buttons
                    $buttons.prop('disabled', false);
                    $('#deleteRequest').parent().find("#loading").remove();
                    
                    $("#acknowledger_id").toggleClass('active');
                    
                    $('.alert.alert-danger').remove();

                    $("#acknowledger_id").off('click');
                    $("#acknowledger_id").parent().remove();
                
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
                    $buttons.prop('disabled', false);
                    $('#deleteRequest').parent().find("#loading").remove();

                    $("#acknowledger_id").toggleClass('active');
                    //utilities.loadingScreen.hide();

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
            
            // Disable the buttons!
            var $buttons = $("#eventRequestView.panel.panel-primary button.btn-block");
            $buttons.prop('disabled', true);
            // We must disable everything and put up our spinner...
            var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
            
            // Prepare to delete the request.
            $('#deleteRequest').attr("disabled", "disabled");
            
            // Show we are busy by appending the spinner to the assign button
            $('#deleteRequest').parent().prepend(spinner);

            // Ok, we will submit the data to the server
            $("#accepter_id").toggleClass('active');

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
                            $("#accepter_id").toggleClass('active');
                            $buttons.prop('disabled', false);
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
                    $('#deleteRequest').parent().find("#loading").remove();
                    
                    // Enable the buttons
                    $buttons.prop('disabled', false);
                    
                    $("#accepter_id").toggleClass('active');

                    $('.alert').remove();

                    $("#acknowledger_id").off('click');
                    $("#acknowledger_id").parent().remove();
                    $("#accepter_id").off('click');
                    $("#accepter_id").remove();
                    $(".rejected_reason").off('save');
                    $(".rejected_reason").off('hidden');
                    $(".rejected_reason").remove();
                    $("#rejector_id").off('click');
                    $("#rejector_id").parent().remove();

                    _eventRequest.addReservationButton($parent);
                    
                    utilities.addAlert("alerts",
                        "alert alert-success",
                        "Request successfully accepted!",
                        "The request has been accepted.<br />The requester has " +
                            "been notified via e-mail that their request " +
                            "has been accepted.<br />Please be sure to respond " +
                            "to any other pending requests.<br />To remove this " +
                            "event from the search results, you may update the event status to closed." +
                            "<br />You may use the Message button to contact the requester if " +
                            "you need to.<br />"
                    );
                },
                error: function(xhr, status, errorThrown) {
                    $buttons.prop('disabled', false);
                    $('#deleteRequest').parent().find("#loading").remove();

                    $("#accepter_id").toggleClass('active');

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

            var width = $(window).width();
            
            var placement = 'right';
            
            if (width < 767)
            {
                placement = 'top';
            }
            
            $(".message_box").editable({
                showbuttons: 'bottom',
                placement: placement
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
            var $buttons = $("#eventRequestView.panel.panel-primary button.btn-block");
            $buttons.prop('disabled', true);
            // We must disable everything and put up our spinner...
            var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
            
            // Prepare to delete the request.
            $('#deleteRequest').attr("disabled", "disabled");
            
            // Show we are busy by appending the spinner to the assign button
            $('#deleteRequest').parent().prepend(spinner);
            
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
            $("#message").toggleClass('active');

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
                            $("#message").toggleClass('active');
                            $buttons.prop('disabled', false);
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
                    $buttons.prop('disabled', false);
                    $('#deleteRequest').prop('disabled', false);
                    $('#deleteRequest').parent().find("#loading").remove();

                    $("#message").toggleClass('active');

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
                    $buttons.prop('disabled', false);
                    $('#deleteRequest').prop('disabled', false);
                    $('#deleteRequest').parent().find("#loading").remove();

                    $("#message").toggleClass('active');

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
        // for now we are just going to return as we will implement this
        // in a future release...
        return;
        
        var button = '<button class="btn btn-block btn-large btn-success" ' +
                'type="button" data-toggle="tooltip" data-original-title="' +
                'Create a reservation for this request" id="createReservation' +
                '"><i class="fa fa-lg fa-plus-square"></i> <br /><span>' +
                'Reservation</span></button>';
        
        $parent.append(button);
        
        _eventRequest.makeButtonsEqualHeight();
        
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
                url: _eventRequest.endpoints.addReservation,
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
    
    _eventRequest.setupDeleteButton = function () {
        $("#deleteRequest").on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var newParms = {
                output: 'html',
                id: _eventRequest.data.item.fields.id.value,
                pk: _eventRequest.data.item.fields.id.value,
                eid: _eventRequest.data.parms.eid,
                aid: _eventRequest.data.parms.aid
            };

            var $modal = _eventRequest.createDeleteModal();
            
            $modal.modal({
                loading: false,
                replace: false,
                modalOverflow: false
            });

            // The modal is now in the DOM so we can hook in to the button
            // clicks. Specifically, we only care about the 'yes' button.
            $('button#yes').on('click', function (e) {
                // They clicked yes and so now we must delete the event request!!!
                var $buttons = $("#eventRequestView.panel.panel-primary button.btn-block");
                $buttons.prop('disabled', true);
                // We must disable everything and put up our spinner...
                var spinner = '<div id="loading"' +
                    '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                    'alt="Loading..." /></div>';
            
                // Prepare to delete the request.
                $('#deleteRequest').attr("disabled", "disabled");
            
                // Show we are busy by appending the spinner to the assign button
                $('#deleteRequest').parent().prepend(spinner);
                
                $.ajax({
                    url: _eventRequest.endpoints.deleteRecord,
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
                                // Enable the buttons
                                $buttons.prop('disabled', false);
                                utilities.ajaxError.show(
                                    "Delete Event Request",
                                    "Failed to retrieve data",
                                    xhr,
                                    "error",
                                    "Login Required"
                                );
                            }, 1000);
                            return;
                        }
                        
                        var $panel = $('#eventRequestView').find('.panel-body');
                        
                        var $message = $('<h1 class="text-success">The request has been deleted!</h1>');
                        
                        $message.hide();
                        
                        $panel.fadeOut(250, function () {
                            $panel.empty();
                            $panel.append($message);
                            $panel.fadeIn(250, function () {
                                $message.fadeIn(250);
                            });
                        });
                    },
                    error: function(xhr, status, errorThrown) {
                        // Enable the buttons
                        $buttons.prop('disabled', false);
                        $('#deleteRequest').parent().find('#loading').remove();
                         
                        window.setTimeout(function () {
                            utilities.ajaxError.show(
                                "Delete Event Request",
                                "Failed to retrieve data",
                                xhr,
                                status,
                                errorThrown
                        );
                        }, 1000);
                    }
                });
            });
        });
    };
    
    _eventRequest.createDeleteModal = function () {
        var modalBody = '<div id="deleteModal" class="well"><h3>Are you sure you want to ' +
            'permanently delete this item?</h3><p class="lead text-error">This operation ' +
            'cannot be undone and will also delete this item from any related ' +
            'items. For example, deleting a location / venue will also delete ' +
            'any events associated with that location / venue.</p></div>';
    
        var modalFooter = '<button class="btn btn-large btn-danger" data-dismiss="' +
                'modal" type="button" aria-hidden="true" id="yes"><i class="' +
                'fa fa-fw fa-lg fa-check"></i> Yes</button>' +
                '<button class="btn btn-large" data-dismiss="' +
                'modal" type="button" aria-hidden="true" id="close"><i class="' +
                'fa fa-fw fa-lg fa-times"></i> No</button>';

        return utilities.modal.add('Confirm Action', modalBody, modalFooter, false, false);
    };
    
}( window._eventRequest = window._eventRequest || {}, jQuery ));