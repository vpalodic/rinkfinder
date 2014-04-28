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
                        echo '<div class="control-group">'
                        . '<label class="control-label" for="Profile_birth_day">'
                                . 'Birthday'
                                . '</label>'
                                . '<div class="controls">'
                                . '<div id="Profile_birth_day_picker" class="input-append">'
                                . '<input data-format="MM/dd/yyyy" value="" id="Profile_birth_day" name="Profile[birth_day]" type="text" />'
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
                                '<input class="span5" id="Profile_phone" name="Profile[phone]" type="text" maxlength="14" />' .
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
                    <input id="User_acceptTerms" type="checkbox" name="User[acceptTerms]">
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
</div>
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
</div>
<?php endif; ?>
<script type="text/javascript">
$(document).ready(function () {
    $('#User_acceptTerms').on('destroyed', function () {
        // We have been closed, so clean everything up!!!
        console.log("Registration Destroyed!");
        $(".bootstrap-datetimepicker-widget").each(function () {
            $(this).remove();
        });
    });
        
    var $switch = $("#User_acceptTerms_switch");
    var $checkbox = $("#User_acceptTerms");
    
    if ($switch.hasClass('has-switch') === false)
    {
        if(typeof $('.make-switch')['bootstrapSwitch'] !== "undefined")
        {
            $switch['bootstrapSwitch']();
            $switch.removeClass('make-switch');
            
            $switch.on('change', function (e) {
                if ($checkbox.is(':checked'))
                {
                    $checkbox.val(1);
                }
                else
                {
                    $checkbox.val(0)
                }
            });
        }
    }
    
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