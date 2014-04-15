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
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-datetimepicker.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/bootstrap-editable/css/bootstrap-editable.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/footable.core.css');
        } elseif ($this->includeCss) {
            Yii::app()->clientScript->registerCssFile($path . '/css/font-awesome.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/daterangepicker.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-modal.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-switch.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-datetimepicker.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/bootstrap-editable/css/bootstrap-editable.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/footable.core.min.css');
        }
    ?>
    <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/styles.css" /> 
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/console.js"></script>
</head>

<body>
<?php if($this->navigation): ?>
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a class="no-ajaxy btn btn-navbar" data-toggle="collapse" data-target="#navCollapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <a class="brand" href="<?php echo Yii::app()->request->baseUrl . '/'; ?>">
                    <?php echo Yii::app()->name; ?>
                </a>
                <ul id="navOuter" class="nav hidden-desktop" role="menu">
                    <li role="menuitem">
                        <a tabindex="-1" href="<?php echo $this->createUrl('/arena/locationSearch'); ?>">
                            <i class="fa fa-search fa-lg"></i> Facilities
                        </a>
                    </li>
                    <li role="menuitem">
                        <a tabindex="-1" href="<?php echo $this->createUrl('/arena/eventSearch'); ?>">
                            <i class="fa fa-search fa-lg"></i> Events
                        </a>
                    </li>
                </ul>
                <div class="nav-collapse collapse" id="navCollapse">
                    <ul id="navInner1" class="nav" role="menu">
                        <li role="menuitem">
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/index'); ?>">
                                <i class="fa fa-lg fa-home fa-fw"></i> Home
                            </a>
                        </li>
                        <li role="menuitem" class="hidden-phone  hidden-tablet">
                            <a tabindex="-1" href="<?php echo $this->createUrl('/arena/locationSearch'); ?>">
                                <i class="fa fa-search fa-lg fa-fw"></i> Facilities
                            </a>
                        </li>
                        <li role="menuitem" class="hidden-phone  hidden-tablet">
                            <a tabindex="-1" href="<?php echo $this->createUrl('/arena/eventSearch'); ?>">
                                <i class="fa fa-search fa-lg fa-fw"></i> Events
                            </a>
                        </li>
                        <?php if(Yii::app()->user->isRestrictedArenaManager()) : ?>
                        <li role="menuitem">
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/management'); ?>">
                                <i class="fa fa-lg fa-briefcase fa-fw"></i> Management
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(Yii::app()->user->isApplicationAdministrator()) : ?>
                        <li role="menuitem">
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/administration'); ?>">
                                <i class="fa fa-lg fa-tasks fa-fw"></i> Administration
                            </a>
                        </li>
                        <?php endif; ?>
                        <li role="menuitem">
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/contact'); ?>">
                                <i class="fa fa-lg fa-envelope fa-fw"></i> Contact
                            </a>
                        </li>
                        <li role="menuitem">
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/page?view=about'); ?>">
                                <i class="fa fa-lg fa-info fa-fw"></i> About</a>
                        </li>
                    </ul>
                    <?php if(Yii::app()->user->isGuest) : ?>
                    <ul class="pull-right nav" id="navInner2" role="menu">
                        <li visible="1" role="menuitem">
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/login'); ?>">
                                <i class="fa fa-lg fa-user fa-fw"></i> Login
                            </a>
                        </li>
                        <li visible="1" role="menuitem">
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/register'); ?>">
                                <i class="fa fa-lg fa-users fa-fw"></i> Register
                            </a>
                        </li>
                    </ul>
                    <?php else : ?>
                    <ul class="pull-right nav" id="navInner3" role="menu">
                        <li visible="1" role="menuitem" class="dropdown">
                            <a class="no-ajaxy dropdown-toggle" data-toggle="dropdown" href="">
                                <i class="fa fa-lg fa-user fa-fw"></i> <?php echo Yii::app()->user->fullName; ?>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" id="dropdownUser" aria-labelledby="dropdownUser" role="menu">
                                <li class="nav-header">Profile</li>
                                <li visible="1" role="menuitem">
                                    <a tabindex="-1" href="<?php echo $this->createUrl('/profile/view', array('id' => Yii::app()->user->id)); ?>">
                                        <i class="fa fa-lg fa-list-ul fa-fw"></i> View Profile
                                    </a>
                                </li>
                                <li visible="1" role="menuitem">
                                    <a tabindex="-1" href="<?php echo $this->createUrl('/profile/update', array('id' => Yii::app()->user->id)); ?>">
                                        <i class="fa fa-lg fa-pencil fa-fw"></i> Edit Profile</a>
                                </li>
                                <li visible="1" role="menuitem">
                                    <a tabindex="-1" href="<?php echo $this->createUrl('/user/changePassword', array('id' => Yii::app()->user->id)); ?>">
                                        <i class="fa fa-lg fa-edit fa-fw"></i> Change Password
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li visible="1" role="menuitem">
                                    <a class="no-ajaxy" tabindex="-1" href="<?php echo $this->createUrl('/site/logout'); ?>">
                                        <i class="fa fa-lg fa-power-off fa-fw"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

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
        <?php if(isset($this->breadcrumbs) && !empty($this->breakcrumbs)):?>
            <div class="row-fluid">
                <div class="span12">
                    <?php 
                        $this->widget(
                                'bootstrap.widgets.TbBreadcrumb',
                                array(
                                    'links' => $this->breadcrumbs,
                                )
                        );
                    ?><!-- breadcrumbs -->
                </div>
            </div>
        <?php endif?>
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
    
<?php endif; ?><!-- if $hits->navigation -->
</body>
</html>
