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
        <?php
            $widget = $this->widget(
                    'yiiwheels.widgets.switch.WhSwitch',
                    array(
                        'model' => $model,
                        'attribute' => 'copyMe',
                        'onLabel' => 'Yes',
                        'offLabel' => 'No',
                        'size' => 'large',
                        'offColor' => 'warning',
                        'htmlOptions' => array(
                        ),
                    ),
                    true
            );
            
            echo '<div class="control-group">';
            echo '<div class="controls">';
            echo $widget;
            echo $form->labelEx(
                    $model,
                    'copyMe',
                    array(
//                        'class' => 'control-label',
                        )
                    );
            echo $form->error($model, 'copyMe');
            echo '</div>';
            echo '</div>';
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