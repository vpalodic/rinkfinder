/* 
 * This is the jQuery plugin for the view action of the Arena Controller
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( arenaView, $, undefined ) {
    "use strict";
    // public properties
    arenaView.endpoints = {
        calendar: '/server/endpoint'
    };
    
    arenaView.$calendar;
    arenaView.data;
    arenaView.earliestDate = moment().format("YYYY-MM-DD");
    arenaView.start_date = moment().format("YYYY-MM-DD");
    arenaView.spinner = '<div id="loading"><img src="' + utilities.urls.base +
            '/images/spinners/ajax-loader.gif" ' + 'alt="Loading..." /></div>';
    
    arenaView.onReady = function () {
        $('[data-toggle="tooltip"]').tooltip();
        
        var that = this;
        
        $.ajax({
            url: that.endpoints.calendar,
            data: {
                output: "html"
            },
            dataType: 'html',
            type: 'GET',
            success: function (data, textStatus, jqXhr) {
                var $data = $(data);
                var $content = $data.find('#content');
                that.$calendar.html(data);
            }
        });
    };
    
}( window.arenaView = window.arenaView || {}, jQuery ));