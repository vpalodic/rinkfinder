<?php
/* @var $this IceSheetController */
/* @var $model IceSheet */
?>

<?php
$this->breadcrumbs=array(
	'Ice Sheets'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List IceSheet', 'url'=>array('index')),
	array('label'=>'Manage IceSheet', 'url'=>array('admin')),
);
?>

<h1>Create IceSheet</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>