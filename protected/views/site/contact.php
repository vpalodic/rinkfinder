<?php
    /* @var $this SiteController */
    /* @var $model ContactForm */
    /* @var $form TbActiveForm */
    /* @var $contacted bool */

    $this->pageTitle = Yii::app()->name . ' - Contact Us';
    $this->breadcrumbs = array(
        'Contact'
    );
?>

<h2 class="sectionHeader">Contact Us</h2>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<?php if(!isset($contacted) || $contacted == false) : ?>

<p>
If you have business inquiries or other questions, please fill out the following form to contact us. Thank you.
</p>

<div class="form">
    <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
                                   array('layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
                                         'id' => 'contact-form',
                                         'enableAjaxValidation' => false,
                                         'enableClientValidation' => true,
                                         'clientOptions' => array('validateOnSubmit' => true),
                                         'htmlOptions' => array('enctype' => 'multipart/form-data')
                                         ));
    ?>

    <fieldset>
        <p class="note">
            <legend class="help-block">Fields with <span class="required">*</span> are required.</legend>
        </p>
        <?php echo $form->errorSummary($model); ?>
        <?php
            $htmlOptions = array(
                        'span' => 5,
            );
            
            if(!Yii::app()->user->isGuest) {
                $htmlOptions['readonly'] = true;
            }
            
            echo $form->textFieldControlGroup(
                    $model,
                    'name',
                    $htmlOptions
            );
        ?>
        <?php
            $htmlOptions['maxlength'] = 128;
            
            echo $form->emailFieldControlGroup(
                    $model,
                    'email',
                    $htmlOptions
            );
        ?>
        <?php
            echo $form->textFieldControlGroup(
                    $model,
                    'subject',
                    array(
                        'maxlength' => 128,
                        'span' => 5,
                    )
            );
        ?>
        <?php
            echo $form->textAreaControlGroup(
                    $model,
                    'body',
                    array(
                        'rows' => 6,
                        'span' => 5,
                    )
            );
        ?>
        <div class="control-group">
            <div class="controls">
                <div class="make-switch switch-large" data-on-label="Yes" data-off-label="No" data-on="primary" data-off="warning" id="ContactForm_copyMe_switch">
                    <input id="ContactForm_copyMe" name="ContactForm[copyMe]" value="1" checked type="checkbox">
                </div>
                <label for="ContactForm_copyMe">Send me a copy?</label>
                <p id="ContactForm_copyMe_em_" style="display:none" class="help-block"></p>
            </div>
        </div>
	<?php if(Yii::app()->doCaptcha('contact')): ?>
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
    </fieldset>
    <?php
        echo TbHtml::formActions(
                TbHtml::submitButton(
                        'Submit',
                        array(
                            'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                            'size' => TbHtml::BUTTON_SIZE_LARGE,
                        )
                )
            );
    ?>
    <?php $this->endWidget(); ?>
</div><!-- form -->
<?php endif; ?>
<script type="text/javascript">
    var $switch = $("#ContactForm_copyMe_switch");
    
    if ($switch.hasClass('make-switch'))
    {
        if(typeof $('.make-switch')['bootstrapSwitch'] !== "undefined")
        {
            $('.make-switch')['bootstrapSwitch']();
        }
    }
    
    jQuery('body').popover({'selector':'a[rel=popover]'});
    jQuery('body').tooltip({'selector':'a[rel=tooltip]'});

        jQuery('#yw1').after("<a id=\"yw1_button\" href=\"\/rinkfinder\/site\/captcha?refresh=1\">Get a new code<\/a>");
        
        jQuery(document).on('click', '#yw1_button', function(){
            jQuery.ajax({
                url: "\/rinkfinder\/site\/captcha?refresh=1",
                dataType: 'json',
                cache: false,
                success: function(data) {
                    jQuery('#yw1').attr('src', data['url']);
                    jQuery('body').data('captcha.hash', [data['hash1'], data['hash2']]);
                }
            });
            return false;
        });

        jQuery('#contact-form').yiiactiveform({
            'validateOnSubmit':true,
            'attributes':[
                {
                    'id':'ContactForm_name',
                    'inputID':'ContactForm_name',
                    'errorID':'ContactForm_name_em_',
                    'model':'ContactForm',
                    'name':'name',
                    'enableAjaxValidation':false,
                    'inputContainer':'div.control-group',
                    'clientValidation':function(value, messages, attribute) {
                        if(jQuery.trim(value) == '') {
                            messages.push("Your Name cannot be blank.");
                        }
                    }
                },
                {
                    'id':'ContactForm_email',
                    'inputID':'ContactForm_email',
                    'errorID':'ContactForm_email_em_',
                    'model':'ContactForm',
                    'name':'email',
                    'enableAjaxValidation':false,
                    'inputContainer':'div.control-group',
                    'clientValidation':function(value, messages, attribute) {
                        if(jQuery.trim(value) == '') {
                            messages.push("Your E-mail Address cannot be blank.");
                        }
                        
                        if(jQuery.trim(value) != '' && !value.match(/^[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/)) {
                            messages.push("Your E-mail Address is not a valid email address.");
                        }
                    }
                },
                {
                    'id':'ContactForm_subject',
                    'inputID':'ContactForm_subject',
                    'errorID':'ContactForm_subject_em_',
                    'model':'ContactForm',
                    'name':'subject',
                    'enableAjaxValidation':false,
                    'inputContainer':'div.control-group',
                    'clientValidation':function(value, messages, attribute) {
                        if(jQuery.trim(value) == '') {
                            messages.push("Subject cannot be blank.");
                        }
                    }
                },
                {
                    'id':'ContactForm_body',
                    'inputID':'ContactForm_body',
                    'errorID':'ContactForm_body_em_',
                    'model':'ContactForm',
                    'name':'body',
                    'enableAjaxValidation':false,
                    'inputContainer':'div.control-group',
                    'clientValidation':function(value, messages, attribute) {
                        if(jQuery.trim(value) == '') {
                            messages.push("Message cannot be blank.");
                        }
                    }
                },
                {
                    'id':'ContactForm_verifyCode',
                    'inputID':'ContactForm_verifyCode',
                    'errorID':'ContactForm_verifyCode_em_',
                    'model':'ContactForm',
                    'name':'verifyCode',
                    'enableAjaxValidation':false,
                    'inputContainer':'div.control-group',
                    'clientValidation':function(value, messages, attribute) {
                        var hash = jQuery('body').data('captcha.hash');
                        
                        if (hash == null)
                            hash = 641;
                        else
                            hash = hash[1];
                        
                        for (var i = value.length - 1, h = 0; i >= 0; --i)
                            h+=value.toLowerCase().charCodeAt(i);
                        
                        if (h != hash) {
                            messages.push("The verification code is incorrect.");
                        }
                    }
                }
            ],
            'summaryID':'contact-form_es_',
            'errorCss':'error'
        });

</script>