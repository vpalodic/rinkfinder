<?php
    /* @var $this Controller */
    /* @var $data array */
?>
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
<table cellspacing="0" cellpadding="10" style="color:#666;font:13px Arial;line-height:1.4em;width:100%;">
    <tbody>
        <tr>
            <td colspan="2" style="text-align:center;padding:10px;font-size:2.0em;font-weight:bold;color:#FFFFFF;border-top-left-radius:5px;border-top-right-radius:5px;background:#0d1b72;background: -moz-linear-gradient(top,  #0d1b72 0%, #207cca 65%, #2989d8 71%, #7db9e8 100%);background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#0d1b72), color-stop(65%,#207cca), color-stop(71%,#2989d8), color-stop(100%,#7db9e8));background: -webkit-linear-gradient(top,  #0d1b72 0%,#207cca 65%,#2989d8 71%,#7db9e8 100%);background: -o-linear-gradient(top,  #0d1b72 0%,#207cca 65%,#2989d8 71%,#7db9e8 100%);background: -ms-linear-gradient(top,  #0d1b72 0%,#207cca 65%,#2989d8 71%,#7db9e8 100%);background: linear-gradient(to bottom,  #0d1b72 0%,#207cca 65%,#2989d8 71%,#7db9e8 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0d1b72', endColorstr='#7db9e8',GradientType=0 );">
                <a href="http://www.rinkfinder.com/"><img alt="rinkfinder.com" src="siteHeader.jpg" /></a>
            </td>
        </tr>
        <tr>
            <td style="padding:10px;font-size:2.0em;font-weight:bold;color:#FFFFFF;background:#0d1b72;background: -moz-linear-gradient(top,  #0d1b72 0%, #207cca 65%, #2989d8 71%, #7db9e8 100%);background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#0d1b72), color-stop(65%,#207cca), color-stop(71%,#2989d8), color-stop(100%,#7db9e8));background: -webkit-linear-gradient(top,  #0d1b72 0%,#207cca 65%,#2989d8 71%,#7db9e8 100%);background: -o-linear-gradient(top,  #0d1b72 0%,#207cca 65%,#2989d8 71%,#7db9e8 100%);background: -ms-linear-gradient(top,  #0d1b72 0%,#207cca 65%,#2989d8 71%,#7db9e8 100%);background: linear-gradient(to bottom,  #0d1b72 0%,#207cca 65%,#2989d8 71%,#7db9e8 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0d1b72', endColorstr='#7db9e8',GradientType=0 );" colspan="2">
                <i><?php echo CHtml::encode(Yii::app()->name); ?></i>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php echo $content ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:15px 20px;font-size:.8em;text-align:center;padding-top:5px;border-top:solid 1px #dfdfdf">
                Copyright &copy; <?php echo date('Y'); ?> <?php echo CHtml::encode(Yii::app()->name); ?><br/>
                All Rights Reserved.<br/>
                <br />
                <a href="http://www.rinkfinder.com/"><img alt="rinkfinder.com" src="rinkfinder.jpg" /></a>
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>
