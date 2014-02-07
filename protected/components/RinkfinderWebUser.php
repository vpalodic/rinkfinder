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
        Yii::trace('getFirstName()', 'application.components.RinkfinderWebUser');
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
        Yii::trace('getLastName()', 'application.components.RinkfinderWebUser');
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
        Yii::trace('getFullName()', 'application.components.RinkfinderWebUser');
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
            $id = $this->id;
        }
        
        return Yii::app()->authManager->isAssigned(
                'Administrator', $id);
    }
    
    public function isApplicationAdministrator($id = null) {
        Yii::trace('isApplicationAdministrator()', 'application.components.RinkfinderWebUser');

        if($id === null) {
            $id = $this->id;
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

        if($id === null) {
            $id = $this->id;
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

        if($id === null) {
            $id = $this->id;
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

        if($id === null) {
            $id = $this->id;
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
}
