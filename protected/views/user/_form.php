<?php
    /* @var $this UserController */
    /* @var $model User */
    /* @var $profile Profile */
    /* @var $arena Arena */
    /* @var $role string */
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
            if ($model->isNewRecord) {
                echo $form->passwordFieldControlGroup(
                        $model,
                        'passwordSave',
                        array(
                            'span' => 5,
                            'maxlength' => 48,
                        )
                );
                
                echo $form->passwordFieldControlGroup(
                        $model,
                        'passwordRepeat',
                        array(
                            'span' => 5,
                            'maxlength' => 48,
                        )
                );
            } else {
                echo '<div class="control-group">'
                . '<label class="control-label" for="User_password">'
                        . 'Password'
                        . '</label>'
                        . '<div class="controls">'
                        . '<div id="User_password">'
                        . '<i class="fa fa-fw fa-pencil"></i> <a href="' . Yii::app()->createUrl('user/changePassword', array('id' => $model->id)) . '">Change Password</a>'
                        . '</div>'
                        . '</div></div>';                
            }
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
            if(Yii::app()->user->isArenaManager()) {
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
            }
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
                    } elseif($field->varname == 'birth_day') {
                        echo '<div class="control-group">'
                        . '<label class="control-label" for="Profile_birth_day">'
                                . 'Birthday'
                                . '</label>'
                                . '<div class="controls">'
                                . '<div id="Profile_birth_day_picker" class="input-append">'
                                . '<input data-format="MM/dd/yyyy" value="' . $profile->birth_day . '" id="Profile_birth_day" name="Profile[birth_day]" type="text" />'
                                . '<span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>'
                                . '</div>'
                                . '<p id="Profile_birth_day_em_" style="display:none" class="help-block"></p>'
                                . '</div></div>';
                    } elseif($field->varname == "phone") {
                        echo '<div class="control-group">' .
                            '<label class="control-label required" for="Profile_phone">' .
                                'Phone Number <span class="required">*</span>' .
                            '</label>' .
                            '<div class="controls">' .
                                '<input class="span5" id="Profile_phone" name="Profile[phone]" value="' . $profile->phone . '" type="text" maxlength="14" />' .
                                '<p id="Profile_phone_em_" style="display:none" class="help-block"></p>' .
                            '</div>' .
                        '</div>';
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
<script type="text/javascript">
$(document).ready(function () {
    var $phone = $("#Profile_phone");
    
    $phone.inputmask({
        mask: "(999) 999-9999",
        autoUnmask: true,
        showTooltip: true
    });
    
    $.fn.datetimepicker.defaults = {
        maskInput: true,           // disables the text input mask
        pick12HourFormat: true,   // enables the 12-hour format time picker
        pickSeconds: false,         // disables seconds in the time picker
        startDate: moment().subtract('years', 115).startOf('day').toDate(),      // set a minimum date
        endDate: moment().subtract('years', 13).endOf('day').toDate()  // set a maximum date
    };
        
    $('#Profile_birth_day_picker').datetimepicker({
        pickDate: true,
        pickTime: false
    });
});
</script>