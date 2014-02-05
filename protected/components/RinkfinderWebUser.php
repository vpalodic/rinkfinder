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
    private $_model;

    /**
     * Return first name of the user.
     * access it by Yii::app()->user->firstName
     * @return string The user's first name or ''
     */
    public function getFirstName(){
	$user = $this->loadUser(Yii::app()->user->id);
        $firstName = ($user !== null) ? $user->firstName : '';
	return $firstName;
    }
    
    /**
     * Return last name of the user.
     * access it by Yii::app()->user->lastName
     * @return string The user's last name or ''
     */
    public function getLastName(){
	$user = $this->loadUser(Yii::app()->user->id);
        $lastName = ($user !== null) ? $user->lastName : '';
	return $lastName;
    }
    
    /**
     * Return full name of the user.
     * access it by Yii::app()->user->fullName
     * @return string The user's full name or ''
     */
    public function getFullName(){
	$user = $this->loadUser(Yii::app()->user->id);
        $fullName = ($user !== null) ? $user->fullName : '';
	return $fullName;
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
        // Site administrator can perform every action!
        if($this->isSiteAdministrator()) {
            return true;
        }

        return parent::checkAccess($operation, $params, $allowCaching);
    }
    
    public function isSiteAdministrator($id = null) {
        return Yii::app()->authManager->isAssigned(
                'Administrator',
                ($id ? $id : $this->id));
    }
    
    public function isApplicationAdministrator($id = null) {
        return Yii::app()->authManager->isAssigned(
                'ApplicationAdministrator',
                ($id ? $id : $this->id));
    }
    
    public function isArenaManager($id = null) {
        return Yii::app()->authManager->isAssigned(
                'Manager',
                ($id ? $id : $this->id));
    }
    
    public function isRestrictedArenaManager($id = null) {
        return Yii::app()->authManager->isAssigned(
                'RestrictedManager',
                ($id ? $id : $this->id));
    }
    
    public function isSiteUser($id = null) {
        return Yii::app()->authManager->isAssigned(
                'User',
                ($id ? $id : $this->id));
    }
    
    // Load user model.
    protected function loadUser($id = null)
    {
        if($this->_model === null) {
	    if($id !== null) {
                $this->_model = User::model()->with('profile')->findByPk($id);
            }
        }

        return $this->_model;
    }
}
