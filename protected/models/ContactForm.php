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
	public $body;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// name, email, subject and body are required
			array('email, body', 'required'), #name, subject,
            // Не более 50 символов для имени
            array('name', 'length', 'max'=>50),
			// email has to be a valid email address
			array('email', 'email'),
			// Не более 5k символов на body (2500 для utf-8)
			array('body', 'length', 'max'=>5000),
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
			#'verifyCode'=>'Verification Code',
		);
	}
}