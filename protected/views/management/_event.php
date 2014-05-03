<?php
    /**
     * This doubles as both a view/edit form for existing records
     * 
     * @var $this ManagementController
     * @var $model Event
     * @var $path string
     * @var $doReady boolean
     * @var $newRecord
     * @var $params []
     */

    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));

    $eventsStatuses = Event::getStatusesList();
    $eventsTypes = Event::getTypesList();
    $arenas = User::getArenasList(Yii::app()->user->id);
    if(!$newRecord) {
        $locations = Arena::getLocationsList($model->arena_id);
    } else {
        $locations = array();
    }
?>
<div id="eventManagementView" class="panel panel-primary">
    <div class="panel-heading">
        <h3>
            <?php echo (!$newRecord ? $model->arena_name . ' - Events' : 'New Event'); ?>
        </h3>
    </div>
    <div class="panel-body">
        <div class="row-fluid">
            <div class="span12">
                <strong>Select an event to edit</strong><br />
                <select id="eventsSelect"
                        class="span12">
                    <option value="none"></option>
                    <?php if(!$newRecord) : ?>
                    <option value="<?php echo $model->id; ?>" selected="selected">
                                <?php echo $model->eventName . ' - ' . $model->etype . ' (' . $model->estatus . ')'; ?>
                    </option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <strong>Event Actions</strong><br />
                <div class="well">
                    <div class="row-fluid">
                        <div class="span3 offset3">
                            <button class="btn btn-block btn-primary"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Create a new event"
                                    id="newEventButton">
                                <i class="fa fa-lg fa-plus-square"></i> <br />
                                <span>New</span>
                            </button>
                        </div>
                        <div class="span3">
                            <button class="btn btn-block btn-danger"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Delete this event"
                                    id="deleteEventButton">
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
                <div id="eventDetails">
                    
                </div>
                <div id="newEventButtons">
                    <button id="saveEventButton"
                            class="btn btn-large btn-primary"
                            type="button"
                            data-toggle="tooltip"
                            data-original-title="Save the new event">
                        <i class="fa fa-lg fa-fw fa-check"></i>
                        <span>Save</span>
                    </button>
                    <button id="cancelEventButton"
                            class="btn btn-large pull-right"
                            type="button"
                            data-toggle="tooltip"
                            data-original-title="Cancel adding a new event">
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
            . 'eventManagementView.endpoints.event.newRecord = "' . $params['endpoints']['event']['new'] . '";'
            . 'eventManagementView.endpoints.event.updateRecord = "' . $params['endpoints']['event']['update'] . '";'
            . 'eventManagementView.endpoints.event.viewRecord = "' . $params['endpoints']['event']['view'] . '";'
            . 'eventManagementView.endpoints.event.deleteRecord = "' . $params['endpoints']['event']['delete'] . '";'
            . 'eventManagementView.params = ' . json_encode($params['data']) . ';'
            . 'eventManagementView.event = ' . (!$newRecord ? json_encode($model->attributes) : '{}') . ';'
            . 'eventManagementView.event.arena_name = "' . (!$newRecord ? $model->arena_name : '') . '";'
            . 'eventManagementView.event.location_name = "' . (!$newRecord ? $model->location_name : '') . '";'
            . 'eventManagementView.event.eventName = "' . (!$newRecord ? $model->eventName : '') . '";'
            . 'eventManagementView.event.startDate = "' . (!$newRecord ? $model->startDate : '') . '";'
            . 'eventManagementView.event.startTime = "' . (!$newRecord ? $model->startTime : '') . '";'
            . 'eventManagementView.event.type = "' . (!$newRecord ? $model->etype : '') . '";'
            . 'eventManagementView.event.status = "' . (!$newRecord ? $model->estatus : '') . '";'
            . 'eventManagementView.arenas = ' . json_encode($arenas) . ';'
            . 'eventManagementView.locations = ' . json_encode($locations) . ';'
            . 'eventManagementView.eventTypes = ' . json_encode($eventsTypes) . ';'
            . 'eventManagementView.eventStatuses = ' . json_encode($eventsStatuses) . ';'
            . 'eventManagementView.newRecord = ' . $newRecord . ';'
            . 'eventManagementView.isArenaManager = ' . (Yii::app()->user->isArenaManager() ? 1 : 0) . ';'
            . 'eventManagementView.Id = ' . (integer)Yii::app()->user->id . ';'
            . 'eventManagementView.Name = "' . Yii::app()->user->fullName . '";'
            . 'eventManagementView.onReady();';
    
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
    
    if(typeof eventManagementView === "undefined")
    {
        var scriptName = utilities.urls.assets + '/js/management/eventManagementView.' + (utilities.debug ? 'js' : 'min.js');
        
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
            if (typeof eventManagementView !== "undefined") {
                clearInterval(interval);
                eventManagementView.endpoints.event.newRecord = "<?php echo $params['endpoints']['event']['new']; ?>";
                eventManagementView.endpoints.event.updateRecord = "<?php echo $params['endpoints']['event']['update']; ?>";
                eventManagementView.endpoints.event.viewRecord = "<?php echo $params['endpoints']['event']['view']; ?>";
                eventManagementView.endpoints.event.deleteRecord = "<?php echo $params['endpoints']['event']['delete']; ?>";
                eventManagementView.params = <?php echo json_encode($params['data']); ?>;
                eventManagementView.event = <?php echo (!$newRecord ? json_encode($model->attributes) : '{}'); ?>;
                eventManagementView.event.arena_name = "<?php echo (!$newRecord ? $model->arena_name : ''); ?>";
                eventManagementView.event.location_name = "<?php echo (!$newRecord ? $model->location_name : ''); ?>";
                eventManagementView.event.eventName = "<?php echo (!$newRecord ? $model->eventName : ''); ?>";
                eventManagementView.event.startDate = "<?php echo (!$newRecord ? $model->startDate : ''); ?>";
                eventManagementView.event.startTime = "<?php echo (!$newRecord ? $model->startTime : ''); ?>";
                eventManagementView.event.type = "<?php echo (!$newRecord ? $model->etype : ''); ?>";
                eventManagementView.event.status = "<?php echo (!$newRecord ? $model->estatus : ''); ?>";
                eventManagementView.arenas = <?php echo json_encode($arenas); ?>;
                eventManagementView.locations = <?php echo json_encode($locations); ?>;
                eventManagementView.eventTypes = <?php echo json_encode($eventsTypes); ?>;
                eventManagementView.eventStatuses = <?php echo json_encode($eventsStatuses); ?>;
                eventManagementView.newRecord = <?php echo $newRecord; ?>;
                eventManagementView.isArenaManager = <?php echo (Yii::app()->user->isArenaManager()) ? 1 : 0; ?>;
                eventManagementView.Id = <?php echo Yii::app()->user->id; ?>;
                eventManagementView.Name = "<?php echo Yii::app()->user->fullName; ?>";
                eventManagementView.onReady();
            } else if (console && console.log) {
                console.log("Loading... " + scriptName);
            }
        }, 500);
    }
    else
    {
        eventManagementView.endpoints.event.newRecord = "<?php echo $params['endpoints']['event']['new']; ?>";
        eventManagementView.endpoints.event.updateRecord = "<?php echo $params['endpoints']['event']['update']; ?>";
        eventManagementView.endpoints.event.viewRecord = "<?php echo $params['endpoints']['event']['view']; ?>";
        eventManagementView.endpoints.event.deleteRecord = "<?php echo $params['endpoints']['event']['delete']; ?>";
        eventManagementView.params = <?php echo json_encode($params['data']); ?>;
        eventManagementView.event = <?php echo (!$newRecord ? json_encode($model->attributes) : '{}'); ?>;
        eventManagementView.event.arena_name = "<?php echo (!$newRecord ? $model->arena_name : ''); ?>";
        eventManagementView.event.location_name = "<?php echo (!$newRecord ? $model->location_name : ''); ?>";
        eventManagementView.event.eventName = "<?php echo (!$newRecord ? $model->eventName : ''); ?>";
        eventManagementView.event.startDate = "<?php echo (!$newRecord ? $model->startDate : ''); ?>";
        eventManagementView.event.startTime = "<?php echo (!$newRecord ? $model->startTime : ''); ?>";
        eventManagementView.event.type = "<?php echo (!$newRecord ? $model->etype : ''); ?>";
        eventManagementView.event.status = "<?php echo (!$newRecord ? $model->estatus : ''); ?>";
        eventManagementView.arenas = <?php echo json_encode($arenas); ?>;
        eventManagementView.locations = <?php echo json_encode($locations); ?>;
        eventManagementView.eventTypes = <?php echo json_encode($eventsTypes); ?>;
        eventManagementView.eventStatuses = <?php echo json_encode($eventsStatuses); ?>;
        eventManagementView.newRecord = <?php echo $newRecord; ?>;
        eventManagementView.isArenaManager = <?php echo (Yii::app()->user->isArenaManager()) ? 1 : 0; ?>;
        eventManagementView.Id = <?php echo Yii::app()->user->id; ?>;
        eventManagementView.Name = "<?php echo Yii::app()->user->fullName; ?>";
        eventManagementView.onReady();
    }
});
</script>
<?php endif; ?>