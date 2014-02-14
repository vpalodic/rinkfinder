<?php
    /* @var $this ArenaController */
    /* @var $model ArenaUploadForm */
    /* @var $form TbActiveForm */
    /* @var $uploaded bool */

    $this->pageTitle = Yii::app()->name . ' - Upload Arenas';
    $this->breadcrumbs = array(
        'Upload Arenas'
    );
?>

<h2 class="sectionHeader">Upload Arenas</h2>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<?php if(!isset($uploaded) || $uploaded == false) : ?>

<div id="beginUploadCSV" class="row-fluid">
<p class="sectionSubHeaderContent">
Click the button below to select a CSV file that contains the arenas you wish to upload.
</p>

        <?php
            $fileWidget = $this->widget(
                    'yiiwheels.widgets.fineuploader.WhFineUploader',
                    array(
                        'model' => $model,
                        'attribute' => 'fileName',
                        'uploadAction' => $this->createUrl(
                                'arena/uploadArenasFile',
                                array(
                                    'fine' => 1
                                )
                        ),
                        'pluginOptions' => array(
                            'debug' => true,
                            'multiple' => true,
                            'dragAndDrop' => array(
                                'disableDefaultDropzone' => true,
                            ),
                            'callbacks' => array(
                                'onComplete' => 'function(id, fileName, response) { alert(response); }',
                            ),
                            'text' => array(
                                'uploadButton' => 'Select File',
                            ),
                            'template' => '<div class="qq-uploader">' . '<div class="qq-upload-button btn btn-primary btn-large"><div>{uploadButtonText}</div></div>' . '<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>' . '<ul class="qq-upload-list"></ul>' . '</div>',
                            'classes' => array(
                                'button' => 'qq-upload-button.btn.btn-primary.btn-large',
                                'success' => 'btn-success',
                                'buttonHover' => '',
                                'buttonFocus' => '',
                            )
//                            'validation' => array(
//                                'allowedExtensions' => array('csv')
//                            )
                        ),
                        'htmlOptions' => array(
                            'class' => 'span5', //'btn btn-primary btn-large',
                        ),
                    ),
                    true
            );
            
            echo '<div class="control-group">';
            echo '<div class="controls">';
            echo $fileWidget;
            echo TbHtml::error($model, 'fileName');
            echo '</div>';
            echo '</div>';
        ?>
</div>
<div class="form">
    <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
                                   array('layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
                                         'id' => 'arena-upload-form',
                                         'enableAjaxValidation' => true,
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
            
/*            echo $form->fileFieldControlGroup(
                    $model,
                    'fileName',
                    $htmlOptions
            );*/
        ?>
        <?php
            $widget = $this->widget(
                    'yiiwheels.widgets.switch.WhSwitch',
                    array(
                        'model' => $model,
                        'attribute' => 'emailResults',
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
                    'emailResults',
                    array(
//                        'class' => 'control-label',
                        )
                    );
            echo $form->error($model, 'emailResults');
            echo '</div>';
            echo '</div>';
        ?>
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
