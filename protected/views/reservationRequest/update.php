<?php
/* @var $this ReservationRequestController */
/* @var $model ReservationRequest */
?>

<?php
$this->breadcrumbs=array(
	'Reservation Requests'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ReservationRequest', 'url'=>array('index')),
	array('label'=>'Create ReservationRequest', 'url'=>array('create')),
	array('label'=>'View ReservationRequest', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ReservationRequest', 'url'=>array('admin')),
);
?>

    <h1>Update ReservationRequest <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>