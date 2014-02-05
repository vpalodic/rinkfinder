<?php
    /* @var $this SiteController */
    /* @var $error array */

    $this->pageTitle = Yii::app()->name . ' - Error';
    $this->breadcrumbs = array(
        'Error'
    );
?>

<h2 class="sectionHeader">Error <?php echo $code; ?></h2>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

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