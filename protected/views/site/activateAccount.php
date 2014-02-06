<?php
    /* @var $this SiteController */
    /* @var $email string */
    /* @var $user_key string */
    /* @var $resendEmail bool */
    /* @var $activated bool */
    /* @var $message string */
    /* @var $form TbActiveForm  */

    $this->pageTitle = Yii::app()->name . ' - Account Activation';
    $this->breadcrumbs = array(
        'Login' => array('site/login'),
        'Account Activation',
    );
?>

<h2 class="sectionHeader">Account Activation</h2>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<?php if(!isset($activated) || $activated == false) : ?>

<p>
    Please fill out the following form with your E-mail Address and User Key.
    <br />
    If you are having problems activating your account, select to have the activation e-mail resent.
</p>

<div class="form">
    <?php
        $form = $this->beginWidget(
                'bootstrap.widgets.TbActiveForm',
                array(
                    'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
                    'id' => 'activation-form',
                    'action' => 'activateAccount',
                    'method' => 'get',
                    'enableAjaxValidation' => false,
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
            echo TbHtml::emailFieldControlGroup(
                    'email',
                    $email,
                    array(
                        'span' => 5,
                        'maxlength' => 128,
                        'label' => 'E-mail Address',
                        'labelOptions' => array(
                            'required' => true,
                        ),
                    )
            );
        ?>
        <?php
            echo TbHtml::textFieldControlGroup(
                    'user_key',
                    $user_key,
                    array(
                        'span' => 5,
                        'maxlength' => 64,
                        'label' => 'User Key',
                    )
            );
        ?>
        <?php
           $widget = $this->widget(
                    'yiiwheels.widgets.switch.WhSwitch',
                    array(
                        'name' => 'resendEmail',
                        'onLabel' => 'Yes',
                        'offLabel' => 'No',
                        'size' => 'large',
                        'offColor' => 'warning',
                        'value' => $resendEmail,
                        'htmlOptions' => array(
                        ),
                    ),
                    true
            );
            
            echo '<div class="control-group">';
            echo '<div class="controls">';
            echo $widget;
            echo CHtml::label(
                    'Resend Activation E-mail?',
                    'resendEmail',
                    array(
//                        'class' => 'control-label',
                        )
                    );
            echo '</div>';
            echo '</div>';
         ?>
    </fieldset>
    <div class="form-actions">
        <?php
            echo TbHtml::submitButton(
                    'Activate',
                    array(
                        'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                        'size' => TbHtml::BUTTON_SIZE_LARGE,
                    )
            );
        ?>
    </div>
    <?php $this->endWidget(); ?>
</div><!-- form -->
<?php endif; ?>
