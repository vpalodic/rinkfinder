<?php
/* @var $this ReservationRequestController */
/* @var $model ReservationRequest */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

                    <?php echo $form->textFieldControlGroup($model,'id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'event_id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'requester_id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'acknowledger_id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'acknowledged_on',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'accepter_id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'accepted_on',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'rejector_id',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'rejected_on',array('span'=>5)); ?>

                    <?php echo $form->textFieldControlGroup($model,'rejected_reason',array('span'=>5,'maxlength'=>255)); ?>

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