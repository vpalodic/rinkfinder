<?php
?>

<div class="row-fluid">
    <form id="searchForm" class="form" method="get" action="<?php echo $this->createUrl('arena/locationSearch'); ?>">
        <div class="well well-small">
            <fieldset>
                <legend>Locate a Facility</legend>
                <div class="controls controls-row">
                    <label for="addressInput">Starting Location</label>
                    <?php if(isset($_GET['saddr']) && !empty($_GET['saddr'])) : ?>
                    <input name="saddr" value="<?php echo $_GET['saddr']; ?>" style="margin-bottom: 15px;" class="span3 search-query" type="text" id="addressInput" placeholder="123 Main St, St Paul, MN 55122" />
                    <?php else: ?>
                    <input name="saddr" style="margin-bottom: 15px;" class="span3 search-query" type="text" id="addressInput" placeholder="123 Main St, St Paul, MN 55122" />
                    <?php endif; ?>
                    <input type="hidden" name="submit" value="false">
                    <div class="span3">
                        <button rel="tooltip" title="Search" id="searchButton" style="margin-bottom: 15px;" class="btn btn-primary" type="submit">
                            <i class="fa fa-lg fa-search"></i>
                        </button>
                        <button rel="tooltip" title="Search Filter" id="searchFilterButton" style="margin-bottom: 15px;" class="btn" type="button">
                            <i class="fa fa-lg fa-filter"></i>
                        </button>
                        <button rel="tooltip" title="Reset" id="searchResetButton" style="margin-bottom: 15px;" class="btn btn-warning" type="reset">
                            <i class="fa fa-lg fa-times"></i>
                        </button>
                    </div>
                </div>
                <div id="searchFilterDiv" style="display: none;">
                    <div class="controls controls-row">
                        <label for="radiusSelect">Within</label>
                        <select name="radius" style="margin-bottom: 15px;" class="span3" id="radiusSelect">
                        <?php
                            $radius = (isset($_GET['radius']) && is_numeric($_GET['radius'])) ? $_GET['radius'] : 20;
                            $radii = array(
                                'No Limit' => 0,
                                '5 miles' => 5,
                                '10 miles' => 10,
                                '15 miles' => 15,
                                '20 miles' => 20,
                                '25 miles' => 25,
                                '50 miles' => 50,
                                '75 miles' => 75,
                                '100 miles' => 100,
                                '200 miles' => 200,
                            );
                            
                            foreach($radii as $key => $value) {
                                if($radius == $value) {
                                    echo '<option value="' . $value . '" selected="selected">' . $key . '</option>';
                                } else {
                                    echo '<option value="' . $value . '">' . $key . '</option>';
                                }
                            }
                        ?>
                        </select>
                    </div>
                    <div class="controls controls-row">
                        <label for="eventType">That Has</label>
                        <select name="types[]" style="margin-bottom: 15px;" class="span3" id="eventType" multiple="multiple">
                        <?php
                            $myTypes = isset($_GET['types']) && is_array($_GET['types']) ? $_GET['types'] : array();
                            
                            foreach($types as $type) {
                                if(in_array($type['id'], $myTypes, true)) {
                                    echo '<option selected="selected" value="' . $type['id'] . '">' . $type['display_name'] . '</option>';
                                } else {
                                    echo '<option value="' . $type['id'] . '">' . $type['display_name'] . '</option>';
                                }
                                
                            }
                        ?>
                        </select>
                    </div>
                    <div class="controls controls-row">
                        <label for="searchDate">Available On</label>
                        <div class="span3">
                            <div id="searchDate" class="mydate input-append date">
                                <?php if(isset($_GET['start_date']) && !empty($_GET['start_date'])) : ?>
                                <input class="uneditable-input" name="start_date" value="<?php echo $_GET['start_date']; ?>" style="margin-bottom: 15px;" data-format="MM/dd/yyyy" type="text" id="startDate" />
                                <?php else: ?>
                                <input class="uneditable-input" name="start_date" style="margin-bottom: 15px;" data-format="MM/dd/yyyy" type="text" id="startDate" />
                                <?php endif; ?>
                                <span class="add-on">
                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                                    </i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="controls controls-row">
                        <label for="searchDateEnd">Available To</label>
                        <div class="span3">
                            <div id="searchDateEnd" class="mydate input-append date">
                                <?php if(isset($_GET['end_date']) && !empty($_GET['end_date'])) : ?>
                                <input class="uneditable-input" name="end_date" value="<?php echo $_GET['end_date']; ?>" style="margin-bottom: 15px;" data-format="MM/dd/yyyy" type="text" id="endDate" />
                                <?php else: ?>
                                <input class="uneditable-input" name="end_date" style="margin-bottom: 15px;" data-format="MM/dd/yyyy" type="text" id="endDate" />
                                <?php endif; ?>
                                <span class="add-on">
                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                                    </i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="controls controls-row">
                        <label for="searchTime">Starts No Earlier Than</label>
                        <div class="span3">
                            <div id="searchTime" class="mytime input-append date">
                                <?php if(isset($_GET['start_time']) && !empty($_GET['start_time'])) : ?>
                                <input name="start_time" value="<?php echo $_GET['start_time']; ?>" style="margin-bottom: 15px;" data-format="HH:mm:ss PP" type="text" id="startTime" />
                                <?php else: ?>
                                <input name="start_time" style="margin-bottom: 15px;" data-format="HH:mm:ss PP" type="text" id="startTime" />
                                <?php endif; ?>
                                <span class="add-on">
                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                                    </i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="controls controls-row">
                        <label for="searchTimeEnd">Starts No Later Than</label>
                        <div class="span3">
                            <div id="searchTimeEnd" class="mytime input-append date">
                                <?php if(isset($_GET['end_time']) && !empty($_GET['end_time'])) : ?>
                                <input name="end_time" value="<?php echo $_GET['end_time']; ?>" style="margin-bottom: 15px;" data-format="HH:mm:ss PP" type="text" id="endTime" />
                                <?php else: ?>
                                <input name="end_time" style="margin-bottom: 15px;" data-format="HH:mm:ss PP" type="text" id="endTime" />
                                <?php endif; ?>
                                <span class="add-on">
                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                                    </i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </form>
