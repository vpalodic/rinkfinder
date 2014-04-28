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
        if(defined('YII_DEBUG') && $this->includeCss) {
            Yii::app()->clientScript->registerCssFile($path . '/font-awesome-4.0.3/css/font-awesome.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/daterangepicker.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-modal.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-switch.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-datetimepicker.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/wysiwyg-color.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-wysihtml5.css');
            Yii::app()->clientScript->registerCssFile($path . '/bootstrap-editable/css/bootstrap-editable.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/footable.core.css');
        } elseif ($this->includeCss) {
            Yii::app()->clientScript->registerCssFile($path . '/css/font-awesome.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/daterangepicker.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-modal.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-switch.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-datetimepicker.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/wysiwyg-color.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/bootstrap-wysihtml5.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/bootstrap-editable/css/bootstrap-editable.min.css');
            Yii::app()->clientScript->registerCssFile($path . '/css/footable.core.min.css');
        }
        
        // Register Google Apps Maps API
        Yii::app()->clientScript->registerScriptFile("//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&" . 
                "key=" . Yii::app()->params['googleApi']['key'], CClientScript::POS_HEAD);
        
        if(defined('YII_DEBUG')) {
            Yii::app()->clientScript->registerScriptFile($path . '/js/jquery.history.js', CClientScript::POS_HEAD);
            Yii::app()->clientScript->registerScriptFile($path . '/jquery-scrollto/lib/jquery-scrollto.js', CClientScript::POS_HEAD);
            Yii::app()->clientScript->registerScriptFile($path . '/js/moment.js', CClientScript::POS_BEGIN);
            Yii::app()->clientScript->registerScriptFile($path . '/js/moment-recur.js', CClientScript::POS_BEGIN);
            Yii::app()->clientScript->registerScriptFile($path . '/js/daterangepicker.js', CClientScript::POS_BEGIN);
            Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-switch.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modalmanager.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modal.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-datetimepicker.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/bootstrap-editable/js/bootstrap-editable.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/wysihtml5/advanced.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/wysihtml5/wysihtml5-0.3.0.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-wysihtml5.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/bootstrap-editable/js/wysihtml5.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/jquery.inputmask.bundle.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/footable.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/footable.filter.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/footable.sort.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/footable.paginate.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/utilities.js', CClientScript::POS_BEGIN);
        } else {
            Yii::app()->clientScript->registerScriptFile($path . '/js/jquery.history.min.js', CClientScript::POS_HEAD);
            Yii::app()->clientScript->registerScriptFile($path . '/jquery-scrollto/lib/jquery-scrollto.min.js', CClientScript::POS_HEAD);
            Yii::app()->clientScript->registerScriptFile($path . '/js/moment.min.js', CClientScript::POS_BEGIN);
            Yii::app()->clientScript->registerScriptFile($path . '/js/moment-recur.min.js', CClientScript::POS_BEGIN);
            Yii::app()->clientScript->registerScriptFile($path . '/js/daterangepicker.min.js', CClientScript::POS_BEGIN);
            Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-switch.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modalmanager.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modal.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-datetimepicker.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/bootstrap-editable/js/bootstrap-editable.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/wysihtml5/advanced.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/wysihtml5/wysihtml5-0.3.0.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-wysihtml5.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/bootstrap-editable/js/wysihtml5.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/jquery.inputmask.bundle.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/footable.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/footable.filter.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/footable.sort.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/footable.min.paginate.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/utilities.min.js', CClientScript::POS_BEGIN);
        }
    ?>
    <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/styles.css" /> 
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/console.js"></script>
</head>

