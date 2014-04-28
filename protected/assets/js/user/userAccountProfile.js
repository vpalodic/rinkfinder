/* 
 * This is the jQuery plugin for the user view / update / create actions
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( userAccountProfile, $, undefined ) {
    "use strict";
    // public properties
    userAccountProfile.endpoints = {
        updateRecord: "/server/endpoint",
        newRecord: "/server/endpoint"
    };
    
    userAccountProfile.account = {};
    userAccountProfile.profile = {};
    userAccountProfile.params = {};
    userAccountProfile.isArenaManager = false;
    userAccountProfile.statusList = [];
    userAccountProfile.stateList = [];
    
    userAccountProfile.attribute = {
        name: "",
        oldVal: "",
        newVal: ""
    };
    
    userAccountProfile.Id = 0;
    userAccountProfile.Name = '';
    
    userAccountProfile.onReady = function () {
        if (typeof $.fn.editable === "undefined")
        { 
            userAccountProfile.loadEditable();
        }
        else
        {
            userAccountProfile.enableEditable();
        }
        
        var $panel = $("#userAccountProfileView.panel.panel-primary");
        
        if ($panel.length > 0)
        {
            $panel.on('destroyed', function () {
                // We have been closed, so clean everything up!!!
                var $editables = $("#userAccountProfileView.panel.panel-primary .editable");
                
                $editables.editable('destroy');
            });
        }
        
        userAccountProfile.setupPassword();
    };
    
    userAccountProfile.loadEditable = function () {
        
        var scriptName = utilities.debug ? 'bootstrap-editable.js' : 'bootstrap-editable.min.js';
        
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? 
                "/bootstrap-editable/js/bootstrap-editable.js" : 
                        "/bootstrap-editable/js/bootstrap-editable.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
                console.log("Loaded: " + scriptName);
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
        
        var interval = setInterval(function () {
            if (typeof typeof $.fn.editable !== "undefined") {
                clearInterval(interval);
                userAccountProfile.enableEditable();
            } else if (console && console.log) {
                console.log("Loading... " + scriptName);
            }
        }, 500);
        
    };
    
    userAccountProfile.enableEditable = function () {
        userAccountProfile.setupUsername();
        userAccountProfile.setupEmail();
        userAccountProfile.setupStatus();
        userAccountProfile.setupFirstName();
        userAccountProfile.setupLastName();
        userAccountProfile.setupAddressLine1();
        userAccountProfile.setupAddressLine2();
        userAccountProfile.setupCity();
        userAccountProfile.setupState();
        userAccountProfile.setupZip();
        userAccountProfile.setupPhone();
        userAccountProfile.setupExtension();
        userAccountProfile.setupBirthday();
        
        $('[data-toggle="tooltip"]').tooltip();
    };
    
    userAccountProfile.setupFirstName = function () {
        $('#first_name').editable({
            params: userAccountProfile.params
        });
    };
    
    userAccountProfile.setupLastName = function () {
        $('#last_name').editable({
            params: userAccountProfile.params
        });
    };
    
    userAccountProfile.setupAddressLine1 = function () {
        $('#address_line1').editable({
            params: userAccountProfile.params
        });
    };
    
    userAccountProfile.setupAddressLine2 = function () {
        $('#address_line2').editable({
            params: userAccountProfile.params
        });
    };
    
    userAccountProfile.setupCity = function () {
        $('#city').editable({
            params: userAccountProfile.params
        });
    };
    
    userAccountProfile.setupState = function () {
        $('#state').editable({
            params: userAccountProfile.params,
            showbuttons: false,
            source: userAccountProfile.stateList
        });
    };
    
    userAccountProfile.setupZip = function () {
        $('#zip').editable({
            params: userAccountProfile.params
        });
    };
    
    userAccountProfile.setupPhone = function () {
        $('#phone').editable({
            params: userAccountProfile.params,
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

        $("#phone").on('shown', function(e, editable) {
            // ensure that we only get the unmasked value
            if (editable) {
                $(this).data('editable').input.$input.inputmask(
                {
                    "mask": "(999) 999-9999",
                    "clearIncomplete": false,
                    "autoUnmask" : true,
                    "showTooltip" : true
                });
            }
        });
    };
    
    userAccountProfile.setupExtension = function () {
        $('#ext').editable({
            params: userAccountProfile.params
        });
    };
    
    userAccountProfile.setupBirthday = function () {
        $('#birth_day').editable({
            params: userAccountProfile.params,
            datepicker: {
                autoclose: true,
                cleanBtn: true,
                endDate: moment().subtract('years', 13).endOf('day').toDate(),
                startDate: moment().subtract('years', 115).startOf('day').toDate(),
                startView: 'decade',
                todayBtn: false,
                todayHighlight: false
            }
        });
    };
    
    userAccountProfile.setupUsername = function () {
        $('#username').editable({
            params: userAccountProfile.params
        });
    };
    
    userAccountProfile.setupEmail = function () {
        $('#email').editable({
            params: userAccountProfile.params
        });
    };
    
    userAccountProfile.setupPassword = function () {
        $('#password').on('click', function (e) {
            e.preventDefault();
            
            var $this = $(this);
            
            var url = $this.attr('href');
            
            var $modal = utilities.modal.add("Change Password", '', false, false, true);
        
            $modal.modal({
                loading: true,
                replace: false,
                modalOverflow: true
            });
            
            $.ajax({
                url: url,
                type: "GET",
                dataType: "html",
                data: {
                    output: "html"
                },
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
                            $modal.modal('loading');
                            $modal.find('.modal-body').empty().append('<h1 class="text-error">Error</h1>');
                            
                            utilities.ajaxError.show(
                                "User Account Profile",
                                "Failed to retrieve data",
                                xhr,
                                "error",
                                "Login Required"
                            );
                        }, 1000);
                        
                        return;
                    }
                    
                    window.setTimeout(function () {
                        var $content = $(result).find("#content");
                        $modal.find('.modal-body').empty().append($content.html());
                        $modal.modal('loading');
                    }, 1000);
                },
                error: function(xhr, status, errorThrown) {
                    window.setTimeout(function () {
                        $modal.modal('loading');
                        $modal.find('.modal-body').empty().append('<h1 class="text-error">Error</h1>');
                        
                        utilities.ajaxError.show(
                            "User Account Profile",
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
    
    userAccountProfile.setupStatus = function () {
        if(userAccountProfile.isArenaManager !== 1)
        {
            return;
        }
        
        $('#status_id').editable({
            params: userAccountProfile.params,
            showbuttons: false,
            source: userAccountProfile.statusList
        });
    };
    
}( window.userAccountProfile = window.userAccountProfile || {}, jQuery ));