<?php
/**
 * CaptchaBehavior class file.
 * @author Vincent Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; Vincent Palodichuk 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package app.components
 */

/**
 * Captcha module behavior for the Yii Application object.
 *
 * @property CWebApplication $owner The application object.
 */
class CaptchaBehavior extends CBehavior
{
    /**
     * @var array holds the items that we want to require Captcha.
     */
    private $_captcha = array('registration' => true,
                              'contact' => true);

    /**
     * @desc Checks if Captcha should be done
     * @param string $view the view to use, defaults to contact.
     * @return bool true if Captcha should be done.
     */
	public function doCaptcha($view)
    {
        if(!extension_loaded('gd')) {
            return false;
        }
        if(in_array($view, $this->_captcha)) {
            return $this->_captcha[$view];
        }
	    return false;
	}
}
