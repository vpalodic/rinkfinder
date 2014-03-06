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
            . '$("#resetButton").on("click", function () {'
            . '    return uploadArenas.onResetButtonClick();'
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
                        'debug' => true,
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
            <button id="resetButton" class="btn btn-large btn-primary pull-right" type="button" aria-hidden="true">
                <i class="icon-repeat icon-white"></i> Restart Import
            </button>
        </div>
    </div>
</div><!-- step 2 -->
<div id="arenaUploadStep3" class="row-fluid" style="display: none;">
    <h3 class="sectionSubHeader">
        Step 3: <h4>Import Mappings</h4>
    </h3>
    <a href="#arenaModalMapping" role="button" class="btn btn-large" data-toggle="modal">
        <i class="icon-info-sign"></i> Instructions
    </a>
    <table id="mappingTable" class="items table table-striped table-bordered table-condensed table-hover" style="padding: 0px;">
    </table>
    <div class="control-group">
        <div class="controls">
            <button id="step3Continue" class="btn btn-success btn-large disabled" type="button" name="yt3" disabled>
                <i class="icon-upload icon-white"></i> Import
            </button>
            <button id="resetButton" class="btn btn-large btn-primary pull-right" type="button" aria-hidden="true">
                <i class="icon-repeat icon-white"></i> Restart Import
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
<!-- Error Modal Dialog -->
<div id="arenaModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="arenaModalLabel" aria-hidden="true">
  <div id="arenaModalHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="arenaModalLabel"></h3>
  </div>
  <div id="arenaModalBody" class="modal-body">
  </div>
  <div id="arenaModalFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
    <button id="resetButton" class="btn btn-large btn-primary pull-right" type="button" aria-hidden="true">
        <i class="icon-repeat icon-white"></i> Restart Import
    </button>
  </div>
</div><!-- Error Modal Dialog -->

<!-- Instructions Modal Dialog -->
<div id="arenaModalMapping" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="arenaModalMappingLabel" aria-hidden="true">
  <div id="arenaModalMappingHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="arenaModalMappingLabel">Mapping Instructions</h3>
  </div>
  <div id="arenaModalMappingBody" class="modal-body">
    <dl>
        <dt>
            How to Map Fields
        </dt>
        <dd>
            Use the drop-down lists to map fields in the data file to fields in the Arena table.
        </dd>
        <dt>
            What Fields to Map
        </dt>
        <dd>
            Fields with a <span class="required">*</span> are required to be mapped.
        </dd>
        <dt>
            Map To Multiple Fields
        </dt>
        <dd>
            You can map a data file field to multiple Arena table fields.
        </dd>
        <dt>
            Automatic Field Mappings
        </dt>
        <dd>
            A field will automatically be mapped if the field name appears in the data file header.
        </dd>
        <dt>
            Data File Header Row
        </dt>
        <dd>
            Although not absolutely required, if your data file does not have a header row, then the
            first data row will be used as the header and will not be imported.
        </dd>
        <dt>
            Field Information
        </dt>
        <dd>
            Placing your cursor over a field in the mapping table will display a tool-tip
            that will display information about the field such as its size and type.
        </dd>
        <dt>
            Field Size
        </dt>
        <dd>
            Data in the file that exceeds the size of the database field will be truncated.
        </dd>
        <dt>
            Field Type
        </dt>
        <dd>
            Any characters in the data from the file that do not conform to the database field type
            will be stripped before being imported. For example, for the phone number field, all
            formatting characters such as () and - will be removed before being imported.
        </dd>
        <dt>
            Data Type Conversion
        </dt>
        <dd>
            The importer will attempt to auto detect and convert certain data types before importing
            the data in to the database. For example, if there is a date field and the data file
            contains November 25, 2013 as the value, it will be converted to 2013-11-25 before being
            stored in the database.
        </dd>
        <dt>
            Auto Tagging
        </dt>
        <dd>
            Imported records will automatically be tagged with the name, city, and state.
        </dd>
        <dt>
            How to Continue
        </dt>
        <dd>
            You will not be able to continue until all required <span class="required">*</span>
            table fields have been mapped to a data file field.
        </dd>
    </dl>
  </div>
  <div id="arenaModalFooter" class="modal-footer">
    <button href="#" class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Instructions Modal Dialog -->
