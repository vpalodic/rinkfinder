/* 
 * This is the jQuery plugin for the view action of the Arena Controller
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.assets.js
 */

(function ( arenaView, $, undefined ) {
    "use strict";
    // public properties
    
    arenaView.onReady = function () {
        $('[data-toggle="tooltip"]').tooltip();
    };
    
}( window.arenaView = window.arenaView || {}, jQuery ));