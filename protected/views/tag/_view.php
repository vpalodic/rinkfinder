<?php
/* @var $this TagController */
/* @var $data Tag */
?>

<div class="view">

    	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('frequency')); ?>:</b>
	<?php echo CHtml::encode($data->frequency); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('lock_version')); ?>:</b>
	<?php echo CHtml::encode($data->lock_version); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_by_id')); ?>:</b>
	<?php echo CHtml::encode($data->created_by_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_on')); ?>:</b>
	<?php echo CHtml::encode($data->created_on); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('updated_by_id')); ?>:</b>
	<?php echo CHtml::encode($data->updated_by_id); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('updated_on')); ?>:</b>
	<?php echo CHtml::encode($data->updated_on); ?>
	<br />

	*/ ?>

</div>