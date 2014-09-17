<?php
    /**
     * This doubles as both a view/edit form for existing records
     * 
     * @var $this ManagementController
     * @var $model Contact
     * @var $path string
     * @var $doReady boolean
     * @var $newRecord
     * @var $params []
     */

    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<?php
    // We are going to grab two lists of arenas for the list views
    // We need the arena name and id, along with the city, state, and status
if(!isset($newRecord) || !$newRecord) {
    $availableArenas = Arena::getAvailableAssignedForContact($model->id, Yii::app()->user->id);
    $assignedArenas = Arena::getAssignedAssignedForContact($model->id, Yii::app()->user->id);
}
?>
<div id="contactManagementView" class="panel panel-primary">
    <div class="panel-heading">
        <h3>
            Contacts
        </h3>
    </div>
    <div class="panel-body">
        <div class="row-fluid">
            <div class="span6">
                <strong>Available Arenas</strong><br />
                <select id="availableContactsMSelect"
                        multiple
                        class="span12">
                    <?php if(!isset($newRecord) || !$newRecord) : ?>
                    <?php foreach($availableArenas as $aav) : ?>
                    <option value="<?php echo $aav['id']; ?>">
                       <?php echo $aav['name'] . ', ' . $aav['city'] . ', ' . $aav['state'] . ' (' . $aav['status'] . ')'; ?>
                    </option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="span6">
                <strong>Assigned Arenas</strong><br />
                <select id="assignedContactsMSelect"
                        multiple
                        class="span12">
                    <?php if(!isset($newRecord) || !$newRecord) : ?>
                    <?php foreach($assignedArenas as $aa) : ?>
                    <option value="<?php echo $aa['id']; ?>">
                        <?php echo $aa['name'] . ', ' . $aa['city'] . ', ' . $aa['state'] . ' (' . $aa['status'] . ')'; ?>
                    </option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <strong>Assignment Actions</strong><br />
                <div class="well">
                    <div class="row-fluid">
                        <div class="span3 offset3">
                            <button class="btn btn-block btn-success"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Assign contact to selected facilities"
                                    id="assignContactButton">
                                <i class="fa fa-lg fa-fw fa-chevron-right"></i> <br />
                                <span>Assign</span>
                            </button>
                        </div>
                        <div class="span3">
                            <button class="btn btn-block btn-warning"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Unassign contact from selected facilities"
                                    id="unassignContactButton">
                                <i class="fa fa-lg fa-fw fa-chevron-left"></i> <br />
                                <span>Unassign</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <strong>Select a contact to edit</strong><br />
                <select id="assignedContactsSelect"
                        class="span12">
                    <option value="none"></option>
                    <?php if(!isset($newRecord) || !$newRecord) : ?>
                    <option value="<?php echo $model->id; ?>"  selected="selected">
                        <?php echo $model->last_name . ', ' . $model->first_name . ' - ' . $model->email . ($model->active == 1 ? ' (Active)' : ' (Inactive)'); ?>
                    </option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <strong>Contact Actions</strong><br />
                <div class="well">
                    <div class="row-fluid">
                        <div class="span3 offset3">
                            <button class="btn btn-block btn-primary"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Create a new contact"
                                    id="newContactButton">
                                <i class="fa fa-lg fa-plus-square"></i> <br />
                                <span>New</span>
                            </button>
                        </div>
                        <div class="span3">
                            <button class="btn btn-block btn-danger"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Delete this contact"
                                    id="deleteContactButton">
                                <i class="fa fa-lg fa-minus-square"></i> <br />
                                <span>Delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>                    
        <div class="row-fluid">
            <div class="span8 offset2">
                <div id="contactDetails">
                    
                </div>
                <div id="newContactButtons">
                    <button id="saveContactButton"
                            class="btn btn-large btn-primary"
                            type="button"
                            data-toggle="tooltip"
                            data-original-title="Save the new contact">
                        <i class="fa fa-lg fa-fw fa-check"></i>
                        <span>Save</span>
                    </button>
                    <button id="cancelContactButton"
                            class="btn btn-large pull-right"
                            type="button"
                            data-toggle="tooltip"
                            data-original-title="Cancel adding a new contact">
                        <i class="fa fa-lg fa-fw fa-times"></i>
                        <span>Cancel</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($doReady) : ?>
