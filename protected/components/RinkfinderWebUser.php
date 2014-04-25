<?php
/**
 * RinkfinderWebUser class file.
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.components
 */

/**
 * Web user that allows for bypassing access checks when a
 * site administrator. Also includes utility functions to quickly
 * determine if a user belongs to a certain role
 */
class RinkfinderWebUser extends CWebUser
{
    /**
     * @var User User model to not repeat query.
     */
    protected $_model;

    /**
     * Return first name of the user.
     * access it by Yii::app()->user->firstName
     * @return string The user's first name or ''
     */
    public function getFirstName()
    {
        Yii::trace('getFirstName()', 'application.components.RinkfinderWebUser');
	return $this->getState('__firstName');
    }
    
    /**
     * Sets first name of the user.
     * @param string $value The user's first name
     */
    public function setFirstName($value)
    {
        Yii::trace('setFirstName()', 'application.components.RinkfinderWebUser');
	$this->setState('__firstName', $value);
    }
    
    /**
     * Return last name of the user.
     * access it by Yii::app()->user->lastName
     * @return string The user's last name
     */
    public function getLastName()
    {
        Yii::trace('getLastName()', 'application.components.RinkfinderWebUser');
	return $this->getState('__lastName');
    }
    
    /**
     * Sets last name of the user.
     * @param string $value The user's last name
     */
    public function setLastName($value)
    {
        Yii::trace('setLastName()', 'application.components.RinkfinderWebUser');
	$this->setState('__lastName', $value);
    }
    
    /**
     * Return full name of the user.
     * access it by Yii::app()->user->fullName
     * @return string The user's full name
     */
    public function getFullName()
    {
        Yii::trace('getFullName()', 'application.components.RinkfinderWebUser');
	return $this->getState('__fullName');
    }
    
    /**
     * Sets full name of the user.
     * @param string $value The user's full name
     */
    public function setFullName($value)
    {
        Yii::trace('setFullName()', 'application.components.RinkfinderWebUser');
	$this->setState('__fullName', $value);
    }
    
    /**
     * Return email address of the user.
     * access it by Yii::app()->user->email
     * @return string The user's email address
     */
    public function getEmail()
    {
        Yii::trace('getEmail()', 'application.components.RinkfinderWebUser');
	return $this->getState('__email');
    }
    
    /**
     * Sets email address of the user.
     * @param string $value The user's email address
     */
    public function setEmail($value)
    {
        Yii::trace('setEmail()', 'application.components.RinkfinderWebUser');
	$this->setState('__email', $value);
    }
    
    /**
     * Return phone number of the user.
     * access it by Yii::app()->user->phone
     * @return string The user's phone number
     */
    public function getPhone()
    {
        Yii::trace('getPhone()', 'application.components.RinkfinderWebUser');
	return $this->getState('__phone');
    }
    
    /**
     * Sets phone number of the user.
     * @param string $value The user's phone number
     */
    public function setPhone($value)
    {
        Yii::trace('setPhone()', 'application.components.RinkfinderWebUser');
	$this->setState('__phone', $value);
    }
    
    /**
     * Return roles of the user.
     * access it by Yii::app()->user->roles
     * @return string[] The user's roles
     */
    public function getRoles()
    {
        Yii::trace('getRoles()', 'application.components.RinkfinderWebUser');
	return $this->getState('__roles');
    }
    
    /**
     * Sets email address of the user.
     * @param string[] $value The user's roles
     */
    public function setRoles($value)
    {
        Yii::trace('setRoles()', 'application.components.RinkfinderWebUser');
	$this->setState('__roles', $value);
    }
    
    public function getModel($uid = null)
    {
        if($uid == null) {
            $uid = $this->id;
        }
        
        return $this->loadUser($uid);
    }
    /**
     * Performs access check for this user.
     * @param string $operation the name of the operation that need access check.
     * @param array $params name-value pairs that would be passed to business rules associated
     * with the tasks and roles assigned to the user.
     * @param boolean $allowCaching whether to allow caching the result of access check.
     * @return boolean whether the operations can be performed by this user.
     */
    public function checkAccess($operation, $params = array(), $allowCaching = true)
    {
        Yii::trace('checkAccess()', 'application.components.RinkfinderWebUser');
        // Site administrator can perform every action!
        if($this->isApplicationAdministrator()) {
            Yii::trace('checkAccess() - isApplicationAdministrator() returned true', 'application.components.RinkfinderWebUser');
            return true;
        }

        Yii::trace('checkAccess() - isApplicationAdministrator() returned false', 'application.components.RinkfinderWebUser');
        return parent::checkAccess($operation, $params, $allowCaching);
    }
    
    public function isSiteAdministrator($id = null) {
        Yii::trace('isSiteAdministrator()', 'application.components.RinkfinderWebUser');

        if($id === null) {
            if(Yii::app()->user->isGuest) {
                return false;
            }
            
            $id = $this->id;
        }
            
        // Check our cached roles!
        $roles = $this->roles;
            
        if(is_array($roles)) {
            foreach($roles as $role) {
                if($role == 'Administrator') {
                    return true;
                }
            }
            
            return false;
        }
        
        return Yii::app()->authManager->isAssigned(
                'Administrator', $id);
    }
    
