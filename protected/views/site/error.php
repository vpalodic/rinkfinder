<?php
    /* @var $this SiteController */
    /* @var $error array */

    $this->pageTitle=Yii::app()->name . ' - Error';
    $this->breadcrumbs=array('Error',);
?>

<?php $this->widget('bootstrap.widgets.TbAlert'); ?>

<h2 class="sectionHeader">Error <?php echo $code; ?></h2>

<div class="error">
    <?php echo CHtml::encode($message); ?>
</div>