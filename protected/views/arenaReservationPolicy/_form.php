<?php
/* @var $this ArenaReservationPolicyController */
/* @var $model ArenaReservationPolicy */
/* @var $form TbActiveForm */
?>

<div class="form">

    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'arena-reservation-policy-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

    <p class="help-block">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

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
        <?php echo TbHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array(
		    'color'=>TbHtml::BUTTON_COLOR_PRIMARY,
		    'size'=>TbHtml::BUTTON_SIZE_LARGE,
		)); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->