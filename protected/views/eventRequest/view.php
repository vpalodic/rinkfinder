<?php
/* @var $this EventRequestController */
/* @var $model EventRequest */
?>

<?php
$this->breadcrumbs=array(
	'Event Requests'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List EventRequest', 'url'=>array('index')),
	array('label'=>'Create EventRequest', 'url'=>array('create')),
	array('label'=>'Update EventRequest', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete EventRequest', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage EventRequest', 'url'=>array('admin')),
);
?>

<h1>View EventRequest #<?php echo $model->id; ?></h1>

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