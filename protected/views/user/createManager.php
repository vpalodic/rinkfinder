<?php
    /* @var $this UserController */
    /* @var $model User */
    /* @var $profile Profile */
?>

<?php
$this->breadcrumbs = array(
	'Users' => array('index'),
	'Create Arena Manager',
);

$this->menu = array(
	array('label' => 'List User', 'url' => array('index')),
	array('label' => 'Manage User', 'url' => array('admin')),
);
?>

<h2 class="sectionHeader">Create Arena Manager</h2>
<p class="sectionSubHeaderContent">
    Use the form below to create an Arena Manager. This user has
    complete access to the Arena's they have been assigned to manage.    
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