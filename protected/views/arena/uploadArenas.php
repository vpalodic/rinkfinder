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
            'uploadArenas.addUploadAndDeleteButtons();'
            . '$("#step2Continue").on("click", function () {'
            . '    return uploadArenas.onContinueStep2ButtonClick();'
            . '});'
            . '$("#step3Continue").on("click", function () {'
            . '    return uploadArenas.onContinueStep3ButtonClick();'
            . '});'
            . '$("#step4Continue").on("click", function () {'
            . '    return uploadArenas.onContinueStep4ButtonClick();'
            . '});'
            . '$("#uploadButton").on("click", function () {'
            . '    return uploadArenas.onUploadButtonClick();'
            . '});'
            . 'uploadArenas.baseUrl = "' . Yii::app()->request->baseUrl . '";',
            CClientScript::POS_READY
    );
    Yii::app()->clientScript->registerScript(
            'uploadArenaCSV',
            '$("#ArenaUploadForm_fileName").on("complete", function (event, id, name, response, xhr) {'
            . '    return uploadArenas.onUploadComplete(event, id, name, response, xhr);'
            . '});'
            . '$("#ArenaUploadForm_fileName").on("submit", function (event, id, name) {'
            . '    return uploadArenas.onUploadSubmit(event, id, name);'
            . '});'
            . '$("#ArenaUploadForm_fileName").on("cancel", function (event, id, name) {'
            . '    return uploadArenas.onUploadCancel(event, id, name);'
            . '});'
            . '$("#ArenaUploadForm_fileName").on("manualRetry", function (event, id, name) {'
            . '    return uploadArenas.onUploadRetry(event, id, name);'
            . '});'
            . '$("#ArenaUploadForm_fileName").on("error", function (event, id, name, errorReason, xhr) {'
            . '    return uploadArenas.onUploadError(event, id, name, errorReason, xhr);'
            . '});',
            CClientScript::POS_READY
    );
?>

<h2 class="sectionHeader">Upload Arenas</h2>

<div id="arenaUploadStep1" class="row-fluid">
    <h3 class="sectionSubHeader">
        Step 1: <h4>Select A File To Upload</h4>
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
                            )
                    ),
                    'pluginOptions' => array(
                        'debug' => false,
                        'multiple' => false,
                        'autoUpload' => false,
                        'deleteFile' => array(
                            'enabled' => true,
                            'endpoint' => $this->createUrl(
                                    'arena/uploadArenasFileDelete',
                                    array(
                                        )
                            )
                        ),                                
                        'dragAndDrop' => array(
                            'disableDefaultDropzone' => true,
                        ),
                        'text' => array(
                            'uploadButton' => '<i class="icon-file icon-white"></i> Select File',
                        ),
                        'failedUploadTextDisplay' => array(
                            'mode' => 'custom',
                            'responseProperty' => 'error',
                        ),
                        'retry' => array(
                            'showButton' => true,
                        ),
                        'template' => '<div class="qq-uploader">'
                        . '<div class="qq-upload-button btn btn-warning btn-large">'
                        . '<div>{uploadButtonText}</div></div>'
                        . '<span class="qq-drop-processing"><span>{dropProcessingText}'
                        . '</span><span class="qq-drop-processing-spinner"></span>'
                        . '</span><ul class="qq-upload-list"></ul></div>',
                        'classes' => array(
                            'button' => 'qq-upload-button.btn.btn-warning.btn-large',
                            'success' => 'alert alert-success',
                            'buttonHover' => '',
                            'buttonFocus' => '',
                        )
                    ),
                    'htmlOptions' => array(
                    ),
                ),
                true
        );
    ?>    
    <div class="control-group">
        <div class="controls">
            <?php
                echo $fileWidget;
                echo TbHtml::error($model, 'fileName');
            ?>
        </div>
    </div>
