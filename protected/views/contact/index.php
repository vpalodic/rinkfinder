<?php
/* @var $this ContactController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php
$this->breadcrumbs=array(
	'Contacts',
);

$this->menu=array(
	array('label'=>'Create Contact','url'=>array('create')),
	array('label'=>'Manage Contact','url'=>array('admin')),
);
?>

<h1>Contacts</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>