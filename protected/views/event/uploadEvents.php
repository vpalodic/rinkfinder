<?php
    /* @var $this EventController */
    /* @var $model EventUploadForm */
    /* @var $form TbActiveForm */
    /* @var $fields array[][] */
    /* @var $arenaId integer */
    /* @var $eventTypes array[] */

    $this->pageTitle = Yii::app()->name . ' - Upload Events';
    $this->breadcrumbs = array(
        'Upload Events'
    );
?>
<?php
    Yii::app()->clientScript->registerScript(
            'addUploadButton',
            'uploadEvents.addUploadAndDeleteButtons();'
            . '$("#step2Continue").on("click", function () {'
            . '    return uploadEvents.onContinueStep2ButtonClick();'
            . '});'
            . '$("#step3Previous").on("click", function () {'
            . '    return uploadEvents.onPreviousStep3ButtonClick();'
            . '});'
            . '$("#step3Continue").on("click", function () {'
            . '    return uploadEvents.onContinueStep3ButtonClick();'
            . '});'
            . '$("#step4Continue").on("click", function () {'
            . '    return uploadEvents.onContinueStep4ButtonClick();'
            . '});'
            . '$("#uploadButton").on("click", function () {'
            . '    return uploadEvents.onUploadButtonClick();'
            . '});'
            . '$("#resetButton1").on("click", function () {'
            . '    return uploadEvents.onResetButtonClick();'
            . '});'
            . '$("#resetButton2").on("click", function () {'
            . '    return uploadEvents.onResetButtonClick();'
            . '});'
            . '$("#resetButton3").on("click", function () {'
            . '    return uploadEvents.onResetButtonClick();'
            . '});'
            . 'uploadEvents.loginUrl = "' . $this->createUrl('site/login') . '";'
            . 'uploadEvents.baseUrl = "' . Yii::app()->request->baseUrl . '";'
            . 'uploadEvents.step = 1;'
            . 'uploadEvents.arenaId = ' . $arenaId . ';',
            CClientScript::POS_READY
    );
    Yii::app()->clientScript->registerScript(
            'uploadEventsCSV',
            '$("#EventUploadForm_fileName").on("complete", function (event, id, name, response, xhr) {'
            . '    return uploadEvents.onUploadComplete(event, id, name, response, xhr);'
            . '});'
            . '$("#EventUploadForm_fileName").on("submit", function (event, id, name) {'
            . '    return uploadEvents.onUploadSubmit(event, id, name);'
            . '});'
            . '$("#EventUploadForm_fileName").on("cancel", function (event, id, name) {'
            . '    return uploadEvents.onUploadCancel(event, id, name);'
            . '});'
            . '$("#EventUploadForm_fileName").on("manualRetry", function (event, id, name) {'
            . '    return uploadEvents.onUploadRetry(event, id, name);'
            . '});'
            . '$("#EventUploadForm_fileName").on("error", function (event, id, name, errorReason, xhr) {'
            . '    return uploadEvents.onUploadError(event, id, name, errorReason, xhr);'
            . '});',
            CClientScript::POS_READY
    );
?>

<h2 class="sectionHeader">Upload Events</h2>

