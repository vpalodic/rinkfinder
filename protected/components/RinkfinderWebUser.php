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
     * Performs access check for this user.
     * @param string $operation the name of the operation that need access check.
     * @param array $params name-value pairs that would be passed to business rules associated
     * with the tasks and roles assigned to the user.
     * @param boolean $allowCaching whether to allow caching the result of access check.
     * @return boolean whether the operations can be performed by this user.
     */
    public function checkAccess($operation, $params = array(), $allowCaching = true)
    {
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
}
