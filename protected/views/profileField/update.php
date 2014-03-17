<?php
$this->breadcrumbs=array(
	'Profile Fields'=>array('admin'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);
?>

<h1><?php echo 'Update ProfileField '.$model->id; ?></h1>

<?php echo $this->renderPartial('_menu', array(
		'list'=> array(
			CHtml::link('Create Profile Field',array('create')),
			CHtml::link('View Profile Field',array('view','id'=>$model->id)),
		),
	));
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>