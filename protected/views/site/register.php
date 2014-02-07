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
            <div class="control-group">
                <div class="controls">
                    <span class="hint">
                        By clicking on the "Register" button below, you agree you have read 
                        and agree to both the
                        <?php
                            echo CHtml::link(
                                    'Terms of Use',
                                    array(
                                        'site/page', 'view' => 'terms_of_use'
                                    ),
                                    array(
                                        'id' => 'terms_of_use'
                                    )
                            );
                        ?>
                        and the 
                        <?php
                            echo CHtml::link(
                                    'Privacy Policy',
                                    array(
                                        'site/page', 'view' => 'privacy_policy'
                                    ),
                                    array(
                                        'id' => 'privacy_policy'
                                    )
                            );
                        ?> 
                        of this site and that you are over the age of thirteen (13). 
                    </span>
                </div>
            </div>
        <?php endif; ?>
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
<?php
    $this->widget(
            'ext.magnific-popup.EMagnificPopup',
            array(
                'target' => '#terms_of_use',
                'type' => 'iframe',
            )
    );
?>
<?php
    $this->widget(
            'ext.magnific-popup.EMagnificPopup',
            array(
                'target' => '#privacy_policy',
                'type' => 'iframe',
            )
    );
?>
<?php endif; ?>