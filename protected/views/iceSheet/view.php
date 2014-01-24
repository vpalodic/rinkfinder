<?php
/* @var $this IceSheetController */
/* @var $model IceSheet */
?>

<?php
$this->breadcrumbs=array(
	'Ice Sheets'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List IceSheet', 'url'=>array('index')),
	array('label'=>'Create IceSheet', 'url'=>array('create')),
	array('label'=>'Update IceSheet', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete IceSheet', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage IceSheet', 'url'=>array('admin')),
);
?>

<h1>View IceSheet #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView',array(
    'htmlOptions' => array(
        'class' => 'table table-striped table-condensed table-hover',
    ),
    'data'=>$model,
    'attributes'=>array(
		'id',
		'arena_id',
		'external_id',
		'name',
		'description',
		'tags',
		'length',
		'width',
		'radius',
		'seating',
		'base_id',
		'refrigeration_id',
		'resurfacer_id',
		'notes',
		'type_id',
		'status_id',
		'lock_version',
		'created_by_id',
		'created_on',
		'updated_by_id',
		'updated_on',
	),
)); ?>