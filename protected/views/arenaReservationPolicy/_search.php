<?php
/* @var $this ArenaReservationPolicyController */
/* @var $model ArenaReservationPolicy */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

                    <?php echo $form->textFieldControlGroup($model,'id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'arena_id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'days',array('span'=>5,'maxlength'=>64)); ?>

                    <?php echo $form->textFieldControlGroup($model,'cutoff_time',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'cutoff_day',array('span'=>5,'maxlength'=>16)); ?>

                    <?php echo $form->textAreaControlGroup($model,'notes',array('rows'=>6,'span'=>8)); ?>

                    <?php echo $form->textFieldControlGroup($model,'event_type_id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'lock_version',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'created_by_id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'created_on',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'updated_by_id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'updated_on',array('span'=>5)); ?>

        <div class="form-actions">
        <?php echo TbHtml::submitButton('Search',  array('color' => TbHtml::BUTTON_COLOR_PRIMARY,));?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->