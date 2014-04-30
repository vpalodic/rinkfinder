<?php
    /* @var $this SiteController */
    /* @var $endpoints array[] */
    /* @var $path string */

    $this->pageTitle = Yii::app()->name . ' - Management';
    $this->breadcrumbs = array(
        'Management'
    );
?>

<h2 class="sectionHeader">Management Dashboard</h2>
<div id="managementContainer">
    <div id="eventsContainer" class="row-fluid accordion">
        <div class="accordion-heading">
            <div class="well well-small" style="display: inline-block;
                 overflow: auto; cursor: pointer">
                <div id="reportrangeAll">
                    <span id="reportrange">
                        <i class="fa fa-calendar fa-lg"></i>
                        <span id="reportrangeDate">
                            <?php echo date("F j, Y", strtotime('-29 day')); ?> - 
                            <?php echo date("F j, Y", strtotime('+29 day')); ?>
                        </span>
                        <b class="caret"></b>                    
                    </span>
                </div> 
            </div>
            <br />
            <button id="reportrangeRefreshButton" class="btn btn-success">
                <i class="fa fa-refresh fa-lg"></i>
            </button>
            <div id="eventsBadge" style="display: inline-block;">
            </div>
            <a class="no-ajaxy accordion-toggle" data-toggle="collapse"
               data-parent="eventsContainer" href="#eventsCollapse"
               style="display: inline-block;">
                <h5 id="eventsHeader">Events</h5>
            </a>
        </div>
        <div id="eventsCollapse" class="accordion-body collapse in">
            <div class="accordion-inner">
                <div id="eventsWell" class="well well-small">
                </div>
            </div>
        </div>
    </div>
<!--                <div class="row-fluid">
                    <div id="reservationsContainer" class="span4 accordion">
                        <div class="accordion-heading">
                            <div id="reservationsBadge" style="display: inline-block;">
                            </div>
                            <a class="no-ajaxy accordion-toggle" data-toggle="collapse"
                               data-parent="reservationsContainer" href="#reservationsCollapse"
                               style="display: inline-block;">
                                <h5 id="reservationsHeader">Reservations</h5>
                            </a>
                        </div>
                        <div id="reservationsCollapse" class="accordion-body collapse in">
                            <div class="accordion-inner">
                                <div id="reservationsWell" class="well well-large">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="locationsContainer" class="span4 accordion">
                        <div class="accordion-heading">
                            <div id="locationsBadge" style="display: inline-block;">
                            </div>
                            <a class="no-ajaxy accordion-toggle" data-toggle="collapse"
                               data-parent="locationsContainer" href="#locationsCollapse"
                               style="display: inline-block;">
                                <h5 id="locationsHeader">Locations</h5>
                            </a>
                        </div>
                        <div id="locationsCollapse" class="accordion-body collapse in">
                            <div class="accordion-inner">
                                <div id="locationsWell" class="well well-large">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="contactsContainer" class="span4 accordion">
                        <div class="accordion-heading">
                            <div id="contactsBadge" style="display: inline-block;">
                            </div>
                            <a class="no-ajaxy accordion-toggle" data-toggle="collapse"
                               data-parent="contactsContainer" href="#contactsCollapse"
                               style="display: inline-block;">
                                <h5 id="contactsHeader">Contacts</h5>
                            </a>
                        </div>
                        <div id="contactsCollapse" class="accordion-body collapse in">
                            <div class="accordion-inner">
                                <div id="contactsWell" class="well well-large">
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

    <div id="requestsContainer" class="row-fluid accordion">
        <div class="accordion-heading">
            <button id="refreshRequestsButton" class="btn btn-success">
                <i class="fa fa-refresh fa-lg"></i>
            </button>
            <div id="requestsBadge" style="display: inline-block;">
            </div>
            <a class="no-ajaxy accordion-toggle" data-toggle="collapse"
               data-parent="requestsContainer" href="#requestsCollapse"
               style="display: inline-block;">
                <h5 id="requestsHeader">Requests</h5>
            </a>
        </div>
        <div id="requestsCollapse" class="accordion-body collapse in">
            <div class="accordion-inner">
                <div id="requestsWell" class="well well-small">
                </div>
            </div>
        </div>
    </div>

    <div id="arenasContainer" class="row-fluid accordion">
        <div class="accordion-heading">
            <button id="refreshArenasButton" class="btn btn-success">
                <i class="fa fa-refresh fa-lg"></i>
            </button>
            <div id="arenasBadge" style="display: inline-block;">
            </div>
            <a class="no-ajaxy accordion-toggle" data-toggle="collapse"
               data-parent="arenasContainer" href="#arenasCollapse"
               style="display: inline-block;">
                <h5 id="arenasHeader">Arenas</h5>
            </a>
        </div>
        <div id="arenasCollapse" class="accordion-body collapse in">
            <div class="accordion-inner">
                <div id="arenasWell" class="well well-small">
                </div>
            </div>
        </div>
    </div>

</div>

<?php
    Yii::app()->clientScript->registerScript(
            'setupManagementDash',
            'utilities.urls.login = "' . $this->createUrl('site/login') . '";'
            . 'utilities.urls.logout = "' . $this->createUrl('site/logout') . '";'
            . 'utilities.urls.base = "' . Yii::app()->request->baseUrl . '";'
            . 'utilities.urls.assets = "' . $path . '";'
            . 'utilities.debug = ' . (defined('YII_DEBUG') ? 'true' : 'false') . ';'
            . 'utilities.ajaxError.dialogBox = "managementModal";'
            . 'utilities.loadingScreen.parentId = "countsContainer";'
            . 'utilities.loadingScreen.containerId = "countsAccordionHeader";'
            . 'utilities.loadingScreen.image.enabled = true;'
            . 'utilities.loadingScreen.image.src = "/images/spinners/ajax-loader-roller-bg_red-fg_blue.gif";'
            . 'utilities.loadingScreen.progress.enabled = false;'
            . 'utilities.loadingScreen.progress.type = "progress progress-striped active";'
            . 'utilities.loadingScreen.progress.percent = 40;'
            . 'management.endpoints.counts = "' . $endpoints['counts'] . '";'
            . 'management.endpoints.details = "' . $endpoints['details'] . '";'
            . 'management.endpoints.operations = "' . $endpoints['operations'] . '";'
            . 'management.dialogBox = "managementDataModal";'
            . 'management.editDialogBox = "managementEditDataModal";'
            . 'management.mainContainer = "managementContainer";'
            . 'management.getEvents();'
            . '$(window).on("load",function(){ '
            . 'if( $(this).width() < 767 ) '
            . '{'
            . '    $(".accordion-body.collapse").removeClass("in");'
            . '    $(".accordion-body.collapse").addClass("out");'
            . '}'
            . 'else'
            . '{'
            . '    $(".accordion-body.collapse").removeClass("out");'
            . '    $(".accordion-body.collapse").addClass("in");'
            . '}'
            . '});'
            . 'management.doReady()',
            CClientScript::POS_READY
    );
?>


<script type="text/javascript">

</script>        
