<?php
/* @var $this ArenaController */
/* @var $model Arena */
?>

<?php
$this->breadcrumbs=array(
	'Arenas'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Arena', 'url'=>array('index')),
	array('label'=>'Create Arena', 'url'=>array('create')),
	array('label'=>'Update Arena', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Arena', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Arena', 'url'=>array('admin')),
);
?>

<h1>View Arena #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView',array(
    'htmlOptions' => array(
        'class' => 'table table-striped table-condensed table-hover',
    ),
    'data'=>$model,
    'attributes'=>array(
		'id',
		'external_id',
		'name',
		'description',
		'tags',
		'address_line1',
		'address_line2',
		'city',
		'state',
		'zip',
		'lat',
		'lng',
		'phone',
		'ext',
		'fax',
		'fax_ext',
		'logo',
		'url',
		'notes',
		'status_id',
		'lock_version',
		'created_by_id',
		'created_on',
		'updated_by_id',
		'updated_on',
	),
)); ?>