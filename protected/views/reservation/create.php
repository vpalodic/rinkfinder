<?php
/* @var $this ReservationController */
/* @var $model Reservation */
?>

<?php
$this->breadcrumbs=array(
	'Reservations'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Reservation', 'url'=>array('index')),
	array('label'=>'Manage Reservation', 'url'=>array('admin')),
);
?>

<h1>Create Reservation</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>