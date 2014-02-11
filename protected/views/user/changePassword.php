<?php
    /* @var $this SiteController */
    /* @var $model UserChangePassword */
    /* @var $form TbActiveForm  */

    $this->pageTitle = Yii::app()->name . ' - Change Password';
    $this->breadcrumbs = array(
        'Users' => array('user/index', 'id' => $model->id),
        CHtml::encode($model->fullName) => array('profile/view', 'id' => $model->id),
        'Change Password',
    );
    
    $this->menu = array(
        array('label' => 'List User', 'url' => array('index')),
        array('label' => 'Create User', 'url' => array('create')),
        array('label' => 'Update User', 'url' => array('update', 'id'=>$model->id)),
        array('label' => 'Delete User', 'url' => '#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
        array('label' => 'Manage User', 'url' => array('admin')),
);
?>

<h2 class="sectionHeader">Change Password</h2>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<h3 class="sectionSubHeader">
    <?php echo CHtml::encode($model->fullName); ?>
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
                        'Change Password',
                        array(
                            'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                            'size' => TbHtml::BUTTON_SIZE_LARGE,
                        )
                )
            );
    ?>
    <?php $this->endWidget(); ?>
</div><!-- form -->
