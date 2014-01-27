<?php
/* @var $this EventRequestController */
/* @var $model EventRequest */
?>

<?php
$this->breadcrumbs=array(
	'Event Requests'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List EventRequest', 'url'=>array('index')),
	array('label'=>'Manage EventRequest', 'url'=>array('admin')),
);
?>

<h1>Create EventRequest</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>