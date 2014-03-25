/**
 * console javascript object and other utility functions.
 * If the console is undefined, we define empty functions.
 * Used for browsers that don't support the console like IE.
 *
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package www.js
 */

if (typeof console === "undefined") {
	console = {
		log: function() {},
		debug: function() {},
		info: function() {},
		warn: function() {},
		error: function() {}
	};
}
