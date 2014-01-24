<?php
/* @var $this ArenaReservationPolicyController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php
$this->breadcrumbs=array(
	'Arena Reservation Policies',
);

$this->menu=array(
	array('label'=>'Create ArenaReservationPolicy','url'=>array('create')),
	array('label'=>'Manage ArenaReservationPolicy','url'=>array('admin')),
);
?>

<h1>Arena Reservation Policies</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>