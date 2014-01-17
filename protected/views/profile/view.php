<?php
/* @var $this ProfileController */
/* @var $model Profile */
?>

<?php
$this->breadcrumbs=array(
	'Profiles'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Profile', 'url'=>array('index')),
	array('label'=>'Create Profile', 'url'=>array('create')),
	array('label'=>'Update Profile', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Profile', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Profile', 'url'=>array('admin')),
);
?>

<h1>View Profile #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView',array(
    'htmlOptions' => array(
        'class' => 'table table-striped table-condensed table-hover',
    ),
    'data'=>$model,
    'attributes'=>array(
		'id',
		'user_id',
		'first_name',
		'last_name',
		'address_line1',
		'address_line2',
		'city',
		'state',
		'zip',
		'lat',
		'lng',
		'phone',
		'ext',
		'birthday',
		'created_by_id',
		'created_on',
		'updated_by_id',
		'updated_on',
	),
)); ?>