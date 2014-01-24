<?php
/* @var $this TagController */
/* @var $model Tag */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

                    <?php echo $form->textFieldControlGroup($model,'id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'name',array('span'=>5,'maxlength'=>128)); ?>

                    <?php echo $form->textFieldControlGroup($model,'frequency',array('span'=>5)); ?>

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