<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class ContactForm extends CFormModel
{
	public $name;
	public $email;
	public $subject;
	public $body;
	public $verifyCode;
        public $copyMe;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// name, email, subject and body are required
			array('name, email, subject, body', 'required'),
			// email has to be a valid email address
			array('email', 'email'),
			// copyMe needs to be a boolean
			array('copyMe', 'boolean'),
			// verifyCode needs to be entered correctly
			array('verifyCode', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements()),
		);
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
                        'name' => 'Your Name',
                        'email' => 'Your E-mail Address',
                        'subject' => 'Subject',
                        'body' => 'Message',
                        'copyMe' => 'Send me a copy!',
			'verifyCode' => 'Verification Code',
		);
	}
}