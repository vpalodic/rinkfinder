<?php
    $this->breadcrumbs = array('Profile Fields' => array('admin'),
                               $model->title,
                               );
?>

<?php $this->widget('bootstrap.widgets.TbAlert'); ?>

<h2><?php echo 'View Profile Field: ' . $model->varname; ?></h2>

<?php echo $this->renderPartial('_menu', array(
		'list'=> array(
			CHtml::link('Create Profile Field',array('create')),
			CHtml::link('Update Profile Field',array('update','id'=>$model->id)),
			CHtml::linkButton('Delete Profile Field',array('submit' => array('delete','id'=>$model->id),'confirm'=>'Are you sure to delete this item?')),
		),
	));
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
		'id',
		'varname',
		'title',
		'field_type',
		'field_size',
		'field_size_min',
		'required',
		'match',
		'range',
		'error_message',
		'other_validator',
		'widget',
		'widgetparams',
		'default',
		'position',
		'visible',
	),
)); ?>
