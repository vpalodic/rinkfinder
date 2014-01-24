<?php
/* @var $this ArenaController */
/* @var $model Arena */
?>

<?php
$this->breadcrumbs=array(
	'Arenas'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Arena', 'url'=>array('index')),
	array('label'=>'Create Arena', 'url'=>array('create')),
	array('label'=>'View Arena', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Arena', 'url'=>array('admin')),
);
?>

    <h1>Update Arena <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>