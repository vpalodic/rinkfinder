<ul class="actions">
	<li><?php echo CHtml::link('Manage User',array('/user/admin')); ?></li>
	<li><?php echo CHtml::link('Manage Profile Field',array('admin')); ?></li>
<?php 
	if (isset($list)) {
		foreach ($list as $item)
			echo "<li>".$item."</li>";
	}
?>
</ul><!-- actions -->