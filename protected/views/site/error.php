<?php
    /* @var $this SiteController */
    /* @var $error array */

    $this->pageTitle=Yii::app()->name . ' - Error';
    $this->breadcrumbs=array('Error',);
?>

<?php $this->widget('bootstrap.widgets.TbAlert'); ?>

<h2 class="sectionHeader">Error <?php echo $code; ?></h2>

<div class="error">
    <?php
        if(is_array($message)) {
            foreach($message as $key => $value) {
                echo CHtml::encode($key) . ': ';
                echo (is_array($value) ? CHtml::encode($value[0]) : CHtml::encode($value)) . '<br />';
            }
        } else {
            echo CHtml::encode($message);
        }
    ?>
</div>