<div id="uploadEventsStep1" class="row-fluid">
    <h3 class="sectionSubHeader">
        Step 1: <h4>Select A File To Upload</h4>
    </h3>
    <a href="#eventModalFileUpload" role="button" class="btn btn-large" data-toggle="modal">
        <i class="icon-info-sign"></i> Instructions
    </a>
    <a href="#eventModalFields" role="button" class="btn btn-large" data-toggle="modal">
        <i class="icon-info-sign"></i> Field Information
    </a>
    <br><br>
    <?php
        $fileWidget = $this->widget(
                'yiiwheels.widgets.fineuploader.WhFineUploader',
                array(
                    'model' => $model,
                    'attribute' => 'fileName',
                    'uploadAction' => $this->createUrl(
                            'event/uploadEventsFile',
                            array(
                                'aid' => $arenaId
                            )
                    ),
                    'pluginOptions' => array(
                        'debug' => true,
                        'multiple' => false,
                        'autoUpload' => false,
                        'deleteFile' => array(
                            'enabled' => true,
                            'endpoint' => $this->createUrl(
                                    'event/uploadEventsFileDelete',
                                    array(
                                        'aid' => $arenaId
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
<div id="uploadEventsStep2" class="row-fluid" style="display: none;">
    <h3 class="sectionSubHeader">
        Step 2: <h4>Import Options</h4>
    </h3>
    <a href="#eventModalSettings" role="button" class="btn btn-large" data-toggle="modal">
        <i class="icon-info-sign"></i> Instructions
    </a>
    <br><br>
    <?php
        echo TbHtml::dropDownListControlGroup(
                'eventType',
                1,
                $eventTypes,
                array(
                    'span' => 5,
                    'label' => 'Event Type'
                )
        );
    ?>
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
                    'Update existing records? (If you select No and an event '
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
            <button id="resetButton1" class="btn btn-large btn-primary pull-right" type="button" aria-hidden="true">
                <i class="icon-repeat icon-white"></i> Restart Import
            </button>
        </div>
    </div>
</div><!-- step 2 -->
<div id="uploadEventsStep3" class="row-fluid" style="display: none;">
    <h3 class="sectionSubHeader">
        Step 3: <h4>Import Mappings</h4>
    </h3>
    <a href="#eventModalMapping" role="button" class="btn btn-large" data-toggle="modal">
        <i class="icon-info-sign"></i> Instructions
    </a>
    <br><br>
    <p class="note">
        <div class="help-block">Fields with <span class="required">*</span> are required.</div>
    </p>
    <table id="mappingTable" class="items table table-striped table-bordered table-condensed table-hover" style="padding: 0px;">
    </table>
    <div class="control-group">
        <div class="controls">
            <button id="step3Previous" class="btn btn-large disabled" type="button" name="yt2" disabled>
                <i class="icon-arrow-left"></i> Previous
            </button>
            <button id="step3Continue" class="btn btn-success btn-large disabled" type="button" name="yt3" disabled>
                <i class="icon-upload icon-white"></i> Import
            </button>
            <button id="resetButton2" class="btn btn-large btn-primary pull-right" type="button" aria-hidden="true">
                <i class="icon-repeat icon-white"></i> Restart Import
            </button>
        </div>
    </div>
</div><!-- step 3 -->
<div id="uploadEventsStep4" class="row-fluid" style="display: none;">
    <h3 class="sectionSubHeader">
        Step 4: <h4>Import Summary</h4>
    </h3>
    <dl class="dl-horizontal">
        <dt>
            Records Updated
        </dt>
        <dd id="uploadEventsSummaryUpdated" class="text-success">
        </dd>
        <dt>
            Records Created
        </dt>
        <dd id="uploadEventsSummaryCreated" class="text-success">
        </dd>
        <dt>
            Records In File
        </dt>
        <dd id="uploadEventsSummaryTotal" class="text-success">
        </dd>
        <dt>
            Records Auto Tagged?
        </dt>
        <dd id="uploadEventsSummaryAutoTagged">
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
<div id="eventModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div id="eventModalHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="eventModalLabel"></h3>
  </div>
  <div id="eventModalBody" class="modal-body">
  </div>
  <div id="eventModalFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
    <button id="resetButton3" class="btn btn-large btn-primary" type="button" aria-hidden="true">
        <i class="icon-repeat icon-white"></i> Restart Import
    </button>
  </div>
</div><!-- Error Modal Dialog -->

<!-- Mapping Instructions Modal Dialog -->
<div id="eventModalMapping" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="eventModalMappingLabel" aria-hidden="true">
  <div id="eventModalMappingHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="eventModalMappingLabel">Mapping Instructions</h3>
  </div>
  <div id="eventModalMappingBody" class="modal-body">
    <dl>
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
            You may map a data file field to multiple table fields.
        </dd>
        <dt>
            Automatic Field Mappings
        </dt>
        <dd>
            A field will automatically be mapped if the table field name appears in
            the data file header row.
        </dd>
        <dt>
            Table Column
        </dt>
        <dd>
            Displays the column names that can be populated through the import process.
            Placing your cursor over a field in this column will display a tool-tip
            that will display information about the field such as its size and type.
        </dd>
        <dt>
            Data File Column
        </dt>
        <dd>
            Use the drop-down lists in this column to map fields in the data file to
            fields in the table. If the selection list is blank or does not contain
            what you expect, return to the previous step and double check the selections
            for field separator, field enclosure, and field escape.
        </dd>
        <dt>
            Data File Example
        </dt>
        <dd>
            Displays the file field data as it exists in the file for the currently
            mapped data file field. This column will automatically update based on the
            the selection in the Data File Column. It does not display the data as it
            will be stored in the database. In other words, It displays the data without
            stripping and without conversion.
        </dd>
        <dt>
            Field Size
        </dt>
        <dd>
            Data in the file that exceeds the size of the database field will be truncated.
            If the tool-tip does not explicitly state a size or length, then the size is
            based on the type of data that the field accepts.
        </dd>
        <dt>
            Field Type
        </dt>
        <dd>
            Any characters in the data from the file that do not conform to the database field type
            will be stripped before being imported. For example, for the price field, all
            formatting characters such as $ will be removed before being imported. If the
            tool-tip does not explicitly state a field type, then the type is implied by the name
            of the field.
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
            Imported records will automatically be tagged with the arena name, event type, event name,
            and location. You may also provide your own tags and they will be processed correctly in
            addition to the automatic tags.
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
  <div id="eventModalMappingFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Mapping Instructions Modal Dialog -->
<!-- Settings Instructions Modal Dialog -->
<div id="eventModalSettings" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="eventModalSettingsLabel" aria-hidden="true">
  <div id="eventModalSettingsHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="eventModalSettingsLabel">Import Options Instructions</h3>
  </div>
  <div id="eventModalSettingsBody" class="modal-body">
    <dl>
        <dt>
            What Settings to change
        </dt>
        <dd>
            If you are uploading <strong>Ice For Sale</strong> in a standard
            CSV file with field headers as the first row and the file contains
            only new records, then you can simply click the continue button
            below. Otherwise, please select the options for your file and click
            the continue button to proceed.
        </dd>
        <dt>
            Event Type
        </dt>
        <dd>
            Specify which type of events you are uploading. The default is set
            to Ice For Sale.
        </dd>
        <dt>
            Field Header Row
        </dt>
        <dd>
            Specify which row (1 - 10) that the field header is located. Data
            will be imported starting after that row and the field headers will
            be used on the next screen to assist in mapping the file data to
            database fields. Although not absolutely required, if your data
            file does not have a header row, then the data row (1 -10) will be
            used as the header and will not be imported. Automatic field
            mapping will also likely not work in this case.
        </dd>
        <dt>
            Field Separator
        </dt>
        <dd>
            Select the field separator character that the data file uses. For a
            CSV file, the comma (,) is the most common separator and for a TSV
            file, a tab is the most common separator. Selecting an incorrect 
            field separator will make the mapping in the next step impossible.
            If the data fields do not appear correctly in the next step, return
            to this step and check this selection. Consult the documentation
            of the program used to generate the data file to determine what
            separator character you should select. If in doubt, leave the
            default selection.
        </dd>
        <dt>
            Field Enclosure
        </dt>
        <dd>
            In order for a data field to contain the character used as the field
            separator and not be mistaken as a new field, the data field must be
            enclosed by another character so that the importer knows not to
            create a new field. For example, the tags field accepts a list of
            tags separated by a comma (,). In order to properly import the
            tags field, it needs to be enclosed by a character so that the
            commas (,) in the list are preserved. The default enclosure
            character is the double-quotation mark (&quot;) and so for our
            example, the tags field would appear in the data file as &quot;tag1,
            tag2, tag3, tag4&quot;. Consult the documentation of the program
            used to generate the data file to determine what enclosure character
            you should select. If in doubt, leave the default selection.
        </dd>
        <dt>
            Field Escaping
        </dt>
        <dd>
            In order for a data field to contain special characters, the
            special character needs to be immediately preceeded by an escape
            character. The escape character tells the importer to treat the
            next character as a literal rather than as say, the field enclosure
            or field separator. Consult the documentation of the program
            used to generate the data file to determine what enclosure character
            you should select. If in doubt, leave the default selection.
        </dd>
        <dt>
            Existing Records
        </dt>
        <dd>
            The external_id is used to form a unique key. 
            Therefore, only a single record for an arena with that external_id
            can exist. If the data file you are importing contains
            rows that will cause the unique key to be violated and the option
            to update existing records is set to No, then the import will fail
            with a unique constraint violation. By setting the update existing 
            records option to Yes, the importer will first check for any
            existing records that match the rows in the data file and will
            update them with the data in the data file and the import will
            complete. Please note that if the data file itself contains
            multiple rows for the same new record, the import will fail.
        </dd>
    </dl>
  </div>
  <div id="eventModalSettingsFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Settings Instructions Modal Dialog -->
<!-- File Uploading Instructions Modal Dialog -->
<div id="eventModalFileUpload" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="eventModalFileUploadLabel" aria-hidden="true">
  <div id="eventModalFileUploadHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="eventModalFileUploadLabel">Import Instructions</h3>
  </div>
  <div id="eventModalFileUploadBody" class="modal-body">
    <h3>
      Important Information
    </h3>
    <dl>
        <dt>
            Before You Begin
        </dt>
        <dd>
            Before you attempt to import your first file using this importer,
            please open the <strong>Field Information</strong> dialog to
            determine what type of data to include in your import file.
        </dd>
        <dt>
            Mac Users
        </dt>
        <dd>
            If the file you are importing was created using an Apple Mac
            computer and you are having problems importing it, please ensure
            that the file was saved using MS-DOS / MS-Windows line endings.
        </dd>
        <dt>
            Event Type
        </dt>
        <dd>
            Specify which type of events you are uploading. The default is set
            to Ice For Sale.
        </dd>
        <dt>
            Field Header Row
        </dt>
        <dd>
            Although not absolutely required, if your data file does not have a
            header row, then the first row to be imported will be used as the
            header and will not itself be imported. Automatic field mapping will
            also likely not work in this case.
        </dd>
        <dt>
            Existing Records
        </dt>
        <dd>
            The external_id is used to form a unique key. 
            Therefore, only a single record for an arena with that external_id
            can exist. If the data file you are importing contains
            rows that will cause the unique key to be violated and the option
            to update existing records is set to No, then the import will fail
            with a unique constraint violation. By setting the update existing 
            records option to Yes, the importer will first check for any
            existing records that match the rows in the data file and will
            update them with the data in the data file and the import will
            complete. Please note that if the data file itself contains
            multiple rows for the same new record, the import will fail.
        </dd>
        <dt>
            Data File Types
        </dt>
        <dd>
            The importer can import virtually any text based data file, however;
            on the <strong>Select File</strong> dialog, you can only select
            files with a csv, tsv, or txt extension.
        </dd>
    </dl>
    <h3>
      Importer Features
    </h3>
    <dl>
        <dt>
            Auto Tagging
        </dt>
        <dd>
            Imported records will automatically be tagged with the arena name, event type, event name,
            and location. You may also provide your own tags and they will be processed correctly in
            addition to the automatic tags.
        </dd>
        <dt>
            Map To Multiple Fields
        </dt>
        <dd>
            You may map a data file field to multiple table fields. This is useful
            if your import file contains the start date and time for an event in a
            single field. In this case you can map that single field to both the
            start_date and start_time fields and the importer will correctly
            import the data.
        </dd>
        <dt>
            Automatic Field Mappings
        </dt>
        <dd>
            A field will automatically be mapped if the table field name appears
            in the data file header row.
        </dd>
        <dt>
            Data Type Conversion
        </dt>
        <dd>
            The importer will attempt to auto detect and convert certain data
            types before importing the data in to the database. For example, if
            there is a date field and the data file contains November 25, 2013
            as the value, it will be converted to 2013-11-25 before being
            stored in the database.
        </dd>
        <dt>
            Field Truncation
        </dt>
        <dd>
            Data in the file that exceeds the size of the database field will be
            truncated. This means that if a field in the data file contains ten
            characters and it is mapped to a database field that only holds five
            characters, then only the first five characters in the data file
            will be imported.
        </dd>
        <dt>
            Field Stripping
        </dt>
        <dd>
            Any characters in a data file field that do not conform to the
            database field type will be stripped before being imported. For
            example, a phone number field only holds the ten digit phone
            number, therefore all non-numeric characters such as () and - will
            be stripped from the data before being imported.
        </dd>
    </dl>
    <h3>
      Performing an Import
    </h3>
    <dl>
        <dt>
            Step 1
        </dt>
        <dd>
            Begin by uploading your data file to the server.
            <ol>
                <li>
                    Click the <strong>Select File</strong> button and select
                    your import file. Keep in mind that it must have a <strong>
                    csv, tsv, or txt</strong> extension.
                </li>
                <li>
                    Click the <strong>Begin Upload</strong> button to send your
                    file to the server. If the transfer is successful, you will
                    be brought to step 2.
                </li>
                <li>
                    If an error happens, you will be shown an error dialog with
                    details about the error that occurred. You can close the
                    error dialog and retry the upload. If the error persists, 
                    copy the error information in to an e-mail and send it off
                    to your Application Administrator.
                </li>
            </ol>
        </dd>
        <dt>
            Step 2
        </dt>
        <dd>
            Set your import options.
            <ol>
                <li>
                    Click the <strong>Instructions</strong> button to review
                    detailed information on how to set the options for this step.
                </li>
                <li>
                    Once you have selected your options, click the <strong>
                    Continue</strong> button to proceed to step 3.
                </li>
                <li>
                    If an error happens, you will be shown an error dialog with
                    details about the error that occurred. You can close the
                    error dialog and make another attempt to continue. If the
                    error persists, copy the error information in to an e-mail
                    and send it off to your Application Administrator.
                </li>
            </ol>
        </dd>
        <dt>
            Step 3
        </dt>
        <dd>
            Create the table and data file mappings.
            <ol>
                <li>
                    Click the <strong>Instructions</strong> button to review
                    detailed information on how to set the settings for this step.
                </li>
                <li>
                    Once you have selected your options, click the <strong>
                    Import</strong> button to import the uploaded data file in
                    to the database. If the import succeeds, you will be brought
                    to the summary screen.
                </li>
                <li>
                    If an error happens, you will be shown an error dialog with
                    details about the error that occurred. You can close the
                    error dialog and make another attempt to continue. If the
                    error persists, copy the error information in to an e-mail
                    and send it off to your Application Administrator.
                </li>
            </ol>
        </dd>
        <dt>
            Summary Screen
        </dt>
        <dd>
            The summary screen will display the results from the import. After
            reviewing the results, you may import another file if you wish.
        </dd>
    </dl>
  </div>
  <div id="eventModalFileUploadFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- File Uploading Instructions Modal Dialog -->
<!-- Fields and File Information Modal Dialog -->
<div id="eventModalFields" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="eventModalFieldsLabel" aria-hidden="true">
  <div id="eventModalFieldsHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="eventModalFieldsLabel">Fields Information</h3>
  </div>
  <div id="eventModalFieldsBody" class="modal-body">
    <h3>
        Tips
    </h3>
    <dl>
        <dt>
            Data File Headers
        </dt>
        <dd>
            Whenever possible, the first row in your data file should be a
            header row with the field names matching exactly the name of the
            fields in this table.
        </dd>
        <dt>
            Data File Fields
        </dt>
        <dd>
            Whenever possible, should exactly match the expected type and size
            of data for the database field. This will greatly reduce any
            import errors
        </dd>
    </dl>
    <h3>
        Field Types
    </h3>
    <dl>
        <dt>
            string
        </dt>
        <dd>
            The <strong>string</strong> data type consists of character data
            and the <strong>size</strong> of the field refers to the number of
            characters in the string. Character data that exceeds the size will
            be truncated.
        </dd>
        <dt>
            text
        </dt>
        <dd>
            The <strong>text</strong> data type consists of character data
            and the <strong>size</strong> of the field is unlimited. Performance
            can be greatly reduced if an excessive amount of data is imported to
            fields of this type. 
        </dd>
        <dt>
            integer
        </dt>
        <dd>
            The <strong>integer</strong> data type consists of numeric data. It
            is a whole number, that is it does not have a fractional part. Any
            fractional parts specified are truncated. An optional sign may
            preceed the number.
        </dd>
        <dt>
            float
        </dt>
        <dd>
            The <strong>float</strong> data type consists of numeric data. It
            is a real number, that is it may have a fractional part. An 
            optional sign may preceed the number.
        </dd>
        <dt>
            datetime
        </dt>
        <dd>
            The <strong>datetime</strong> data type consists of both date and
            time data. If either the date part or time part is missing, the
            system will provide a value for the missing part. Internally, the
            data is stored in this format: 2014-01-31 19:59:59, but, the system
            is able to understand and convert the most common U.S. datetime
            formats such as January 31, 2014 7:59:59 PM.
        </dd>
        <dt>
            date
        </dt>
        <dd>
            The <strong>date</strong> data type consists of only the date
            data. If a portion of the date part is missing, the
            system will provide a value for the missing part. Internally, the
            data is stored in this format: 2014-01-31, but, the system
            is able to understand and convert the most common U.S. date
            formats such as January 31, 2014.
        </dd>
        <dt>
            time
        </dt>
        <dd>
            The <strong>time</strong> data type consists of time data. If a 
            portion of the time part is missing, the system will provide a value
            for the missing part. Internally, the data is stored in this format:
            19:59:59, but, the system is able to understand and convert the most
            common U.S. time formats such as 7:59:59 PM.
        </dd>
        <dt>
            phone
        </dt>
        <dd>
            The <strong>phone</strong> data type consists of character data that
            is stripped of all non numeric characters. The size specifies the
            maximum number of digits allowed in the number.
        </dd>
    </dl>
    <h3>
        Required Fields
    </h3>
    <dl>
        <?php foreach($fields as $field) : ?>
            <?php if($field['required'] == true) : ?>
            <dt>
                <h4  class="text-error">
                    <?php echo $field['name'] ?>
                </h4>
            </dt>
            <dd>
                <dl class="dl-horizontal">
                    <dt>
                        Friendly Name:
                    </dt>
                    <dd>
                        <?php echo $field['display'] ?>
                    </dd>
                    <dt>
                        Type:
                    </dt>
                    <dd>
                        <?php echo $field['type'] ?>
                    </dd>
                    <dt>
                        Size:
                    </dt>
                    <dd>
                        <?php 
                            if($field['size'] > 0) {
                                echo $field['size'];
                            } else {
                                echo 'N/A';
                            }
                        ?>
                    </dd>
                    <dt>
                        Description:
                    </dt>
                    <dd>
                        <?php echo $field['tooltip'] ?>
                    </dd>
                    <dt>
                        Example:
                    </dt>
                    <dd>
                        <?php echo $field['example'] ?>
                    </dd>
                </dl>
            </dd>
            <?php endif; ?>
        <?php endforeach; ?>
    </dl>
    <h3>
        Optional Fields
    </h3>
    <dl>
        <?php foreach($fields as $field) : ?>
            <?php if($field['required'] == false) : ?>
            <dt>
                <h4>
                    <?php echo $field['name'] ?>
                </h4>
            </dt>
            <dd>
                <dl class="dl-horizontal">
                    <dt>
                        Friendly Name:
                    </dt>
                    <dd>
                        <?php echo $field['display'] ?>
                    </dd>
                    <dt>
                        Type:
                    </dt>
                    <dd>
                        <?php echo $field['type'] ?>
                    </dd>
                    <dt>
                        Size:
                    </dt>
                    <dd>
                        <?php 
                            if($field['size'] > 0) {
                                echo $field['size'];
                            } else {
                                echo 'N/A';
                            }
                        ?>
                    </dd>
                    <dt>
                        Description:
                    </dt>
                    <dd>
                        <?php echo $field['tooltip'] ?>
                    </dd>
                    <dt>
                        Example:
                    </dt>
                    <dd>
                        <?php echo $field['example'] ?>
                    </dd>
                </dl>
            </dd>
            <?php endif; ?>
        <?php endforeach; ?>
    </dl>
  </div>
  <div id="eventModalFieldsFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Fields and File Information Modal Dialog -->
