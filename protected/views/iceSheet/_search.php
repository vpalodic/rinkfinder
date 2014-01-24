<?php
/* @var $this IceSheetController */
/* @var $model IceSheet */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

                    <?php echo $form->textFieldControlGroup($model,'id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'arena_id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'external_id',array('span'=>5,'maxlength'=>32)); ?>

                    <?php echo $form->textFieldControlGroup($model,'name',array('span'=>5,'maxlength'=>128)); ?>

                    <?php echo $form->textAreaControlGroup($model,'description',array('rows'=>6,'span'=>8)); ?>

                    <?php echo $form->textFieldControlGroup($model,'tags',array('span'=>5,'maxlength'=>1024)); ?>

                    <?php echo $form->textFieldControlGroup($model,'length',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'width',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'radius',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'seating',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'base_id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'refrigeration_id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'resurfacer_id',array('span'=>5)); ?>

                    <?php echo $form->textAreaControlGroup($model,'notes',array('rows'=>6,'span'=>8)); ?>

                    <?php echo $form->textFieldControlGroup($model,'type_id',array('span'=>5)); ?>

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