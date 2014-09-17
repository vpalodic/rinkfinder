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
    
    <div class="row-fluid">
        <div class="span4">
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
        </div>            
    </div>
    <div class="row-fluid">
        <div class="span3">
            <div id="eventsContainer" class="row-fluid accordion">
                <div id="eventsAccordionHeader" class="accordion-heading">
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
        </div>
        <div class="span3">
            <div id="requestsContainer" class="row-fluid accordion">
                <div id="requestsAccordionHeader" class="accordion-heading">
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
        </div>
        <div class="span3">
            <div id="arenasContainer" class="row-fluid accordion">
                <div id="arenasAccordionHeader" class="accordion-heading">
                    <button id="refreshArenasButton" class="btn btn-success">
                        <i class="fa fa-refresh fa-lg"></i>
                    </button>
                    <div id="arenasBadge" style="display: inline-block;">
                    </div>
                    <a class="no-ajaxy accordion-toggle" data-toggle="collapse"
                       data-parent="arenasContainer" href="#arenasCollapse"
                       style="display: inline-block;">
                        <h5 id="arenasHeader">Facilities</h5>
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
        <div class="span3">
            <div id="contactsContainer" class="row-fluid accordion">
                <div id="contactsAccordionHeader" class="accordion-heading">
                    <button id="refreshContactsButton" class="btn btn-success">
                        <i class="fa fa-refresh fa-lg"></i>
                    </button>
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
                        <div id="contactsWell" class="well well-small">
                        </div>
                    </div>
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
            . 'utilities.debug = ' . (YII_DEBUG ? 'true' : 'false') . ';'
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
            . 'management.getAll();'
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
