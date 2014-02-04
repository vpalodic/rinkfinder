<?php
/**
 * UserChangePassword class.
 * UserChangePassword is the data structure for keeping change password
 * form data. It is used by the 'changePassword' action of 'UserController'.
 */
class UserChangePassword extends CFormModel {
    /**
     * Holds the new unecrypted password
     * @var string
     */
    public $passwordSave;
    
    /**
     * Holds the new unecrypted password and must match $passwordSave
     * @var string
     */
    public $passwordRepeat;
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = array(
            array(
                'passwordSave, passwordRepeat',
                'required',
            ),
            array(
                'passwordSave, passwordRepeat',
                'length',
                'max' => 48,
                'min' => 8,
                'message' => "Invalid password (length between 8 and 48 characters).",
            ),
            array(
                'passwordSave, passwordRepeat',
                'match',
                'pattern' => '/(?=^.{8,}$)(?=.*\d)(?=.*[!@#$%^&*]+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/u',
                'message' => "Password must contain at least one from each set (a-z, A-Z, 0-9, !@#$%^&*).",
            ),
            array(
                'passwordRepeat',
                'compare',
                'compareAttribute' => 'passwordSave',
                'message' => "Passwords do not match!",
            ),
        );
        
        return $rules;
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'passwordSave' => "Password",
            'passwordRepeat' => "Confirm Password",
        );
    }
} 