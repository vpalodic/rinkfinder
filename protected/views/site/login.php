<?php
    /* @var $this SiteController */
    /* @var $model LoginForm */
    /* @var $form TbActiveForm  */

    $this->pageTitle = Yii::app()->name . ' - Login';
    $this->breadcrumbs = array('Login',);
?>

<h2 class="sectionHeader">Login</h2>

<p>Please fill out the following form with your login credentials:</p>

<div class="form">
    <?php
        $form = $this->beginWidget(
                'bootstrap.widgets.TbActiveForm',
                array(
                    'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
                    'id' => 'login-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => true,
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
		<?php echo TbHtml::link("Register", array("site/register")); ?>
                    | 
                <?php echo TbHtml::link("Lost Password?", array("site/resetUser")); ?>
            </div>
	</div>
        <?php
            echo $form->checkBoxControlGroup(
                    $model,
                    'rememberMe',
                    array(
                        'span' => 5,
                    )
            );
        ?>
    </fieldset>
    <div class="form-actions">
        <?php
            echo TbHtml::submitButton(
                    'Login',
                    array(
                        'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                        'size' => TbHtml::BUTTON_SIZE_LARGE,
                    )
            );
        ?>
    </div>
    <?php $this->endWidget(); ?>
</div><!-- form -->
