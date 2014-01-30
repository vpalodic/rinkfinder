<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
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
            $this->_user = User::model()->find('LOWER(email) = :username', array(':username' => $username));
        } else {
            $this->_user = User::model()->find('LOWER(username) = :username', array(':username' => $username));
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
                $this->errorCode = self::ERROR_NONE;
                $this->_user->loginSuccessful(true);
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
                $this->_user->loginFailed(true);
            } else {
                $this->processUserStatus();
            }
        }
        return !$this->errorCode;
    } // end authenticate()
}