</div>
<div class="row-fluid info-infos search-results">
    <div id="searchResults">
        <div class="span3">
            <div id="searchResultsWell" class="well well-small hidden">
                <div class="hidden-desktop hidden-tablet">
                    <select id="locationSelect" style="width:100%;"></select>
                </div>
                <div class="hidden-phone">
                    <ul id="locationList"  class="my-search-results">
                    </ul>
                </div>
            </div>
        </div>
        <div class="span9">
            <div class="google-map-canvas">
                <div id="map-canvas" style="width: 100%;  height: 100%;">
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($doReady) : ?>
<?php
    Yii::app()->clientScript->registerScript(
            'doReady_Index',
            'utilities.urls.login = "' . $this->createUrl('site/login') . '";'
            . 'utilities.urls.logout = "' . $this->createUrl('site/logout') . '";'
            . 'utilities.urls.base = "' . Yii::app()->request->baseUrl . '";'
            . 'utilities.urls.assets = "' . $path . '";'
            . 'utilities.debug = ' . (defined('YII_DEBUG') ? 'true' : 'false') . ';'
            . 'locationSearch.endpoints.markers = "' . $searchUrl . '";'
            . 'locationSearch.onReady();',
            CClientScript::POS_READY
    );
?>
<?php else: ?>
<script type="text/javascript">
$(document).ready(function () {
    utilities.urls.login = "<?php echo $this->createUrl('site/login'); ?>";
    utilities.urls.logout = "<?php echo $this->createUrl('site/logout'); ?>";
    utilities.urls.base = "<?php echo Yii::app()->request->baseUrl; ?>";
    utilities.urls.assets = "<?php echo $path; ?>";
    utilities.debug = <?php echo (defined('YII_DEBUG') ? 'true' : 'false'); ?>;
    
    if(typeof locationSearch === "undefined")
    {
        var scriptName = utilities.urls.assets + '/js/arena/locationSearch.' + (utilities.debug ? 'js' : 'min.js');
        
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
            if (typeof locationSearch !== "undefined") {
                clearInterval(interval);
                locationSearch.endpoints.markers = "<?php echo $searchUrl; ?>";
                locationSearch.onReady();
            } else if (console && console.log) {
                console.log("Loading locationSearch.js");
            }
        }, 500);
    }
    else
    {
        locationSearch.endpoints.markers = "<?php echo $searchUrl; ?>";
        locationSearch.onReady();
    }
});
</script>
<?php endif; ?>
