<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/styles.css" />
    <?php Yii::app()->bootstrap->register(); ?>
    
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<?php
    Yii::app()->clientScript->registerScript(
		'fadeAndHideEffect',
		'$(".fade-message").animate({opacity: 1.0}, 30000).fadeOut("slow");'
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
                    'encodeLabel' => false,
                    'items' => array(
                        array('label' => '<i class="icon-home"></i> Home', 'url' => array('/site/index')),
                        array('label' => '<i class="icon-info-sign"></i> About', 'url' => array('/site/page', 'view' => 'about')),
                        array('label' => '<i class="icon-envelope"></i> Contact', 'url' => array('/site/contact')),
                    ),
                ),
                array(
                    'class' => 'bootstrap.widgets.TbNav',
                    'htmlOptions'=>array('class' => 'pull-right'),
                    'encodeLabel' => false,
                    'items' => array(
                        array(
                            'label'=> '<i class="icon-user"></i> Login',
                            'url' => array('/site/login'),
                            'visible'=> Yii::app()->user->isGuest
                        ),
                        array(
                            'label'=> '<i class="icon-plus-sign"></i> Register',
                            'url' => array('/site/register'),
                            'visible'=> Yii::app()->user->isGuest
                        ),
                        array(
                            'label'=> '<i class="icon-user"></i> ' . Yii::app()->user->fullName,
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
                                    'label' => '<i class="icon-minus-sign"></i> Logout',
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

<div class="container-fluid" id="page">
    <div class="row-fluid">
        <div class="span12">
            <?php if(isset($this->breadcrumbs)):?>
                <?php 
                    $this->widget(
                            'bootstrap.widgets.TbBreadcrumb',
                            array(
                                'links' => $this->breadcrumbs,
                            )
                    );
                ?><!-- breadcrumbs -->
            <?php endif?>
        </div>
    </div>

    <?php echo $content; ?>

    <div class="clear"></div>

    <div class="row-fluid">
        <div class="span12">
            <footer id="footer">
                Copyright &copy; <?php echo date('Y'); ?> <?php echo CHtml::encode(Yii::app()->name); ?>
                All Rights Reserved.<br/>
                <?php echo CHtml::link('Terms of Use', array('site/page', 'view' => 'terms_of_use')); ?> 
                <?php echo CHtml::link('Privacy Policy', array('site/page', 'view' => 'privacy_policy')); ?>
                <br />
            </footer><!-- footer -->
        </div>
    </div>
</div><!-- page -->

</body>
</html>
