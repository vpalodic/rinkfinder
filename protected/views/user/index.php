<?php
/* @var $this UserController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php
$this->breadcrumbs=array(
	'Users',
);

$this->menu=array(
	array('label'=>'Create User','url'=>array('create')),
	array('label'=>'Manage User','url'=>array('admin')),
);
?>

<h2 class="sectionHeader">Users</h2>

<?php
    $this->widget('yiiwheels.widgets.grid.WhGridView',
            array(
                'filter' => null,
                'responsiveTable' => true,
                'fixedHeader' => true,
                'headerOffset' => 40,
                'type' => 'striped bordered',
                'template' => "{summary}\n{pager}\n{items}\n{pager}\n{summary}",
                'dataProvider' => $dataProvider,
                'columns' => array(
                    array(
                        'name' => 'username',
                        'type'=>'raw',
                        'value' => 'TbHtml::link(
                            CHtml::encode($data->username),
                            array(
                                "user/view",
                                "id" => $data->id
                            )
                        )',
                    ),
                    array(
                        'name' => 'last_visited_on',
                        'value' => '((isset($data->last_visisted_on) && $data->last_visited_on != "0000-00-00 00:00:00") ?
                                      date_format(date_create_from_format("Y-m-d H:i:s", $data->last_visit), "m-d-Y H:i:s") :
                                      "Not visited"
                        )',
                    ),
                    array(
                        'name' => 'created_on',
                        'value' => 'date_format(date_create_from_format("Y-m-d H:i:s", $data->created_on), "m-d-Y H:i:s")',
                    ),
                ),
            )
    );
?>
<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>