<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
    /**
     * @var string The username or email address
     */
    public $username;
    
    /**
     * @var string The unecrypted password
     */
    public $password;

    /**
     * @var bool If true sets a cookie
     */
    public $rememberMe;
    
    /**
     * @var UserIdentity The identity model
     */
    private $_identity;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     * @return array An array of rules keyed by attribute name.
     */
    public function rules()
    {
        return array(
            // username and password are required
            array('username, password', 'required'),
            // rememberMe needs to be a boolean
            array('rememberMe', 'boolean'),
            // password needs to be authenticated
            array('password', 'authenticate'),
        );
    }

    /**
     * Declares attribute labels.
     * @return array An array of labels keyed by attribute name.
     */
    public function attributeLabels()
    {
        return array(
            'rememberMe' => 'Remember me next time?',
            'username' => 'Username or E-mail',
            'password' => 'Password',
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute,$params)
    {
        if(!$this->hasErrors()) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            
            $this->_identity->authenticate();
                    
            switch($this->_identity->errorCode) {
                case UserIdentity::ERROR_NONE:
                    break;
                case UserIdentity::ERROR_PASSWORD_INVALID:
                    $this->addError('password', 'Invalid password.');
                    break;
                case UserIdentity::ERROR_USERNAME_INVALID:
                    $this->addError('username', 'Invalid username.');
                    break;
                case UserIdentity::ERROR_EMAIL_INVALID:
                    $this->addError('username', 'Invalid e-mail address.');
                    break;
                case UserIdentity::ERROR_STATUS_NOTACTIVATED:
                    $message = '<h4>Your account has not been activated!</h4>';
                    $message .= 'Please click <a href="';
                    $message .= Yii::app()->controller->createUrl('site/activateAccount');
                    $message .= '">here</a> to activate your account.';
                    
                    $this->addError('username', $message);
                    break;
                case UserIdentity::ERROR_STATUS_LOCKED:
                    $message = '<h4>Your account is currently locked!</h4>';
                    $message .= 'Please wait fifteen minutes ';
                    $message .= 'to try again or click <a href="';
                    $message .= Yii::app()->controller->createUrl('site/resetAccount');
                    $message .= '">here</a> to reset your account.';
                    
                    $this->addError('username', $message);
                    break;
                case UserIdentity::ERROR_STATUS_RESET:
                    $message = '<h4>Your account is currently reset!</h4>';
                    $message .= 'Please click <a href="';
                    $message .= Yii::app()->controller->createUrl('site/resetAccount');
                    $message .= '">here</a> to finish resetting your account.';
                    
                    $this->addError('username', $message);
                    break;
                case UserIdentity::ERROR_STATUS_INACTIVE:
                    $message = '<h4>Your account is currently inactive!</h4>';
                    $message .= 'Please click <a href="';
                    $message .= Yii::app()->controller->createUrl('site/resetAccount');
                    $message .= '">here</a> to reset your account.';
                    
                    $this->addError('username', $message);
                    break;
                case UserIdentity::ERROR_STATUS_DELETED:
                    $message = '<h4>Your account has been deleted!</h4>';
                    $message .= 'Please click <a href="';
                    $message .= Yii::app()->controller->createUrl('site/contact');
                    $message .= '">here</a> to request your account be reinstated.';
                    
                    $this->addError('username', $message);
                    break;
                case UserIdentity::ERROR_STATUS_BANNED:
                    $message = '<h4>Your account has been blocked!</h4>';
                    $message .= 'Please click <a href="';
                    $message .= Yii::app()->controller->createUrl('site/contact');
                    $message .= '">here</a> to request your account be reinstated.';
                    
                    $this->addError('username', $message);
                    break;
                case UserIdentity::ERROR_STATUS_UNKNOWN:
                    $message = '<h4>Your account has an unknown status!</h4>';
                    $message .= 'Please click <a href="';
                    $message .= Yii::app()->controller->createUrl('site/contact');
                    $message .= '">here</a> to request your account be reinstated.';
                    
                    $this->addError('username', $message);
                    break;
            }
        }
    }

    /**
     * Logs in the user using the given username and password in the model.
     * @return boolean whether login is successful
     */
    public function login()
    {
        if($this->_identity === null) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            $this->_identity->authenticate();
        }
        
        if($this->_identity->errorCode === UserIdentity::ERROR_NONE)
        {
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
            Yii::app()->user->login($this->_identity, $duration);
   
            if(isset(Yii::app()->user->id)) {
                $user = User::model()->with('profile')->findByPk(Yii::app()->user->id);
        
                if($user !== null) {
                    $firstName = $user->firstName;
                    $lastName = $user->lastName;
                    $fullName = $user->fullName;
                    
                    Yii::app()->user->setState(
                            '_names',
                            array(
                                'firstName' => $firstName,
                                'lastName' => $lastName,
                                'fullName' => $fullName,
                            )
                    );

                    Yii::app()->user->setState(
                            '_email',
                            $user->email
                    );
                }
            }
            return true;
        } else {
            return false;
        }
    }
}
