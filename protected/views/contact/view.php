<?php
/* @var $this ContactController */
/* @var $model Contact */
?>

<?php
$this->breadcrumbs=array(
	'Contacts'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Contact', 'url'=>array('index')),
	array('label'=>'Create Contact', 'url'=>array('create')),
	array('label'=>'Update Contact', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Contact', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Contact', 'url'=>array('admin')),
);
?>

<h1>View Contact #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView',array(
    'htmlOptions' => array(
        'class' => 'table table-striped table-condensed table-hover',
    ),
    'data'=>$model,
    'attributes'=>array(
		'id',
		'first_name',
		'last_name',
		'address_line1',
		'address_line2',
		'city',
		'state',
		'zip',
		'phone',
		'ext',
		'fax',
		'fax_ext',
		'email',
		'active',
		'lock_version',
		'created_by_id',
		'created_on',
		'updated_by_id',
		'updated_on',
	),
)); ?>