<?php
/* @var $this ReservationRequestController */
/* @var $model ReservationRequest */
?>

<?php
$this->breadcrumbs=array(
	'Reservation Requests'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ReservationRequest', 'url'=>array('index')),
	array('label'=>'Manage ReservationRequest', 'url'=>array('admin')),
);
?>

<h1>Create ReservationRequest</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>