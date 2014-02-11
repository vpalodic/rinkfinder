<?php
    /* @var $this UserController */
    /* @var $model User */
    /* @var $profile Profile */
?>

<?php
$this->breadcrumbs = array(
	'Users' => array('index'),
	'Create Application Administrator',
);

$this->menu = array(
	array('label' => 'List User', 'url' => array('index')),
	array('label' => 'Manage User', 'url' => array('admin')),
);
?>

<h2 class="sectionHeader">Create Application Administrator</h2>
<p class="sectionSubHeaderContent">
    Use the form below to create an Application Administrator. This user has
    complete access to all application functions.
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