<?php
/* @var $this ArenaController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php
$this->breadcrumbs=array(
	'Arenas',
);

$this->menu=array(
	array('label'=>'Create Arena','url'=>array('create')),
	array('label'=>'Manage Arena','url'=>array('admin')),
);
?>

<h1>Arenas</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>