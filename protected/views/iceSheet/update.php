<?php
/* @var $this IceSheetController */
/* @var $model IceSheet */
?>

<?php
$this->breadcrumbs=array(
	'Ice Sheets'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List IceSheet', 'url'=>array('index')),
	array('label'=>'Create IceSheet', 'url'=>array('create')),
	array('label'=>'View IceSheet', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage IceSheet', 'url'=>array('admin')),
);
?>

    <h1>Update IceSheet <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>