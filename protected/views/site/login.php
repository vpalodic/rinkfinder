<?php
    /* @var $this SiteController */
    /* @var $model LoginForm */
    /* @var $form TbActiveForm  */

    $this->pageTitle = Yii::app()->name . ' - Login';
    $this->breadcrumbs = array(
        'Login'
    );
?>

<h2 class="sectionHeader">Login</h2>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<p>Please fill out the following form with your login credentials:</p>

<div class="form">
    <?php
        $form = $this->beginWidget(
                'bootstrap.widgets.TbActiveForm',
                array(
                    'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
                    'id' => 'login-form',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'clientOptions' => array('validateOnSubmit' => true),
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
                        'maxlength' => 128,
                    )
            );
        ?>
        <?php
            echo $form->passwordFieldControlGroup(
                    $model,
                    'password',
                    array(
                        'span' => 5,
                        'maxlength' => 48,
                    )
            );
        ?>
	<div class="control-group">
            <div class="controls">
		<!--<?php echo TbHtml::link("Register", array("site/register")); ?>
                    | -->
                <?php echo TbHtml::link("Activate Account", array("site/activateAccount")); ?>
                    |
                <?php echo TbHtml::link("Lost Password?", array("site/resetAccount")); ?>
            </div>
	</div>
        <div class="control-group">
            <div class="controls">
                <label for="LoginForm_rememberMe">
                     Remember me?
                </label>
                <div class="make-switch switch-large" data-on-label="Yes" data-off-label="No" data-on="primary" data-off="warning" id="LoginForm_rememberMe_switch">
                    <input id="LoginForm_rememberMe" type="checkbox" value="0" name="LoginForm[rememberMe]">
                </div>
                <p id="LoginForm_rememberMe_em_" style="display:none" class="help-block">
                </p>
            </div>
        </div>
    </fieldset>
    <?php
        echo TbHtml::formActions(
                TbHtml::submitButton(
                        'Login',
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
    var $switch = $("#LoginForm_rememberMe_switch");
    var $checkbox = $("#LoginForm_rememberMe");
    
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