    public function isApplicationAdministrator($id = null) {
        Yii::trace('isApplicationAdministrator()', 'application.components.RinkfinderWebUser');

        if(Yii::app()->user->isGuest) {
            return false;
        }

        if($id === null) {
            $id = $this->id;
        }
            
        // Check our cached roles!
        $roles = $this->roles;
        
        if(is_array($roles)) {
            foreach($roles as $role) {
                if($role == 'Administrator' || $role == 'ApplicationAdministrator') {
                    return true;
                }
            }
            
            return false;
        }
        
        return Yii::app()->db->createCommand()
        ->select('itemname')
        ->from(Yii::app()->authManager->assignmentTable)
        ->where('itemname IN (:itemname1, :itemname2) AND userid=:userid', array(
            ':itemname1' => 'Administrator',
            ':itemname2' => 'ApplicationAdministrator',
            ':userid' => $id))
        ->queryScalar() !== false;
    }
    
    public function isArenaManager($id = null) {
        Yii::trace('isArenaManager()', 'application.components.RinkfinderWebUser');

        if(Yii::app()->user->isGuest) {
            return false;
        }

        if($id === null) {
            $id = $this->id;
        }
            
        // Check our cached roles!
        $roles = $this->roles;
        
        if(is_array($roles)) {
            foreach($roles as $role) {
                if($role == 'Administrator' ||
                        $role == 'ApplicationAdministrator' ||
                        $role == 'Manager') {
                    return true;
                }
            }
            
            return false;
        }
        
        return Yii::app()->db->createCommand()
        ->select('itemname')
        ->from(Yii::app()->authManager->assignmentTable)
        ->where('itemname IN (:itemname1, :itemname2, :itemname3) AND userid=:userid', array(
            ':itemname1' => 'Administrator',
            ':itemname2' => 'ApplicationAdministrator',
            ':itemname3' => 'Manager',
            ':userid' => $id))
        ->queryScalar() !== false;
    }
    
    public function isRestrictedArenaManager($id = null) {
        Yii::trace('isRestrictedArenaManager()', 'application.components.RinkfinderWebUser');

        if(Yii::app()->user->isGuest) {
            return false;
        }

        if($id === null) {
            $id = $this->id;
        }
            
        // Check our cached roles!
        $roles = $this->roles;
        
        if(is_array($roles)) {
            foreach($roles as $role) {
                if($role == 'Administrator' ||
                        $role == 'ApplicationAdministrator' ||
                        $role == 'Manager' ||
                        $role == 'RestrictedManager') {
                    return true;
                }
            }
            
            return false;
        }
        
        return Yii::app()->db->createCommand()
        ->select('itemname')
        ->from(Yii::app()->authManager->assignmentTable)
        ->where('itemname IN (:itemname1, :itemname2, :itemname3, :itemname4) AND userid=:userid', array(
            ':itemname1' => 'Administrator',
            ':itemname2' => 'ApplicationAdministrator',
            ':itemname3' => 'Manager',
            ':itemname4' => 'RestrictedManager',
            ':userid' => $id))
        ->queryScalar() !== false;
    }
    
    public function isSiteUser($id = null) {
        Yii::trace('isSiteUser()', 'application.components.RinkfinderWebUser');

        if(Yii::app()->user->isGuest) {
            return false;
        }

        if($id === null) {
            $id = $this->id;
        }
            
        // Check our cached roles!
        $roles = $this->roles;
        
        if(is_array($roles)) {
            foreach($roles as $role) {
                if($role == 'Administrator' ||
                        $role == 'ApplicationAdministrator' ||
                        $role == 'Manager' ||
                        $role == 'RestrictedManager' ||
                        $role == 'User') {
                    return true;
                }
            }
            
            return false;
        }
        
        return Yii::app()->db->createCommand()
        ->select('itemname')
        ->from(Yii::app()->authManager->assignmentTable)
        ->where('itemname IN (:itemname1, :itemname2, :itemname3, :itemname4, itemname5) AND userid=:userid', array(
            ':itemname1' => 'Administrator',
            ':itemname2' => 'ApplicationAdministrator',
            ':itemname3' => 'Manager',
            ':itemname4' => 'RestrictedManager',
            ':itemname5' => 'User',
            ':userid' => $id))
        ->queryScalar() !== false;
    }
    
    // Load user model.
    protected function loadUser($id = null)
    {
        Yii::trace('loadUser()', 'application.components.RinkfinderWebUser');
        if($this->_model === null) {
	    if($id !== null) {
                $this->_model = User::model()->with('profile')->findByPk($id);
            }
        }

        return $this->_model;
    }
    
    /**
     * Logs in a user.
     *
     * The user identity information will be saved in storage that is
     * persistent during the user session. By default, the storage is simply
     * the session storage. If the duration parameter is greater than 0,
     * a cookie will be sent to prepare for cookie-based login in future.
     *
     * Note, you have to set {@link allowAutoLogin} to true
     * if you want to allow user to be authenticated based on the cookie information.
     *
     * @param IUserIdentity $identity the user identity (which should already be authenticated)
     * @param integer $duration number of seconds that the user can remain in logged-in status. Defaults to 0, meaning login till the user closes the browser.
     * If greater than 0, cookie-based login will be used. In this case, {@link allowAutoLogin}
     * must be set true, otherwise an exception will be thrown.
     * @return boolean whether the user is logged in
     */
    public function login($identity, $duration = 0)
    {
        if(parent::login($identity, $duration)) {
            $this->setFirstName($identity->getFirstName());
            $this->setLastName($identity->getLastName());
            $this->setFullName($identity->getFullName());
            $this->setEmail($identity->getEmail());
            $this->setPhone($identity->getPhone());
            $this->setRoles($identity->getRoles());
        }
        
        return !$this->getIsGuest();
    }

}
