<?php
/* @var $this ArenaController */
/* @var $model Arena */


$this->breadcrumbs=array(
	'Arenas'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Arena', 'url'=>array('index')),
	array('label'=>'Create Arena', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#arena-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Arenas</h1>

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
	'id'=>'arena-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'external_id',
		'name',
		'city',
		'state',
		'zip',
		'status_id',
		'created_by_id',
		'created_on',
		'updated_by_id',
		'updated_on',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>