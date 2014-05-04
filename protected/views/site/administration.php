<?php
    /* @var $this SiteController */
    /* @var $endpoints array[] */
    /* @var $path string */

    $this->pageTitle = Yii::app()->name . ' - Administration';
    $this->breadcrumbs = array(
        'Administration'
    );
?>

<h2 class="sectionHeader">Administration Dashboard</h2>
<div id="AdministrationContainer">
    <div class="row-fluid">
        <div class="span6">
            <div id="arenasContainer" class="row-fluid accordion">
                <div id="arenasAccordionHeader" class="accordion-heading">
                    <a class="no-ajaxy accordion-toggle" data-toggle="collapse"
                       data-parent="arenasContainer" href="#arenasCollapse"
                       style="display: inline-block;">
                        <h5 id="arenasHeader">Facility Actions</h5>
                    </a>
                </div>
                <div id="arenasCollapse" class="accordion-body collapse in">
                    <div class="accordion-inner">
                        <div id="arenasWell" class="well well-small">
                            <a href="<?php echo $this->createUrl('/arena/create'); ?>">
                                Create New
                            </a>
                            <br />
                            <a href="<?php echo $this->createUrl('/arena/uploadArenas'); ?>">
                                Import
                            </a>
                            <br />
                            <a href="<?php echo $this->createUrl('/arena/admin'); ?>">
                                Administer Existing
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="span6">
            <div id="usersContainer" class="row-fluid accordion">
                <div id="usersAccordionHeader" class="accordion-heading">
                    <a class="no-ajaxy accordion-toggle" data-toggle="collapse"
                       data-parent="usersContainer" href="#usersCollapse"
                       style="display: inline-block;">
                        <h5 id="usersHeader">User Actions</h5>
                    </a>
                </div>
                <div id="usersCollapse" class="accordion-body collapse in">
                    <div class="accordion-inner">
                        <div id="usersWell" class="well well-small">
                            <?php if(Yii::app()->user->isSiteAdministrator()) : ?>
                            <a href="<?php echo $this->createUrl('/rbam/rbam'); ?>">
                                Manage Roles
                            </a>
                            <br />
                            <a href="<?php echo $this->createUrl('/user/create', array('role' => 'Administrator')); ?>">
                                Create New Site Administrator
                            </a>
                            <br />
                            <a href="<?php echo $this->createUrl('/user/create', array('role' => 'ApplicationAdministrator')); ?>">
                                Create New Application Administrator
                            </a>
                            <br />
                            <?php endif; ?>
                            <a href="<?php echo $this->createUrl('/user/create', array('role' => 'Manager')); ?>">
                                Create New Facility Manager
                            </a>
                            <br />
                            <a href="<?php echo $this->createUrl('/user/create', array('role' => 'RestrictedManager')); ?>">
                                Create New Restricted Facility Manager
                            </a>
                            <br />
                            <a href="<?php echo $this->createUrl('/user/admin'); ?>">
                                Administer Existing
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
