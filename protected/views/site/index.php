<?php
    /* @var $this SiteController    */
    /* @var $types []               */
    /* @var $searchUrl string       */

    $this->pageTitle = Yii::app()->name;
?>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<h3 class="sectionSubHeader" style="text-align: center;"><span>Welcome to the new Rinkfinder.com</span></h3>
<h5 style="text-align: center;"><span>A Service of the Minnesota Ice Arena Manager's Association</span></h5>
<p class="sectionSubHeaderContent">We hope this improved layout will better assist you in quickly finding facilities in your community and their events or directions on a desktop or mobile device.
    <br />
    <br />
    To get started click on <a tabindex="-1" href="<?php echo $this->createUrl('/site/locationSearch'); ?>"> <i class="fa fa-search fa-lg"></i> Find</a> to quickly locate the closet facility to your current location or from a location you specify. The first time you search, the site will ask permission to use your current location; additional filters for your search are available as well.
    <br />
    <br />
    If you are looking for open program schedules or ice for sale, click on <a tabindex="-1" href="<?php echo $this->createUrl('/site/eventSearch'); ?>"> <i class="fa fa-ticket fa-lg"></i> Events</a>.
    <br />
    <br />
    Looking for a specific facility or any facility in a specific city? Click on <a tabindex="-1" href="<?php echo $this->createUrl('/arena/index'); ?>"> <i class="fa fa-th-list fa-lg fa-fw"></i> Facilities</a> and start typing!

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
