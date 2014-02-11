<?php
/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.components
 */
class UserIdentity extends CUserIdentity
{
    const ERROR_EMAIL_INVALID = 3;
    const ERROR_STATUS_NOTACTIVATED = 4;
    const ERROR_STATUS_LOCKED = 5;
    const ERROR_STATUS_RESET = 6;
    const ERROR_STATUS_INACTIVE = 7;
    const ERROR_STATUS_DELETED = 8;
    const ERROR_STATUS_BANNED = 9;
    const ERROR_STATUS_UNKNOWN = 99;

    /**
     * @var string Holds the user's first name
     */
    private $_firstName;

    /**
     * @var string Holds the user's last name
     */
    private $_lastName;

    /**
     * @var string Holds the user's full name
     */
    private $_fullName;

    /**
     * @var string Holds the user's email address
     */
    private $_email;

    /**
     * @var integer Holds the user id
     */
    private $_id;

    /**
     * @var User Holds the user account
     */
    private $_user;
    
    /**
     * @var boolean True if searching for user by e-mail address
     */
    private $_useemail;
    
    /**
     * Returns the first name of the user
     * @return string
     */
    public function getFirstName()
    {
        return $this->_firstName;
    }

    /**
     * Returns the last name of the user
     * @return string
     */
    public function getLastName()
    {
        return $this->_lastName;
    }

    /**
     * Returns the full name of the user
     * @return string
     */
    public function getFullName()
    {
        return $this->_fullName;
    }

    /**
     * Returns the email address of the user
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Returns the id of the user
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets the private property _user
     * 
     */
    protected function findUser()
    {
        // Case insensitive searching!
        $username = strtolower($this->username);
        $this->_useemail = (strpos($this->username, "@") ? 1 : 0);

        // Check for an existing user
        if($this->_useemail) {
            $this->_user = User::model()->forLogin()->with(array('profile' => array('together' => true)))->find('LOWER(email) = :username', array(':username' => $username));
        } else {
            $this->_user = User::model()->forLogin()->with(array('profile' => array('together' => true)))->find('LOWER(username) = :username', array(':username' => $username));
        }
    }
    
    protected function processUserStatus()
    {
        switch($this->_user->status_id) {
            case User::STATUS_BANNED:
                $this->errorCode = self::ERROR_STATUS_BANNED;
                break;
            case User::STATUS_NOTACTIVATED:
                $this->errorCode = self::ERROR_STATUS_NOTACTIVATED;
                break;
            case User::STATUS_ACTIVE:
                $this->_id = $this->_user->id;
                $this->username = $this->_user->username;
                $this->_firstName = $this->_user->firstName;
                $this->_lastName = $this->_user->lastName;
                $this->_fullName = $this->_user->fullName;
                $this->_email = $this->_user->email;
                $this->errorCode = self::ERROR_NONE;
                $this->_user->loginSuccessful();
                break;
            case User::STATUS_LOCKED:
                $this->errorCode = self::ERROR_STATUS_LOCKED;
                break;
            case User::STATUS_RESET:
                $this->errorCode = self::ERROR_STATUS_RESET;
                break;
            case User::STATUS_INACTIVE:
                $this->errorCode = self::ERROR_STATUS_INACTIVE;
                break;
            case User::STATUS_DELETED:
                $this->errorCode = self::ERROR_STATUS_DELETED;
                break;
            default:
                $this->errorCode = self::ERROR_STATUS_UNKNOWN;
                break;
        }
    }
    /**
     * Authenticates a user by either username or e-mail address.
     * Searching is case insensitive!
     * Uses CPasswordHelper which uses the Blowfish Crypto Algo.
     * @return boolean whether authentication succeeded.
     */
    public function authenticate()
    {
        $this->findUser();
        
        if($this->_user === null) {
            if($this->_useemail) {
                $this->errorCode = self::ERROR_EMAIL_INVALID;
            } else {
                $this->errorCode = self::ERROR_USERNAME_INVALID;
            }
        } else {
            // We have a valid user account that we need to authenticate!
            if(!$this->_user->verifyPassword($this->password)) {
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
                $this->_user->loginFailed();
            } else {
                $this->processUserStatus();
            }
        }
        return !$this->errorCode;
    } // end authenticate()
}