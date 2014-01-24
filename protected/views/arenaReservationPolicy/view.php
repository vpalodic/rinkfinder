<?php
/* @var $this ArenaReservationPolicyController */
/* @var $model ArenaReservationPolicy */
?>

<?php
$this->breadcrumbs=array(
	'Arena Reservation Policies'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List ArenaReservationPolicy', 'url'=>array('index')),
	array('label'=>'Create ArenaReservationPolicy', 'url'=>array('create')),
	array('label'=>'Update ArenaReservationPolicy', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ArenaReservationPolicy', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ArenaReservationPolicy', 'url'=>array('admin')),
);
?>

<h1>View ArenaReservationPolicy #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView',array(
    'htmlOptions' => array(
        'class' => 'table table-striped table-condensed table-hover',
    ),
    'data'=>$model,
    'attributes'=>array(
		'id',
		'arena_id',
		'days',
		'cutoff_time',
		'cutoff_day',
		'notes',
		'event_type_id',
		'lock_version',
		'created_by_id',
		'created_on',
		'updated_by_id',
		'updated_on',
	),
)); ?>