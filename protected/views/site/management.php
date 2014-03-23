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

<h2 class="sectionHeader">Management</h2>

<div id="managementContainer" style="display: none;">
    <h3 id="requestsHeader" class="sectionSubHeader">Requests</h3>
    <div id="requestsWell" class="span12 well well-large">
        <p>I'm in a well!</p>
    </div>
    <h3 id="reservationsHeader" class="sectionSubHeader">Reservations</h3>
    <div id="reservationsWell" class="span12 well well-large">
        <p>I'm in a well!</p>
    </div>
    <h3 id="eventsHeader" class="sectionSubHeader">Events</h3>
    <div id="eventsWell" class="span12 well well-large">
        <p>I'm in a well!</p>
    </div>
    <h3 id="arenasHeader" class="sectionSubHeader">Arenas</h3>
    <div id="arenasWell" class="span12 well well-large">
        <p>I'm in a well!</p>
    </div>
</div>

<div id="loadingScreen" class="row-fluid">
</div><!-- Loading Screen -->

<!-- Error Modal Dialog -->
<div id="managementModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="managementModalLabel" aria-hidden="true">
  <div id="managementModalHeader" class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="managementModalLabel"></h3>
  </div>
  <div id="managementModalBody" class="modal-body">
  </div>
  <div id="managementModalFooter" class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" type="button" aria-hidden="true">
        <i class="icon-remove-sign"></i> Close
    </button>
  </div>
</div><!-- Error Modal Dialog -->