<body>
<?php if($this->navigation == true): ?>
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
                <ul id="navOuter" role="menu" class="nav hidden-desktop<?php if(Yii::app()->controller->uniqueId === 'site' && Yii::app()->controller->action->id === 'locationSearch') echo ' active'; ?>">
                    <li role="menuitem"<?php if(Yii::app()->controller->uniqueId === 'site' && Yii::app()->controller->action->id === 'locationSearch') echo ' class="active"'; ?>>
                        <a tabindex="-1" href="<?php echo $this->createUrl('/site/locationSearch'); ?>">
                            <i class="fa fa-search fa-lg"></i> Find
                        </a>
                    </li>
                    <li role="menuitem"<?php if(Yii::app()->controller->uniqueId === 'site' && Yii::app()->controller->action->id === 'eventSearch') echo ' class="active"'; ?>>
                        <a tabindex="-1" href="<?php echo $this->createUrl('/site/eventSearch'); ?>">
                            <i class="fa fa-ticket fa-lg"></i> Events
                        </a>
                    </li>
                </ul>
                <div class="nav-collapse collapse" id="navCollapse">
                    <ul id="navInner1" class="nav" role="menu">
                        <li role="menuitem"<?php if(Yii::app()->controller->uniqueId === 'site' && Yii::app()->controller->action->id === 'index') echo ' class="active"'; ?>>
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/index'); ?>">
                                <i class="fa fa-lg fa-home fa-fw"></i> Home
                            </a>
                        </li>
                        <li role="menuitem" class="hidden-phone hidden-tablet<?php if(Yii::app()->controller->uniqueId === 'site' && Yii::app()->controller->action->id === 'locationSearch') echo ' active'; ?>">
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/locationSearch'); ?>">
                                <i class="fa fa-search fa-lg fa-fw"></i> Find
                            </a>
                        </li>
                        <li role="menuitem" class="hidden-phone hidden-tablet<?php if(Yii::app()->controller->uniqueId === 'site' && Yii::app()->controller->action->id === 'eventSearch') echo ' active'; ?>">
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/eventSearch'); ?>">
                                <i class="fa fa-ticket fa-lg"></i> Events
                            </a>
                        </li>
                        <li role="menuitem" <?php if(Yii::app()->controller->uniqueId === 'arena') echo 'class="active"'; ?>>
                            <a tabindex="-1" href="<?php echo $this->createUrl('/arena/index'); ?>">
                                <i class="fa fa-th-list fa-lg fa-fw"></i> Facilities
                            </a>
                        </li>
                        <?php if(Yii::app()->user->isRestrictedArenaManager()) : ?>
                        <li role="menuitem"<?php if(Yii::app()->controller->uniqueId === 'site' && Yii::app()->controller->action->id === 'management') echo ' class="active"'; ?>>
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/management'); ?>">
                                <i class="fa fa-lg fa-briefcase fa-fw"></i> Management
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(Yii::app()->user->isApplicationAdministrator()) : ?>
                        <li role="menuitem"<?php if(Yii::app()->controller->uniqueId === 'site' && Yii::app()->controller->action->id === 'administration') echo ' class="active"'; ?>>
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/administration'); ?>">
                                <i class="fa fa-lg fa-tasks fa-fw"></i> Administration
                            </a>
                        </li>
                        <?php endif; ?>
                        <li role="menuitem"<?php if(Yii::app()->controller->uniqueId === 'site' && Yii::app()->controller->action->id === 'contact') echo ' class="active"'; ?>>
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/contact'); ?>">
                                <i class="fa fa-lg fa-envelope fa-fw"></i> Contact
                            </a>
                        </li>
                        <li role="menuitem"<?php if(Yii::app()->controller->uniqueId === 'site' && Yii::app()->controller->action->id === 'page') echo ' class="active"'; ?>>
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/page?view=about'); ?>">
                                <i class="fa fa-lg fa-info fa-fw"></i> About</a>
                        </li>
                    </ul>
                    <?php if(Yii::app()->user->isGuest) : ?>
                    <ul class="pull-right nav" id="navInner2" role="menu">
                        <li role="menuitem"<?php if(Yii::app()->controller->uniqueId === 'site' && Yii::app()->controller->action->id === 'login') echo ' class="active"'; ?>>
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/login'); ?>">
                                <i class="fa fa-lg fa-user fa-fw"></i> Login
                            </a>
                        </li>
                        <li role="menuitem"<?php if(Yii::app()->controller->uniqueId === 'site' && Yii::app()->controller->action->id === 'register') echo ' class="active"'; ?>>
                            <a tabindex="-1" href="<?php echo $this->createUrl('/site/register'); ?>">
                                <i class="fa fa-lg fa-users fa-fw"></i> Register
                            </a>
                        </li>
                    </ul>
                    <?php else : ?>
                    <ul class="pull-right nav" id="navInner3" role="menu">
                        <li role="menuitem" class="dropdown<?php if(Yii::app()->controller->uniqueId === 'user' || Yii::app()->controller->uniqueId === 'profile') echo ' active'; ?>">
                            <a class="no-ajaxy dropdown-toggle" data-toggle="dropdown" href="">
                                <i class="fa fa-lg fa-user fa-fw"></i> <?php echo Yii::app()->user->fullName; ?>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" id="dropdownUser" aria-labelledby="dropdownUser" role="menu">
                                <li class="nav-header">Profile</li>
                                <li role="menuitem"<?php if(Yii::app()->controller->uniqueId === 'user' && Yii::app()->controller->action->id === 'view') echo ' class="active"'; ?>>
                                    <a tabindex="-1" href="<?php echo $this->createUrl('/user/view', array('id' => Yii::app()->user->id)); ?>">
                                        <i class="fa fa-lg fa-edit fa-fw"></i> Account
                                    </a>
                                </li>
                                <li role="menuitem"<?php if(Yii::app()->controller->uniqueId === 'user' && Yii::app()->controller->action->id === 'changePassword') echo ' class="active"'; ?>>
                                    <a tabindex="-1" href="<?php echo $this->createUrl('/user/changePassword', array('id' => Yii::app()->user->id)); ?>">
                                        <i class="fa fa-lg fa-pencil fa-fw"></i> Change Password
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li role="menuitem">
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
        <?php if(isset($this->breadcrumbs) && !empty($this->breadcrumbs)):?>
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
                <div id="footer">
                    Copyright &copy; <?php echo date('Y'); ?> <?php echo CHtml::encode(Yii::app()->name); ?>
                    All Rights Reserved.<br/>
                    <?php echo CHtml::link('Terms of Use', array('site/page', 'view' => 'terms_of_use')); ?> | 
                    <?php echo CHtml::link('Privacy Policy', array('site/page', 'view' => 'privacy_policy')); ?>
                    <br />
                </div><!-- footer -->
            </div>
        </div>
    </div><!-- page -->
    
<?php endif; ?><!-- if $this->navigation -->
</body>
</html>
