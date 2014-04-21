/* 
 * This is the jQuery plugin for the index action of the Arena Controller
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( arenaIndex, $, undefined ) {
    "use strict";
    // public properties
    // custom css expression for a case-insensitive contains()
    jQuery.expr[':'].Contains = function(a, i, m){
        return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };

    arenaIndex.listFilter = function (header, list) { 
        // create and add the filter form to the header
        var form = $("<form>").attr({"class":"form-search", "action":"#", "id":"arenaFilterForm"}),
                input = $("<input>").attr({"class":"search-query", "type":"text", "id":"arenaFilterInput", "placeholder":"For Sale, Facility name, city, state"});
        
        var timerId;
        var spinner = '<div id="loading"' +
                '><img src="' + utilities.urls.base + '/images/spinners/ajax-loader.gif" ' +
                'alt="Loading..." /></div>';      

        $(form).append(input).appendTo(header);
        
        // Cache the list of list item anchors as they will never change!!
        var $list = $(list);
        var $listAnchors = $list.find("a.searchable");
        var $listItems = $list.find("li");

        $(input).on('change keyup', function () {
            var $this = $(this);
            window.clearTimeout(timerId);
            timerId = window.setTimeout(function () {
                var filter = $this.val();
                if(filter && filter.length >= 2) {
                    // this finds all links in a list that contain the input,
                    // and hide the ones not containing the input while showing the ones that do
                    $(form).append(spinner);
                    var anim1, anim2;
                    $listAnchors.filter(":Contains(" + filter + ")").each(function() {
                        anim1 = $($(this).attr('data-for')).slideDown();
                    });
                    $listAnchors.filter(":not(:Contains(" + filter + "))").each(function() {
                        anim2 = $($(this).attr('data-for')).slideUp();
                    });
                            
                    $.when(anim1, anim2).done(function() {
                        $("#loading").remove();
                    });
                } else {
                    $(form).append(spinner);
                    var anim3;
                    $listItems.each(function () {
                        anim3 = $(this).slideDown();
                    });
                    
                    $.when(anim3).done(function() {
                        $("#loading").remove();
                    });
                }
                return false;
            }, 500);
        });
    };
    
    arenaIndex.onReady = function () {
        var panels = $('.info-infos');
        var panelsButton = $('.info-row');
        panels.hide();
        
        //Click dropdown
        panelsButton.click(function() {
            //get data-for attribute
            var currentButton = $(this).find('.dropdown-info');
            var dataFor = currentButton.attr('data-for');
            var idFor = $(dataFor);
            
            //current button
            idFor.slideToggle(400, function() {
                //Completed slidetoggle
                if(idFor.is(':visible'))
                {
                    currentButton.html('<i class="fa fa-lg fa-chevron-up text-muted"></i>');
                }
                else
                {
                    currentButton.html('<i class="fa fa-lg fa-chevron-down text-muted"></i>');
                }
            });
        });
        
        $('[data-toggle="tooltip"]').tooltip();

        arenaIndex.listFilter($("#arenaHeader"), $("#arenaList"));
        
        $('#arenaFilterForm').on('submit', function (e) {
            e.preventDefault();
            
            return false;
        });
    };
    
}( window.arenaIndex = window.arenaIndex || {}, jQuery ));