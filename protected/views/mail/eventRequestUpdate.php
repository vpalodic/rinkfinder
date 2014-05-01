<table cellspacing="5" cellpadding="10" style="color:#666;font:13px Arial;line-height:1.4em;width:100%;">
    <tbody>
        <tr>
            <td>
                <h2 style="padding-bottom:0px;padding-right:0px;padding-top:0px;padding-right:0px;font-size:1.75em;font-weight:bold;color:#0d1b72;background:#ffffff;">
                    <?php echo CHtml::encode(Yii::app()->name); ?>
                </h2>
                <h3 style="padding-bottom:0px;padding-right:0px;padding-top:0px;padding-right:0px;font-size:1.45em;font-weight:bold;color:#0d1b72;background:#ffffff;">
                    A Service of the Minnesota Ice Arena Manager's Association
                </h3>
                <p style="padding-bottom:0px;padding-right:0px;padding-top:0px;padding-right:0px;font-size:1.0em;font-weight:normal;color:#000000;background:#ffffff;">
                <?php if(is_array($requestStatus) && $requestStatus[0] == "Rejected") : ?>
                    Your request has been <strong><?php echo $requestStatus[0]; ?>.</strong><br />
                    
                    <br />It was rejected for the following reason: <?php echo nl2br(CHtml::encode($requestStatus[1])); ?> <br /><br />
                <?php elseif(is_array($requestStatus) && $requestStatus[0] == "Message") : ?>
                    A facility manager has sent you the following message regarding your request:<br />
                    
                    <br /><?php echo nl2br(CHtml::encode($requestStatus[1])); ?> <br /><br />
                <?php else: ?>
                    Your request has been <strong><?php echo $requestStatus; ?>.</strong><br /><br />
                <?php endif; ?>
                    Here are the details for the event that your request is based off of: <br />
                </p>
                <h3>Event Details:</h3>
                <p>
                    <ul>
                        <li>
                            Facility: <b><?php echo $event->arena->name; ?></b>
                        </li>
                        <?php if(isset($event->arena->address_line2) && !empty($event->arena->address_line2)) : ?>
                        <li>
                            Address: <b><?php echo $event->arena->address_line1 . ", " . $event->arena->address_line2 . ", " . $event->arena->city . ". " . $event->arena->state . " " . $event->arena->zip; ?></b>
                        </li>
                        <?php else: ?>
                        <li>
                            Address: <b><?php echo $event->arena->address_line1 . ", " . $event->arena->city . ". " . $event->arena->state . " " . $event->arena->zip; ?></b>
                        </li>
                        <?php endif; ?>
                        <?php if(isset($event->arena->locations[0]->name) && !empty($event->arena->locations[0]->name)) : ?>
                        <li>
                            Venue: <b><?php echo $event->arena->locations[0]->name; ?></b>
                        </li>
                        <?php endif; ?>
                        <li>
                            Event Start: <b><?php $date = DateTime::createFromFormat("Y-m-d H:i:s", $event->start_date . " " . $event->start_time); echo $date->format("m/d/Y h:i:s A"); ?></b>
                        </li>
                        <li>
                            Event End: <b><?php $date = DateTime::createFromFormat("Y-m-d H:i:s", $event->end_date . " " . $event->end_time); echo $date->format("m/d/Y h:i:s A"); ?></b>
                        </li>
                        <li>
                            Event Link: <b><a href="<?php echo $eventUrl; ?>">Click here!</a></b>
                        </li>
                        <li>
                            Facility Link: <b><a href="<?php echo $arenaUrl; ?>"><?php echo $event->arena->name; ?></a></b>
                        </li>
                    </ul>
                </p>
                <p>If you have any questions or need to cancel your request, please contact the arena by clicking on the Arena Link above.</p>
                <br />
                <p>Thank you for using <?php echo CHtml::encode(Yii::app()->name); ?>!</p>
            </td>
        </tr>
    </tbody>
</table>