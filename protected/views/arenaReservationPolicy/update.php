<?php
/* @var $this ArenaReservationPolicyController */
/* @var $model ArenaReservationPolicy */
?>

<?php
$this->breadcrumbs=array(
	'Arena Reservation Policies'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ArenaReservationPolicy', 'url'=>array('index')),
	array('label'=>'Create ArenaReservationPolicy', 'url'=>array('create')),
	array('label'=>'View ArenaReservationPolicy', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ArenaReservationPolicy', 'url'=>array('admin')),
);
?>

    <h1>Update ArenaReservationPolicy <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>