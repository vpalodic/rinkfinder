<?php
    /* @var $this UserController */
    /* @var $model User */
    /* @var $profile Profile */
?>

<?php
$this->breadcrumbs = array(
	'Users' => array('index'),
	'Create Restricted Arena Manager',
);

$this->menu = array(
	array('label' => 'List User', 'url' => array('index')),
	array('label' => 'Manage User', 'url' => array('admin')),
);
?>

<h2 class="sectionHeader">Create Restricted Arena Manager</h2>
<p class="sectionSubHeaderContent">
    Use the form below to create a Restricted Arena Manager. This user can
    perform all actions except for creating additional managers.
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