<?php
/* @var $this IceSheetController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php
$this->breadcrumbs=array(
	'Ice Sheets',
);

$this->menu=array(
	array('label'=>'Create IceSheet','url'=>array('create')),
	array('label'=>'Manage IceSheet','url'=>array('admin')),
);
?>

<h1>Ice Sheets</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>