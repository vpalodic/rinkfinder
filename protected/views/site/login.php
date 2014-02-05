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
		<?php echo TbHtml::link("Register", array("site/register")); ?>
                    | 
                <?php echo TbHtml::link("Activate Account", array("site/activateAccount")); ?>
                    |
                <?php echo TbHtml::link("Lost Password?", array("site/resetAccount")); ?>
            </div>
	</div>
        <?php
           $widget = $this->widget(
                    'yiiwheels.widgets.switch.WhSwitch',
                    array(
                        'model' => $model,
                        'attribute' => 'rememberMe',
                        'onLabel' => 'Yes',
                        'offLabel' => 'No',
                        'textLabel' => $model->getAttributeLabel('rememberMe'),
                        'size' => 'large',
                        'offColor' => 'warning',
                        'htmlOptions' => array(
                            'class' => 'span5',
                        ),
                    ),
                    true
            );
            
            echo '<div class="control-group">';
            echo '<div class="controls">';
/*            echo TbHtml::tag(
                    'label',
                    array(
                        'for' => 'LoginForm_rememberMe',
                        'class' => 'control-label',
                    ),
                    $widget . ' ' . $model->getAttributeLabel('rememberMe'));*/
            echo $widget;
            echo $form->labelEx(
                    $model,
                    'rememberMe',
                    array(
//                        'class' => 'control-label',
                        )
                    );
            echo $form->error($model, 'rememberMe');
            echo '</div>';
            echo '</div>';
         ?>
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
