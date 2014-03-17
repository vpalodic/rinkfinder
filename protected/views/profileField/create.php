<?php
$this->breadcrumbs=array(
	'Profile Fields' => array('admin'),
	'Create',
);
?>
<h1><?php echo 'Create Profile Field'; ?></h1>

<?php echo $this->renderPartial('_menu',array(
		'list'=> array(),
	)); ?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>