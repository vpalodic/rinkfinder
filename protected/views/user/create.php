<?php
    /* @var $this UserController */
    /* @var $model User */
    /* @var $profile Profile */
    /* @var $arena Arena */
    /* @var $role string */
    /* @var $displayRole string */
    /* @var $description string */
?>

<?php
    $this->buildBreadcrumbs('create', $displayRole, $arena);

$this->menu = array(
	array('label' => 'List User', 'url' => array('index')),
	array('label' => 'Manage User', 'url' => array('admin')),
);
?>

<h2 class="sectionHeader">Create <?php echo $displayRole; ?></h2>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<p class="sectionSubHeaderContent">
    Use the form below to create a new <b><?php echo $displayRole; ?></b>.
</p>
<?php if(isset($arena) && $arena !== null) : ?>
    <p class="sectionSubHeaderContent">
        The newly created <b><?php echo $displayRole; ?></b> will automatically be assigned to <b><?php echo $arena->name; ?></b>.
    </p>
<?php endif; ?>

<?php
    $this->renderPartial(
            '_form',
            array(
                'model' => $model,
                'profile' => $profile,
                'arena' => $arena,
                'role' => $role,
            )
    );
?>