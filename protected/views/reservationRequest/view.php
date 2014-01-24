<?php
/* @var $this ReservationRequestController */
/* @var $model ReservationRequest */
?>

<?php
$this->breadcrumbs=array(
	'Reservation Requests'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List ReservationRequest', 'url'=>array('index')),
	array('label'=>'Create ReservationRequest', 'url'=>array('create')),
	array('label'=>'Update ReservationRequest', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ReservationRequest', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ReservationRequest', 'url'=>array('admin')),
);
?>

<h1>View ReservationRequest #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView',array(
    'htmlOptions' => array(
        'class' => 'table table-striped table-condensed table-hover',
    ),
    'data'=>$model,
    'attributes'=>array(
		'id',
		'event_id',
		'requester_id',
		'acknowledger_id',
		'acknowledged_on',
		'accepter_id',
		'accepted_on',
		'rejector_id',
		'rejected_on',
		'rejected_reason',
		'notes',
		'type_id',
		'status_id',
		'lock_version',
		'created_by_id',
		'created_on',
		'updated_by_id',
		'updated_on',
	),
)); ?>