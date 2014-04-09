<?php
    /* @var $model ArenaUploadForm */
    /* @var $form TbActiveForm */
    /* @var $fields array[][] */
    /* @var $path string */
    /* @var $doReady boolean */
?>

<div id="arenaInstructions" class="row-fluid">
    <a href="#arenaModalFileUpload" role="button" class="btn btn-large" data-toggle="modal">
        <i class="icon-info-sign"></i> Overview
    </a>
    <a href="#arenaModalFields" role="button" class="btn btn-large" data-toggle="modal">
        <i class="icon-info-sign"></i> Field Information
    </a>
    <br><br>
</div>
<div id="arenaUploadStep1" class="row-fluid">
    <h3 class="sectionSubHeader">
        Step 1: <h4>Select A File To Upload</h4>
    </h3>
    <div class="control-group">
        <div class="controls">
            <div id="ArenaUploadForm_fileName" name="ArenaUploadForm[fileName]">
                <noscript>
                    Please enable JavaScript to use file uploader.
                </noscript>
            </div>
        </div>
    </div>
</div><!-- step 1 -->
<div id="arenaUploadStep2" class="row-fluid" style="display: none;">
    <h3 class="sectionSubHeader">
        Step 2: <h4>Import Options</h4>
    </h3>
    <a href="#arenaModalSettings" role="button" class="btn btn-large" data-toggle="modal">
        <i class="icon-info-sign"></i> Instructions
    </a>
    <br><br>
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
                Update existing records? (If you select No and an arena exists
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
<div id="arenaUploadStep3" class="row-fluid" style="display: none;">
    <h3 class="sectionSubHeader">
        Step 3: <h4>Import Mappings</h4>
    </h3>
    <a href="#arenaModalMapping" role="button" class="btn btn-large" data-toggle="modal">
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
<!-- Mapping Instructions Modal Dialog -->
<div id="arenaModalMapping" class="modal hide fade container" tabindex="-1" 
     role="dialog" aria-labelledby="arenaModalMappingLabel" 
     aria-hidden="true" data-backdrop="static" data-max-height="500" >
  <div id="arenaModalMappingHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="arenaModalMappingLabel">Mapping Instructions</h3>
  </div>
  <div id="arenaModalMappingBody" class="modal-body">
      <?php $this->renderPartial('/arena/_uploadArenasInstructionsStep3a'); ?>
  </div>
  <div id="arenaModalMappingFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Mapping Instructions Modal Dialog -->
<!-- Settings Instructions Modal Dialog -->
<div id="arenaModalSettings" class="modal hide fade container" tabindex="-1" 
     role="dialog" aria-labelledby="arenaModalSettingsLabel" 
     aria-hidden="true" data-backdrop="static" data-max-height="500" >
  <div id="arenaModalSettingsHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="arenaModalSettingsLabel">Import Options Instructions</h3>
  </div>
  <div id="arenaModalSettingsBody" class="modal-body">
      <?php $this->renderPartial('/arena/_uploadArenasInstructionsStep2'); ?>
  </div>
  <div id="arenaModalSettingsFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Settings Instructions Modal Dialog -->
<!-- File Uploading Instructions Modal Dialog -->
<div id="arenaModalFileUpload" class="modal hide fade container" tabindex="-1"
     role="dialog" aria-labelledby="arenaModalFileUploadLabel" 
     aria-hidden="true" data-backdrop="static" data-max-height="500" >
  <div id="arenaModalFileUploadHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="arenaModalFileUploadLabel">Import Instructions</h3>
  </div>
  <div id="arenaModalFileUploadBody" class="modal-body">
      <?php $this->renderPartial('/arena/_uploadArenasInstructionsStep1'); ?>
  </div>
  <div id="arenaModalFileUploadFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- File Uploading Instructions Modal Dialog -->
<!-- Fields and File Information Modal Dialog -->
<div id="arenaModalFields" class="modal hide fade container" tabindex="-1" 
     role="dialog" aria-labelledby="arenaModalFieldsLabel"
     aria-hidden="true" data-backdrop="static" data-max-height="500" >
  <div id="arenaModalFieldsHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="arenaModalFieldsLabel">Fields Information</h3>
  </div>
  <div id="arenaModalFieldsBody" class="modal-body">
      <?php $this->renderPartial('/arena/_uploadArenasInstructionsStep3b', array('fields' => $fields)); ?>
  </div>
  <div id="arenaModalFieldsFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Fields and File Information Modal Dialog -->

<?php if($doReady) : ?>
<?php
    Yii::app()->clientScript->registerScript(
            'doReadyUploadArenas',
            'utilities.urls.login = "' . $this->createUrl('site/login') . '";'
            . 'utilities.urls.logout = "' . $this->createUrl('site/logout') . '";'
            . 'utilities.urls.base = "' . Yii::app()->request->baseUrl . '";'
            . 'utilities.urls.assets = "' . $path . '";'
            . 'utilities.debug = ' . (defined('YII_DEBUG') ? 'true' : 'false') . ';'
            . 'uploadArenas.step = 1;'
            . 'uploadArenas.onReady();',
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
    uploadArenas.step = 1;
    uploadArenas.onReady();
</script>
<?php endif; ?>
