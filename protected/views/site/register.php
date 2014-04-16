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
        <div class="control-group">
            <div class="controls">
                <label for="User_acceptTerms" class="required">
                     <span class="required">*</span> I have read and agree to the
                     <a class="no-ajaxy" href="#termsOfUseModal" role="button" data-toggle="modal">
                         Terms of Use
                     </a> of this site, the
                     <a class="no-ajaxy" href="#privacyPolicyModal">
                         Privacy Policy
                     </a> of this site, and that I am thirteen (13) years of age or older.
                </label>
                <div class="make-switch switch-large" data-on-label="Yes" data-off-label="No" data-on="primary" data-off="warning" id="User_acceptTerms_switch">
                    <input id="User_acceptTerms" type="checkbox" value="0" name="User[acceptTerms]">
                </div>
                <p id="User_acceptTerms_em_" style="display:none" class="help-block">
                </p>
            </div>
        </div>
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
<!-- Terms of use Instructions Modal Dialog -->
<div id="termsOfUseModal" class="modal hide fade container" tabindex="-1" 
     role="dialog" aria-labelledby="termsOfUseModalLabel" 
     aria-hidden="true" data-backdrop="static" data-max-height="500" >
  <div id="termsOfUseModalHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="termsOfUseModalLabel">Terms of Use</h3>
  </div>
  <div id="termsOfUseModalBody" class="modal-body">
      <?php $this->renderPartial('pages/terms_of_use', array('noTitle' => true)); ?>
  </div>
  <div id="termsOfUseModalFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Mapping Instructions Modal Dialog -->
<!-- Settings Instructions Modal Dialog -->
<div id="privacyPolicyModal" class="modal hide fade container" tabindex="-1" 
     role="dialog" aria-labelledby="privacyPolicyModalLabel" 
     aria-hidden="true" data-backdrop="static" data-max-height="500" >
  <div id="privacyPolicyModalHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="privacyPolicyModalLabel">Privacy Policy</h3>
  </div>
  <div id="privacyPolicyModalBody" class="modal-body">
      <?php $this->renderPartial('pages/privacy_policy', array('noTitle' => true)); ?>
  </div>
  <div id="privacyPolicyModalFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Settings Instructions Modal Dialog -->
<?php endif; ?>
<script type="text/javascript">
        $('#User_acceptTerms').on('destroyed', function () {
            // We have been closed, so clean everything up!!!
            console.log("Registration Destroyed!");
            $(".bootstrap-datetimepicker-widget").each(function () {
                $(this).remove();
            });
        });
        
    var $switch = $("#User_acceptTerms_switch");
    
    if ($switch.hasClass('make-switch'))
    {
        if(typeof $('.make-switch')['bootstrapSwitch'] !== "undefined")
        {
            $('.make-switch')['bootstrapSwitch']();
        }
    }
</script>