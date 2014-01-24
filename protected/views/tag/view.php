<?php
/* @var $this TagController */
/* @var $model Tag */
?>

<?php
$this->breadcrumbs=array(
	'Tags'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Tag', 'url'=>array('index')),
	array('label'=>'Create Tag', 'url'=>array('create')),
	array('label'=>'Update Tag', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Tag', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Tag', 'url'=>array('admin')),
);
?>

<h1>View Tag #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView',array(
    'htmlOptions' => array(
        'class' => 'table table-striped table-condensed table-hover',
    ),
    'data'=>$model,
    'attributes'=>array(
		'id',
		'name',
		'frequency',
		'lock_version',
		'created_by_id',
		'created_on',
		'updated_by_id',
		'updated_on',
	),
)); ?>