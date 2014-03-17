<?php
/* @var $this ProfileController */
/* @var $model Profile */
/* @var $form TbActiveForm */
?>

<div class="form">

    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'profile-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

    <p class="help-block">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>
<?php
            $modelFields = $model->getFields();

            if($modelFields) {
                foreach($modelFields as $field) {
                    if($field->widgetEdit($model)) {
                        echo '<div class="control-group">';
                        echo $form->labelEx(
                                $model,
                                $field->varname,
                                array(
                                    'class' => 'control-label',
                                )
                             );
                        echo '<div class="controls">';
                        echo $field->widgetEdit($model);
                        echo $form->error($model, $field->varname);
                        echo '</div>';
                        echo '</div>';
                    } elseif($field->range) {
                        echo $form->dropDownListControlGroup(
                                $model,
                                $field->varname,
                                Profile::range($field->range),
                                array(
                                    'span' => 5
                                )
                            );
                    } elseif($field->varname == 'lock_version') {
                        echo CHtml::activeHiddenField($model, $field->varname);
                    } elseif($field->varname == 'birth_day') {
                        $widget = $this->widget(
                                'yiiwheels.widgets.datetimepicker.WhDateTimePicker',
                                array(
                                    'model' => $model,
                                    'attribute' => $field->varname,
                                    'format' => 'MM/dd/yyyy',
                                    'pluginOptions' => array(
                                        'pickTime' => false,
                                        'maskInput' => true,                                        
                                    ),
                                    'htmlOptions' => array(
                                        'data-format' => 'MM/dd/yyyy',
//                                        'class' => 'span5',
                                    ),
                                ),
                                true
                        );
                        
                        echo '<div class="control-group">';
                        echo $form->labelEx(
                                $model,
                                $field->varname,
                                array(
                                    'class' => 'control-label',
                                )
                             );
                        echo '<div class="controls">';
                        echo $widget;
                        echo $form->error($model, $field->varname);
                        echo '</div>';
                        echo '</div>';
                    } elseif($field->varname == "phone") {
                        $widget = $this->widget(
                                'yiiwheels.widgets.maskinput.WhMaskInput',
                                array(
                                    'model' => $model,
                                    'attribute' => $field->varname,
                                    'mask' => '(000) 000-0000',
                                    'htmlOptions' => array(
                                        'class' => 'span5',
                                    ),
                                ),
                                true
                        );
                        
                        echo '<div class="control-group">';
                        echo $form->labelEx(
                                $model,
                                $field->varname,
                                array(
                                    'class' => 'control-label',
                                )
                             );
                        echo '<div class="controls">';
                        echo $widget;
                        echo $form->error($model, $field->varname);
                        echo '</div>';
                        echo '</div>';
                    }  elseif($field->varname == "state") {
                        $widget = $this->widget(
                                'yiiwheels.widgets.formhelpers.WhStates',
                                array(
                                    'model' => $model,
                                    'attribute' => $field->varname,
                                    'pluginOptions' => array(
                                        'country' => 'US',
                                        'flags' => 'true',
                                    ),
                                    'useHelperSelectBox' => false,
                                    'htmlOptions' => array(
                                        'class' => 'span5',
                                        'prompt' => 'Select a state',
                                    ),                                    
                                ),
                                true
                        );
                        
                        echo '<div class="control-group">';
                        echo $form->labelEx(
                                $model,
                                $field->varname,
                                array(
                                    'class' => 'control-label',
                                )
                             );
                        echo '<div class="controls">';
                        echo $widget;
                        echo $form->error($model, $field->varname);
                        echo '</div>';
                        echo '</div>';
                    } elseif($field->field_type == "TEXT") {
                        echo $form->textAreaControlGroup(
                                $model,
                                $field->varname,
                                array(
                                    'rows' => 6,
                                    'span' => 5
                                )
                            );
                    } else {
                        echo $form->textFieldControlGroup(
                                $model,
                                $field->varname,
                                array(
                                    'size' => 60,
                                    'maxlength' => (($field->field_size) ? $field->field_size : 255),
                                    'span' => 5
                                )
                            );
                    }
                }
            }
?>
        <div class="form-actions">
        <?php echo TbHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array(
		    'color'=>TbHtml::BUTTON_COLOR_PRIMARY,
		    'size'=>TbHtml::BUTTON_SIZE_LARGE,
		)); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->