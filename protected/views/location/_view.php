<?php
/* @var $this LocationController */
/* @var $data Location */
?>

<div class="view">

    	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('arena_id')); ?>:</b>
	<?php echo CHtml::encode($data->arena_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('external_id')); ?>:</b>
	<?php echo CHtml::encode($data->external_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tags')); ?>:</b>
	<?php echo CHtml::encode($data->tags); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('length')); ?>:</b>
	<?php echo CHtml::encode($data->length); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('width')); ?>:</b>
	<?php echo CHtml::encode($data->width); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('radius')); ?>:</b>
	<?php echo CHtml::encode($data->radius); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('seating')); ?>:</b>
	<?php echo CHtml::encode($data->seating); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('base_id')); ?>:</b>
	<?php echo CHtml::encode($data->base_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('refrigeration_id')); ?>:</b>
	<?php echo CHtml::encode($data->refrigeration_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('resurfacer_id')); ?>:</b>
	<?php echo CHtml::encode($data->resurfacer_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('notes')); ?>:</b>
	<?php echo CHtml::encode($data->notes); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type_id')); ?>:</b>
	<?php echo CHtml::encode($data->type_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status_id')); ?>:</b>
	<?php echo CHtml::encode($data->status_id); ?>
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

	<b><?php echo CHtml::encode($data->getAttributeLabel('updated_on')); ?>:</b>
	<?php echo CHtml::encode($data->updated_on); ?>
	<br />
</div>