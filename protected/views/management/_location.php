<?php
    /**
     * This doubles as both a view/edit form for existing records
     * 
     * @var $this ManagementController
     * @var $model Location
     * @var $path string
     * @var $doReady boolean
     * @var $newRecord
     * @var $params []
     */

    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));

    $locationsStatuses = Location::getStatusesList();
    $locationsTypes = Location::getTypesList();
?>
<div id="locationManagementView" class="panel panel-primary">
    <div class="panel-heading">
        <h3>
            <?php echo $model->arena_name; ?> - Venues
        </h3>
    </div>
    <div class="panel-body">
        <div class="row-fluid">
            <div class="span12">
                <strong>Select a venue to edit</strong><br />
                <select id="locationsSelect"
                        class="span12">
                    <option value="none"></option>
                    <option value="<?php echo $model->id; ?>" selected="selected">
                                <?php echo $model->name . ' - ' . $model->type . ' (' . $model->status . ')'; ?>
                    </option>
                </select>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <strong>Venue Actions</strong><br />
                <div class="well">
                    <div class="row-fluid">
                        <div class="span3 offset3">
                            <button class="btn btn-block btn-primary"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Create a new venue"
                                    id="newLocationButton">
                                <i class="fa fa-lg fa-plus-square"></i> <br />
                                <span>New</span>
                            </button>
                        </div>
                        <div class="span3">
                            <button class="btn btn-block btn-danger"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Delete this venue"
                                    id="deleteLocationButton">
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
                <div id="locationDetails">
                    
                </div>
                <div id="newLocationButtons">
                    <button id="saveLocationButton"
                            class="btn btn-large btn-primary"
                            type="button"
                            data-toggle="tooltip"
                            data-original-title="Save the new venue">
                        <i class="fa fa-lg fa-fw fa-check"></i>
                        <span>Save</span>
                    </button>
                    <button id="cancelLocationButton"
                            class="btn btn-large pull-right"
                            type="button"
                            data-toggle="tooltip"
                            data-original-title="Cancel adding a new venue">
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
            . 'locationManagementView.endpoints.location.newRecord = "' . $params['endpoints']['location']['new'] . '";'
            . 'locationManagementView.endpoints.location.updateRecord = "' . $params['endpoints']['location']['update'] . '";'
            . 'locationManagementView.endpoints.location.viewRecord = "' . $params['endpoints']['location']['view'] . '";'
            . 'locationManagementView.endpoints.location.deleteRecord = "' . $params['endpoints']['location']['delete'] . '";'
            . 'locationManagementView.params = ' . json_encode($params['data']) . ';'
            . 'locationManagementView.location = ' . json_encode($model->attributes) . ';'
            . 'locationManagementView.location.arena_name = "' . $model->arena_name . '";'
            . 'locationManagementView.location.type = "' . $model->type . '";'
            . 'locationManagementView.location.status = "' . $model->status . '";'
            . 'locationManagementView.locationTypes = ' . json_encode($locationsTypes) . ';'
            . 'locationManagementView.locationStatuses = ' . json_encode($locationsStatuses) . ';'
            . 'locationManagementView.isArenaManager = ' . (Yii::app()->user->isArenaManager() ? 1 : 0) . ';'
            . 'locationManagementView.Id = ' . (integer)Yii::app()->user->id . ';'
            . 'locationManagementView.Name = "' . Yii::app()->user->fullName . '";'
            . 'locationManagementView.onReady();';
    
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
    
    if(typeof locationManagementView === "undefined")
    {
        var scriptName = utilities.urls.assets + '/js/management/locationManagementView.' + (utilities.debug ? 'js' : 'min.js');
        
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
            if (typeof locationManagementView !== "undefined") {
                clearInterval(interval);
                locationManagementView.endpoints.location.newRecord = "<?php echo $params['endpoints']['location']['new']; ?>";
                locationManagementView.endpoints.location.updateRecord = "<?php echo $params['endpoints']['location']['update']; ?>";
                locationManagementView.endpoints.location.viewRecord = "<?php echo $params['endpoints']['location']['view']; ?>";
                locationManagementView.endpoints.location.deleteRecord = "<?php echo $params['endpoints']['location']['delete']; ?>";
                locationManagementView.params = <?php echo json_encode($params['data']); ?>;
                locationManagementView.location = <?php echo json_encode($model->attributes); ?>;
                locationManagementView.location.arena_name = "<?php echo $model->arena_name; ?>";
                locationManagementView.location.type = "<?php echo $model->type; ?>";
                locationManagementView.location.status = "<?php echo $model->status; ?>";
                locationManagementView.locationTypes = <?php echo json_encode($locationsTypes); ?>;
                locationManagementView.locationsStatuses = <?php echo json_encode($locationsStatuses); ?>;
                locationManagementView.isArenaManager = <?php echo (Yii::app()->user->isArenaManager()) ? 1 : 0; ?>;
                locationManagementView.Id = <?php echo Yii::app()->user->id; ?>;
                locationManagementView.Name = "<?php echo Yii::app()->user->fullName; ?>";
                locationManagementView.onReady();
            } else if (console && console.log) {
                console.log("Loading... " + scriptName);
            }
        }, 500);
    }
    else
    {
        locationManagementView.endpoints.location.newRecord = "<?php echo $params['endpoints']['location']['new']; ?>";
        locationManagementView.endpoints.location.updateRecord = "<?php echo $params['endpoints']['location']['update']; ?>";
        locationManagementView.endpoints.location.viewRecord = "<?php echo $params['endpoints']['location']['view']; ?>";
        locationManagementView.endpoints.location.deleteRecord = "<?php echo $params['endpoints']['location']['delete']; ?>";
        locationManagementView.params = <?php echo json_encode($params['data']); ?>;
        locationManagementView.location = <?php echo json_encode($model->attributes); ?>;
        locationManagementView.location.arena_name = "<?php echo $model->arena_name; ?>";
        locationManagementView.location.type = "<?php echo $model->type; ?>";
        locationManagementView.location.status = "<?php echo $model->status; ?>";
        locationManagementView.locationTypes = <?php echo json_encode($locationsTypes); ?>;
        locationManagementView.locationsStatuses = <?php echo json_encode($locationsStatuses); ?>;
        locationManagementView.isArenaManager = <?php echo (Yii::app()->user->isArenaManager()) ? 1 : 0; ?>;
        locationManagementView.Id = <?php echo Yii::app()->user->id; ?>;
        locationManagementView.Name = "<?php echo Yii::app()->user->fullName; ?>";
        locationManagementView.onReady();
    }
});
</script>
<?php endif; ?>