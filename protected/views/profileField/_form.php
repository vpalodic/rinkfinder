<div class="form">

<?php echo CHtml::beginForm(); ?>

	<p class="note"><?php echo 'Fields with <span class="required">*</span> are required.'; ?></p>

	<?php echo CHtml::errorSummary($model); ?>
	
	<div class="row varname">
		<?php echo CHtml::activeLabelEx($model,'varname'); ?>
		<?php echo (($model->id)?CHtml::activeTextField($model,'varname',array('size'=>60,'maxlength'=>50,'readonly'=>true)):CHtml::activeTextField($model,'varname',array('size'=>60,'maxlength'=>50))); ?>
		<?php echo CHtml::error($model,'varname'); ?>
		<p class="hint"><?php echo "Allowed lowercase letters and digits."; ?></p>
	</div>

	<div class="row title">
		<?php echo CHtml::activeLabelEx($model,'title'); ?>
		<?php echo CHtml::activeTextField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo CHtml::error($model,'title'); ?>
		<p class="hint"><?php echo 'Field name.'; ?></p>
	</div>

	<div class="row field_type">
		<?php echo CHtml::activeLabelEx($model,'field_type'); ?>
		<?php echo (($model->id)?CHtml::activeTextField($model,'field_type',array('size'=>60,'maxlength'=>50,'readonly'=>true,'id'=>'field_type')):CHtml::activeDropDownList($model,'field_type',ProfileField::itemAlias('field_type'),array('id'=>'field_type'))); ?>
		<?php echo CHtml::error($model,'field_type'); ?>
		<p class="hint"><?php echo 'Field type column in the database.'; ?></p>
	</div>

	<div class="row field_size">
		<?php echo CHtml::activeLabelEx($model,'field_size'); ?>
		<?php echo (($model->id)?CHtml::activeTextField($model,'field_size',array('readonly'=>true)):CHtml::activeTextField($model,'field_size')); ?>
		<?php echo CHtml::error($model,'field_size'); ?>
		<p class="hint"><?php echo 'Field size column in the database.'; ?></p>
	</div>

	<div class="row field_size_min">
		<?php echo CHtml::activeLabelEx($model,'field_size_min'); ?>
		<?php echo CHtml::activeTextField($model,'field_size_min'); ?>
		<?php echo CHtml::error($model,'field_size_min'); ?>
		<p class="hint"><?php echo 'The minimum value of the field (form validator).'; ?></p>
	</div>

	<div class="row required">
		<?php echo CHtml::activeLabelEx($model,'required'); ?>
		<?php echo CHtml::activeDropDownList($model,'required',ProfileField::itemAlias('required')); ?>
		<?php echo CHtml::error($model,'required'); ?>
		<p class="hint"><?php echo 'Required field (form validator).'; ?></p>
	</div>

	<div class="row match">
		<?php echo CHtml::activeLabelEx($model,'match'); ?>
		<?php echo CHtml::activeTextField($model,'match',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo CHtml::error($model,'match'); ?>
		<p class="hint"><?php echo "Regular expression (example: '/^[A-Za-z0-9\s,]+$/u')."; ?></p>
	</div>

	<div class="row range">
		<?php echo CHtml::activeLabelEx($model,'range'); ?>
		<?php echo CHtml::activeTextField($model,'range',array('size'=>60,'maxlength'=>5000)); ?>
		<?php echo CHtml::error($model,'range'); ?>
		<p class="hint"><?php echo 'Predefined values (example: 1;2;3;4;5 or 1==One;2==Two;3==Three;4==Four;5==Five).'; ?></p>
	</div>

	<div class="row error_message">
		<?php echo CHtml::activeLabelEx($model,'error_message'); ?>
		<?php echo CHtml::activeTextField($model,'error_message',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo CHtml::error($model,'error_message'); ?>
		<p class="hint"><?php echo 'Error message when you validate the form.'; ?></p>
	</div>

	<div class="row other_validator">
		<?php echo CHtml::activeLabelEx($model,'other_validator'); ?>
		<?php echo CHtml::activeTextField($model,'other_validator',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo CHtml::error($model,'other_validator'); ?>
		<p class="hint"><?php echo 'JSON string (example: ' . CJavaScript::jsonEncode(array("file"=>array("types"=>"jpg, gif, png"))) . ').'; ?></p>
	</div>

	<div class="row default">
		<?php echo CHtml::activeLabelEx($model,'default'); ?>
		<?php echo (($model->id)?CHtml::activeTextField($model,'default',array('size'=>60,'maxlength'=>255,'readonly'=>true)):CHtml::activeTextField($model,'default',array('size'=>60,'maxlength'=>255))); ?>
		<?php echo CHtml::error($model,'default'); ?>
		<p class="hint"><?php echo 'The value of the default field (database).'; ?></p>
	</div>

	<div class="row widget">
		<?php echo CHtml::activeLabelEx($model,'widget'); ?>
		<?php 
		list($widgetsList) = ProfileFieldController::getWidgets($model->field_type);
		echo CHtml::activeDropDownList($model,'widget',$widgetsList,array('id'=>'widgetlist'));
		//echo CHtml::activeTextField($model,'widget',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo CHtml::error($model,'widget'); ?>
		<p class="hint"><?php echo 'Widget name.'; ?></p>
	</div>

	<div class="row widgetparams">
		<?php echo CHtml::activeLabelEx($model,'widget_params'); ?>
		<?php echo CHtml::activeTextField($model,'widget_params',array('size'=>60,'maxlength'=>5000,'id'=>'widget_params')); ?>
		<?php echo CHtml::error($model,'widget_params'); ?>
		<p class="hint"><?php echo 'JSON string (example: ' . CJavaScript::jsonEncode(array("param1"=>array("val1","val2"),"param2"=>array("k1"=>"v1","k2"=>"v2"))) . ').'; ?></p>
	</div>

	<div class="row position">
		<?php echo CHtml::activeLabelEx($model,'position'); ?>
		<?php echo CHtml::activeTextField($model,'position'); ?>
		<?php echo CHtml::error($model,'position'); ?>
		<p class="hint"><?php echo 'Display order of fields.'; ?></p>
	</div>

	<div class="row visible">
		<?php echo CHtml::activeLabelEx($model,'visible'); ?>
		<?php echo CHtml::activeDropDownList($model,'visible',ProfileField::itemAlias('visible')); ?>
		<?php echo CHtml::error($model,'visible'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php echo CHtml::endForm(); ?>

</div><!-- form -->
<div id="dialog-form" title="<?php echo 'Widget parametrs'; ?>">
	<form>
	<fieldset>
		<label for="name">Name</label>
		<input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
		<label for="value">Value</label>
		<input type="text" name="value" id="value" value="" class="text ui-widget-content ui-corner-all" />
	</fieldset>
	</form>
</div>
