<?php
/* @var $this LocationController */
/* @var $model Location */
?>

<?php
$this->breadcrumbs=array(
	'Venues'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Location', 'url'=>array('index')),
	array('label'=>'Manage Location', 'url'=>array('admin')),
);
?>

<h1>Create Venue</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>