<?php
    /**
     * This doubles as both a view/edit form for existing records
     * 
     * @var $this ManagementController
     * @var $model Manager
     * @var $path string
     * @var $doReady boolean
     * @var $newRecord
     * @var $params []
     */

    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));

    $managersStatuses = Manager::getStatusesList();
    $managerRole = Manager::getTypesList();
    $arenas = User::getArenasList(Yii::app()->user->id);
    if(!$newRecord) {
        $locations = Arena::getLocationsList($model->arena_id);
    } else {
        $locations = array();
    }
?>
<div id="managerManagementView" class="panel panel-primary">
    <div class="panel-heading">
        <h3>
            <?php echo (!$newRecord ? $model->arena_name . ' - Managers' : 'New Manager'); ?>
        </h3>
    </div>
    <div class="panel-body">
        <div class="row-fluid">
            <div class="span12">
                <strong>Select an manager to edit</strong><br />
                <select id="managersSelect"
                        class="span12">
                    <option value="none"></option>
                    <?php if(!$newRecord) : ?>
                    <option value="<?php echo $model->id; ?>" selected="selected">
                                <?php echo $model->managerName . ' - ' . $model->etype . ' (' . $model->estatus . ')'; ?>
                    </option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <strong>Manager Actions</strong><br />
                <div class="well">
                    <div class="row-fluid">
                        <div class="span3 offset3">
                            <button class="btn btn-block btn-primary"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Create a new manager"
                                    id="newManagerButton">
                                <i class="fa fa-lg fa-plus-square"></i> <br />
                                <span>New</span>
                            </button>
                        </div>
                        <div class="span3">
                            <button class="btn btn-block btn-danger"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Delete this manager"
                                    id="deleteManagerButton">
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
                <div id="managerDetails">
                    
                </div>
                <div id="newManagerButtons">
                    <button id="saveManagerButton"
                            class="btn btn-large btn-primary"
                            type="button"
                            data-toggle="tooltip"
                            data-original-title="Save the new manager">
                        <i class="fa fa-lg fa-fw fa-check"></i>
                        <span>Save</span>
                    </button>
                    <button id="cancelManagerButton"
                            class="btn btn-large pull-right"
                            type="button"
                            data-toggle="tooltip"
                            data-original-title="Cancel adding a new manager">
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
            . 'utilities.debug = ' . (defined('YII_DEBUG') ? 'true' : 'false') . ';'
            . 'managerManagementView.endpoints.manager.newRecord = "' . $params['endpoints']['manager']['new'] . '";'
            . 'managerManagementView.endpoints.manager.updateRecord = "' . $params['endpoints']['manager']['update'] . '";'
            . 'managerManagementView.endpoints.manager.viewRecord = "' . $params['endpoints']['manager']['view'] . '";'
            . 'managerManagementView.endpoints.manager.deleteRecord = "' . $params['endpoints']['manager']['delete'] . '";'
            . 'managerManagementView.params = ' . json_encode($params['data']) . ';'
            . 'managerManagementView.manager = ' . (!$newRecord ? json_encode($model->attributes) : '{}') . ';'
            . 'managerManagementView.manager.arena_name = "' . (!$newRecord ? $model->arena_name : '') . '";'
            . 'managerManagementView.manager.location_name = "' . (!$newRecord ? $model->location_name : '') . '";'
            . 'managerManagementView.manager.managerName = "' . (!$newRecord ? $model->managerName : '') . '";'
            . 'managerManagementView.manager.startDate = "' . (!$newRecord ? $model->startDate : '') . '";'
            . 'managerManagementView.manager.startTime = "' . (!$newRecord ? $model->startTime : '') . '";'
            . 'managerManagementView.manager.type = "' . (!$newRecord ? $model->etype : '') . '";'
            . 'managerManagementView.manager.status = "' . (!$newRecord ? $model->estatus : '') . '";'
            . 'managerManagementView.arenas = ' . json_encode($arenas) . ';'
            . 'managerManagementView.locations = ' . json_encode($locations) . ';'
            . 'managerManagementView.managerTypes = ' . json_encode($managersTypes) . ';'
            . 'managerManagementView.managerStatuses = ' . json_encode($managersStatuses) . ';'
            . 'managerManagementView.newRecord = ' . $newRecord . ';'
            . 'managerManagementView.isArenaManager = ' . (Yii::app()->user->isArenaManager() ? 1 : 0) . ';'
            . 'managerManagementView.Id = ' . (integer)Yii::app()->user->id . ';'
            . 'managerManagementView.Name = "' . Yii::app()->user->fullName . '";'
            . 'managerManagementView.onReady();';
    
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
    utilities.debug = <?php echo (defined('YII_DEBUG') ? 'true' : 'false'); ?>;
    
    if(typeof managerManagementView === "undefined")
    {
        var scriptName = utilities.urls.assets + '/js/management/managerManagementView.' + (utilities.debug ? 'js' : 'min.js');
        
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
            if (typeof managerManagementView !== "undefined") {
                clearInterval(interval);
                managerManagementView.endpoints.manager.newRecord = "<?php echo $params['endpoints']['manager']['new']; ?>";
                managerManagementView.endpoints.manager.updateRecord = "<?php echo $params['endpoints']['manager']['update']; ?>";
                managerManagementView.endpoints.manager.viewRecord = "<?php echo $params['endpoints']['manager']['view']; ?>";
                managerManagementView.endpoints.manager.deleteRecord = "<?php echo $params['endpoints']['manager']['delete']; ?>";
                managerManagementView.params = <?php echo json_encode($params['data']); ?>;
                managerManagementView.manager = <?php echo (!$newRecord ? json_encode($model->attributes) : '{}'); ?>;
                managerManagementView.manager.arena_name = "<?php echo (!$newRecord ? $model->arena_name : ''); ?>";
                managerManagementView.manager.location_name = "<?php echo (!$newRecord ? $model->location_name : ''); ?>";
                managerManagementView.manager.managerName = "<?php echo (!$newRecord ? $model->managerName : ''); ?>";
                managerManagementView.manager.startDate = "<?php echo (!$newRecord ? $model->startDate : ''); ?>";
                managerManagementView.manager.startTime = "<?php echo (!$newRecord ? $model->startTime : ''); ?>";
                managerManagementView.manager.type = "<?php echo (!$newRecord ? $model->etype : ''); ?>";
                managerManagementView.manager.status = "<?php echo (!$newRecord ? $model->estatus : ''); ?>";
                managerManagementView.arenas = <?php echo json_encode($arenas); ?>;
                managerManagementView.locations = <?php echo json_encode($locations); ?>;
                managerManagementView.managerTypes = <?php echo json_encode($managersTypes); ?>;
                managerManagementView.managerStatuses = <?php echo json_encode($managersStatuses); ?>;
                managerManagementView.newRecord = <?php echo $newRecord; ?>;
                managerManagementView.isArenaManager = <?php echo (Yii::app()->user->isArenaManager()) ? 1 : 0; ?>;
                managerManagementView.Id = <?php echo Yii::app()->user->id; ?>;
                managerManagementView.Name = "<?php echo Yii::app()->user->fullName; ?>";
                managerManagementView.onReady();
            } else if (console && console.log) {
                console.log("Loading... " + scriptName);
            }
        }, 500);
    }
    else
    {
        managerManagementView.endpoints.manager.newRecord = "<?php echo $params['endpoints']['manager']['new']; ?>";
        managerManagementView.endpoints.manager.updateRecord = "<?php echo $params['endpoints']['manager']['update']; ?>";
        managerManagementView.endpoints.manager.viewRecord = "<?php echo $params['endpoints']['manager']['view']; ?>";
        managerManagementView.endpoints.manager.deleteRecord = "<?php echo $params['endpoints']['manager']['delete']; ?>";
        managerManagementView.params = <?php echo json_encode($params['data']); ?>;
        managerManagementView.manager = <?php echo (!$newRecord ? json_encode($model->attributes) : '{}'); ?>;
        managerManagementView.manager.arena_name = "<?php echo (!$newRecord ? $model->arena_name : ''); ?>";
        managerManagementView.manager.location_name = "<?php echo (!$newRecord ? $model->location_name : ''); ?>";
        managerManagementView.manager.managerName = "<?php echo (!$newRecord ? $model->managerName : ''); ?>";
        managerManagementView.manager.startDate = "<?php echo (!$newRecord ? $model->startDate : ''); ?>";
        managerManagementView.manager.startTime = "<?php echo (!$newRecord ? $model->startTime : ''); ?>";
        managerManagementView.manager.type = "<?php echo (!$newRecord ? $model->etype : ''); ?>";
        managerManagementView.manager.status = "<?php echo (!$newRecord ? $model->estatus : ''); ?>";
        managerManagementView.arenas = <?php echo json_encode($arenas); ?>;
        managerManagementView.locations = <?php echo json_encode($locations); ?>;
        managerManagementView.managerTypes = <?php echo json_encode($managersTypes); ?>;
        managerManagementView.managerStatuses = <?php echo json_encode($managersStatuses); ?>;
        managerManagementView.newRecord = <?php echo $newRecord; ?>;
        managerManagementView.isArenaManager = <?php echo (Yii::app()->user->isArenaManager()) ? 1 : 0; ?>;
        managerManagementView.Id = <?php echo Yii::app()->user->id; ?>;
        managerManagementView.Name = "<?php echo Yii::app()->user->fullName; ?>";
        managerManagementView.onReady();
    }
});
</script>
<?php endif; ?>