<?php
/* @var $this ArenaController */
/* @var $model Arena */
/* @var $form TbActiveForm */
?>

<div class="form">

    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'arena-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

    <p class="help-block">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

            <?php echo $form->textFieldControlGroup($model,'external_id',array('span'=>5,'maxlength'=>32)); ?>

            <?php echo $form->textFieldControlGroup($model,'name',array('span'=>5,'maxlength'=>128)); ?>

            <?php echo $form->textAreaControlGroup($model,'description',array('rows'=>6,'span'=>8)); ?>

            <?php echo $form->textFieldControlGroup($model,'tags',array('span'=>5,'maxlength'=>255)); ?>

            <?php echo $form->textFieldControlGroup($model,'address_line1',array('span'=>5,'maxlength'=>128)); ?>

            <?php echo $form->textFieldControlGroup($model,'address_line2',array('span'=>5,'maxlength'=>128)); ?>

            <?php echo $form->textFieldControlGroup($model,'city',array('span'=>5,'maxlength'=>128)); ?>

            <?php echo $form->textFieldControlGroup($model,'state',array('span'=>5,'maxlength'=>2)); ?>

            <?php echo $form->textFieldControlGroup($model,'zip',array('span'=>5,'maxlength'=>5)); ?>

            <?php echo $form->textFieldControlGroup($model,'lat',array('span'=>5)); ?>

            <?php echo $form->textFieldControlGroup($model,'lng',array('span'=>5)); ?>

            <?php echo $form->textFieldControlGroup($model,'phone',array('span'=>5,'maxlength'=>10)); ?>

            <?php echo $form->textFieldControlGroup($model,'ext',array('span'=>5,'maxlength'=>10)); ?>

            <?php echo $form->textFieldControlGroup($model,'fax',array('span'=>5,'maxlength'=>10)); ?>

            <?php echo $form->textFieldControlGroup($model,'fax_ext',array('span'=>5,'maxlength'=>10)); ?>

            <?php echo $form->textFieldControlGroup($model,'logo',array('span'=>5,'maxlength'=>511)); ?>

            <?php echo $form->textFieldControlGroup($model,'url',array('span'=>5,'maxlength'=>511)); ?>

            <?php echo $form->textAreaControlGroup($model,'notes',array('rows'=>6,'span'=>8)); ?>

            <?php echo $form->textFieldControlGroup($model,'status_id',array('span'=>5)); ?>

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