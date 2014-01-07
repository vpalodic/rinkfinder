<?php

/**
 * This is the model base class Project Tracker.
 * All ActiveRecord based models should extend this class.
 */
abstract class RinkfinderActiveRecord extends CActiveRecord
{
    /**
     * Prepares create_user_id and update_user_id attributes before
     * saving.
     */
    protected function beforeSave()
    {
        if(null !== Yii::app()->user) {
            $id = Yii::app()->user->id;
        } else {
            $id = 1;
        }
        
        if($this->isNewRecord) {
            $this->create_user_id = $id;
        }
        
        $this->update_user_id = $id;
        
        return parent::beforeSave();
    }
    
	/**
	 * Returns the list of behaviors to attach to this class.
     * This base class attaches the CTimestampBehavior
	 * @return array an array of configured behaviors for the class.
	 */
	public function behaviors()
	{
		return array('CTimestampBehavior' => array('class' => 'zii.behaviors.CTimestampBehavior',
                                                   'createAttribute' => 'create_time',
                                                   'updateAttribute' => 'update_time',
                                                   'setUpdateOnCreate' => true,
                                                   'timestampExpression' => new CDbExpression('NOW()'),
                                                  ),
                    );
	}
}