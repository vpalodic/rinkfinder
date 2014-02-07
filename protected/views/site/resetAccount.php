<?php
    /* @var $this SiteController */
    /* @var $model UserChangePassword */
    /* @var $email string */
    /* @var $user_key string */
    /* @var $reset bool */
    /* @var $message string */
    /* @var $form TbActiveForm  */

    $this->pageTitle = Yii::app()->name . ' - Account Recovery';
    $this->breadcrumbs = array(
        'Login' => array('site/login'),
        'Account Recovery',
    );
?>

<h2 class="sectionHeader">Account Recovery</h2>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<?php if(!isset($model) && (!isset($email) || empty($email))) : ?>

<h3 class="sectionSubHeader">
    Step 1 of 3:
</h3>

<p>
    Please fill out the following form with your E-mail Address.
    <br />
</p>

<div class="form">
    <?php
        $form = $this->beginWidget(
                'bootstrap.widgets.TbActiveForm',
                array(
                    'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
                    'id' => 'recovery-form',
                    'action' => 'resetAccount',
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
            echo TbHtml::hiddenField('sendEmail', 1);
        ?>
    </fieldset>
    <?php
        echo TbHtml::formActions(
                TbHtml::submitButton(
                        'Begin Account Recovery',
                        array(
                            'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                            'size' => TbHtml::BUTTON_SIZE_LARGE,
                        )
                )
            );
    ?>
    <?php $this->endWidget(); ?>
</div><!-- form -->
<?php elseif(!isset($model) && (isset($email) && !empty($email)) && (!isset($user_key) || empty($user_key))) : ?>

<h3 class="sectionSubHeader">
    Step 2 of 3:
</h3>

<p>
    Please fill out the following form with your E-mail Address and User Key.
    <br />
</p>

<div class="form">
    <?php
        $form = $this->beginWidget(
                'bootstrap.widgets.TbActiveForm',
                array(
                    'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
                    'id' => 'recovery-form',
                    'action' => 'resetAccount',
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
                        'labelOptions' => array(
                            'required' => true,
                        ),
                    )
            );
        ?>
    </fieldset>
    <?php
        echo TbHtml::formActions(
                TbHtml::submitButton(
                        'Continue Account Recovery',
                        array(
                            'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                            'size' => TbHtml::BUTTON_SIZE_LARGE,
                        )
                )
            );
    ?>
    <?php $this->endWidget(); ?>
</div><!-- form -->
<?php elseif(isset($model)) : ?>

<h3 class="sectionSubHeader">
    Step 3 of 3:
</h3>

<p>
    Please fill out the following form with your new password.
    <br />
</p>

<div class="form">
    <?php
        $form = $this->beginWidget(
                'bootstrap.widgets.TbActiveForm',
                array(
                    'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
                    'id' => 'recovery-form',
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
            echo $form->errorSummary($model);
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
    </fieldset>
    <?php
        echo TbHtml::formActions(
                TbHtml::submitButton(
                        'Finish Account Recovery',
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
