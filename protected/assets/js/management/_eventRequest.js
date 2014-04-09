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

        $(".rejector_id_reason").editable({
            params: _eventRequest.data.parms
        });
    
        $(".rejector_id").editable({
            params: _eventRequest.data.parms
        });
    
        $("#rejected_reason.rejector_id_reason").on('save', function (e, params) {
            if (arguments.length != 2)
            {
                return;
            }
        
            var $element = $("#rejected_reason.rejector_id_reason");
            var $id = $("#rejector_id.rejector_id_reason");
            $element.hide();
            $element.editable('setValue', params.newValue);
            $id.editable('setValue', _eventRequest.userId , false)
        
            var newParms = _eventRequest.data.parms;
            newParms.action = 'reject';
            newParms.pk = _eventRequest.data.pk.value;
            newParms.requester_name = _eventRequest.data.item.fields.requester_name.value;
            newParms.requester_email = _eventRequest.data.item.fields.requester_email.value;
            
            // Ok, we will submit the data to the server
            $(".rejector_id_reason").editable('submit', {
                url: _eventRequest.endpoints.updateRecord,
                data: newParms,
                success: function(response, newValue) {
                    
                    _eventRequest.data.parms.action = null;
                     $("#notes").editable('option', 'params', _eventRequest.data.parms);
                    if (typeof response !== 'undefined' && response.length > 0)
                    {
                        return "Data not saved. Please refresh the page as it appears" +
                                " the session has expired."
                    }
                    
                    // We update the rejected on
                    $(".rejector_id").fade();
                },
                error: function (response, newValue) {
                    _eventRequest.data.parms.action = null;
                    $("#notes").editable('option', 'params', _eventRequest.data.parms);
                    return response.responseText;
                }
            });
            
        });
    
        $(".rejector_id").click(function (e) {
            e.preventDefault();

            e.stopPropagation();

            // Show the editable
            var $element = $("#rejected_reason.rejector_id_reason");

            $element.show();
            $element.editable('show');
        });
    
        $('#rejected_reason').editable({
            params: _eventRequest.data.parms
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