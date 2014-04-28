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
                'type' => 'striped bordered condensed hover',
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
                        'htmlOptions' => array(
                            'style' => 'word-break:break-all;word-wrap:break-word',
                        )
                    ),
                    array(
                        'name' => 'fullName',
                        'type'=>'raw',
                        'htmlOptions' => array(
                            'style' => 'word-break:break-all;word-wrap:break-word',
                        )
                    ),
                    array(
                        'name' => 'email',
                        'type'=>'raw',
                        'value' => 'TbHtml::mailto($data->email)',
                        'htmlOptions' => array(
                            'style' => 'word-break:break-all;word-wrap:break-word',
                        )
                    ),
                    array(
                        'name' => 'status_id',
                        'type'=>'raw',
                        'value' => '($data->itemAlias("UserStatus", $data->status_id) !== false) ?
                            $data->itemAlias("UserStatus", $data->status_id) :
                            "Unknown"',
                    ),
                    array(
                        'name' => 'activated_on',
                        'value' => '((isset($data->activated_on) && $data->activated_on != "0000-00-00 00:00:00") ?
                                      date_format(date_create_from_format("Y-m-d H:i:s", $data->activated_on), "m-d-Y H:i:s") :
                                      "Not Activated"
                        )',
                    ),
                    array(
                        'name' => 'last_visited_on',
                        'value' => '((isset($data->last_visited_on) && $data->last_visited_on != "0000-00-00 00:00:00") ?
                                      date_format(date_create_from_format("Y-m-d H:i:s", $data->last_visited_on), "m-d-Y H:i:s") :
                                      "Not visited"
                        )',
                    ),
                    array(
                        'name' => 'last_visited_from',
                        'type'=>'raw',
                        'value' => '((isset($data->last_visited_from) && $data->last_visited_from != "") ? 
                                      $data->last_visited_from :
                                      "Not visited"
                        )'
                    ),
                    array(
                        'name' => 'created_on',
                        'value' => 'date_format(date_create_from_format("Y-m-d H:i:s", $data->created_on), "m-d-Y H:i:s")',
                    ),
                    array(
                        'name' => 'created_by_id',
                        'type'=>'raw',
                        'value' => 'TbHtml::link(
                            CHtml::encode($data->createdBy->username),
                            array(
                                "user/view",
                                "id" => $data->created_by_id
                            )
                        )',
                   ),
                    array(
                        'name' => 'updated_on',
                        'value' => 'date_format(date_create_from_format("Y-m-d H:i:s", $data->updated_on), "m-d-Y H:i:s")',
                    ),
                    array(
                        'name' => 'updated_by_id',
                        'type'=>'raw',
                        'value' => 'TbHtml::link(
                            CHtml::encode($data->updatedBy->username),
                            array(
                                "user/view",
                                "id" => $data->updated_by_id
                            )
                        )',
                    ),
                ),
            )
    );
?>
