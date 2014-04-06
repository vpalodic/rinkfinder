<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php  // Publish and register our jQuery and Bootstrap plugin CSS files
        $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
        if(defined('YII_DEBUG') && $this->includeCss) {
            Yii::app()->clientScript->registerCssFile($path . '/css/fineuploader.css');
        } elseif ($this->includeCss) {
            Yii::app()->clientScript->registerCssFile($path . '/css/fineuploader.min.css');
        }
    ?>
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <?php Yii::app()->bootstrap->register(); ?>
    <?php  // Publish and register our jQuery and Bootstrap plugin CSS files
        $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
        if(defined('YII_DEBUG') && $this->includeCss) {
            Yii::app()->clientScript->registerCssFile($path . '/css/font-awesome.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/daterangepicker.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-modal.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-switch.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/footable.core.css');
        } elseif ($this->includeCss) {
            Yii::app()->clientScript->registerCssFile($path . '/css/font-awesome.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/daterangepicker.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-modal.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-switch.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/footable.core.min.css');
        }
    ?>
    <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/styles.css" /> 
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/console.js"></script>
</head>

<body>
<?php if($this->navigation): ?>
    <?php
        Yii::app()->clientScript->registerScript(
                    'fadeAndHideEffect',
                    '$(".fade-message").animate({opacity: 1.0}, 30000).fadeOut("slow");'
        );
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
                            array(
                                'label' => '<i class="icon-home"></i> Home',
                                'url' => array('/site/index')
                            ),
                            array(
                                'label' => '<i class="icon-briefcase"></i> Management',
                                'url' => array('/site/management'),
                                'visible' => Yii::app()->user->isRestrictedArenaManager()
                            ),
                            array(
                                'label' => '<i class="icon-tasks"></i> Administration',
                                'url' => array('/site/administration'),
                                'visible' => Yii::app()->user->isApplicationAdministrator()
                            ),
                            array(
                                'label' => '<i class="icon-envelope"></i> Contact',
                                'url' => array('/site/contact')
                            ),
                            array(
                                'label' => '<i class="icon-info-sign"></i> About',
                                'url' => array('/site/page', 'view' => 'about')
                            ),
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
                                        'label' => '<i class="icon-list"></i> View Profile',
                                        'url' => array('/profile/' . Yii::app()->user->id),
                                        'visible'=> !Yii::app()->user->isGuest,
                                    ),
                                    array(
                                        'label' => '<i class="icon-wrench"></i> Edit Profile',
                                        'url' => array('/profile/update/' . Yii::app()->user->id),
                                        'visible'=> !Yii::app()->user->isGuest,
                                    ),
                                    array(
                                        'label' => '<i class="icon-edit"></i> Change Password',
                                        'url' => array('/user/changePassword/' . Yii::app()->user->id),
                                        'visible'=> !Yii::app()->user->isGuest,
                                    ),
                                    TbHtml::menuDivider(),
                                    array(
                                        'label' => '<i class="icon-off"></i> Logout',
                                        'url' => array('/site/logout'),
                                        'visible'=> !Yii::app()->user->isGuest,
                                    ),
                                )
                            ),
                        ),
                    ),
                ),
    )); ?>

    <?php
    preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);

    if(count($matches)>1):
        //Then we're using IE
        $version = $matches[1];

        if($version <= 9):
    ?>      
            <div class="alert alert-error alert-block fade-message">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4>Warning!</h4>
                You are using an unsupported browser. For the best experience with this site, please
                <a target="_blank" href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                    upgrade your version of Internet Explorer
                </a> or try using <a target="_blank" href="http://www.google.com/chrome">
                    Google's Chrome
                </a> or <a target="_blank" href="http://www.mozilla.org/firefox">
                    Mozilla's FireFox
                </a> browser.
            </div>
        <?php endif; ?>
    <?php endif; ?>

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
                    <?php echo CHtml::link('Terms of Use', array('site/page', 'view' => 'terms_of_use')); ?> | 
                    <?php echo CHtml::link('Privacy Policy', array('site/page', 'view' => 'privacy_policy')); ?>
                    <br />
                </footer><!-- footer -->
            </div>
        </div>
    </div><!-- page -->
<?php else: ?>
    <div class="container-fluid" id="page">
        <?php echo $content; ?>
        
        <div class="clear"></div>
        
        <div class="row-fluid">
            <div class="span12">
                <footer id="footer">
                    Copyright &copy; <?php echo date('Y'); ?> <?php echo CHtml::encode(Yii::app()->name); ?>
                    All Rights Reserved.<br/>
                    <?php echo CHtml::link('Terms of Use', array('site/page', 'view' => 'terms_of_use')); ?> | 
                    <?php echo CHtml::link('Privacy Policy', array('site/page', 'view' => 'privacy_policy')); ?>
                    <br />
                </footer><!-- footer -->
            </div>
        </div>
    </div><!-- page -->
    
<?php endif; ?><!-- if $printing -->
</body>
</html>
