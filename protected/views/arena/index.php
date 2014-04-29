<?php
    /* @var $this ArenaController   */
    /* @var $data mixed[]           */
    /* @var $start_date string      */
?>

<h2 id="arenaHeader">Facilities</h2>

<ul id="arenaList">
    <?php foreach($data as $record) : ?>
    <li id="arenaListItem<?php echo preg_replace("/[^A-Za-z0-9]/", "", $record['arena_name']) . $record['id']; ?>">
        <div class="well well-large">
            <div class="row-fluid info-row">
                <div class="span1">
                <?php if(isset($record['logo']) && !empty($record['logo']) && $record['logo'] != '') : ?>
                    <img class="img-circle"
                         src="<?php echo $record['logo']; ?>"
                         alt="Facility Logo">
                <?php endif; ?>
                </div>
                <div class="span10">
                    <a href="<?php echo $record['view_url']; ?>">
                        <strong><?php echo $record['arena_name']; ?></strong><br>
                    </a>
                    <span class="text-muted"><?php echo $record['city_state_zip']; ?></span>
                    <a class="searchable" style="display: none;" data-for="<?php echo "#arenaListItem" . preg_replace("/[^A-Za-z0-9]/", "", $record['arena_name']) . $record['id']; ?>">
                        <?php echo $record['tags']; ?>
                    </a>
                </div>
                <div class="span1 dropdown-info" data-for=".<?php echo preg_replace("/[^A-Za-z0-9]/", "", $record['arena_name']) . $record['id']; ?>">
                    <i class="fa fa-lg fa-chevron-down text-muted"></i>
                </div>
            </div>
            <div style="display: none;" class="row-fluid info-infos <?php echo preg_replace("/[^A-Za-z0-9]/", "", $record['arena_name']) . $record['id']; ?>">
                <div class="span10 offset1">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Facility information</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row-fluid">
                                <div class="span4">
                                    <h5><?php echo $record['arena_name']; ?></h5>
                                    <address>
                                        <?php echo $record['address_line1']; ?><br />
                                        <?php if(isset($record['address_line2']) && !empty($record['address_line2'])) {
                                            echo $record['address_line2'] . '<br />';
                                        } ?>
                                        <?php echo $record['city_state_zip'] . '<br />'; ?>
                                        <?php if(isset($record['phone']) && !empty($record['phone'])) {
                                            echo '<abbr title="Phone">P:</abbr> ' . RinkfinderActiveRecord::format_telephone($record['phone']);
                                            if(isset($record['ext']) && !empty($record['ext'])) {
                                                echo ' <abbr title="Extension">E:</abbr> ' . $record['ext'] . '<br />';
                                            } else {
                                                echo '<br />';
                                            }
                                        } ?>
                                        <?php if(isset($record['fax']) && !empty($record['fax'])) {
                                            echo '<abbr title="Fax">F:</abbr> ' . RinkfinderActiveRecord::format_telephone($record['fax']);
                                            if(isset($record['fax_ext']) && !empty($record['fax_ext'])) {
                                                echo ' <abbr title="Fax Extension">E:</abbr> ' . $record['fax_ext'] . '<br />';
                                            } else {
                                                echo '<br />';
                                            }
                                        } ?>
                                        <?php if(isset($record['home_url']) && !empty($record['home_url'])) {
                                            echo  '<abbr title="Home Page">H:</abbr> <a target="_blank" href="' . $record['home_url'] . '">' . 'Home Page' . '</a><br />';
                                        } ?>
                                        <br /><strong>Venues:</strong> <?php echo $record['location_count']; ?><br /><br />
                                        <a target="_blank" href="http://maps.google.com/maps?daddr=<?php echo urlencode($record['address_line1'] . ', ' . $record['city_state_zip']); ?>">
                                            Driving Directions
                                        </a>
                                    </address>
                                </div>
                                <?php if(isset($record['contacts']) && count($record['contacts']) > 0) : ?>
                                <div class="span4">
                                    <h5>Contacts</h5>
                                    <?php foreach($record['contacts'] as $contact) : ?>
                                    <address>
                                        <?php if($contact['contact_type'] == "Primary") : ?>
                                            <abbr title='Primary Contact'>*:</abbr> <strong><?php echo $contact['contact_name']; ?></strong><br />
                                        <?php else : ?>
                                            <?php echo $contact['contact_name']; ?><br />
                                        <?php endif; ?>
                                        <?php if(isset($contact['contact_phone']) && !empty($contact['contact_phone'])) {
                                            echo '<abbr title="Phone">P:</abbr> ' . RinkfinderActiveRecord::format_telephone($contact['contact_phone']);
                                            if(isset($contact['contact_ext']) && !empty($contact['contact_ext'])) {
                                                echo ' <abbr title="Extension">E:</abbr> ' . $contact['contact_ext'] . '<br />';
                                            } else {
                                                echo '<br />';
                                            }
                                        } ?>
                                        <?php if(isset($contact['contact_fax']) && !empty($contact['contact_fax'])) {
                                            echo '<abbr title="Fax">F:</abbr> ' . RinkfinderActiveRecord::format_telephone($contact['contact_fax']);
                                            if(isset($contact['contact_fax_ext']) && !empty($contact['contact_fax_ext'])) {
                                                echo ' <abbr title="Fax Extension">E:</abbr> ' . $contact['contact_fax_ext'] . '<br />';
                                            } else {
                                                echo '<br />';
                                            }
                                        } ?>
                                        <?php if(isset($contact['contact_email']) && !empty($contact['contact_email'])) {
                                            echo  '<abbr title="Email Address">M:</abbr> <a href="mailto:' . $contact['contact_email'] . '">' . $contact['contact_email'] . '</a><br />';
                                        } ?>
                                    </address>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                <?php if(isset($record['events']) && count($record['events']) > 0) : ?>
                                <div class="span4">
                                    <h5><a href="<?php echo $record['events_url']; ?>">Event Calendar For <?php echo date('F', strtotime($start_date)); ?></a></h5>
                                    <?php foreach($record['events'] as $event) : ?>
                                    <address>
                                        <strong><a href="<?php echo $event['event_view_url']; ?>"><?php echo $event['event_type_name']; ?></a></strong><br />
                                        First Available: <?php echo $event['start_date_time']; ?><br />
                                        Total Available: <?php echo $event['event_count']; ?><br />
                                    </address>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <div class="span4">
                                    <h5><a href="<?php echo $record['events_url']; ?>">Event Calendar</a></h5>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </li>
    <?php endforeach; ?>
</ul>



<?php if($doReady) : ?>
<?php
    Yii::app()->clientScript->registerScript(
            'doReady_Index',
            'utilities.urls.login = "' . $this->createUrl('site/login') . '";'
            . 'utilities.urls.logout = "' . $this->createUrl('site/logout') . '";'
            . 'utilities.urls.base = "' . Yii::app()->request->baseUrl . '";'
            . 'utilities.urls.assets = "' . $path . '";'
            . 'utilities.debug = ' . (defined('YII_DEBUG') ? 'true' : 'false') . ';'
            . 'arenaIndex.onReady();',
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
    
    if(typeof arenaIndex === "undefined")
    {
        var scriptName = utilities.urls.assets + '/js/arena/index.' + (utilities.debug ? 'js' : 'min.js');
        
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
            if (typeof arenaIndex !== "undefined") {
                clearInterval(interval);
                arenaIndex.onReady();
            } else if (console && console.log) {
                console.log("Loading arenaIndex.js");
            }
        }, 500);
    }
    else
    {
        arenaIndex.onReady();
    }
});
</script>
<?php endif; ?>
