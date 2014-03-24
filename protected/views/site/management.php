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
            'management.urls.login = "' . $this->createUrl('site/login') . '";'
            . 'management.urls.base = "' . Yii::app()->request->baseUrl . '";'
            . 'management.endpoints.counts = "' . $endpoints['counts'] . '";'
            . 'management.endpoints.details = "' . $endpoints['details'] . '";'
            . 'management.dialogBox = "managementModal";'
            . 'management.mainContainer = "managementContainer";'
            . 'management.getInitialCounts();',
            CClientScript::POS_READY
    );
?>

<h2 class="sectionHeader">Management Dashboard</h2>

<div id="managementContainer" style="display: none;">
    <div id="countsContainer" class="row-fluid">
        <h3 id="countsHeader" class="sectionSubHeader">
            Counts
        </h3>
        <div id="requestsContainer" class="span3">
            <h5 id="requestsHeader">Requests</h5>
            <div id="requestsWell" class="well well-large">
            </div>
        </div>
        <div id="reservationsContainer" class="span3">
            <h5 id="reservationsHeader">Reservations</h5>
            <div id="reservationsWell" class="well well-large">
            </div>
        </div>
        <div id="eventsContainer" class="span3">
            <h5 id="eventsHeader">Events</h5>
            <div id="eventsWell" class="well well-large">
            </div>
        </div>
        <div id="arenasContainer" class="span2">
            <h5 id="arenasHeader">Arenas</h5>
            <div id="arenasWell" class="well well-large">
            </div>
        </div>
    </div>
</div>

<div id="loadingScreen" class="row-fluid">
</div><!-- Loading Screen -->

<!-- Error Modal Dialog -->
<div id="managementModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="managementModalLabel" aria-hidden="true">
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

