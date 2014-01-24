<?php
/* @var $this ArenaController */
/* @var $model Arena */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

                    <?php echo $form->textFieldControlGroup($model,'id',array('span'=>5)); ?>

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
        <?php echo TbHtml::submitButton('Search',  array('color' => TbHtml::BUTTON_COLOR_PRIMARY,));?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->