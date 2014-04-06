<?php
    /* @var $model EventUploadForm */
    /* @var $form TbActiveForm */
    /* @var $fields array[][] */
    /* @var $path string */
    /* @var $doReady boolean */
    /* @var $arenaId integer */
    /* @var $arenaName string */
    /* @var $eventTypes array[] */
?>

<div id="eventInstructions" class="row-fluid">
    <a href="#eventModalFileUpload" role="button" class="btn btn-large" data-toggle="modal">
        <i class="icon-info-sign"></i> Overview
    </a>
    <a href="#eventModalFields" role="button" class="btn btn-large" data-toggle="modal">
        <i class="icon-info-sign"></i> Field Information
    </a>
    <br><br>
</div>
<div id="uploadEventsStep1" class="row-fluid">
    <h3 class="sectionSubHeader">
        Step 1: <h4>Select A File To Upload</h4>
    </h3>
    <div class="control-group">
        <div class="controls">
            <div id="EventUploadForm_fileName" name="EventUploadForm[fileName]">
                <noscript>
                    Please enable JavaScript to use file uploader.
                </noscript>
            </div>
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
    <div class="control-group">
        <div class="controls">
            <label for="update-existing">
                Update existing records? (If you select No and an event exists
                in the database that you are trying to import, the import will
                fail.)
            </label>
            <div class="make-switch switch-large" data-on-label="Yes" data-off-label="No" data-on="primary" data-off="warning" id="update-existing_switch">
                <input id="update-existing" name="update-existing" type="checkbox" value="1" checked />
            </div>
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <button id="step2Continue" class="btn btn-success btn-large disabled" type="button" name="yt2" disabled>
                <i class="icon-arrow-right icon-white"></i> Continue
            </button>
            <button id="resetButton1" class="btn btn-large btn-danger" type="button" aria-hidden="true">
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
    <table id="mappingTable" class="items table table-striped table-bordered 
           table-condensed table-hover footable toggle-large toggle-circle" style="padding: 0px;">
    </table>
    <div class="control-group">
        <div class="controls">
            <button id="step3Previous" class="btn btn-large disabled" type="button" name="yt2" disabled>
                <i class="icon-arrow-left"></i> Previous
            </button>
            <button id="step3Continue" class="btn btn-success btn-large disabled" type="button" name="yt3" disabled>
                <i class="icon-upload icon-white"></i> Import
            </button>
            <button id="resetButton2" class="btn btn-large btn-danger" type="button" aria-hidden="true">
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
<!-- Mapping Instructions Modal Dialog -->
<div id="eventModalMapping" class="modal hide fade container" tabindex="-1" 
     role="dialog" aria-labelledby="arenaModalFieldsLabel"
     aria-hidden="true" data-backdrop="static" data-max-height="500" >
  <div id="eventModalMappingHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="eventModalMappingLabel">Mapping Instructions</h3>
  </div>
  <div id="eventModalMappingBody" class="modal-body">
      <?php $this->renderPartial('/event/_uploadEventsInstructionsStep3a'); ?>
  </div>
  <div id="eventModalMappingFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Mapping Instructions Modal Dialog -->
<!-- Settings Instructions Modal Dialog -->
<div id="eventModalSettings" class="modal hide fade container" tabindex="-1" 
     role="dialog" aria-labelledby="arenaModalFieldsLabel"
     aria-hidden="true" data-backdrop="static" data-max-height="500" >
  <div id="eventModalSettingsHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="eventModalSettingsLabel">Import Options Instructions</h3>
  </div>
  <div id="eventModalSettingsBody" class="modal-body">
      <?php $this->renderPartial('/event/_uploadEventsInstructionsStep2'); ?>
  </div>
  <div id="eventModalSettingsFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Settings Instructions Modal Dialog -->
<!-- File Uploading Instructions Modal Dialog -->
<div id="eventModalFileUpload" class="modal hide fade container" tabindex="-1" 
     role="dialog" aria-labelledby="arenaModalFieldsLabel"
     aria-hidden="true" data-backdrop="static" data-max-height="500" >
  <div id="eventModalFileUploadHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="eventModalFileUploadLabel">Import Instructions</h3>
  </div>
  <div id="eventModalFileUploadBody" class="modal-body">
      <?php $this->renderPartial('/event/_uploadEventsInstructionsStep1'); ?>
  </div>
  <div id="eventModalFileUploadFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- File Uploading Instructions Modal Dialog -->
<!-- Fields and File Information Modal Dialog -->
<div id="eventModalFields" class="modal hide fade container" tabindex="-1" 
     role="dialog" aria-labelledby="arenaModalFieldsLabel"
     aria-hidden="true" data-backdrop="static" data-max-height="500" >
  <div id="eventModalFieldsHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="eventModalFieldsLabel">Fields Information</h3>
  </div>
  <div id="eventModalFieldsBody" class="modal-body">
      <?php $this->renderPartial('/event/_uploadEventsInstructionsStep3b', array('fields' => $fields)); ?>
  </div>
  <div id="eventModalFieldsFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Fields and File Information Modal Dialog -->
<?php if($doReady) : ?>
<?php
    Yii::app()->clientScript->registerScript(
            'doReady',
            'utilities.urls.login = "' . $this->createUrl('site/login') . '";'
            . 'utilities.urls.logout = "' . $this->createUrl('site/logout') . '";'
            . 'utilities.urls.base = "' . Yii::app()->request->baseUrl . '";'
            . 'utilities.urls.assets = "' . $path . '";'
            . 'utilities.debug = ' . (defined('YII_DEBUG') ? 'true' : 'false') . ';'
            . 'uploadEvents.step = 1;'
            . 'uploadEvents.arenaId = ' . $arenaId . ';'
            . 'uploadEvents.arenaName = "' . $arenaName .'";'
            . 'uploadEvents.onReady();',
            CClientScript::POS_READY
    );
?>
<?php else: ?>
<script type="text/javascript">
    utilities.urls.login = "<?php echo $this->createUrl('site/login'); ?>";
    utilities.urls.logout = "<?php echo $this->createUrl('site/logout'); ?>";
    utilities.urls.base = "<?php echo Yii::app()->request->baseUrl; ?>";
    utilities.urls.assets = "<?php echo $path; ?>";
    utilities.debug = <?php echo (defined('YII_DEBUG') ? 'true' : 'false'); ?>;
    uploadEvents.step = 1;
    uploadEvents.arenaId = <?php echo $arenaId; ?>;
    uploadEvents.arenaName = "<?php echo $arenaName; ?>";
    uploadEvents.onReady();
</script>
<?php endif; ?>
