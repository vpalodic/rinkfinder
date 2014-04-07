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
            Event Request #: <?php echo $data['item']['fields']['id']['value']; ?>
        </h3>
    </div>
    <div class="panel-body">
        <?php if(!isset($data['item']['fields']['acknowledger']['value'])) : ?>
        <div class="row-fluid">
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <span class="badge badge-important">Heads Up!</span>
                This request has not been <strong>acknowledged</strong> yet.
            </div>
        </div>
        <?php endif; ?>
        <?php if(!isset($data['item']['fields']['accepter']['value']) && 
                !isset($data['item']['fields']['rejector']['value'])) : ?>
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
                    <button class="btn btn-block btn-large btn-primary" type="button" data-toggle="tooltip"
                            data-original-title="Send message to requester">
                        <i class="fa fa-envelope"></i> Message
                    </button>                    
                    <?php if(!isset($data['item']['fields']['acknowledger']['value'])) : ?>
                        <button class="btn btn-block btn-large btn-warning" type="button" data-toggle="tooltip"
                                    data-original-title="Acknowledge this request">
                                <i class="fa fa-lg fa-square"></i> Acknowledge
                        </button>
                    <?php endif; ?>
                    <?php if(!isset($data['item']['fields']['accepter']['value'])) : ?>
                        <button class="btn btn-block btn-large btn-success" type="button" data-toggle="tooltip"
                                data-original-title="Accept this request">
                            <i class="fa fa-lg fa-check"></i> Accept
                        </button>
                    <?php endif; ?>
                    <?php if(!isset($data['item']['fields']['rejector']['value'])) : ?>
                        <button class="btn btn-block btn-large btn-danger" type="button" data-toggle="tooltip"
                                data-original-title="Reject this request">
                            <i class="fa fa-lg fa-times"></i> Reject
                        </button>
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
                        <?php if($data['item']['fields']['requester_email']['hidden'] == true): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['requester_email']['label']; ?>
                            </td>
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['requester_email']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['requester_email']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['requester_email']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['requester_email']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['requester_email']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['requester_email']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['requester_email']['editable'] == false): ?>
                                   data-disabled="true"
                                   <?php endif; ?>
                                   data-mode="inline"
                                   title="<?php echo $data['item']['fields']['requester_email']['label']; ?>">
                                    <?php echo $data['item']['fields']['requester_email']['value']; ?>
                                </a>
                            </td>
                        </tr>
                        <?php if($data['item']['fields']['requester_phone']['hidden'] == true): ?>
                        <tr style="display: none;">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td>
                                <?php echo $data['item']['fields']['requester_phone']['label']; ?>
                            </td>
                            <td>
                                <a href="#"
                                   id="<?php echo $data['item']['fields']['requester_phone']['name']; ?>"
                                   data-type="<?php echo $data['item']['fields']['requester_phone']['controlType']; ?>" 
                                   data-pk="<?php echo $data['pk']['value']; ?>"
                                   data-url="<?php echo $data['endpoint']['update']; ?>"
                                   <?php if(isset($data['item']['fields']['requester_phone']['format'])): ?>
                                   data-format="<?php echo $data['item']['fields']['requester_phone']['format']; ?>"
                                   <?php endif; ?>
                                   <?php if(isset($data['item']['fields']['requester_phone']['viewformat'])): ?>
                                   data-viewformat="<?php echo $data['item']['fields']['requester_phone']['viewformat']; ?>"
                                   <?php endif; ?>
                                   <?php if($data['item']['fields']['requester_phone']['editable'] == false): ?>
                                   data-disable="true"
                                   <?php endif; ?>
                                   data-mode="popup"
                                   <?php if(isset($data['item']['fields']['requester_phone']['inputmask'])): ?>
                                   data-inputmask="<?php echo json_encode($data['item']['fields']['requester_phone']['inputmask']); ?>"
                                   <?php endif; ?>
                                   title="<?php echo $data['item']['fields']['requester_phone']['label']; ?>">
                                    <?php echo $data['item']['fields']['requester_phone']['value']; ?>
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
    $('#created_on').editable({
        params: <?php echo json_encode($data['parms']); ?>,
        datetimepicker: {
            showMeridian: true
        }
    });
    
    $('#type_id').editable({
        params: <?php echo json_encode($data['parms']); ?>,
    });
    
    $('#status_id').editable({
        params: <?php echo json_encode($data['parms']); ?>,
    });
    
    $('#requester_name').editable({
        params: <?php echo json_encode($data['parms']); ?>,
    });
    
    $('#requester_email').editable({
        params: <?php echo json_encode($data['parms']); ?>,
    });
    
    $('#requester_phone').editable({
        params: <?php echo json_encode($data['parms']); ?>,
        display: function(value, sourceData) {
            //display checklist as comma-separated values
            var html = '';
            
            html = value.replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
            $(this).html(html);
        }
    });
    
    $("#<?php echo $data['item']['fields']['requester_phone']['name']; ?>").on('shown', function(e, editable) {
        if (editable) {
            $(this).data('editable').input.$input.inputmask(
            {
                "mask": "<?php echo $data['item']['fields']['requester_phone']['inputmask']['mask']; ?>",
                "clearIncomplete": true,
                'autoUnmask' : true
            });
        }
    });
    
    $('#event_id').editable({
        params: <?php echo json_encode($data['parms']); ?>,
    });
    
    $('#event_start').editable({
        params: <?php echo json_encode($data['parms']); ?>,
        datetimepicker: {
            showMeridian: true
        }
    });
    
    $('#arena_id').editable({
        params: <?php echo json_encode($data['parms']); ?>,
    });
    
    $('#arena').editable({
        params: <?php echo json_encode($data['parms']); ?>,
    });
    
    $('#location_id').editable({
        params: <?php echo json_encode($data['parms']); ?>,
    });
    
    $('#location').editable({
        params: <?php echo json_encode($data['parms']); ?>,
    });
    
    $('[data-toggle="tooltip"]').tooltip();

    $('button').click(function(e) {
        e.preventDefault();
        alert("This is a demo.\n :-)");
    });
});
</script>
<?php endif; ?>