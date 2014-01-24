<?php
/* @var $this ArenaReservationPolicyController */
/* @var $model ArenaReservationPolicy */
?>

<?php
$this->breadcrumbs=array(
	'Arena Reservation Policies'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ArenaReservationPolicy', 'url'=>array('index')),
	array('label'=>'Manage ArenaReservationPolicy', 'url'=>array('admin')),
);
?>

<h1>Create ArenaReservationPolicy</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>