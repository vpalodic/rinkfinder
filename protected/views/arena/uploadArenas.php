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
<?php
    Yii::app()->clientScript->registerScript(
            'addUploadButton',
            '$(\'<span>   </span><button style="display:none;" id="uploadButton"'
            . ' class="btn btn-primary btn-large disabled" name="yt0" '
            . 'type="button">Begin Upload</button>\')'
            . '.insertAfter($(".qq-upload-button"));',
            CClientScript::POS_READY
    );
    Yii::app()->clientScript->registerScript(
            'uploadArenaCSV',
            '$("#ArenaUploadForm_fileName").on("complete", function (event, id, name, response, xhr) {'
            . '    if (response.success !== true) {'
            . '    } else {'
            . '        $("#beginUploadCSV").animate({opacity: 1.0}, 0).fadeOut("fast");'
            . '        $("#previewUploadCSV").animate({opacity: 1.0}, 0).fadeIn("slow");'
            . '    }'
            . '});'
            . '$("#ArenaUploadForm_fileName").on("submit", function (event, id, name) {'
            . '    $("#uploadButton").prop("disabled", false);'
            . '    $("#uploadButton").removeClass("disabled");'
            . '    if($("#uploadButton").css("display") === "none") {'
            . '        $("#uploadButton").animate({opacity: 1.0}, 0).fadeIn("fast");'
            . '    }'
            . '});'
            . '$("#ArenaUploadForm_fileName").on("cancel", function (event, id, name) {'
            . '    $("#uploadButton").prop("disabled", true);'
            . '    if($("#uploadButton").css("display") !== "none") {'
            . '        $("#uploadButton").animate({opacity: 1.0}, 0).fadeOut("fast");'
            . '    }'
            . '    return true;'
            . '});'
            . '$("#uploadButton").on("click", function () {'
            . '    $("#uploadButton").prop("disabled", true);'
            . '    if($("#uploadButton").css("display") !== "none") {'
            . '        $("#uploadButton").animate({opacity: 1.0}, 100).fadeOut("fast");'
            . '    }'
            . '    $("#ArenaUploadForm_fileName").fineUploader("uploadStoredFiles");'
            . '    return true;'
            . '});',
            CClientScript::POS_READY
    );
?>

<h2 class="sectionHeader">Upload Arenas</h2>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<?php if(!isset($uploaded) || $uploaded == false) : ?>

<div id="beginUploadCSV" class="row-fluid">
    <?php echo Yii::app()->getBaseUrl(true) . '/'; ?>

    <h3 class="sectionSubHeader">
        Step 1:
    </h3>
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
                            'multiple' => false,
                            'autoUpload' => false,
                            'deleteFile' => array(
                                'enabled' => true,
                                'endpoint' => $this->createUrl(
                                        'arena/uploadArenasFileDelete',
                                        array(
                                            'fine' => 1
                                            )
                                )
                            ),                                
                            'dragAndDrop' => array(
                                'disableDefaultDropzone' => true,
                            ),
                            'text' => array(
                                'uploadButton' => 'Select File',
                            ),
                            'failedUploadTextDisplay' => array(
                                'mode' => 'custom',
                                'responseProperty' => 'error',
                                
                            ),
                            'retry' => array(
                                'showButton' => true,
                                
                            ),
                            'template' => '<div class="qq-uploader">'
                            . '<div class="qq-upload-button btn btn-primary btn-large">'
                            . '<div>{uploadButtonText}</div></div>'
                            . '<span class="qq-drop-processing"><span>{dropProcessingText}'
                            . '</span><span class="qq-drop-processing-spinner"></span>'
                            . '</span><ul class="qq-upload-list"></ul></div>',
                            'classes' => array(
                                'button' => 'qq-upload-button.btn.btn-primary.btn-large',
                                'success' => 'btn-success',
                                'buttonHover' => '',
                                'buttonFocus' => '',
                            )
                        ),
                        'htmlOptions' => array(
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
/*            echo '<div class="control-group">';
            echo '<div class="controls">';
            echo TbHtml::button(
                    'Begin Upload',
                    array(
                        'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                        'size' => TbHtml::BUTTON_SIZE_LARGE,
                        'style' =>  'display:none;',
                        'id' => 'uploadButton',
                        'disabled' => true,
                        )
            );
            echo '</div>';
            echo '</div>';*/
        ?>
</div>
<div id="previewUploadCSV" class="form" style="display: none">
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
