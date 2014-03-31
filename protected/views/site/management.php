<?php
    /* @var $this SiteController */
    /* @var $endpoints array[] */

    $this->pageTitle = Yii::app()->name . ' - Management';
    $this->breadcrumbs = array(
        'Management'
    );
?>
<?php
    Yii::app()->clientScript->registerScript(
            'setupManagementDash',
            'utilities.urls.login = "' . $this->createUrl('site/login') . '";'
            . 'utilities.urls.logout = "' . $this->createUrl('site/loout') . '";'
            . 'utilities.urls.base = "' . Yii::app()->request->baseUrl . '";'
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
            . 'management.mainContainer = "managementContainer";'
            . 'management.getCounts();'
            . '$(window).bind("load",function(){ '
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
            . '});',
            CClientScript::POS_READY
    );
?>

<h2 class="sectionHeader">Management Dashboard</h2>
<div id="managementContainer">
    <div id="countsContainer" class="row-fluid accordion">
        <div id="countsAccordionHeader" class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse"
               data-parent="countsContainer" href="#countsCollapse"
               style="display: inline-block">
                <h3 id="countsHeader">
                    <i class="fa fa-list fa-lg"></i> Summary
                </h3>
            </a>
            <br />
        </div>
        <div class="well well-small" style="display: inline-block;
             overflow: auto; cursor: pointer">
            <div id="reportrange">
                <i class="fa fa-calendar fa-lg"></i>
                <span>
                    <?php echo date("F j, Y", strtotime('-29 day')); ?> - 
                    <?php echo date("F j, Y", strtotime('+29 day')); ?>
                </span>
                <b class="caret"></b>
            </div> 
        </div>
        <div id="countsCollapse" class="accordion-body collapse in">
            <div class="accordion-inner">
                <div class="row-fluid">
                    <div id="requestsContainer" class="span6 accordion">
                        <div class="accordion-heading">
                            <div id="requestsBadge" style="display: inline-block;">
                            </div>
                            <a class="accordion-toggle" data-toggle="collapse"
                               data-parent="requestsContainer" href="#requestsCollapse"
                               style="display: inline-block;">
                                <h5 id="requestsHeader">Requests</h5>
                            </a>
                        </div>
                        <div id="requestsCollapse" class="accordion-body collapse in">
                            <div class="accordion-inner">
                                <div id="requestsWell" class="well well-large">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="reservationsContainer" class="span6 accordion">
                        <div class="accordion-heading">
                            <div id="reservationsBadge" style="display: inline-block;">
                            </div>
                            <a class="accordion-toggle" data-toggle="collapse"
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
                </div>            
                <div class="row-fluid">
                    <div id="eventsContainer" class="span6 accordion">
                        <div class="accordion-heading">
                            <div id="eventsBadge" style="display: inline-block;">
                            </div>
                            <a class="accordion-toggle" data-toggle="collapse"
                               data-parent="eventsContainer" href="#eventsCollapse"
                               style="display: inline-block;">
                                <h5 id="eventsHeader">Events</h5>
                            </a>
                        </div>
                        <div id="eventsCollapse" class="accordion-body collapse in">
                            <div class="accordion-inner">
                                <div id="eventsWell" class="well well-large">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="arenasContainer" class="span6 accordion">
                        <div class="accordion-heading">
                            <div id="arenasBadge" style="display: inline-block;">
                            </div>
                            <a class="accordion-toggle" data-toggle="collapse"
                               data-parent="arenasContainer" href="#arenasCollapse"
                               style="display: inline-block;">
                                <h5 id="arenasHeader">Arenas</h5>
                            </a>
                        </div>
                        <div id="arenasCollapse" class="accordion-body collapse in">
                            <div class="accordion-inner">
                                <div id="arenasWell" class="well well-large">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="operationsContainer" class="row-fluid accordion">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse"
               data-parent="operationsContainer" href="#operationsCollapse">
                <h3 id="operationsHeader">
                    <i class="fa fa-list fa-lg"></i> Operations
                </h3>
            </a>
        </div>
        <div id="operationsCollapse" class="accordion-body collapse in">
            <div class="accordion-inner">
                <div id="requestsContainer" class="span6 accordion">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse"
                           data-parent="requestsContainer" href="#requestsCollapse"
                           style="display: inline-block;">
                            <h5 id="requestsHeader">Requests</h5>
                        </a>
                    </div>
                    <div id="requestsCollapse" class="accordion-body collapse in">
                        <div class="accordion-inner">
                            <div id="requestsWell" class="well well-large">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="reservationsContainer" class="span6 accordion">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse"
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
                <div id="eventsContainer" class="span6 accordion">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse"
                           data-parent="eventsContainer" href="#eventsCollapse"
                           style="display: inline-block;">
                            <h5 id="eventsHeader">Events</h5>
                        </a>
                    </div>
                    <div id="eventsCollapse" class="accordion-body collapse in">
                        <div class="accordion-inner">
                            <div id="eventsWell" class="well well-large">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="arenasContainer" class="span6 accordion">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse"
                           data-parent="arenasContainer" href="#arenasCollapse"
                           style="display: inline-block;">
                            <h5 id="arenasHeader">Arenas</h5>
                        </a>
                    </div>
                    <div id="arenasCollapse" class="accordion-body collapse in">
                        <div class="accordion-inner">
                            <div id="arenasWell" class="well well-large">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal Dialog -->
<div id="managementModal" class="modal hide fade" 
     tabindex="-1" role="dialog" aria-labelledby="managementModalLabel"
     aria-hidden="true" data-width="760" data-backdrop="static" data-max-height="500" data-keyboard="false">
  <div id="managementModalHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 id="managementModalLabel"></h4>
  </div>
  <div id="managementModalBody" class="modal-body">
  </div>
  <div id="managementModalFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Error Modal Dialog -->

<!-- Data Modal Dialog -->
<div id="managementDataModal" class="modal hide fade" 
     tabindex="-1" role="dialog" aria-labelledby="managementDataModalLabel"
     aria-hidden="true" data-width="960" data-backdrop="static" data-max-height="500" data-keyboard="false">
  <div id="managementDataModalHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 id="managementDataModalLabel"></h4>
  </div>
  <div id="managementDataModalBody" class="modal-body">
  </div>
  <div id="managementDataModalFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Data Modal Dialog -->

<script type="text/javascript">
$('#reportrange').daterangepicker(
    {
      ranges: {
         'Today': [moment(), moment()],
         'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
         'Tomorrow': [moment().add('days', 1), moment().add('days', 1)],
         'Last 7 Days': [moment().subtract('days', 6), moment()],
         'Last 30 Days': [moment().subtract('days', 29), moment()],
         'Next 7 Days': [moment(), moment().add('days', 6)],
         'Next 30 Days': [moment(), moment().add('days', 29)],
         'This Month': [moment().startOf('month'), moment().endOf('month')],
         'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
         'Next Month': [moment().add('month', 1).startOf('month'), moment().add('month', 1).endOf('month')]
      },
      startDate: moment().subtract('days', 29),
      endDate: moment().add('days', 29)
    },
    function(start, end) {
        if (start && end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            management.fromDate = start;
            management.toDate = end;
            management.getCounts();
        }
    }
);
</script>        
