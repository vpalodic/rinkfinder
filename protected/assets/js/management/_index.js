/* 
 * This is the jQuery plugin for the _index view action
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( _index, $, undefined ) {
    "use strict";
    // public properties
    _index.data = {};
    
    _index.headers = {};
    
    _index.loadScriptFile = false;
    
    _index.scriptFile = "";
    
    _index.onReady = function () {
        if (typeof $.fn.footable === "undefined")
        {
            _index.loadFootable();
        }
        else
        {
            _index.enableFootable();
        }
        
        if (this.loadScriptFile === true)
        {
            this.loadFile();
        }
    };
    
    _index.loadFile = function () {
        $.ajax({
            url: this.scriptFile,
            dataType: "script",
            cache: true,
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
    
    _index.loadFootable = function () {
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? "/js/footable.js" : "/js/footable.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
                _index.loadPaginate();
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
    
    _index.loadPaginate = function () {
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? "/js/footable.paginate.js" : "/js/footable.paginate.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
                _index.loadSort();
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
    
    _index.loadSort = function () {
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? "/js/footable.sort.js" : "/js/footable.sort.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
                _index.loadFilter();
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
    
    _index.loadFilter = function () {
        $.ajax({
            url: utilities.urls.assets + (utilities.debug ? "/js/footable.filter.js" : "/js/footable.filter.min.js"),
            dataType: "script",
            cache: true,
            success: function() {
                    _index.enableFootable();
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
    
    _index.enableFootable = function () {
        $("#" + _index.data.model + "Footable").footable().on('click', 'tbody a', function (e) {
            e.stopPropagation();
            e.preventDefault();
            
            var $target = $(e.target);
            
            console.log($target.attr("href"));
            
            _index.handleLinkClick($target.attr("href"));
        });
        
        $("#" + _index.data.model + "Footable").footable();
    
        $("#" + _index.data.model + "Footable").footable().on('footable_filtering', function (e) {
            var selected = $('#tableFilterStatus').find(':selected').text();
            var selected2 = $('#tableFilterType').find(':selected').text();
            if (selected && selected.length > 0) {
                e.filter += (e.filter && e.filter.length > 0) ? ' ' + selected : selected;
                e.clear = !e.filter;
            }
            if (selected2 && selected2.length > 0) {
                e.filter += (e.filter && e.filter.length > 0) ? ' ' + selected2 : selected2;
                e.clear = !e.filter;
            }
        });

        $('.clear-filter').click(function (e) {
          e.preventDefault();
          $('#tableFilterStatus').val('');
          $('#tableFilterType').val('');
        });

        $('#tableFilterStatus').change(function (e) {
          e.preventDefault();
          $("#" + _index.data.model + "Footable").trigger('footable_filter', {filter: $('#tableFilter').val()});
        });

        $('#tableFilterType').change(function (e) {
          e.preventDefault();
          $("#" + _index.data.model + "Footable").trigger('footable_filter', {filter: $('#tableFilter').val()});
        });
    };
    
    _index.handleLinkClick = function (url) {
        var $modal = utilities.modal.add('', '', false, false, true);
        
        $modal.modal({
            loading: true,
            replace: false,
            modalOverflow: true
        });
        
        management.getModalData($modal, url);
        
        return false;
    };
    
}( window._index = window._index || {}, jQuery ));