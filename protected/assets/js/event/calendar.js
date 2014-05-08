/* 
 * This is the jQuery plugin for the view action of the Arena Controller
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( eventCalendar, $, undefined ) {
    "use strict";
    // public properties
    eventCalendar.requester = {
        requester_name: '',
        requester_email: '',
        requester_phone: ''
    };
    
    eventCalendar.onReady = function () {
        var $mycalendar = $('.my-calendar-list');
    
        $mycalendar.on('click', '.previous-link, .next-link', function (e) {
            e.preventDefault();

            var url = $(this).attr('href');

            $.ajax({
                url: url,
                dataType: 'html',
                type: 'GET',
                success: function (data, textStatus, jqXhr) {
                    var $data = $(data);
                    $data.hide();
                    var $parent = $mycalendar.parent();

                    $mycalendar.fadeOut(333, function () {
                        $mycalendar.remove();
                        $parent.append($data);
                        
                        if ($(window).width() > 767)
                        {
                            $parent.ScrollTo({
                                offsetTop: 60
                            });
                        }
                        else
                        {
                            $parent.ScrollTo();
                        }

                        $data.fadeIn(666, function() {
                            $mycalendar = $data;
                        });
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    utilities.ajaxError.show('Facility Calendar',
                        'Failed to retrieve the calendar',
                        jqXHR, textStatus, errorThrown);
                }
            });
        });
        
        $mycalendar.on('click', '.purchase-request, .information-request', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var $this = $(this);
            var name = '';
            
            if($this.hasClass('purchase-request'))
            {
                name = 'Reservation Request';
            }
            else
            {
                name = 'Information Request';
            }
            
            var $modal = utilities.modal.add(name, eventCalendar.buildRequestModalForm(), false, false, false);
            
            $modal.modal({
                loading: false,
                replace: false,
                modalOverflow: false
            });
            
            // Now that our modal is in the DOM, we can attach our hooks into it!
            $('#requesterPhone').val(eventCalendar.requester.requester_phone);
            $('#requesterName').val(eventCalendar.requester.requester_name);
            $('#requesterEmail').val(eventCalendar.requester.requester_email);
            
            $('#requesterPhone').inputmask({
                mask: "(999) 999-9999",
                autoUnmask: true,
                showTooltip: true,
                clearIncomplete: true
            });
            
            $("button[type=submit]").attr("disabled", "disabled");
            
            //Append a change event listener to modal inputs
            $('input').off('keyup');
            $('input').on('keyup', function(){
                var params = eventCalendar.getRequestInputs();
                
                if(params === false)
                {
                    if($("button[type=submit]").attr("disabled") !== "disabled")
                    {
                        $("button[type=submit]").attr("disabled", "disabled");
                    }
                    
                    return;
                }
                
                // Enable the form submit!
                $("button[type=submit]").removeAttr("disabled");
            });
            
            $("#requestModalForm").off('submit');
            $("#requestModalForm").on('submit', function (e) {
                e.preventDefault();
                
                var params = eventCalendar.getRequestInputs();
                
                if(params === false)
                {
                    alert("Your full name, valid e-mail address, and ten digit phone number are required to submit this request");
                    
                    return false;
                }
                
                // We have got our valid parameters,
                // add a timestamp to the note!
                params.notes = moment().format("MM/DD/YYYY h:mm:ss A") + " by " + params.requester_name + ":\r\n\r\n" + params.notes + "\r\n\r\n";
                
                var spinner = '<div id="loading"' +
                '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                'alt="Loading..." /></div>'; 
                var $spinner = $(spinner);
                
                var $buttons = $modal.find('button');
                
                $buttons.attr("disabled", "disabled");
                $(this).append($spinner);
                
                // We have everything we need to submit our request so lets do it!
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: params,
                    success: function(data) {
                        $spinner.remove();
                        $buttons.removeAttr('disabled');
                                                
                        $('#requestModalForm').fadeOut(250, function() {
                            var $modalBody = $modal.find('.modal-body');
                            
                            var $message = $('<h1 class="text-success">You request has been sent!</h1><p>Please besure to follow-up with the facility.</p>');
                            
                            $message.hide();
                            $modalBody.append($message);
                            
                            $message.fadeIn(250);
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $spinner.remove();
                        $buttons.removeAttr('disabled');
                        
                        utilities.ajaxError.show('Event Request',
                            'Failed to send the request. Please try again later.',
                            jqXHR, textStatus, errorThrown);
                    }
                });
                
            });
            
            $modal.show();
            
           $('input').trigger('keyup');           
        });
    };
    
    eventCalendar.buildRequestModalForm = function () {
        var modalBody = '' +
            '<form id="requestModalForm" class="well"><div class="row-fluid">' +
            '<div><label>Full Name</label>' +
            '<input id="requesterName" name="requester_name" class="span12" ' +
            'placeholder="Your full name" type="text" /><label>Email Address' +
            '</label><input id="requesterEmail" name="requester_email" class=' +
            '"span12" placeholder="Your email address" type="text" /> <label>' +
            'Phone Number</label><input id="requesterPhone" name="requester_' +
            'phone" class="span12" placeholder="Your ten digit phone number" ' +
            'type="text" /><label>Message</label><textarea id="Notes" ' +
            'name="notes" rows="6" class="span12" placeholder="Enter a message">' +
            '</textarea></div><button class="btn btn-primary pull-right" type="' +
            'submit"><i class="fa fa-fw fa-upload"></i> <span>Send</span></button>' +
            '</div></form>';
    
        return modalBody;
    };
    
    eventCalendar.getRequestInputs = function () {
        // get all the inputs into an array.
        var $inputs = $('#requestModalForm :input');
        var that = this;
        
        // get an associative array of just the values.
        var values = {};
        var validCount = 0;

        $inputs.each(function() {
            if (this.name !== "" && $(this).val() !== null && $(this).val() !== "")
            {
                if (typeof $(this).val() === "string")
                {
                    var value = $(this).val();
                    
                    if (typeof value.trim === "function")
                    {
                        value = value.trim();
                    }
                    
                    if (this.name === "requester_phone")
                    {
                        value = $(this).inputmask('unmaskedvalue');
                        
                        if (value.length !== 10)
                        {
                            return false;
                        }
                        values[this.name] = value;
                        validCount++;
                    }
                    else if (this.name === "requester_email")
                    {
                        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                        
                        if(re.test(value))
                        {
                            values[this.name] = value;
                            validCount++;
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else if (this.name === "requester_name")
                    {
                        if(value.length <= 2)
                        {
                            return false;
                        }
                        
                        values[this.name] = value;
                        validCount++;
                    }
                    else
                    {
                        values[this.name] = value;
                    }
                }
            }
        });
        
        if (validCount !== 3)
        {
            return false;
        }

        return values;
    };
    
}( window.eventCalendar = window.eventCalendar || {}, jQuery ));