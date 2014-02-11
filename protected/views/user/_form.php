<?php
    /* @var $this UserController */
    /* @var $model User */
    /* @var $profile Profile */
    /* @var $form TbActiveForm */
?>

<div class="form">
    <?php
        $form = $this->beginWidget(
            'bootstrap.widgets.TbActiveForm',
            array(
                'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
                'id' => 'user-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'clientOptions' => array(
                    'validateOnSubmit' => true
                ),
                'htmlOptions' => array(
                    'enctype' => 'multipart/form-data'
                ),
            )
        );
    ?>

    <fieldset>
        <p class="note">
            <legend class="help-block">Fields with <span class="required">*</span> are required.</legend>
        </p>
    	<?php
            echo $form->errorSummary($model);
        ?>
        <h3 class="sectionSubHeader">
            Account Information:
        </h3>
        <?php
            echo $form->textFieldControlGroup(
                    $model,
                    'username',
                    array(
                        'span' => 5,
                        'maxlength' => 32,
                    )
            );
        ?>
        <?php
            echo $form->passwordFieldControlGroup(
                    $model,
                    'passwordSave',
                    array(
                        'span' => 5,
                        'maxlength' => 48,
                    )
            );
        ?>
        <?php
            echo $form->passwordFieldControlGroup(
                    $model,
                    'passwordRepeat',
                    array(
                        'span' => 5,
                        'maxlength' => 48,
                    )
            );
        ?>
        <?php
            echo $form->emailFieldControlGroup(
                    $model,
                    'email',
                    array(
                        'span' => 5,
                        'maxlength' => 128,
                        'rel' => 'tooltip',
                        'title' => 'Please enter your e-mail address'
                    )
            );
        ?>
        <?php
            echo $form->dropDownListControlGroup(
                    $model,
                    'status_id',
                    $model->itemAlias('UserStatus'),
                    array(
                        'span' => 5,
                        'rel' => 'tooltip',
                        'title' => 'Please select a status for the account'
                    )
            );
        ?>
        <h3 class="sectionSubHeader">
            Profile Information:
        </h3>
        <?php
            $profileFields = $profile->getFields();

            if($profileFields) {
                foreach($profileFields as $field) {
                    if($field->widgetEdit($profile)) {
                        echo '<div class="control-group">';
                        echo $form->labelEx(
                                $profile,
                                $field->varname,
                                array(
                                    'htmlOptions' => array(
                                        'class' => 'control-label',
                                    )
                                )
                             );
                        echo '<div class="controls">';
                        echo $field->widgetEdit($profile);
                        echo $form->error($profile, $field->varname);
                        echo '</div>';
                        echo '</div>';
                    } elseif($field->range) {
                        echo $form->dropDownListControlGroup(
                                $profile,
                                $field->varname,
                                Profile::range($field->range),
                                array(
                                    'span' => 5
                                )
                            );
                    } elseif($field->varname == "phone") {
                        $widget = $this->widget(
                                'yiiwheels.widgets.maskinput.WhMaskInput',
                                array(
                                    'model' => $profile,
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
                                $profile,
                                $field->varname,
                                array(
                                    'class' => 'control-label',
                                )
                             );
                        echo '<div class="controls">';
                        echo $widget;
                        echo $form->error($profile, $field->varname);
                        echo '</div>';
                        echo '</div>';
                    }  elseif($field->varname == "state") {
                        $widget = $this->widget(
                                'yiiwheels.widgets.formhelpers.WhStates',
                                array(
                                    'model' => $profile,
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
                                $profile,
                                $field->varname,
                                array(
                                    'class' => 'control-label',
                                )
                             );
                        echo '<div class="controls">';
                        echo $widget;
                        echo $form->error($profile, $field->varname);
                        echo '</div>';
                        echo '</div>';
                    } elseif($field->field_type == "TEXT") {
                        echo $form->textAreaControlGroup(
                                $profile,
                                $field->varname,
                                array(
                                    'rows' => 6,
                                    'span' => 5
                                )
                            );
                    } else {
                        echo $form->textFieldControlGroup(
                                $profile,
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
    </fieldset>
    <?php
        echo TbHtml::formActions(
                TbHtml::submitButton(
                        $model->isNewRecord ? 'Create' : 'Save',
                        array(
                            'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                            'size' => TbHtml::BUTTON_SIZE_LARGE,
                        )
                )
            );
    ?>
    <?php $this->endWidget(); ?>
</div><!-- form -->
