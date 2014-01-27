<?php
/* @var $this EventRequestController */
/* @var $model EventRequest */
?>

<?php
$this->breadcrumbs=array(
	'Event Requests'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List EventRequest', 'url'=>array('index')),
	array('label'=>'Create EventRequest', 'url'=>array('create')),
	array('label'=>'View EventRequest', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage EventRequest', 'url'=>array('admin')),
);
?>

    <h1>Update EventRequest <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>