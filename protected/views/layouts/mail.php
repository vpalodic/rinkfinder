<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/styles.css" />
    <?php Yii::app()->bootstrap->register(); ?>
    
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
<table cellspacing="0" cellpadding="10" style="color:#666;font:13px Arial;line-height:1.4em;width:100%;">
    <tbody>
        <tr>
            <td style="color:#4D90FE;font-size:22px;border-bottom: 2px solid #4D90FE;">
                <?php echo CHtml::encode(Yii::app()->name); ?>
            </td>
        </tr>
        <tr>
            <td style="color:#777;font-size:16px;padding-top:5px;">
            	<?php if(isset($data['description'])) echo $data['description'];  ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $content ?>
            </td>
        </tr>
        <tr>
            <td style="padding:15px 20px;text-align:right;padding-top:5px;border-top:solid 1px #dfdfdf">
                <a href="http://www.rinkfinder.com/"><img alt="rinkfinder.com" src="rinkfinder.jpg" /></a>
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>
