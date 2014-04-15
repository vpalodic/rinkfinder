/* 
 * This is the jQuery plugin for the management action
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( management, $, undefined ) {
    "use strict";
    // public properties
    management.endpoints = {
        counts: "/server/endpoint",
        details: "/server/endpoint",
        operations: "/server/endpoint"
    };
    
    management.dialogBox = "";
    management.editDialogBox = "";
    management.mainContainer = "";
    management.fromDate = moment().subtract('days', 29);
    management.toDate = moment().add('days', 29);
    management.isLoading = false;
    
    management.processCounts = function (result, status, xhr) {
        if (status !== "success" || result.success !== true)
        {
            return false;
        }
        
        var countSection;
        var countSections = result.model;
        
        for (countSection in countSections)
        {
            switch (countSection)
            {
                case "arenas":
                    this.processCount(countSection, countSections.arenas);
                    break;
                case "contacts":
                    this.processCount(countSection, countSections.contacts);
                    break;
                case "locations":
                    this.processCount(countSection, countSections.locations);
                    break;
                case "events":
                    this.processCount(countSection, countSections.events);
                    break;
                case "requests":
                    this.processCount(countSection, countSections.requests);
                    break;
                case "reservations":
                    this.processCount(countSection, countSections.reservations);
                    break;
            }
        }
    };
    
    management.createBadge = function (type, count) {
        var badgeHtml = '<span class="badge';
        if (type !== '')
        {
            badgeHtml += ' ' + type + '">';
        }
        else
        {
            badgeHtml += '">';
        }
        
        badgeHtml += count + '</span>';
        
        return badgeHtml;
    };
    
    management.processCount = function (name, data) {
        // First the header and then the well body
        var $header = $("#" + name + "Header");
        var $well = $("#" + name + "Well");
        var $badge = $("#" + name + "Badge");
        var linkHtml = '<a href="#" ';
        var htmlOutput = '<i class="fa fa-list fa-lg"></i> ' + name.capitalize();
        
        $badge.empty();
        linkHtml += 'id="' + name + 'BadgeLink" class="btn btn-info">';
        linkHtml += management.createBadge('badge-info', data.total) + '</a>';
        $(linkHtml).appendTo($badge);
        
        // Just in case there is an existing handler, we want to get rid
        // of it!
        $badge.off('click', '#' + name + 'BadgeLink');
        
        $badge.on('click', '#' + name + 'BadgeLink', [data, name], function (e) {
            e.preventDefault();
            management.handleBadgeClick(e.data[0].endpoint, e.data[1].capitalize() + ":");
        });
        
        $header.empty();
        $header.html(htmlOutput);
        
        // Now the well data!
        // We will use lists!
        var i = 0;
        var j = 0;
        var objType = null;
        var objStatus = null;
        var linkIds = [];
        htmlOutput = '';

        if (data.hasOwnProperty('type'))
        {
            htmlOutput = '<ul id="' + name + 'UnorderedList" class="unstyled" style="padding: 0px 0px;margin: 0px 0px">';
            for (i = 0; i < data.type.length; i++) {
                objType = data.type[i];
                
                if (objType.count <= 0)
                {
                    continue;
                }

                linkIds.push(['#' + name + 'Type' + objType.id + 'BadgeLink',
                    objType.endpoint, name.capitalize() + ": " + objType.display_name]);
                linkHtml = '<a href="#" id="' + name + 'Type' + objType.id + 'BadgeLink" class="btn btn-success btn-block">';
                linkHtml += management.createBadge('badge-success', objType.count);

                htmlOutput += '<li data-name="' + objType.name + '" data-id=' +
                        objType.id + ' data-count=' + objType.count +
                        ' data-display_name="' + objType.display_name + '" ' +
                        'data-display_order=' + objType.display_order +
                        ' rel="tooltip" title="' + objType.description + '" ' +
                        'style="word-break:break-all;word-wrap:break-word;padding: 5px 5px;margin: 0px 0px;">' +
                        linkHtml + ' <strong>' + objType.display_name + 
                        '</strong>' + '</a>';

                if (objType.hasOwnProperty('status')) {
                    htmlOutput += '<ul class="inline" style="padding: 5px 5px;">';
                    for (j = 0; j < objType.status.length; j++) {
                        objStatus = objType.status[j];
            
                        if (objStatus.count <= 0)
                        {
                            continue;
                        }

                        linkIds.push(['#' + name + 'Type' + objType.id + 
                                'Status' + objStatus.id + 'BadgeLink',
                                objStatus.endpoint, name.capitalize() + ": " + 
                                        objType.display_name + " - " + 
                                        objStatus.display_name]);
                        linkHtml = '<a href="#" class="btn btn-warning" id="' + name + 'Type' + 
                                objType.id + 'Status' + objStatus.id + 'BadgeLink">';
                        linkHtml += management.createBadge('badge-warning', objStatus.count);

                        htmlOutput += '<li data-name="' + objStatus.name + '" data-id=' +
                                objStatus.id + ' data-count=' + objStatus.count +
                                ' data-display_name="' + objStatus.display_name + '" ' +
                                'data-display_order=' + objStatus.display_order +
                                'rel="tooltip" title="' + objStatus.description + '" ' +
                                'style="word-break:break-all;word-wrap:break-word;padding: 5px 5px;margin: 0px 0px;">' +
                                linkHtml + ' <strong>' + objStatus.display_name +
                                '</strong></li>' + '</a>';
                    }
                    htmlOutput += "</ul></li>";
                }
                else
                {
                    htmlOutput += "</li>";
                }
            }
            htmlOutput += "</ul>";
        }
        else if (data.hasOwnProperty('status'))
        {
            htmlOutput = '<ul id="' + name + 'UnorderedList" class="unstyled" style="padding: 5px 5px;">';
            for (i = 0; i < data.status.length; i++) {
                objStatus = data.status[i];
            
                if (objStatus.count <= 0)
                {
                    continue;
                }

                linkIds.push(['#' + name + 'Status' + objStatus.id + 'BadgeLink',
                    objStatus.endpoint, name.capitalize() + ": " + objStatus.display_name]);
                linkHtml = '<a href="#" class="btn btn-success btn-block" id="' + name + 'Status' + objStatus.id + 'BadgeLink">';
                linkHtml += management.createBadge('badge-success', objStatus.count);

                htmlOutput += '<li data-name="' + objStatus.name + '" data-id=' +
                        objStatus.id + ' data-count=' + objStatus.count +
                        ' data-display_name="' + objStatus.display_name + '" ' +
                        'data-display_order=' + objStatus.display_order +
                        'rel="tooltip" title="' + objStatus.description + '" ' +
                        'style="word-break:break-all;word-wrap:break-word;padding: 5px 5px;">' +
                        linkHtml + ' <strong>' + objStatus.display_name +
                        '</strong>' + '</a>';

                if (objStatus.hasOwnProperty('type')) {
                    htmlOutput += '<ul class="inline" style="padding: 5px 5px;">';
                    for (j = 0; j < objStatus.type.length; j++) {
                        objType = objStatus.type[j];
            
                        if (objType.count <= 0)
                        {
                            continue;
                        }

                        linkIds.push(['#' + name + 'Status' + objStatus.id + 
                                'Type' + objType.id + 'BadgeLink',
                                objType.endpoint, name.capitalize() + ": " +
                                        objStatus.display_name + " - " + 
                                        objType.display_name]);
                        linkHtml = '<a href="#" class="btn btn-warning" id="' + name + 'Status' + 
                                objStatus.id + 'Type' + objType.id + 'BadgeLink">';
                        linkHtml += management.createBadge('badge-warning', objType.count);

                        htmlOutput += '<li data-name="' + objType.name + '" data-id=' +
                                objType.id + ' data-count=' + objType.count +
                                ' data-display_name="' + objType.display_name + '" ' +
                                'data-display_order=' + objType.display_order +
                                'rel="tooltip" title="' + objType.description + '" ' +
                                'style="word-break:break-all;word-wrap:break-word;padding: 5px 5px;">' +
                                linkHtml + ' <strong>' + objType.display_name + 
                                '</strong></a></li>';
                    }
                    htmlOutput += "</ul></li>";
                }
                else
                {
                    htmlOutput += "</li>";
                }
            }
            htmlOutput += "</ul>";
        }
        $well.empty();
        
        $well.html(htmlOutput);
        
        for (i = 0; i < linkIds.length; i++)
        {
            // Get rid of any existing handlers for the badges!!!
            $well.off('click', linkIds[i][0]);
            $well.on('click', linkIds[i][0], [linkIds[i][1], linkIds[i][2]], function (e) {
                e.preventDefault();
                management.handleBadgeClick(e.data[0], e.data[1]);
            });
        }
    };
    
    management.getCounts = function () {
        if (this.isLoading)
        {
            return;
        }
        
        this.isLoading = true;
        var that = this;
        
        var $spinner = $("#reportrangeRefreshButton i");
        $spinner.toggleClass('fa-spin');

        $.ajax({                        
            url: that.endpoints.counts,
            type: "GET",
            dataType: "json",
            data: {
                model: ["arenas", "contacts", "locations", "events", "requests", "reservations"],
                from: management.fromDate.format('YYYY-MM-DD'),
                to: management.toDate.format('YYYY-MM-DD')
            },
            success: function(result, status, xhr) {
                if (result.success === false)
                {
                    $spinner.toggleClass('fa-spin');
                    utilities.ajaxError.show(
                        "Management Dashboard",
                        "Failed to retrieve dashboard counts",
                        xhr,
                        status,
                        'Login Required'
                    );
                    return;
                }

                that.processCounts(result, status, xhr);
                
                window.setTimeout(function () {
                    $spinner.toggleClass('fa-spin');
                    that.isLoading = false;
                },
                1000
                );
        
            },
            error: function(xhr, status, errorThrown) {
                utilities.ajaxError.show(
                        "Management Dashboard",
                        "Failed to retrieve dashboard counts",
                        xhr,
                        status,
                        errorThrown
                        );
                $spinner.toggleClass('fa-spin');
                that.isLoading = false;
            }
        });

        return true;
    };
    
    management.handleBadgeClick = function (url, name) {
        var $modal = utilities.modal.add(name, '', false, false, true);
        
        $modal.modal({
            loading: true,
            replace: true,
            modalOverflow: true
        });
        
        management.getModalData($modal, url);
        
        return false;
    };
    
    management.getModalData = function ($modal, url, dataType) {
        var that = this;
        var $thatModal = $modal;
        
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
                        $thatModal.modal('loading');
                        $thatModal.find('.modal-body').empty().append('<h1 class="text-error">Error</h1>');
                        
                        utilities.ajaxError.show(
                            "Management Dashboard",
                            "Failed to retrieve data",
                            xhr,
                            "error",
                            "Login Required"
                        );
                    }, 1000);
            
                    return;
                }

                window.setTimeout(function () {
                    $thatModal.find('.modal-body').empty().append(that.processModalData(result, status, xhr));
                    $thatModal.modal('loading');
                }, 1000);
            },
            error: function(xhr, status, errorThrown) {
                window.setTimeout(function () {
                    $thatModal.modal('loading');
                    $thatModal.find('.modal-body').empty().append('<h1 class="text-error">Error</h1>');

                    utilities.ajaxError.show(
                        "Management Dashboard",
                        "Failed to retrieve data",
                        xhr,
                        status,
                        errorThrown
                    );
                }, 1000);
            }
        });

        return true;
    };
    
    management.processModalData = function (result, status, xhr) {
        return result;
    };
    
    management.doReady = function () {
        $('#reportrange').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                'Tomorrow': [moment().add('days', 1), moment().add('days', 1)],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
                'Next Month': [moment().add('month', 1).startOf('month'), moment().add('month', 1).endOf('month')]
            },
            showDropdowns: true,
            opens: 'right',
//            parentEl: '#reportrangeAll',
            startDate: moment().subtract('days', 29),
            endDate: moment().add('days', 29)
        },
        function(start, end, label) {
            if (start && end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                utilities.loadingScreen.parentId = "countsContainer";
                utilities.loadingScreen.containerId = "countsAccordionHeader";
                utilities.loadingScreen.image.enabled = true;
                utilities.loadingScreen.image.src = "/images/spinners/ajax-loader-roller-bg_red-fg_blue.gif";
                management.fromDate = start;
                management.toDate = end;
                management.getCounts();
            }
        }
        );

        $("#reportrangeRefreshButton").on('click', function (e) {
            utilities.loadingScreen.parentId = "countsContainer";
            utilities.loadingScreen.containerId = "countsAccordionHeader";
            utilities.loadingScreen.image.enabled = true;
            utilities.loadingScreen.image.src = "/images/spinners/ajax-loader-roller-bg_red-fg_blue.gif";
            management.getCounts();
        });
    };
    
}( window.management = window.management || {}, jQuery ));