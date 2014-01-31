<?php
    /* @var $this SiteController */
    /* @var $model ContactForm */
    /* @var $form TbActiveForm */
    $this->pageTitle = Yii::app()->name . ' - Contact Us';
    $this->breadcrumbs = array('Contact');
?>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>
<h2 class="sectionHeader">Contact Us</h2>
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
            echo $form->textFieldControlGroup(
                    $model,
                    'name',
                    array(
                        'span' => 5,
                    )
            );
        ?>
        <?php
            echo $form->emailFieldControlGroup(
                    $model,
                    'email',
                    array(
                        'maxlength' => 128,
                        'span' => 5,
                    )
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
            echo $form->checkBoxControlGroup(
                    $model,
                    'copyMe',
                    array(
                        'span' => 5,
                    )
            );
        ?>
        <?php if(Yii::app()->doCaptcha('contact')) : ?>
            <div class="controls">
                <?php $this->widget('CCaptcha'); ?>
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

            <div class="controls">
                <p class="hint">Please enter the letters as they are shown in the image above.
                <br/>Letters are not case-sensitive.</p>
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
