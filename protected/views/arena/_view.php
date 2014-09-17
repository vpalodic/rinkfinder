<?php
    /* @var $this ArenaController   */
    /* @var $data mixed[]           */
    /* @var $start_date string      */
    /* @var $doReady boolean        */
    /* @var $path string            */
?>

<div class="row-fluid">
    <div class="page-header">
        <div class="span4">
            <?php if(isset($data['logo']) && !empty($data['logo']) && $data['logo'] != '') : ?>
            <img class="img-circle"
                 src="<?php echo $data['logo']; ?>"
                 alt="Facility Logo">
            <?php endif; ?>
        </div>
        <div class="span8">
            <h2 id="arenaHeader"><?php echo isset($data['arena_name']) ? CHtml::encode($data['arena_name']) : ''; ?>
                <br />
                <address>
                    <small class="text-muted">
                        <?php echo isset($data['address_line1']) ? $data['address_line1'] : ''; ?><br />
                        <?php if(isset($data['address_line2']) && !empty($data['address_line2'])) {
                            echo $data['address_line2'] . '<br />';
                        } ?>
                        <?php echo (isset($data['city_state_zip']) ? $data['city_state_zip'] : '') . '<br />'; ?>
                        <?php if(isset($data['phone']) && !empty($data['phone'])) {
                            echo '<abbr title="Phone">P:</abbr> ' . RinkfinderActiveRecord::format_telephone($data['phone']);
                            if(isset($data['ext']) && !empty($data['ext'])) {
                                echo ' <abbr title="Extension">E:</abbr> ' . $data['ext'] . '<br />';
                            } else {
                                echo '<br />';
                            }
                        } ?>
                        <?php if(isset($data['fax']) && !empty($data['fax'])) {
                            echo '<abbr title="Fax">F:</abbr> ' . RinkfinderActiveRecord::format_telephone($data['fax']);
                            if(isset($data['fax_ext']) && !empty($data['fax_ext'])) {
                                echo ' <abbr title="Fax Extension">E:</abbr> ' . $data['fax_ext'] . '<br />';
                            } else {
                                echo '<br />';
                            }
                        } ?>
                        <?php if(isset($data['home_url']) && !empty($data['home_url'])) {
                            echo  '<abbr title="Home Page">H:</abbr> <a target="_blank" href="' . $data['home_url'] . '">' . 'Home Page' . '</a><br />';
                        } ?>
                        <?php if(isset($data['address_line2']) && !empty($data['address_line2'])) : ?>
                        <a target="_blank" href="http://maps.google.com/maps?daddr=<?php echo urlencode($data['address_line1'] . ', ' . $data['address_line2'] . ', ' . $data['city_state_zip']); ?>">
                            Driving Directions
                        </a>
                        <?php elseif(isset($data['address_line1']) && isset($data['city_state_zip'])) : ?>
                        <a target="_blank" href="http://maps.google.com/maps?daddr=<?php echo urlencode($data['address_line1'] . ', ' . $data['city_state_zip']); ?>">
                            Driving Directions
                        </a>
                        <?php endif; ?>
                    </small>
                </address>
            </h2>
            <a class="searchable" style="display: none;" data-for="<?php echo "#arenaListItem" . (isset($data['arena_name']) && isset($data['id']) ? preg_replace("/[^A-Za-z0-9]/", "", $data['arena_name']) . $data['id'] : ''); ?>">
                <?php echo isset($data['tags']) ? $data['tags'] : ''; ?>
            </a>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="page-container">
        <div class="span3">
            <h5>Contacts</h5>
            <?php if(isset($data['contacts'])) : ?>
            <?php foreach($data['contacts'] as $contact) : ?>
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
            <?php endif; ?>
        </div>
        <div class="span6">
            <?php echo isset($data['description']) ? $data['description'] : ''; ?>
        </div>
        <div class="span3">
            <h5>Venues</h5>
            <?php if(isset($data['locations'])) : ?>
            <?php foreach($data['locations'] as $location) : ?>
            <strong><?php echo $location['location_name']; ?></strong><br />
            <abbr title="Venue Type">T:</abbr> <?php echo $location['location_type_display_name']; ?><br />
            <?php if(isset($location['location_description']) && !empty($location['location_description'])) {
                echo '<p>' . $location['location_description'] . '</p><br />';
            } ?>
            <?php if(isset($location['location_length']) && !empty($location['location_length'])) {
                echo  '<abbr title="Venue Length">L:</abbr> ' . $location['location_length'] . ' ft<br />';
            } ?>
            <?php if(isset($location['location_width']) && !empty($location['location_width'])) {
                echo  '<abbr title="Venue Width">W:</abbr> ' . $location['location_width'] . ' ft<br />';
            } ?>
            <?php if(isset($location['location_radius']) && !empty($location['location_radius'])) {
                echo  '<abbr title="Venue Radius">R:</abbr> ' . $location['location_radius'] . ' ft<br />';
            } ?>
            <?php if(isset($location['location_seating']) && !empty($location['location_seating'])) {
                echo  '<abbr title="Venue Seating Capacity">S:</abbr> ' . $location['location_seating'] . '<br />';
            } ?>
            <?php if(isset($location['location_notes']) && !empty($location['location_notes'])) {
                echo  '<abbr title="Venue Notes">N:</abbr> <p>' . $location['location_notes'] . '</p><br />';
            } ?>
            <br />
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="page-container">
        <div class="span6 offset3">
            <br /><?php echo isset($data['notes']) ? $data['notes'] : ''; ?>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="span10 offset1">
        <div id="eventsCalendar">
            
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
            . 'utilities.debug = ' . (YII_DEBUG ? 'true' : 'false') . ';'
            . 'arenaView.endpoints.calendar ="' . (isset($data['events_json_url']) ? $data['events_json_url'] : '') . '";'
            . 'arenaView.start_date = "' . $start_date . '";'
            . 'arenaView.$calendar = $("#eventsCalendar");'
            . 'arenaView.onReady();',
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
    utilities.debug = <?php echo (YII_DEBUG ? 'true' : 'false'); ?>;
    
    if(typeof arenaView === "undefined")
    {
        var scriptName = utilities.urls.assets + '/js/arena/view.' + (utilities.debug ? 'js' : 'min.js');
        
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
            if (typeof arenaView !== "undefined") {
                clearInterval(interval);
                arenaView.endpoints.calendar ="<?php echo (isset($data['events_json_url']) ? $data['events_json_url'] : ''); ?>";
                arenaView.start_date = "<?php echo $start_date; ?>";
                arenaView.$calendar = $("#eventsCalendar");
                arenaView.onReady();
            } else if (console && console.log) {
                console.log("Loading arenaIndex.js");
            }
        }, 500);
    }
    else
    {
        arenaView.endpoints.calendar ="<?php echo (isset($data['events_json_url']) ? $data['events_json_url'] : ''); ?>";
        arenaView.start_date = "<?php echo $start_date; ?>";
        arenaView.$calendar = $("#eventsCalendar");
        arenaView.onReady();
    }
});
</script>
<?php endif; ?>
