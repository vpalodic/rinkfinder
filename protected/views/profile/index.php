<?php
/* @var $this ProfileController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php
$this->breadcrumbs=array(
	'Profiles',
);

$this->menu=array(
	array('label'=>'Create Profile','url'=>array('create')),
	array('label'=>'Manage Profile','url'=>array('admin')),
);
?>

<h1>Profiles</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>