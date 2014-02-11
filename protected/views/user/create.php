<?php
    /* @var $this UserController */
    /* @var $model User */
    /* @var $profile Profile */
    /* @var $role string */
    /* @var $description string */
?>

<?php
$this->breadcrumbs = array(
	'Users' => array('index'),
	'Create ' . $role,
);

$this->menu = array(
	array('label' => 'List User', 'url' => array('index')),
	array('label' => 'Manage User', 'url' => array('admin')),
);
?>

<h2 class="sectionHeader">Create <?php echo $role; ?></h2>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<p class="sectionSubHeaderContent">
    Use the form below to create a new <b><?php echo $role; ?>:</b>
    <?php echo $description ?>
</p>
<?php
    $this->renderPartial(
            '_form',
            array(
                'model' => $model,
                'profile' => $profile,
            )
    );
?>