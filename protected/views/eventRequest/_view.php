<?php
/* @var $this EventRequestController */
/* @var $data EventRequest */
?>

<div class="view">

    	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('event_id')); ?>:</b>
	<?php echo CHtml::encode($data->event_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('requester_id')); ?>:</b>
	<?php echo CHtml::encode($data->requester_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('acknowledger_id')); ?>:</b>
	<?php echo CHtml::encode($data->acknowledger_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('acknowledged_on')); ?>:</b>
	<?php echo CHtml::encode($data->acknowledged_on); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('accepter_id')); ?>:</b>
	<?php echo CHtml::encode($data->accepter_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('accepted_on')); ?>:</b>
	<?php echo CHtml::encode($data->accepted_on); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('rejector_id')); ?>:</b>
	<?php echo CHtml::encode($data->rejector_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('rejected_on')); ?>:</b>
	<?php echo CHtml::encode($data->rejected_on); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('rejected_reason')); ?>:</b>
	<?php echo CHtml::encode($data->rejected_reason); ?>
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

	*/ ?>

</div>