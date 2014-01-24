<?php
/* @var $this ArenaReservationPolicyController */
/* @var $model ArenaReservationPolicy */


$this->breadcrumbs=array(
	'Arena Reservation Policies'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List ArenaReservationPolicy', 'url'=>array('index')),
	array('label'=>'Create ArenaReservationPolicy', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#arena-reservation-policy-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Arena Reservation Policies</h1>

<p>
    You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>
        &lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'arena-reservation-policy-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'arena_id',
		'days',
		'cutoff_time',
		'cutoff_day',
		'notes',
		/*
		'event_type_id',
		'lock_version',
		'created_by_id',
		'created_on',
		'updated_by_id',
		'updated_on',
		*/
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>