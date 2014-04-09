<?php
    /**
     * This doubles as both a view/edit form for existing records
     * and as a form for new records. 
     * 
     * @var $this ManagementController
     * @var $model EventRequest
     * @var $ownView boolean
     * @var $newRecord boolean
     * @var $data array
     * @var $headers array
     */

?>
<?php if((!isset($data['count']) || (integer)$data['count'] == 0) && $newRecord == false): ?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Event Request</h3>
    </div>
    <div class="panel-body">
        <div class="row-fluid">
            <div class="span3">
                <img class="img-circle"
                     src="<?php echo Yii::app()->request->baseUrl; ?>/images/blank_event.jpg"
                     alt="Generic Event Pic" />
            </div>
            <div class="span6">
                <h3>No Data Found!</h3><br />
            </div>
        </div>
    </div>
    <div class="panel-footer">
    </div>
</div>

<?php else: ?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            Event <?php echo $data['item']['fields']['type_id']['value']; ?> Request: #<?php echo $data['item']['fields']['id']['value']; ?>
        </h3>
    </div>
    <div class="panel-body">
        <?php if($data['parms']['acknowledged'] == false) : ?>
        <div class="row-fluid">
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <span class="badge badge-important">Heads Up!</span>
                This request has not been <strong>acknowledged</strong> yet.
            </div>
        </div>
        <?php endif; ?>
        <?php if($data['parms']['accepted'] == false && $data['parms']['rejected'] == false) : ?>
        <div class="row-fluid">
            <div class="alert">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <span class="badge badge-important">Heads Up!</span>
                This request has not been <strong>accepted</strong> or <strong>rejected</strong> yet.
            </div>
        </div>
        <?php endif; ?>
        <div class="row-fluid">
            <div class="span6">
                <strong>Actions</strong><br />
                <div class="well">
                    <div class="row-fluid">
                        <div class="span3">
                            <button class="btn btn-block btn-large btn-primary " type="button" data-toggle="tooltip"
                                    accesskey=""data-original-title="Send message to requester" id="message">
                                <i class="fa fa-lg fa-envelope"></i> <br />
                                <span>Message</span>
                            </button>
                        </div>
                        <?php if(isset($data['item']['fields']['acknowledger']['button']['enabled']) && 
                                $data['item']['fields']['acknowledger']['button']['enabled'] == true) : ?>
                        <div class="span3">
                            <button class="btn btn-block btn-large btn-warning" type="button" data-toggle="tooltip"
                                        data-original-title="Acknowledge this request"
                                        id="<?php echo $data['item']['fields']['acknowledger']['button']['name']; ?>">
                                <i class="fa fa-lg fa-square"></i> <br />
                                <span>Acknowledge</span>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(isset($data['item']['fields']['accepter']['button']['enabled']) && 
                                $data['item']['fields']['accepter']['button']['enabled'] == true) : ?>
                        <div class="span3">
                            <button class="btn btn-block btn-large btn-success" type="button" data-toggle="tooltip"
                                    data-original-title="Accept this request"
                                        id="<?php echo $data['item']['fields']['accepter']['button']['name']; ?>">
                                <i class="fa fa-lg fa-check"></i> <br />
                                <span>Accept</span>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(isset($data['item']['fields']['rejector']['button']['enabled']) && 
                                $data['item']['fields']['rejector']['button']['enabled'] == true) : ?>
                        <div class="span3">
                            <button class="btn btn-block btn-large btn-danger <?php echo $data['item']['fields']['rejector']['button']['name']; ?>"
                                    type="button" data-toggle="tooltip" data-original-title="Reject this request">
                                <i class="fa fa-lg fa-times"></i> <br />
                                <span>Reject</span>
                            </button>
                        <a class="rejector_id_reason btn-block text-center" style="display: none;" href="#"
                            id="<?php echo $data['item']['fields']['rejected_reason']['name']; ?>"
                            data-type="<?php echo $data['item']['fields']['rejected_reason']['controlType']; ?>" 
                            data-pk="<?php echo $data['pk']['value']; ?>"
                            data-disabled="false"
                            data-mode="popup"
                            title="<?php echo $data['item']['fields']['rejected_reason']['label']; ?>">
                            <?php echo $data['item']['fields']['rejected_reason']['value']; ?>
                        </a>
                        <a class="rejector_id_reason" style="display: none;" href="#"
                            id="<?php echo $data['item']['fields']['rejector']['button']['name']; ?>"
                            data-type="<?php echo $data['item']['fields']['rejector']['controlType']; ?>" 
                            data-pk="<?php echo $data['pk']['value']; ?>",
                            data-disabled="false"
                            data-value="<?php echo Yii::app()->user->id; ?>"
                            data-mode="popup"
                            title="<?php echo $data['item']['fields']['rejector']['label']; ?>">
                            <?php echo Yii::app()->user->id; ?>
                        </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <img class="img-circle"
                     src="<?php echo Yii::app()->request->baseUrl; ?>/images/blank_event.jpg"
                     alt="Generic Event Pic" />
                <br />
                <br />
            </div>
            <div class="span6">
                <strong>Request Details</strong><br />
                <table class="table table-condensed table-information">
                    <tbody>
                        <?php if($data['item']['fields']['created_on']['hidden'] == true): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['created_on']['label']; ?>
                            </td>
                            <td id="<?php echo $data['item']['fields']['created_on']['name']; ?>">
                                <?php echo $data['item']['fields']['created_on']['value']; ?>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['type_id']['hidden'] == true): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['type_id']['label']; ?>
                            </td>
                            <td id="<?php echo $data['item']['fields']['type_id']['name']; ?>">
                                <?php echo $data['item']['fields']['type_id']['value']; ?>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['status_id']['hidden'] == true): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['status_id']['label']; ?>
                            </td>
                            <td id="<?php echo $data['item']['fields']['status_id']['name']; ?>">
                                <?php echo $data['item']['fields']['status_id']['value']; ?>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['requester_name']['hidden'] == true): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['requester_name']['label']; ?>
                            </td>
                            <td id="<?php echo $data['item']['fields']['requester_name']['name']; ?>">
                                <address>
                                    <strong>
                                        <?php echo $data['item']['fields']['requester_name']['value']; ?>
                                    </strong>
                                    <br />
                                    <abbr title="Email">E:</abbr> <a href="mailto:<?php echo $data['item']['fields']['requester_email']['value']; ?>">
                                    <?php echo $data['item']['fields']['requester_email']['value']; ?></a>
                                    <br />
                                    <abbr title="Phone">P:</abbr> <?php echo $data['item']['fields']['requester_phone']['value']; ?>
                                </address>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['notes']['hidden'] == true): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['notes']['label']; ?>
                            </td>
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['notes']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['notes']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['notes']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['notes']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['notes']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['notes']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['notes']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="popup"
                                   title="<?php echo $data['item']['fields']['notes']['label']; ?>">
                                </a>
                                <pre style="overflow: auto; min-height: 60px; max-height: 120px;" id="<?php echo $data['item']['fields']['notes']['name']; ?>History"><?php echo $data['item']['fields']['notes']['value']; ?></pre>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['acknowledger']['hidden'] == true ||
                                (isset($data['item']['fields']['acknowledger']['button']['enabled']) && 
                                $data['item']['fields']['acknowledger']['button']['enabled'] == true)): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['acknowledger']['label']; ?>
                            </td>
                            <td id="<?php echo $data['item']['fields']['acknowledger']['name']; ?>">
                                <?php echo $data['item']['fields']['acknowledger']['value']; ?>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['acknowledged_on']['hidden'] == true ||
                                (isset($data['item']['fields']['acknowledger']['button']['enabled']) && 
                                $data['item']['fields']['acknowledger']['button']['enabled'] == true)): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['acknowledged_on']['label']; ?>
                            </td>
                            <td id="<?php echo $data['item']['fields']['acknowledged_on']['name']; ?>">
                                <?php echo $data['item']['fields']['acknowledged_on']['value']; ?>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['accepter']['hidden'] == true ||
                                (isset($data['item']['fields']['accepter']['button']['enabled']) && 
                                $data['item']['fields']['accepter']['button']['enabled'] == true)): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['accepter']['label']; ?>
                            </td>
                            <td id="<?php echo $data['item']['fields']['accepter']['name']; ?>">
                                <?php echo $data['item']['fields']['accepter']['value']; ?>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['accepted_on']['hidden'] == true ||
                                (isset($data['item']['fields']['accepter']['button']['enabled']) && 
                                $data['item']['fields']['accepter']['button']['enabled'] == true)): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['accepted_on']['label']; ?>
                            </td>
                            <td id="<?php echo $data['item']['fields']['accepted_on']['name']; ?>">
                                <?php echo $data['item']['fields']['accepted_on']['value']; ?>
                            </td>
                        <?php if($data['item']['fields']['rejector']['hidden'] == true ||
                                (isset($data['item']['fields']['rejector']['button']['enabled']) && 
                                $data['item']['fields']['rejector']['button']['enabled'] == true)): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['rejector']['label']; ?>
                            </td>
                            <td id="<?php echo $data['item']['fields']['rejector']['name']; ?>">
                                <?php echo $data['item']['fields']['rejector']['value']; ?>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['rejected_on']['hidden'] == true ||
                                (isset($data['item']['fields']['rejector']['button']['enabled']) && 
                                $data['item']['fields']['rejector']['button']['enabled'] == true)): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['rejected_on']['label']; ?>
                            </td>
                            <td id="<?php echo $data['item']['fields']['rejected_on']['name']; ?>">
                                <?php echo $data['item']['fields']['rejected_on']['value']; ?>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['rejected_reason']['hidden'] == true ||
                                (isset($data['item']['fields']['rejector']['button']['enabled']) && 
                                $data['item']['fields']['rejector']['button']['enabled'] == true)): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['rejected_reason']['label']; ?>
                            </td>
                            <td id="<?php echo $data['item']['fields']['rejected_reason']['name']; ?>">
                                <?php echo $data['item']['fields']['rejected_reason']['value']; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <strong>Event Information</strong><br />
                <table class="table table-condensed table-information">
                    <tbody>
                        <?php if($data['item']['fields']['event_id']['hidden'] == true): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['event_id']['label']; ?>
                            </td>
                            <td>
                                <?php echo $data['item']['fields']['event_id']['value']; ?>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['event_start']['hidden'] == true): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['event_start']['label']; ?>
                            </td>
                            <td>
                                <?php echo $data['item']['fields']['event_start']['value']; ?>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['arena_id']['hidden'] == true): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['arena_id']['label']; ?>
                            </td>
                            <td>
                                <?php echo $data['item']['fields']['arena_id']['value']; ?>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['arena']['hidden'] == true): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['arena']['label']; ?>
                            </td>
                            <td>
                                <?php echo $data['item']['fields']['arena']['value']; ?>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['location_id']['hidden'] == true): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['location_id']['label']; ?>
                            </td>
                            <td>
                                <?php echo $data['item']['fields']['location_id']['value']; ?>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['location']['hidden'] == true): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['location']['label']; ?>
                            </td>
                            <td>
                                <?php echo $data['item']['fields']['location']['value']; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    _eventRequest.data = <?php echo json_encode($data); ?>;
    _eventRequest.endpoints.updateRecord = "<?php echo $data['endpoint']['update']; ?>";
    _eventRequest.endpoints.acknowledgeRecord = "<?php echo $data['endpoint']['update']; ?>";
    _eventRequest.endpoints.acceptRecord = "<?php echo $data['endpoint']['update']; ?>";
    _eventRequest.endpoints.rejectRecord = "<?php echo $data['endpoint']['update']; ?>";
    _eventRequest.userId = <?php echo Yii::app()->user->id; ?>;
    _eventRequest.userName = "<?php echo Yii::app()->user->fullName; ?>";
    _eventRequest.onReady();
});
</script>
<?php endif; ?>