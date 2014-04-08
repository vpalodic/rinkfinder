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
        <?php if(!isset($data['item']['fields']['acknowledger']['value']) &&
                isset($data['item']['fields']['acknowledger']['button']['enabled']) && 
                $data['item']['fields']['acknowledger']['button']['enabled'] == true) : ?>
        <div class="row-fluid">
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <span class="badge badge-important">Heads Up!</span>
                This request has not been <strong>acknowledged</strong> yet.
            </div>
        </div>
        <?php endif; ?>
        <?php if((!isset($data['item']['fields']['accepter']['value']) &&
                            isset($data['item']['fields']['accepter']['button']['enabled']) && 
                                $data['item']['fields']['accepter']['button']['enabled'] == true) && 
                (!isset($data['item']['fields']['rejector']['value']) &&
                            isset($data['item']['fields']['rejector']['button']['enabled']) && 
                                $data['item']['fields']['rejector']['button']['enabled'] == true)) : ?>
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
                    <button class="btn btn-block btn-large btn-primary " type="button" data-toggle="tooltip"
                            data-original-title="Send message to requester" id="message">
                        <i class="fa fa-lg fa-envelope"></i> Message
                    </button>                    
                    <?php if(!isset($data['item']['fields']['acknowledger']['value']) &&
                            isset($data['item']['fields']['acknowledger']['button']['enabled']) && 
                                $data['item']['fields']['acknowledger']['button']['enabled'] == true) : ?>
                        <button class="btn btn-block btn-large btn-warning" type="button" data-toggle="tooltip"
                                    data-original-title="Acknowledge this request"
                                    id="<?php echo $data['item']['fields']['acknowledger']['button']['name']; ?>">
                                <i class="fa fa-lg fa-square"></i> Acknowledge
                        </button>
                    <?php endif; ?>
                    <?php if(!isset($data['item']['fields']['accepter']['value']) &&
                            isset($data['item']['fields']['accepter']['button']['enabled']) && 
                                $data['item']['fields']['accepter']['button']['enabled'] == true) : ?>
                        <button class="btn btn-block btn-large btn-success" type="button" data-toggle="tooltip"
                                data-original-title="Accept this request"
                                    id="<?php echo $data['item']['fields']['accepter']['button']['name']; ?>">
                            <i class="fa fa-lg fa-check"></i> Accept
                        </button>
                    <?php endif; ?>
                    <?php if(!isset($data['item']['fields']['rejector']['value']) &&
                            isset($data['item']['fields']['rejector']['button']['enabled']) && 
                                $data['item']['fields']['rejector']['button']['enabled'] == true) : ?>
                        <button class="btn btn-block btn-large btn-danger <?php echo $data['item']['fields']['rejector']['button']['name']; ?>"
                                type="button" data-toggle="tooltip" data-original-title="Reject this request">
                            <i class="fa fa-lg fa-times"></i> Reject
                        </button>
                    <a class="rejector_id_reason btn-block text-center" style="display: none;" href="#"
                        id="<?php echo $data['item']['fields']['rejected_reason']['name']; ?>"
                        data-type="<?php echo $data['item']['fields']['rejected_reason']['controlType']; ?>" 
                        data-pk="<?php echo $data['pk']['value']; ?>",
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
                    <?php endif; ?>
                </div>
                <img class="img-circle"
                     src="<?php echo Yii::app()->request->baseUrl; ?>/images/blank_event.jpg"
                     alt="Generic Event Pic" />
                <br />
                <br />
            </div>
            <div class="span4">
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
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['created_on']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['created_on']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['created_on']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['created_on']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['created_on']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['created_on']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['created_on']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['created_on']['label']; ?>">
                                    <?php echo $data['item']['fields']['created_on']['value']; ?>
                                </a>
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
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['type_id']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['type_id']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['type_id']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['type_id']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['type_id']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['type_id']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['type_id']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   <?php if($data['item']['fields']['type_id']['controlType'] == 'select'): ?>
                                   data-source="<?php echo $data['item']['fields']['type_id']['source']; ?>"
                                   <?php endif; ?>
                                   title="<?php echo $data['item']['fields']['type_id']['label']; ?>">
                                    <?php echo $data['item']['fields']['type_id']['value']; ?>
                                </a>
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
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['status_id']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['status_id']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['status_id']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['status_id']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['status_id']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['status_id']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['status_id']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   <?php if($data['item']['fields']['status_id']['controlType'] == 'select'): ?>
                                   data-source="<?php echo $data['item']['fields']['status_id']['source']; ?>"
                                   <?php endif; ?>
                                   title="<?php echo $data['item']['fields']['status_id']['label']; ?>">
                                    <?php echo $data['item']['fields']['status_id']['value']; ?>
                                </a>
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
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['requester_name']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['requester_name']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['requester_name']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['requester_name']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['requester_name']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['requester_name']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['requester_name']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['requester_name']['label']; ?>">
                                    <address>
                                        <strong>
                                            <?php echo $data['item']['fields']['requester_name']['value']; ?>
                                        </strong>
                                        <br />
                                        <abbr title="Email">E:</abbr> <?php echo $data['item']['fields']['requester_email']['value']; ?>
                                        <br />
                                        <abbr title="Phone">P:</abbr> <?php echo $data['item']['fields']['requester_phone']['value']; ?>
                                    </address>
                                </a>
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
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['acknowledger']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['acknowledger']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['acknowledger']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['acknowledger']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['acknowledger']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['acknowledger']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['acknowledger']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['acknowledger']['label']; ?>">
                                    <?php echo $data['item']['fields']['acknowledger']['value']; ?>
                                </a>
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
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['acknowledged_on']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['acknowledged_on']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['acknowledged_on']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['acknowledged_on']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['acknowledged_on']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['acknowledged_on']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['acknowledged_on']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['acknowledged_on']['label']; ?>">
                                    <?php echo $data['item']['fields']['acknowledged_on']['value']; ?>
                                </a>
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
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['accepter']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['accepter']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['accepter']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['accepter']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['accepter']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['accepter']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['accepter']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['accepter']['label']; ?>">
                                    <?php echo $data['item']['fields']['accepter']['value']; ?>
                                </a>
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
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['accepted_on']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['accepted_on']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['accepted_on']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['accepted_on']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['accepted_on']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['accepted_on']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['accepted_on']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['accepted_on']['label']; ?>">
                                    <?php echo $data['item']['fields']['accepted_on']['value']; ?>
                                </a>
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
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['rejector']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['rejector']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['rejector']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['rejector']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['rejector']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['rejector']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['rejector']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['rejector']['label']; ?>">
                                    <?php echo $data['item']['fields']['rejector']['value']; ?>
                                </a>
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
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['rejected_on']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['rejected_on']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['rejected_on']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['rejected_on']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['rejected_on']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['rejected_on']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['rejected_on']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['rejected_on']['label']; ?>">
                                    <?php echo $data['item']['fields']['rejected_on']['value']; ?>
                                </a>
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
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['rejected_reason']['name']; ?>Static"
                                   data-type="<?php echo $data['item']['fields']['rejected_reason']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['rejected_reason']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['rejected_reason']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['rejected_reason']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['rejected_reason']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['rejected_reason']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['rejected_reason']['label']; ?>">
                                    <?php echo $data['item']['fields']['rejected_reason']['value']; ?>
                                </a>
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
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['event_id']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['event_id']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['event_id']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['event_id']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['event_id']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['event_id']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['event_id']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['event_id']['label']; ?>">
                                    <?php echo $data['item']['fields']['event_id']['value']; ?>
                                </a>
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
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['event_start']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['event_start']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['event_start']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['event_start']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['event_start']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['event_start']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['event_start']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['event_start']['label']; ?>">
                                    <?php echo $data['item']['fields']['event_start']['value']; ?>
                                </a>
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
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['arena_id']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['arena_id']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['arena_id']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['arena_id']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['arena_id']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['arena_id']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['arena_id']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['arena_id']['label']; ?>">
                                    <?php echo $data['item']['fields']['arena_id']['value']; ?>
                                </a>
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
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['arena']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['arena']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['arena']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['arena']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['arena']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['arena']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['arena']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['arena']['label']; ?>">
                                    <?php echo $data['item']['fields']['arena']['value']; ?>
                                </a>
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
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['location_id']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['location_id']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['location_id']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['location_id']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['location_id']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['location_id']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['location_id']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['location_id']['label']; ?>">
                                    <?php echo $data['item']['fields']['location_id']['value']; ?>
                                </a>
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
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['location']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['location']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['location']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['location']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['location']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['location']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['location']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['location']['label']; ?>">
                                    <?php echo $data['item']['fields']['location']['value']; ?>
                                </a>
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
    $('#<?php echo $data['item']['fields']['requester_phone']['name']; ?>').editable({
        params: <?php echo json_encode($data['parms']); ?>,
        display: function(value, sourceData) {
            // display the supplied digits as a phone number!
            var html = '';
            
            if (typeof value === 'undefined' || value.length <= 0)
            {
                return;
            }
            
            html = value.replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
            $(this).html(html);
        }
    });
    
    $("#<?php echo $data['item']['fields']['requester_phone']['name']; ?>").on('shown', function(e, editable) {
        // ensure that we only get the unmasked value
        if (editable) {
            $(this).data('editable').input.$input.inputmask(
            {
                "mask": "<?php echo $data['item']['fields']['requester_phone']['inputmask']['mask']; ?>",
                "clearIncomplete": false,
                'autoUnmask' : true
            });
        }
    });
    
    $('#acknowledger').editable({
        params: <?php echo json_encode($data['parms']); ?>
    });
    
    $('#acknowledged_on').editable({
        params: <?php echo json_encode($data['parms']); ?>,
        datetimepicker: {
            showMeridian: true
        }
    });
    
    $('#accepter').editable({
        params: <?php echo json_encode($data['parms']); ?>
    });
    
    $('#accepted_on').editable({
        params: <?php echo json_encode($data['parms']); ?>,
        datetimepicker: {
            showMeridian: true
        }
    });
    
    $('#rejector').editable({
        params: <?php echo json_encode($data['parms']); ?>
    });
    
    $('#rejected_on').editable({
        params: <?php echo json_encode($data['parms']); ?>,
        datetimepicker: {
            showMeridian: true
        }
    });
    
    $(".rejector_id_reason").editable({
        params: <?php echo json_encode($data['parms']); ?>
    });
    
    $(".<?php echo $data['item']['fields']['rejector']['button']['name']; ?>").editable({
        params: <?php echo json_encode($data['parms']); ?>
    });
    
    $("#<?php echo $data['item']['fields']['rejected_reason']['name']; ?>.rejector_id_reason").on('save', function (e, params) {        
        if (arguments.length != 2)
        {
            return;
        }
        
        $element = $("#<?php echo $data['item']['fields']['rejected_reason']['name']; ?>.rejector_id_reason");
        $id = $("#<?php echo $data['item']['fields']['rejector']['button']['name']; ?>.rejector_id_reason");
        $element.hide();
        $element.editable('setValue', params.newValue);
        $id.editable('setValue', <?php echo Yii::app()->user->id; ?> , false)
        
        // Ok, we will submit the data to the server
        $(".rejector_id_reason").editable('submit', {
            url: "<?php echo $data['endpoint']['update']; ?>",
            data: <?php $data['parms']['action'] = 'reject'; $data['parms']['pk'] = $data['pk']['value']; echo json_encode($data['parms']); ?>,
            success: function(response, newValue) {
                if (typeof response !== 'undefined' && response.length > 0)
                {
                    return "Data not saved. Please refresh the page as it appears" +
                            " the session has expired."
                }
                
                // We update the rejected on
                $("<?php echo $data['item']['fields']['rejector']['button']['name']; ?>").fade();
            }
        });
            
    });
    
    $(".<?php echo $data['item']['fields']['rejector']['button']['name']; ?>").click(function (e) {
        e.preventDefault();
        
        e.stopPropagation();
        
        // Show the editable
        $element = $("#<?php echo $data['item']['fields']['rejected_reason']['name']; ?>.rejector_id_reason");
        
        $element.show();
        $element.editable('show');
    });
    
    $('#rejected_reason').editable({
        params: <?php echo json_encode($data['parms']); ?>
    });
    
    $("#<?php echo $data['item']['fields']['notes']['name']; ?>").editable({
        emptytext: "Add Note",
        params: <?php echo json_encode($data['parms']); ?>,
        success: function(response, newValue) {
            if (typeof response !== 'undefined' && response.length > 0)
            {
                return "Data not saved. Please refresh the page as it appears" +
                        " the session has expired."
            }
            
            // We hide the editable, set the history, and then clear the value
            // of the editable!
            $(this).data('editable').hide();
            $("#<?php echo $data['item']['fields']['notes']['name']; ?>History").text(newValue);
            $(this).data('editable').input.$input.val('');
            newValue = '';
            
            // What we return gets added to the editable window.
            return "Note added";
        },
        validate: function(value) {
            // Here we add our timestamp information to the note.
            var oldNotes = $("#<?php echo $data['item']['fields']['notes']['name']; ?>History").text();
            oldNotes += moment().format("MM/DD/YYYY h:mm:ss A") + " by <?php echo Yii::app()->user->fullName; ?>:\r\n\r\n";
            oldNotes += value + "\r\n\r\n";
            return {newValue: oldNotes};
        }
    });
    
    $('[data-toggle="tooltip"]').tooltip();

});
</script>
<?php endif; ?>