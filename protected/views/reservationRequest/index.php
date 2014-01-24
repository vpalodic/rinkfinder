<?php
/* @var $this ReservationRequestController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php
$this->breadcrumbs=array(
	'Reservation Requests',
);

$this->menu=array(
	array('label'=>'Create ReservationRequest','url'=>array('create')),
	array('label'=>'Manage ReservationRequest','url'=>array('admin')),
);
?>

<h1>Reservation Requests</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>