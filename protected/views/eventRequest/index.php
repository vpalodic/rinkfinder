<?php
/* @var $this EventRequestController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php
$this->breadcrumbs=array(
	'Event Requests',
);

$this->menu=array(
	array('label'=>'Create EventRequest','url'=>array('create')),
	array('label'=>'Manage EventRequest','url'=>array('admin')),
);
?>

<h1>Event Requests</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>