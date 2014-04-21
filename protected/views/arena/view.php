<?php
    /* @var $this ArenaController   */
    /* @var $data mixed[]           */
    /* @var $start_date string      */
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
            <h2 id="arenaHeader"><?php echo CHtml::encode($data['arena_name']); ?>
                <br />
                <small class="text-muted">
                    <address>
                        <?php echo $data['address_line1']; ?><br />
                        <?php if(isset($data['address_line2']) && !empty($data['address_line2'])) {
                            echo $data['address_line2'] . '<br />';
                        } ?>
                        <?php echo $data['city_state_zip'] . '<br />'; ?>
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
                        <a target="_blank" href="http://maps.google.com/maps?daddr=<?php echo urlencode($data['address_line1'] . ', ' . $data['city_state_zip']); ?>">
                            Driving Directions
                        </a>
                    </address>
                </small>
            </h2>
            <a class="searchable" style="display: none;" data-for="<?php echo "#arenaListItem" . preg_replace("/[^A-Za-z0-9]/", "", $data['arena_name']) . $data['id']; ?>">
                <?php echo $data['tags']; ?>
            </a>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="page-container">
        <div class="span3">
            <h5>Contacts</h5>
            <?php foreach($data['contacts'] as $contact) : ?>
            <address>
                <?php if($contact['contact_type'] == "Primary") : ?>
                <abbr title='Primary Contact'>*:</abbr> <strong><?php echo $contact['contact_name']; ?></strong><br />
                <?php else : ?>
                <?php echo $contact['contact_name']; ?><br />
                <?php endif; ?>
                <?php if(isset($contact['contact_phone']) && !empty($contact['contact_phone'])) {
                    echo '<abbr title="Phone">P:</abbr> ' . RinkfinderActiveRecord::format_telephone($contact['contact_phone']);
                    if(isset($contact['contact_ext']) && !empty($contacted['contact_ext'])) {
                        echo ' <abbr title="Extension">E:</abbr> ' . $contact['contact_ext'] . '<br />';
                    } else {
                        echo '<br />';
                    }
                } ?>
                <?php if(isset($contact['contact_fax']) && !empty($contact['contact_fax'])) {
                    echo '<abbr title="Fax">F:</abbr> ' . RinkfinderActiveRecord::format_telephone($contact['contact_fax']);
                    if(isset($contact['contact_fax_ext']) && !empty($contact['contact_fax_ext'])) {
                        echo ' <abbr title="Fax Extension">E:</abbr> ' . $data['contact_fax_ext'] . '<br />';
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
        <div class="span6">
            <?php echo $data['description']; ?>
        </div>
        <div class="span3">
            <h5>Venues</h5>
            <?php foreach($data['locations'] as $location) : ?>
            <strong><?php echo $location['location_name']; ?></strong><br />
            <abbr title="Venue Type">T:</abbr> <?php echo $location['location_type_display_name']; ?><br />
            <?php if(isset($location['location_description']) && !empty($location['location_description'])) {
                echo '<p>' . $location['location_description'] . '</p>';
            } ?>
            <?php if(isset($location['location_length']) && !empty($location['location_length'])) {
                echo  '<abbr title="Venue Length">L:</abbr> ' . $location['location_length'] . '<br />';
            } ?>
            <?php if(isset($location['location_width']) && !empty($location['location_width'])) {
                echo  '<abbr title="Venue Width">W:</abbr> ' . $location['location_width'] . '<br />';
            } ?>
            <?php if(isset($location['location_radius']) && !empty($location['location_radius'])) {
                echo  '<abbr title="Venue Radius">R:</abbr> ' . $location['location_radius'] . '<br />';
            } ?>
            <?php if(isset($location['location_seating']) && !empty($location['location_seating'])) {
                echo  '<abbr title="Venue Seating Capacity">S:</abbr> ' . $location['location_seating'] . '<br />';
            } ?>
            <?php if(isset($location['location_notes']) && !empty($location['location_notes'])) {
                echo  '<abbr title="Venue Notes">N:</abbr> ' . $location['location_notes'] . '<br />';
            } ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="page-container">
        <div class="span6 offset3">
            <br /><p><?php echo $data['notes']; ?></p>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="page-container">
        <div class="span8 offset2">
            <h2 class="text-center">Event Calendar</h2>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="span8 offset2">
        <div id="eventsCalendar">
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var $calendarContainer = $("#eventsCalendar");
        var options = { 
            events: [
                {
                    title:"Colored events",
                    url: "http://www.google.se",
                    start: {
                        date: 20140515,
                        time: "12.00"
                    },
                    end: {
                        date: "20140515",
                        time: "14.00"
                    },
                    location: "London"
                },
                {
                    title:"Colored events",
                    url: "http://www.google.se",
                    start: {
                        date: 20140515,
                        time: "12.00"
                    },
                    end: {
                        date: "20140515",
                        time: "14.00"
                    },
                    location: "London"
                },
                {
                    title:"Colored events",
                    url: "http://www.google.se",
                    start: {
                        date: 20140516,
                        time: "12.00"
                    },
                    end: {
                        date: "20140516",
                        time: "14.00"
                    },
                    location: "London"
                },
                {
                    title:"Way Out West",
                    url: "http://www.google.se",
                    start: {
                        date: 20140430,
                        time: "12.00"
                    },
                    end: {
                        date: "20140430",
                        time: "14.00"
                    },
                    location: "Gothenburg",
                    color: "yellow"
                }
            ],
            eventcolors: {
                yellow: {
                    background: "#FC0",
                    text: "#000",
                    link: "#000"
                },
                blue: {
                    background: "#6180FC",
                    text: "#FFF",
                    link: "#FFF"
                }
            },
            onDayClick: function(e) {
                console.log(e);
            },
            firstDayOfWeek: "Sunday",
            showDays: true,
            color: "blue",
            urlText: "Help me please..."
        };
        
//        $calendarContainer.height($(window).height() * .80);
//        $("#eventsCalendar").kalendar(options);
        $("#eventsCalendar").eventCalendar({
            showDescription: true,
            eventsScrollable: true,
            startWeekOnMonday: false,
            eventsLimit: 0,
            eventsjson: '<?php echo $data['events_json_url']; ?>',
            txt_NextEvents: 'Comming Events:',
            jsonDateFormat: 'human',  // 'YYYY-MM-DD HH:MM:SS'
            openEventInNewWindow: true
        });
    });
</script>
