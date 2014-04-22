<?php
    /* @var $this EventController */
    /* @var $data mixed[] */
    /* @var $start_date string */
    /* @var $path string */
    /* @var $doReady boolean */
    $sdTime = strtotime($data['params']['start_date']);
    $nextMonth = date('Y-m-01', strtotime("+1 month", $sdTime));
    $previousMonth = date('Y-m-01', strtotime("-1 month", $sdTime));
    
    $nextParams = $data['params'];
    $previousParams = $data['params'];
    
    $nextParams['start_date'] = $nextMonth;
    unset($nextParams['end_date']);
    
    $previousParams['start_date'] = $previousMonth;
    unset($previousParams['end_date']);
    
    $nextUrl = $this->createAbsoluteUrl($data['requestUrl'], $nextParams);
    $previousUrl = $this->createAbsoluteUrl($data['requestUrl'], $previousParams);
?>

<div class="my-calendar-list">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="text-center">
                <?php if($data['month'] != '') : ?>
                Events for <?php echo $data['month'] . ' ' . $data['year']; ?>
                <?php else: ?>
                Events
                <?php endif; ?>
            </h3>
            <ul class="pager">
                <?php if($data['month'] . $data['year'] == date("F", time()) . date("Y", time())) : ?>
                <?php else : ?>
                <li class="previous">
                    <a class="previous-link" href="<?php echo $previousUrl; ?>">&larr; Previous</a>
                </li>
                <?php endif; ?>
                <li class="next">
                    <a class="next-link" href="<?php echo $nextUrl; ?>">Next &rarr;</a>
                </li>
            </ul>
        </div>
    </div>
    <?php $this->renderPartial('/event/_eventList', array('data' => $data)); ?>
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
            . 'eventCalendar.onReady();',
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
    
    if(typeof eventCalendar === "undefined")
    {
        var scriptName = utilities.urls.assets + '/js/event/calendar.' + (utilities.debug ? 'js' : 'min.js');
        
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
            if (typeof eventCalendar !== "undefined") {
                clearInterval(interval);
                eventCalendar.onReady();
            } else if (console && console.log) {
                console.log("Loading arenaIndex.js");
            }
        }, 500);
    }
    else
    {
        eventCalendar.onReady();
    }
});
</script>
<?php endif; ?>
