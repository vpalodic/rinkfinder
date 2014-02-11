<?php
    /* @var $this UserController */
    /* @var $model User */
    /* @var $profile Profile */
?>

<?php
$this->breadcrumbs = array(
	'Users' => array('index'),
	'Create Site Administrator',
);

$this->menu = array(
	array('label' => 'List User', 'url' => array('index')),
	array('label' => 'Manage User', 'url' => array('admin')),
);
?>

<h2 class="sectionHeader">Create Site Administrator</h2>
<p class="sectionSubHeaderContent">
    Use the form below to create a Site Administrator. A Site Administrator has
    complete access to all site functions.
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