<?php
    $myScript = 'utilities.urls.login = "' . $this->createUrl('site/login') . '";'
            . 'utilities.urls.logout = "' . $this->createUrl('site/logout') . '";'
            . 'utilities.urls.base = "' . Yii::app()->request->baseUrl . '";'
            . 'utilities.urls.assets = "' . $path . '";'
            . 'utilities.debug = ' . (YII_DEBUG ? 'true' : 'false') . ';'
            . 'contactManagementView.endpoints.contact.newRecord = "' . $params['endpoints']['contact']['new'] . '";'
            . 'contactManagementView.endpoints.contact.updateRecord = "' . $params['endpoints']['contact']['update'] . '";'
            . 'contactManagementView.endpoints.contact.viewRecord = "' . $params['endpoints']['contact']['view'] . '";'
            . 'contactManagementView.endpoints.contact.deleteRecord = "' . $params['endpoints']['contact']['delete'] . '";'
            . 'contactManagementView.params = ' . json_encode($params['data']) . ';';
    if(!isset($newRecord) || !$newRecord) {
        $myScript .= 'contactManagementView.contact = ' . json_encode($model->attributes) . ';';
    }
    
    $myScript .= 'contactManagementView.isArenaManager = ' . (Yii::app()->user->isArenaManager() ? 1 : 0) . ';'
            . 'contactManagementView.Id = ' . (integer)Yii::app()->user->id . ';'
            . 'contactManagementView.Name = "' . Yii::app()->user->fullName . '";'
            . 'contactManagementView.onReady();';
    
    Yii::app()->clientScript->registerScript(
            'doReady_ArenaManagementView',
            $myScript,
            CClientScript::POS_READY
    );
?>
<?php else: ?>
<script type="text/javascript">
$(document).ready(function() {
    utilities.urls.login = "<?php echo $this->createUrl('site/login'); ?>";
    utilities.urls.logout = "<?php echo $this->createUrl('site/logout'); ?>";
    utilities.urls.base = "<?php echo Yii::app()->request->baseUrl; ?>";
    utilities.urls.assets = "<?php echo $path; ?>";
    utilities.debug = <?php echo (YII_DEBUG ? 'true' : 'false'); ?>;
    
    if(typeof contactManagementView === "undefined")
    {
        var scriptName = utilities.urls.assets + '/js/management/contactManagementView.' + (utilities.debug ? 'js' : 'min.js');
        
        $.ajax({
            url: scriptName,
            dataType: "script",
            cache: true,
            success: function () {
                console.log("Loaded: " + scriptName);
            },
            error: function(xhr, status, errorThrown) {
                utilities.ajaxError.show(
                    "Error",
                    "Failed to retrieve javsScript file",
                    xhr,
                    status,
                    errorThrown
                );
            }
        });
        
        var interval = setInterval(function () {
            if (typeof contactManagementView !== "undefined") {
                clearInterval(interval);
                contactManagementView.endpoints.contact.newRecord = "<?php echo $params['endpoints']['contact']['new']; ?>";
                contactManagementView.endpoints.contact.updateRecord = "<?php echo $params['endpoints']['contact']['update']; ?>";
                contactManagementView.endpoints.contact.viewRecord = "<?php echo $params['endpoints']['contact']['view']; ?>";
                contactManagementView.endpoints.contact.deleteRecord = "<?php echo $params['endpoints']['contact']['delete']; ?>";
                contactManagementView.params = <?php echo json_encode($params['data']); ?>;
                <?php if(!isset($newRecord) || !$newRecord) : ?>
                contactManagementView.contact = <?php echo json_encode($model->attributes); ?>;
                <?php endif; ?>
                contactManagementView.isArenaManager = <?php echo (Yii::app()->user->isArenaManager()) ? 1 : 0; ?>;
                contactManagementView.Id = <?php echo Yii::app()->user->id; ?>;
                contactManagementView.Name = "<?php echo Yii::app()->user->fullName; ?>";
                contactManagementView.onReady();
            } else if (console && console.log) {
                console.log("Loading... " + scriptName);
            }
        }, 500);
    }
    else
    {
        contactManagementView.endpoints.contact.newRecord = "<?php echo $params['endpoints']['contact']['new']; ?>";
        contactManagementView.endpoints.contact.updateRecord = "<?php echo $params['endpoints']['contact']['update']; ?>";
        contactManagementView.endpoints.contact.viewRecord = "<?php echo $params['endpoints']['contact']['view']; ?>";
        contactManagementView.endpoints.contact.deleteRecord = "<?php echo $params['endpoints']['contact']['delete']; ?>";
        contactManagementView.params = <?php echo json_encode($params['data']); ?>;
        <?php if(!isset($newRecord) || !$newRecord) : ?>
        contactManagementView.contact = <?php echo json_encode($model->attributes); ?>;
        <?php endif; ?>
        contactManagementView.isArenaManager = <?php echo (Yii::app()->user->isArenaManager()) ? 1 : 0; ?>;
        contactManagementView.Id = <?php echo Yii::app()->user->id; ?>;
        contactManagementView.Name = "<?php echo Yii::app()->user->fullName; ?>";
        contactManagementView.onReady();
    }
});
</script>
<?php endif; ?>