</div><!-- step 1 -->
<div id="arenaUploadStep2" class="row-fluid" style="display: none;">
    <h3 class="sectionSubHeader">
        Step 2: <h4>Import Settings</h4>
    </h3>
    <p class="sectionSubHeaderContent">
    If you are uploading a standard CSV file with headers as the first row
    then you can simply click the continue button. Otherwise, please select
    the options for your file and click the continue button to proceed.
    </p>
    <?php
        $headerArr = array(
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8',
            9 => '9',
            10 => '10',
        );
        echo TbHtml::dropDownListControlGroup(
                'header-row',
                1,
                $headerArr,
                array(
                    'span' => 5,
                    'label' => 'Field headers start on which row?'
                )
        );
    ?>
    <?php
        $delimArr = array(
            ',' => 'Comma (,)',
            '\t' => 'Tab (    )',
            ' ' => 'Space ( )',
            ';' => 'Semi-colon (;)',
            ':' => 'Colon (:)',
            '~' => 'Tilda (~)',
            '^' => 'Carrot (^)',
        );
        echo TbHtml::dropDownListControlGroup(
                'delimiter',
                ',',
                $delimArr,
                array(
                    'span' => 5,
                    'label' => 'Fields are separated by?'
                )
        );
    ?>
    <?php
        $enclArr = array(
            '"' => 'Double-quotes (")',
            '\'' => 'Single-quote (\')',
            '`' => 'Back-quote (`)',
            '~' => 'Tilda (~)',
            '^' => 'Carrot (^)',
        );
        echo TbHtml::dropDownListControlGroup(
                'enclosure',
                '"',
                $enclArr,
                array(
                    'span' => 5,
                    'label' => 'Text is enclosed by?'
                )
        );
    ?>
    <?php
        $escapeArr = array(
            '\\' => 'Back-slash (\\)',
            '/' => 'Forward-slash (/)',
            '~' => 'Tilda (~)',
            '^' => 'Carrot (^)',
        );
        echo TbHtml::dropDownListControlGroup(
                'escape-char',
                '\\',
                $escapeArr,
                array('span' => 5,
                    'label' => 'Special characters are escaped by?'
                )
        );
    ?>
    <?php
        $widget1 = $this->widget(
                'yiiwheels.widgets.switch.WhSwitch',
                array(
                    'name' => 'update-existing',
                    'id' => 'update-existing',
                    'onLabel' => 'Yes',
                    'offLabel' => 'No',
                    'size' => 'large',
                    'offColor' => 'warning',
                    'htmlOptions' => array(
                    ),
                ),
                true
        );
    ?>
    <div class="control-group">
        <div class="controls">
        <?php
            echo TbHtml::label(
                    'Update existing records? (If you select No and an arena '
                    . 'exists in the database that you are trying to import, '
                    . 'the import will fail.)',
                    'update-existing',
                    array(
                        )
                    );
            echo $widget1;
        ?>
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <button id="step2Continue" class="btn btn-success btn-large disabled" type="button" name="yt2" disabled>
                <i class="icon-arrow-right icon-white"></i> Continue
            </button>
        </div>
    </div>
</div><!-- step 2 -->
<div id="arenaUploadStep3" class="row-fluid" style="display: none;">
    <h3 class="sectionSubHeader">
        Step 3: <h4>Import Mappings</h4>
    </h3>
    <p class="sectionSubHeaderContent">
    Use the drop-down lists to map fields in the CSV file to fields in the Arena table.
    Please remember that fields with a <span class="required">*</span> are required to be mapped.
    A field will automatically be mapped if the field name appears in the CSV header.
    Data in the CSV file that exceeds the length of the database field will be truncated. Any
    characters in the data from the CSV file that do not conform to the database field type
    will be stripped before being imported. For example, for the phone number field, all
    formatting characters such as () and - will be removed before being imported.
    Please note that you will not be able to continue until all required table fields have
    been mapped to a CSV field. 
    </p>
    <table id="mappingTable" class="items table table-striped table-bordered table-condensed table-hover" style="padding: 0px;">
    </table>
    <div class="control-group">
        <div class="controls">
            <button id="step3Continue" class="btn btn-primary btn-large disabled" type="button" name="yt3" disabled>
                <i class="icon-upload icon-white"></i> Import
            </button>
        </div>
    </div>
</div><!-- step 3 -->
<div id="arenaUploadStep4" class="row-fluid" style="display: none;">
    <h3 class="sectionSubHeader">
        Step 4: <h4>Import Summary</h4>
    </h3>
    <dl class="dl-horizontal">
        <dt>
            Records Updated
        </dt>
        <dd id="arenaSummaryUpdated" class="text-success">
        </dd>
        <dt>
            Records Created
        </dt>
        <dd id="arenaSummaryCreated" class="text-success">
        </dd>
        <dt>
            Records In File
        </dt>
        <dd id="arenaSummaryTotal" class="text-success">
        </dd>
        <dt>
            Records Auto Tagged?
        </dt>
        <dd id="arenaSummaryAutoTagged">
        </dd>
    </dl>
    <div class="control-group">
        <div class="controls">
            <button id="step4Continue" class="btn btn-success btn-large disabled" type="button" name="yt3" disabled>
                <i class="icon-repeat icon-white"></i> Import Another File
            </button>
        </div>
    </div>
</div><!-- step 4 -->
<div id="loadingScreen" class="row-fluid">
</div><!-- Loading Screen -->
<!-- Modal Dialog -->
<div id="arenaModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="arenaModalLabel" aria-hidden="true">
  <div id="arenaModalHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="arenaModalLabel">Modal header</h3>
  </div>
  <div id="arenaModalBody" class="modal-body">
  </div>
  <div id="arenaModalFooter" class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
  </div>
</div><!-- Modal Dialog -->