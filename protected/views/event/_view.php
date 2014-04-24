<?php
    /* @var $this EventController   */
    /* @var $data mixed[]           */
    /* @var $arena Arena            */
    /* @var $path string            */
    /* @var $doReady boolean        */
?>

<div class="my-calendar-list">
    <?php if(!is_null($arena) && isset($arena->name) && !empty($arena->name)) : ?>
    <div class="row-fluid">
        <div class="span4">
            <?php if(isset($arena->logo) && !empty($arena->logo) && $arena->logo != '') : ?>
            <img class="img-circle"
                 src="<?php echo $arena->logo; ?>"
                 alt="Facility Logo">
            <?php endif; ?>
        </div>
        <div class="span8">
            <h2 id="arenaHeader"><a href="<?php echo Yii::app()->createAbsoluteUrl('/arena/view', array('id' => $arena->id)); ?>"><?php echo CHtml::encode($arena->name); ?></a>
                <br />
                <small class="text-muted">
                    <address>
                        <?php echo $arena->address_line1; ?><br />
                        <?php if(isset($arena->address_line2) && !empty($arena->address_line2)) {
                            echo $arena->address_line2 . '<br />';
                        } ?>
                        <?php echo $arena->city . ', ' . $arena->state . ' ' . $arena->zip . '<br />'; ?>
                        <?php if(isset($arena->phone) && !empty($arena->phone)) {
                            echo '<abbr title="Phone">P:</abbr> ' . RinkfinderActiveRecord::format_telephone($arena->phone);
                            if(isset($arena->ext) && !empty($arena->ext)) {
                                echo ' <abbr title="Extension">E:</abbr> ' . $arena->ext . '<br />';
                            } else {
                                echo '<br />';
                            }
                        } ?>
                        <?php if(isset($arena->fax) && !empty($arena->fax)) {
                            echo '<abbr title="Fax">F:</abbr> ' . RinkfinderActiveRecord::format_telephone($arena->fax);
                            if(isset($arena->fax_ext) && !empty($arena->fax_ext)) {
                                echo ' <abbr title="Fax Extension">E:</abbr> ' . $arena->fax_ext . '<br />';
                            } else {
                                echo '<br />';
                            }
                        } ?>
                        <?php if(isset($arena->url) && !empty($arena->url)) {
                            echo  '<abbr title="Home Page">H:</abbr> <a target="_blank" href="' . $arena->url . '">' . 'Home Page' . '</a><br />';
                        } ?>
                        <a target="_blank" href="http://maps.google.com/maps?daddr=<?php echo urlencode($arena->address_line1 . ', ' . $arena->city . ', ' . $arena->state . ' ' . $arena->zip); ?>">
                            Driving Directions
                        </a>
                    </address>
                </small>
            </h2>
        </div>
    </div>
    <?php endif; ?>
    <?php if($data['count'] <= 0) : ?>
    <div class="my-calendar-list-item">
        <div class="row-fluid">
            <div class="span12">
                <h3 class='text-center'>Event not found.</h3>
            </div>
        </div>
    </div>
    <?php else: ?>
    <?php $this->renderPartial('/event/_eventList', array('data' => $data)); ?>
    <?php endif; ?>
</div>

<?php if($doReady) : ?>
<?php
$mScript = '';
if(!Yii::app()->user->isGuest) {
    $mScript = 'eventCalendar.requester = { '
            . '    requester_name: "' . Yii::app()->user->fullName . '", '
            . '    requester_email: "' . Yii::app()->user->email . '", '
            . '    requester_phone: "' . Yii::app()->user->phone . '" '
            . '};';
}
$script = 'utilities.urls.login = "' . $this->createUrl('site/login') . '";'
        . 'utilities.urls.logout = "' . $this->createUrl('site/logout') . '";'
        . 'utilities.urls.base = "' . Yii::app()->request->baseUrl . '";'
        . 'utilities.urls.assets = "' . $path . '";'
        . 'utilities.debug = ' . (defined('YII_DEBUG') ? 'true' : 'false') . ';'
        . 'if(typeof eventCalendar === "undefined") '
        . '{ '
        . "    var scriptName = utilities.urls.assets + '/js/event/calendar.' + "
        . "        (utilities.debug ? 'js' : 'min.js'); "
        . "    $.ajax({"
        . "        url: scriptName,"
        . "        dataType: 'script',"
        . "        cache: true,"
        . "        success: function () { "
        . "            console.log('Loaded: ' + scriptName);"
        . "        },"
        . "        error: function(xhr, status, errorThrown) { "
        . "            utilities.ajaxError.show( 'Error', "
        . "                'Failed to retrieve javsScript file',"
        . "                xhr,"
        . "                status,"
        . "                errorThrown"
        . "            );"
        . "        }"
        . "    }); "
        . "    "
        . "    var interval = setInterval(function () {"
        . "        if (typeof eventCalendar !== 'undefined') {"
        . "            clearInterval(interval); " . $mScript
        . "            eventCalendar.onReady(); "
        . "        } else if (console && console.log) {"
        . "            console.log('Loading arenaIndex.js');"
        . "        }"
        . "    }, 500);"
        . "}"
        . "else { " . $mScript 
        . "    eventCalendar.onReady();"
        . "}";

    Yii::app()->clientScript->registerScript(
            'doReady_eventCalendar', $script, CClientScript::POS_READY);
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
                <?php if(!Yii::app()->user->isGuest) : ?>
                eventCalendar.requester = {
                    requester_name: '<?php echo Yii::app()->user->fullName; ?>',
                    requester_email: '<?php echo Yii::app()->user->email; ?>',
                    requester_phone: '<?php echo Yii::app()->user->phone; ?>'
                };
                <?php endif; ?>
        
                eventCalendar.onReady();
            } else if (console && console.log) {
                console.log("Loading arenaIndex.js");
            }
        }, 500);
    }
    else
    {
        <?php if(!Yii::app()->user->isGuest) : ?>
        eventCalendar.requester = {
        requester_name: '<?php echo Yii::app()->user->fullName; ?>',
        requester_email: '<?php echo Yii::app()->user->email; ?>',
        requester_phone: '<?php echo Yii::app()->user->phone; ?>'
        };
        <?php endif; ?>
        
        eventCalendar.onReady();
    }
});
</script>
<?php endif; ?>
