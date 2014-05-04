    <?php foreach($data['records'] as $record): ?>
    <?php $sdtStr = strtotime($record['startDate']); ?>
    <div class="my-calendar-list-item <?php echo $record['type_class']; ?>">
        <div class="row-fluid">
            <div class="span2">
                <time datetime="<?php echo $record['startDate']; ?>">
                    <div class="mycalendar">
                        <span class="month"><?php echo date('M', $sdtStr); ?></span>
                        <span class="day"><?php echo date('j', $sdtStr); ?></span>
                    </div>
                </time>
            </div>
            <div class="span10">
                <h5>
                    <?php if($record['name'] != '') {
                        echo $record['type'] . ' - ' . $record['name'];
                    } else {
                        echo $record['type'];
                    }?>
                </h5>
                <?php if(isset($record['distance']) && !empty($record['distance'])) {
                    echo '<strong><em>' . round($record['distance'], 2) . ' miles</em></strong><br />';
                }?>
                <strong>
                    <?php if($record['all_day'] == 'Yes') {
                        echo 'All Day Event<br />';
                    } else {
                        echo $record['start_time'] . ' - ' . $record['end_time'] . ' <small>(' . $record['duration'] . ' minutes)</small><br /> ';
                    }?>
                </strong>
                <?php if(isset($record['location_name']) && $record['location_name'] != '') {
                    echo '<a href="' . $record['arena_view_url'] . '">' . 
                            $record['arena_name'] . '</a> <small class="text-muted"> -- ' . 
                            $record['location_name'] . '</small>';
                } else {
                    echo '<a href="' . $record['arena_view_url'] . '">' . 
                            $record['arena_name'] . '</a>';
                } ?>
                <br />
                <strong>
                    Price:
                </strong>
                <span class="text-success">
                    $<?php echo $record['price']; ?>
                </span>
                <?php if((integer)$record['price'] <= 0) : ?>
                <span class="required">*</span> <small>Please contact the facility regarding the price of this event</small>
                <?php endif; ?>
                <br />
                <?php if((integer)$record['description'] != '') : ?>
                <p>
                    <?php echo $record['description'];?>
                </p>
                <?php endif; ?>
                <?php if((integer)$record['notes'] != '') : ?>
                <p>
                    <small>Notes: <?php echo $record['notes'];?></small>
                </p>
                <?php endif; ?>
                <?php if($record['type_class'] == 'for_sale') : ?>
                <a class="btn btn-success purchase-request" href="<?php echo $record['pUrl']; ?>">
                    <i class="fa fa-fw fa-ticket"></i><br />
                    <span>Request Reservation</span>
                </a><span> </span>
                <?php endif; ?>
                <a class="btn btn-primary information-request" href="<?php echo $record['iUrl']; ?>">
                    <i class="fa fa-fw fa-info"></i><br />
                    <span>Request Information</span>
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
