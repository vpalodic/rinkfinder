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
    management.mainContainer = "";
    management.fromDate = moment().subtract('days', 29);
    management.toDate = moment().add('days', 29);
    
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
        if (type != '')
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
        linkHtml += 'id="' + name + 'BadgeLink">';
        linkHtml += management.createBadge('badge-info', data.total) + '</a>';
        $(linkHtml).appendTo($badge);
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
            htmlOutput = '<ul id="' + name + 'UnorderedList" class="unstyled">';
            for (i = 0; i < data.type.length; i++) {
                objType = data.type[i];
            
                linkIds.push(['#' + name + 'Type' + objType.id + 'BadgeLink',
                    objType.endpoint, name.capitalize() + ": " + objType.display_name]);
                linkHtml = '<a href="#" id="' + name + 'Type' + objType.id + 'BadgeLink">';
                linkHtml += management.createBadge('badge-success', objType.count);

                htmlOutput += '<li data-name="' + objType.name + '" data-id=' +
                        objType.id + ' data-count=' + objType.count +
                        ' data-display_name="' + objType.display_name + '" ' +
                        'data-display_order=' + objType.display_order +
                        ' rel="tooltip" title="' + objType.description + '" ' +
                        'style="word-break:break-all;word-wrap:break-word;">' +
                        linkHtml + ' <strong>' + objType.display_name + 
                        '</strong>' + '</a>';

                if (objType.hasOwnProperty('status')) {
                    htmlOutput += '<ul class="inline">';
                    for (j = 0; j < objType.status.length; j++) {
                        objStatus = objType.status[j];
            
                        linkIds.push(['#' + name + 'Type' + objType.id + 
                                'Status' + objStatus.id + 'BadgeLink',
                                objStatus.endpoint, name.capitalize() + ": " + 
                                        objType.display_name + " - " + 
                                        objStatus.display_name]);
                        linkHtml = '<a href="#" id="' + name + 'Type' + 
                                objType.id + 'Status' + objStatus.id + 'BadgeLink">';
                        linkHtml += management.createBadge('badge-warning', objStatus.count);

                        htmlOutput += '<li data-name="' + objStatus.name + '" data-id=' +
                                objStatus.id + ' data-count=' + objStatus.count +
                                ' data-display_name="' + objStatus.display_name + '" ' +
                                'data-display_order=' + objStatus.display_order +
                                'rel="tooltip" title="' + objStatus.description + '" ' +
                                'style="word-break:break-all;word-wrap:break-word;">' +
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
            htmlOutput = '<ul id="' + name + 'UnorderedList" class="unstyled">';
            for (i = 0; i < data.status.length; i++) {
                objStatus = data.status[i];
            
                linkIds.push(['#' + name + 'Status' + objStatus.id + 'BadgeLink',
                    objStatus.endpoint, name.capitalize() + ": " + objStatus.display_name]);
                linkHtml = '<a href="#" id="' + name + 'Status' + objStatus.id + 'BadgeLink">';
                linkHtml += management.createBadge('badge-success', objStatus.count);

                htmlOutput += '<li data-name="' + objStatus.name + '" data-id=' +
                        objStatus.id + ' data-count=' + objStatus.count +
                        ' data-display_name="' + objStatus.display_name + '" ' +
                        'data-display_order=' + objStatus.display_order +
                        'rel="tooltip" title="' + objStatus.description + '" ' +
                        'style="word-break:break-all;word-wrap:break-word;">' +
                        linkHtml + ' <strong>' + objStatus.display_name +
                        '</strong>' + '</a>';

                if (objStatus.hasOwnProperty('type')) {
                    htmlOutput += '<ul class="inline">';
                    for (j = 0; j < objStatus.type.length; j++) {
                        objType = objStatus.type[j];
            
                        linkIds.push(['#' + name + 'Status' + objStatus.id + 
                                'Type' + objType.id + 'BadgeLink',
                                objType.endpoint, name.capitalize() + ": " +
                                        objStatus.display_name + " - " + 
                                        objType.display_name]);
                        linkHtml = '<a href="#" id="' + name + 'Status' + 
                                objStatus.id + 'Type' + objType.id + 'BadgeLink">';
                        linkHtml += management.createBadge('badge-warning', objType.count);

                        htmlOutput += '<li data-name="' + objType.name + '" data-id=' +
                                objType.id + ' data-count=' + objType.count +
                                ' data-display_name="' + objType.display_name + '" ' +
                                'data-display_order=' + objType.display_order +
                                'rel="tooltip" title="' + objType.description + '" ' +
                                'style="word-break:break-all;word-wrap:break-word;">' +
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
            $well.on('click', linkIds[i][0], [linkIds[i][1], linkIds[i][2]], function (e) {
                e.preventDefault();
                management.handleBadgeClick(e.data[0], e.data[1]);
            });
        }
    };
    
    management.getCounts = function () {
        var that = this;
        
        utilities.loadingScreen.show();

        $.ajax({                        
            url: that.endpoints.counts,
            type: "GET",
            dataType: "json",
            data: {
                model: ["arenas", "events", "requests", "reservations"],
                from: management.fromDate.format('YYYY-MM-DD'),
                to: management.toDate.format('YYYY-MM-DD')
            },
            success: function(result, status, xhr) {
                that.processCounts(result, status, xhr);
                
                window.setTimeout(function () {
                        utilities.loadingScreen.hide();
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
                utilities.loadingScreen.hide();
            }
        });

        return true;
    };
    
    management.handleBadgeClick = function (url, name) {
        var $modal = $('#' + management.dialogBox);
        var $label = $modal.find('#' + management.dialogBox + "Label");
        
        $label.empty().html(name);
        $modal.modal({
            loading: true,
            replace: true,
            modalOverflow: true
        });
        
        management.getModalData($modal, url);
        
        return false;
    };
    
    management.getModalData = function ($modal, url) {
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
                console.log(result);
                
                that.processModalData(result, status, xhr);

                window.setTimeout(function () {
                    $thatModal.modal('loading');
                    $thatModal.find('.modal-body').empty().append(result);
                },
                1000
                );
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
        
    };
    
}( window.management = window.management || {}, jQuery ));