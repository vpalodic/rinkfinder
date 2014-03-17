<?php
    /* @var $this SiteController */
    /* @var $model User */
    /* @var $profile Profile */
    /* @var $registered bool */
    /* @var $form TbActiveForm  */

    $this->pageTitle = Yii::app()->name . ' - Registration';
    $this->breadcrumbs = array(
        'Login' => array('site/login'),
        'Registration',
    );
?>

<h2 class="sectionHeader">Registration</h2>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<?php if(!isset($registered) || $registered == false) : ?>

<div class="form">
    <?php
        $form = $this->beginWidget(
            'bootstrap.widgets.TbActiveForm',
            array(
                'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
                'id' => 'registration-form',
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
                        $widget = $this->widget(
                                'yiiwheels.widgets.datetimepicker.WhDateTimePicker',
                                array(
                                    'model' => $profile,
                                    'attribute' => $field->varname,
                                    'format' => 'MM/dd/yyyy',
                                    'pluginOptions' => array(
                                        'pickTime' => false,
                                        'maskInput' => true,                                        
                                    ),
                                    'htmlOptions' => array(
                                        'data-format' => 'MM/dd/yyyy',
                                        'value' => '',
//                                        'class' => 'span5',
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
	<?php if(Yii::app()->doCaptcha('registration')): ?>
            <div class="control-group">
                <div class="controls">
                    <?php $this->widget('CCaptcha'); ?>
                </div>
            </div>                
            <?php
                echo $form->textFieldControlGroup(
                        $model,
                        'verifyCode',
                        array(
                            'span' => 5,
                        )
                );
            ?>
            <div class="control-group">
                <div class="controls">
                    <span class="hint">
                        Please enter the letters as they are shown in the image above.
                        Letters are not case-sensitive.
                    </span>
                </div>
            </div>
        <?php endif; ?>
            <?php
                $widget = $this->widget(
                        'yiiwheels.widgets.switch.WhSwitch',
                        array(
                            'model' => $model,
                            'attribute' => 'acceptTerms',
                            'onLabel' => 'Yes',
                            'offLabel' => 'No',
                            'size' => 'large',
                            'offColor' => 'warning',
                            'htmlOptions' => array(
                            ),
                        ),
                        true
                );
            
                echo '<div class="control-group">';
                echo '<div class="controls">';
                echo $form->labelEx(
                        $model,
                        'acceptTerms',
                        array(
                            )
                    );
                echo $widget;
                echo $form->error($model, 'acceptTerms');
                echo '</div>';
                echo '</div>';
            ?>
    </fieldset>
    <?php
        echo TbHtml::formActions(
                TbHtml::submitButton(
                        'Register',
                        array(
                            'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                            'size' => TbHtml::BUTTON_SIZE_LARGE,
                        )
                )
            );
    ?>
    <?php $this->endWidget(); ?>
</div><!-- form -->
<div id="terms-of-use" class="mfp-hide" style="position: relative;background: #FFF;padding: 20px;width: auto;max-width: 70%;margin: 20px auto;">
  <?php $this->renderPartial('pages/terms_of_use', array('noTitle' => true)); ?>
</div>
<div id="privacy-policy" class="mfp-hide" style="position: relative;background: #FFF;padding: 20px;width: auto;max-width: 70%;margin: 20px auto;">
  <?php $this->renderPartial('pages/privacy_policy', array('noTitle' => true)); ?>
</div>
<?php
    $this->widget(
            'ext.magnific-popup.EMagnificPopup',
            array(
                'target' => '.open-popup-link',
                'type' => 'inline',
            )
    );
?>
<?php endif; ?>
