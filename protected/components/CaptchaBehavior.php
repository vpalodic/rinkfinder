<?php
/**
 * CaptchaBehavior class file.
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
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
    private $_captcha = array(
        'registration' => true,
        'contact' => true,
    );

    /**
     * Checks if Captcha should be done
     * @param string $view the view to check.
     * @return bool true if Captcha should and can be done.
     */
    public function doCaptcha($view)
    {
        if(!extension_loaded('gd')) {
            return false;
        }
        if(isset($this->_captcha[$view])) {
            return $this->_captcha[$view];
        }
	    
        return false;
    }
}
