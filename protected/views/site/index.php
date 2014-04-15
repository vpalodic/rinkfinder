<?php
    /* @var $this SiteController    */
    /* @var $types []               */
    /* @var $searchUrl string       */

    $this->pageTitle = Yii::app()->name;
?>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<h3 class="sectionSubHeader" style="text-align: center;"><span>A Service of the Minnesota Ice Arena Manager's Association</span></h3>

<div class="mycalendar pull-right">
    <span class="month"><?php echo date('M'); ?></span>
    <span class="day"><?php echo date('j'); ?></span>
</div>
<br />
<time class="mycalendar-large" datetime="<?php echo date('Y-m-d'); ?>">
    <?php echo date('j'); ?> <em><?php echo date('F'); ?></em>
</time>
<br />
<time class="mycalendar-small pull-right" datetime="<?php echo date('Y-m-d'); ?>">
    <?php echo date('j'); ?> <em><?php echo date('M'); ?></em>
</time>
<br />
<p class="sectionSubHeaderContent">
    Rinkfinder.com will help you find over 160 member arenas throughout Minnesota
    (and other states), including directions, facility details, related vendors
    and MIAMA information. Our facility schedules lists facility activities and
    available hours for sale at member arenas. You can now find a member rink near
    you to go skating for pleasure, not just hockey.
</p>
<p class="sectionSubHeaderContent">
If you have questions or comments on the website, please contact
<a href="mailto:rinkfinder@miama.org?subject=Rinkfinder%20Questions%20and%20Comments">
    rinkfinder@miama.org
</a>.
</p>
