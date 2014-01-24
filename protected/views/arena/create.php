<?php
/* @var $this ArenaController */
/* @var $model Arena */
?>

<?php
$this->breadcrumbs=array(
	'Arenas'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Arena', 'url'=>array('index')),
	array('label'=>'Manage Arena', 'url'=>array('admin')),
);
?>

<h1>Create Arena</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>