(function (window, undefined) {
    "use strict";

    // jQuery
    window.jQuery || document.write('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"><\/script>');

    var dir = document.querySelector('script[src$="ajaxify-bookmarklet-helper.js"]').getAttribute('src');
    var name = dir.split('/').pop(); 
    dir = dir.replace('/' + name, "");
    var scrollto = dir.replace('/js', "");
    
    // History & ScrollTo (Wait for jQuery)
    var interval = setInterval(function () {
        if (window.jQuery) {
            clearInterval(interval);

            // History.js & ScrollTo.js
            (window.History && window.History.initHtml4) || document.write('<script src="' + dir + '/jquery.history.js"><\/script>');
            jQuery.ScrollTo || document.write('<script src="' + scrollto + '/jquery-scrollto/lib/jquery-scrollto.js"><\/script>');

            interval = setInterval(function () {
                if (window.History && window.History.initHtml4) {
                    clearInterval(interval);

                    // Ajaxify-html5.js
                    document.write('<script src="' + dir + '/ajaxify-html5.js"><\/script>');

                    interval = setInterval(function () {
                        if (jQuery.fn.ajaxify) {
                            clearInterval(interval);
                            if (console && console.log) {
                                console.log(dir);
                                console.log('History.js It! Is ready for action!');
                            }
                        }
                    }, 500);
                } else if (console && console.log) {
                    console.log("Loading history.js and scrollto.js");
                }
            }, 500);
        } else if (console && console.log) {
            console.log("Loading jQuery");
        }
    }, 500);
}(window));
