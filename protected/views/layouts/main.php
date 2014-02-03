<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/styles.css" />
    <?php Yii::app()->bootstrap->register(); ?>
    
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<?php
    Yii::app()->clientScript->registerScript(
		'fadeAndHideEffect',
		'$(".fade-message").animate({opacity: 1.0}, 15000).fadeOut("slow");'
    );
?>

<?php
    $fullName = Yii::app()->user->name;
    
    if(($names = Yii::app()->user->getState('_names')) !== null) {
        if(isset($names['fullName'])) {
            $fullName = $names['fullName'];
        }
    }
?>
    
<?php $this->widget('bootstrap.widgets.TbNavbar',array(
            'collapse' => true,
            'fluid' => true,
            //'color' => TbHtml::NAVBAR_COLOR_INVERSE,
            'items' => array(
                array(
                    'class' => 'bootstrap.widgets.TbNav',
                    'items' => array(
                        array('label' => 'Home', 'url' => array('/site/index')),
                        array('label' => 'About', 'url' => array('/site/page', 'view' => 'about')),
                        array('label' => 'Contact', 'url' => array('/site/contact')),
                    ),
                ),
                array(
                    'class' => 'bootstrap.widgets.TbNav',
                    'htmlOptions'=>array('class' => 'pull-right'),
                    'items' => array(
                        array(
                            'label'=> "Login",
                            'url' => array('/site/login'),
                            'visible'=> Yii::app()->user->isGuest
                        ),
                        array(
                            'label'=> "Register",
                            'url' => array('/site/register'),
                            'visible'=> Yii::app()->user->isGuest
                        ),
                        array(
                            'label'=> $fullName,
//                            'label'=> Yii::app()->user->name,
                            //'url' => Yii::app()->getModule('user')->profileUrl,
                            'visible'=> !Yii::app()->user->isGuest,
                            'items' => array(
                                array('label' => 'Profile'),
                                array(
                                    'label' => 'View Profile',
                                    'url' => array('/profile/' . Yii::app()->user->id),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                                array(
                                    'label' => 'Edit Profile',
                                    'url' => array('/profile/update/' . Yii::app()->user->id),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                                array(
                                    'label' => 'Change Password',
                                    'url' => array('/user/changePassword/' . Yii::app()->user->id),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                                TbHtml::menuDivider(),
                                array(
                                    'label' => 'Logout',
                                    'url' => array('/site/logout'),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                            )
                        ),
                        array(
                            'label'=> 'Site Administration',
                            //'url' => array('/user/admin'),
                            //'visible'=>Yii::app()->getModule('user')->isAdmin(),
                            'items' => array(
                                array('label' => 'User Options'),
                                array(
                                    'label' => 'Create New User',
                                    'url' => array('/user/create'),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                                array(
                                    'label' => 'List Users',
                                    'url' => array('/user'),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                                array(
                                    'label' => 'Manage Users',
                                    'url' => array('/user/admin'),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                                TbHtml::menuDivider(),
                                array('label' => 'Profile Field Options'),
                                array(
                                    'label' => 'Create New Profile Field',
                                    'url' => array('/profileField/create'),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                                array(
                                    'label' => 'Manage Profile Fields',
                                    'url' => array('/profileField/admin'),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                                TbHtml::menuDivider(),
                                array(
                                    'label' => 'Authorization & Access Control',
                                    'url' => array('/rbam'),
                                    'visible'=> !Yii::app()->user->isGuest,
                                ),
                            )
                        ),
                    ),
                ),
            ),
)); ?>

<div class="container" id="page">

	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('bootstrap.widgets.TbBreadcrumb', array(
			'links' => $this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> <?php echo CHtml::encode(Yii::app()->name); ?><br/>
		All Rights Reserved.<br/>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
