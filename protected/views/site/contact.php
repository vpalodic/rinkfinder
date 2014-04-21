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
$(document).ready(function () {
    var $switch = $("#ContactForm_copyMe_switch");
    var $checkbox = $("#ContactForm_copyMe");
    
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
});
</script>