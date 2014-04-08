<?php

class ManagementController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column1';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'ajaxOnly + getCounts', // we only allow ajax calls!
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow', // allow authenticated users only!!!
                'actions' => array(
                    'getCounts',
                    'getOperations',
                    'index',
                    'view',
                ),
                'users' => array(
                    '@'
                ),
            ),
            array(
                'deny',  // deny all users
                'users' => array(
                    '*'
                ),
            ),
        );
    }

    /**
     * Action method to get the counts.
     */
    public function actionGetCounts()
    {
        Yii::trace("In actionGetCounts.", "application.controllers.ManagementController");
        
        if(!Yii::app()->user->isRestrictedArenaManager()) {
            $this->sendResponseHeaders(403, 'json');
            echo json_encode(array(
                    'success' => false,
                    'error' => 'Permission denied. You are not authorized to perform this action.'
                )
            );
            Yii::app()->end();
        }
        
        if((!isset($_GET['model']) || !is_array($_GET['model'])) ||
                (!isset($_GET['from']) || !strtotime($_GET['from'])) || 
                (!isset($_GET['to']) || !strtotime($_GET['to'])) ||
                (strtotime($_GET['from']) > strtotime($_GET['to']))) {
            $this->sendResponseHeaders(400, 'json');
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Invalid parameters',
                    )
            );
            Yii::app()->end();
        }        

        // Parameters are valid so save them off!
        $model = $_GET['model'];
        $from = $_GET['from'];
        $to = $_GET['to'];
        
        $user = Yii::app()->user->model;
        $dashData = null;
        
        try {
            $dashData = $user->getManagementDashboardCounts($model, $from, $to);
        } catch(Exception $ex) {
            $errorInfo = null;
            
            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorInfo = array(
                    "sqlState" => $ex->errorInfo[0],
                    "mysqlError" => $ex->errorInfo[1],
                    "message" => $ex->errorInfo[2],
                );
            }
            
            $this->sendResponseHeaders(500, 'json');

            echo json_encode(
                    array(
                        'success' => false,
                        'error' => $ex->getMessage(),
                        'exception' => true,
                        'errorCode' => $ex->getCode(),
                        'errorFile' => $ex->getFile(),
                        'errorLine' => $ex->getLine(),
                        'errorInfo' => $errorInfo,
                    )
            );
            
            Yii::app()->end();
        }
        
        // Data has been retrieved
        $this->sendResponseHeaders(200, 'json');

        echo json_encode(
                array(
                    'success' => true,
                    'error' => false,
                    'model' => $dashData,
                    'from' => $from,
                    'to' => $to,
                )
        );
        
        Yii::app()->end();
    }
    
    /**
     * Action method to view a model.
     */
    public function actionView()
    {
        Yii::trace("In actionView.", "application.controllers.ManagementController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        if(!Yii::app()->user->isRestrictedArenaManager()) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(403);
            }
            
            $this->sendResponseHeaders(403, 'json');
            echo json_encode(array(
                    'success' => false,
                    'error' => 'Permission denied. You are not authorized to perform this action.'
                )
            );
            Yii::app()->end();
        }
        
        // There is at least two GET required parameter: model & id
        // Depending on the model, there may be more required GET parameters
        // Those will be verified by each model's handler
        if(!isset($_GET['model']) || !is_string($_GET['model']) ||
                !isset($_GET['id']) || !is_numeric($_GET['id']) ||
                (integer)$_GET['id'] <= 0) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(400, 'Invalid parameters');
            }
            
            $this->sendResponseHeaders(400, 'json');
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Invalid parameters',
                    )
            );
            Yii::app()->end();
        }
        
        $modelName = $_GET['model'];
        $id = $_GET['id'];
        
        switch(strtolower(trim($modelName))) {
            case 'arena':
                $this->handleArenaView($id, $outputFormat);
                break;
            case 'event':
                $this->handleEventView($id, $outputFormat);
                break;
            case 'eventrequest':
                $this->handleEventRequestView($id, $outputFormat);
                break;
            case 'reservation':
                $this->handleReservationView($id, $outputFormat);
                break;
            case 'contact':
                $this->handleContactView($id, $outputFormat);
                break;
            case 'location':
                $this->handleLocationView($id, $outputFormat);
                break;
            case 'manager':
                $this->handleManagerView($id, $outputFormat);
                break;
            case 'arenareservationpolicy':
                $this->handleArenaReservationPolicyView($id, $outputFormat);
                break;
            case 'recurrence':
                $this->handleRecurrenceView($id, $outputFormat);
                break;
            default:
                if($outputFormat == "html" || $outputFormat == "xml") {
                    throw new CHttpException(400, 'Unknown model');
                }
            
                $this->sendResponseHeaders(400);
                echo json_encode(
                        array(
                            'success' => false,
                            'error' => 'Unknown model',
                        )
                );
                Yii::app()->end();
        }
    }
    
    protected function handleEventRequestView($id, $outputFormat)
    {
        // We need to ensure that both an Arena ID and Event ID are passed in
        // We need this to ensure that the model isn't being edited/viewed out
        // of context!
        
        $aid = null;
        $eid = null;
        $lid = null;
        
        if(isset($_GET['aid']) && is_numeric($_GET['aid']) && $_GET['aid'] > 0) {
            $aid = $_GET['aid'];
        }
        
        if(isset($_GET['eid']) && is_numeric($_GET['eid'])) {
            $eid = $_GET['eid'];
        }
        
        if(isset($_GET['lid']) && is_numeric($_GET['lid'])) {
            $lid = $_GET['lid'];
        }
        
        // Validate we have an Arena ID and Event ID
        if($eid === null || $aid === null) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(400, 'Invalid parameters');
            }
            
            $this->sendResponseHeaders(400, 'json');
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Invalid parameters',
                    )
            );
            Yii::app()->end();
        }
        
        // Always restrict to the currently logged in user!
        $uid = Yii::app()->user->id;
        
        $data = null;
        
        // During the process of retrieving the event request, we validate
        // that the user is authorized to view / update this event request!
        // We do this by only returning a valid record if the user is assigned
        // to the arena that the event and event request are created from

        // Try and get the data!
        try {
            $data = EventRequest::getAssignedRecord($id, $uid, $eid, $aid, $lid);
        } catch (Exception $ex) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(500);
            }
            
            $errorInfo = null;
            
            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorInfo = array(
                    "sqlState" => $ex->errorInfo[0],
                    "mysqlError" => $ex->errorInfo[1],
                    "message" => $ex->errorInfo[2],
                );
            }
            
            $this->sendResponseHeaders(500, 'json');

            echo json_encode(
                    array(
                        'success' => false,
                        'error' => $ex->getMessage(),
                        'exception' => true,
                        'errorCode' => $ex->getCode(),
                        'errorFile' => $ex->getFile(),
                        'errorLine' => $ex->getLine(),
                        'errorInfo' => $errorInfo,
                    )
            );
            
            Yii::app()->end();
        }
        
        // Data has been retrieved!
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => $data,
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array($data, "details", "eventrequest");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_eventRequest",
                        array(
                            'model' => new EventRequest(),
                            'data' => $data,
                            'ownView' => true,
                            'newRecord' => false
                        ));
            } else {
                // Publish and register our jQuery plugin
                $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));

                if(defined('YII_DEBUG')) {
                    Yii::app()->clientScript->registerScriptFile($path . '/js/moment.js', CClientScript::POS_BEGIN);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/moment-recur.js', CClientScript::POS_BEGIN);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/daterangepicker.js', CClientScript::POS_BEGIN);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modalmanager.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modal.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-datetimepicker.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/bootstrap-editable/js/bootstrap-editable.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/footable.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/footable.filter.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/footable.sort.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/footable.paginate.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/jquery.inputmask.bundle.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/utilities.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/site/management.js', CClientScript::POS_END);
                } else {
                    Yii::app()->clientScript->registerScriptFile($path . '/js/moment.min.js', CClientScript::POS_BEGIN);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/moment-recur.min.js', CClientScript::POS_BEGIN);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/daterangepicker.min.js', CClientScript::POS_BEGIN);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modalmanager.min.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modal.min.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-datetimepicker.min.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/bootstrap-editable/js/bootstrap-editable.min.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/footable.min.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/footable.filter.min.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/footable.sort.min.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/footable.min.paginate.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/jquery.inputmask.bundle.min.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/utilities.min.js', CClientScript::POS_END);
                    Yii::app()->clientScript->registerScriptFile($path . '/js/site/management.min.js', CClientScript::POS_END);
                }
        
                $this->navigation = true;
                $this->includeCss = true;
                $this->render(
                        "_eventRequest",
                        array(
                            'model' => new EventRequest(),
                            'data' => $data,
                            'ownView' => true,
                            'newRecord' => false
                        ));
            }
        }
    }
    
    /**
     * Action method to list available records.
     */
    public function actionIndex()
    {
        Yii::trace("In actionIndex.", "application.controllers.ManagementController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        if(!Yii::app()->user->isRestrictedArenaManager()) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(403);
            }
            
            $this->sendResponseHeaders(403, 'json');
            echo json_encode(array(
                    'success' => false,
                    'error' => 'Permission denied. You are not authorized to perform this action.'
                )
            );
            Yii::app()->end();
        }
        
        // There is at least one GET required parameter: model
        // Depending on the model, there may be more required GET parameters
        // Those will be verified by each model's handler
        if(!isset($_GET['model']) || !is_string($_GET['model'])) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(400, 'Invalid parameters');
            }
            
            $this->sendResponseHeaders(400, 'json');
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Invalid parameters',
                    )
            );
            Yii::app()->end();
        }
        
        $modelName = $_GET['model'];
        
        switch(strtolower(trim($modelName))) {
            case 'arena':
                $this->handleArenaIndex($outputFormat);
                break;
            case 'event':
                $this->handleEventIndex($outputFormat);
                break;
            case 'eventrequest':
                $this->handleEventRequestIndex($outputFormat);
                break;
            case 'reservation':
                $this->handleReservationIndex($outputFormat);
                break;
            case 'contact':
                $this->handleContactIndex($outputFormat);
                break;
            case 'location':
                $this->handleLocationIndex($outputFormat);
                break;
            default:
                if($outputFormat == "html" || $outputFormat == "xml") {
                    throw new CHttpException(400, 'Unknown model');
                }
            
                $this->sendResponseHeaders(400);
                echo json_encode(
                        array(
                            'success' => false,
                            'error' => 'Unknown model',
                        )
                );
                Yii::app()->end();
        }
    }
    
    protected function handleArenaIndex($outputFormat)
    {
        // First check to see if we are restricting by anything
        $sid = null;
        
        if(isset($_GET['sid']) && is_numeric($_GET['sid'])) {
            $sid = $_GET['sid'];
        }
        
        // Always restrict to the currently logged in user!
        $uid = Yii::app()->user->id;
        $data = null;
        
        // Try and get the data!
        try {
            $data = Arena::getAssignedSummary($uid, $sid);
        } catch (Exception $ex) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(500);
            }
            
            $errorInfo = null;
            
            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorInfo = array(
                    "sqlState" => $ex->errorInfo[0],
                    "mysqlError" => $ex->errorInfo[1],
                    "message" => $ex->errorInfo[2],
                );
            }
            
            $this->sendResponseHeaders(500, 'json');

            echo json_encode(
                    array(
                        'success' => false,
                        'error' => $ex->getMessage(),
                        'exception' => true,
                        'errorCode' => $ex->getCode(),
                        'errorFile' => $ex->getFile(),
                        'errorLine' => $ex->getLine(),
                        'errorInfo' => $errorInfo,
                    )
            );
            
            Yii::app()->end();
        }
        
        // Data has been retrieved!
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => $data,
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array($data, "summary", "arena");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            $this->renderPartial(
                    "_index",
                    array(
                        'data' => $data,
                        'headers' => Arena::getSummaryAttributes()
                    ));
        }
    }
    
    protected function handleContactIndex($outputFormat)
    {
        // First check to see if we are restricting by anything
        $sid = null;
        $aid = null;
        
        if(isset($_GET['aid']) && is_numeric($_GET['aid'])) {
            $aid = $_GET['aid'];
        }
        
        if(isset($_GET['sid']) && is_numeric($_GET['sid'])) {
            $sid = $_GET['sid'];
        }
        
        // Always restrict to the currently logged in user!
        $uid = Yii::app()->user->id;
        $data = null;
        
        // Try and get the data!
        try {
            $data = Contact::getAssignedSummary($uid, $aid, $sid);
        } catch (Exception $ex) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(500);
            }
            
            $errorInfo = null;
            
            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorInfo = array(
                    "sqlState" => $ex->errorInfo[0],
                    "mysqlError" => $ex->errorInfo[1],
                    "message" => $ex->errorInfo[2],
                );
            }
            
            $this->sendResponseHeaders(500, 'json');

            echo json_encode(
                    array(
                        'success' => false,
                        'error' => $ex->getMessage(),
                        'exception' => true,
                        'errorCode' => $ex->getCode(),
                        'errorFile' => $ex->getFile(),
                        'errorLine' => $ex->getLine(),
                        'errorInfo' => $errorInfo,
                    )
            );
            
            Yii::app()->end();
        }
        
        // Data has been retrieved!
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => $data,
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array($data, "summary", "contact");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            $this->renderPartial(
                    "_index",
                    array(
                        'data' => $data,
                        'headers' => Contact::getSummaryAttributes()
                    ));
        }
    }
    
    protected function handleEventIndex($outputFormat)
    {
        // First check to see if we are restricting by anything
        $aid = null;
        $from = null;
        $to = null;
        $tid = null;
        $sid = null;
        
        if(isset($_GET['aid']) && is_numeric($_GET['aid'])) {
            $aid = $_GET['aid'];
        }
        
        if(isset($_GET['from']) && strtotime($_GET['from'])) {
            $from = $_GET['from'];
        }
        
        if(isset($_GET['to']) && strtotime($_GET['to'])) {
            $to = $_GET['to'];
        }
        
        if(isset($_GET['tid']) && is_numeric($_GET['tid'])) {
            $tid = $_GET['tid'];
        }
        
        if(isset($_GET['sid']) && is_numeric($_GET['sid'])) {
            $sid = $_GET['sid'];
        }
        
        // Always restrict to the currently logged in user!
        $uid = Yii::app()->user->id;
        $data = null;
        
        // Try and get the data!
        try {
            $data = Event::getAssignedSummary($uid, $aid, $from, $to, $tid, $sid);
        } catch (Exception $ex) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(500);
            }
            
            $errorInfo = null;
            
            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorInfo = array(
                    "sqlState" => $ex->errorInfo[0],
                    "mysqlError" => $ex->errorInfo[1],
                    "message" => $ex->errorInfo[2],
                );
            }
            
            $this->sendResponseHeaders(500, 'json');

            echo json_encode(
                    array(
                        'success' => false,
                        'error' => $ex->getMessage(),
                        'exception' => true,
                        'errorCode' => $ex->getCode(),
                        'errorFile' => $ex->getFile(),
                        'errorLine' => $ex->getLine(),
                        'errorInfo' => $errorInfo,
                    )
            );
            
            Yii::app()->end();
        }
        
        // Data has been retrieved!
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => $data,
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array($data, "summary", "event");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            $this->renderPartial(
                    "_index",
                    array(
                        'data' => $data,
                        'headers' => Event::getSummaryAttributes()
                    ));
        }
    }
    
    protected function handleEventRequestIndex($outputFormat)
    {
        // First check to see if we are restricting by anything
        $aid = null;
        $from = null;
        $to = null;
        $tid = null;
        $sid = null;
        
        if(isset($_GET['aid']) && is_numeric($_GET['aid'])) {
            $aid = $_GET['aid'];
        }
        
        if(isset($_GET['from']) && strtotime($_GET['from']) !== false) {
            $from = $_GET['from'];
        }
        
        if(isset($_GET['to']) && strtotime($_GET['to']) !== false) {
            $to = $_GET['to'];
        }
        
        if(isset($_GET['tid']) && is_numeric($_GET['tid'])) {
            $tid = $_GET['tid'];
        }
        
        if(isset($_GET['sid']) && is_numeric($_GET['sid'])) {
            $sid = $_GET['sid'];
        }
        
        // Always restrict to the currently logged in user!
        $uid = Yii::app()->user->id;
        $data = null;
        
        // Try and get the data!
        try {
            $data = EventRequest::getAssignedSummary($uid, $aid, $from, $to, $tid, $sid);
        } catch (Exception $ex) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(500);
            }
            
            $errorInfo = null;
            
            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorInfo = array(
                    "sqlState" => $ex->errorInfo[0],
                    "mysqlError" => $ex->errorInfo[1],
                    "message" => $ex->errorInfo[2],
                );
            }
            
            $this->sendResponseHeaders(500, 'json');

            echo json_encode(
                    array(
                        'success' => false,
                        'error' => $ex->getMessage(),
                        'exception' => true,
                        'errorCode' => $ex->getCode(),
                        'errorFile' => $ex->getFile(),
                        'errorLine' => $ex->getLine(),
                        'errorInfo' => $errorInfo,
                    )
            );
            
            Yii::app()->end();
        }
        
        // Data has been retrieved!
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => $data,
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array($data, "summary", "eventrequest");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            $this->renderPartial(
                    "_index",
                    array(
                        'data' => $data,
                        'headers' => EventRequest::getSummaryAttributes()
                    ));
        }
    }
    
    protected function handleReservationIndex($outputFormat)
    {
        // First check to see if we are restricting by anything
        $aid = null;
        $from = null;
        $to = null;
        $sid = null;
        
        if(isset($_GET['aid']) && is_numeric($_GET['aid'])) {
            $aid = $_GET['aid'];
        }
        
        if(isset($_GET['from']) && strtotime($_GET['from'])) {
            $from = $_GET['from'];
        }
        
        if(isset($_GET['to']) && strtotime($_GET['to'])) {
            $to = $_GET['to'];
        }
        
        if(isset($_GET['sid']) && is_numeric($_GET['sid'])) {
            $sid = $_GET['sid'];
        }
        
        // Always restrict to the currently logged in user!
        $uid = Yii::app()->user->id;
        $data = null;
        
        // Try and get the data!
        try {
            $data = Reservation::getAssignedSummary($uid, $aid, $from, $to, $sid);
        } catch (Exception $ex) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(500);
            }
            
            $errorInfo = null;
            
            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorInfo = array(
                    "sqlState" => $ex->errorInfo[0],
                    "mysqlError" => $ex->errorInfo[1],
                    "message" => $ex->errorInfo[2],
                );
            }
            
            $this->sendResponseHeaders(500, 'json');

            echo json_encode(
                    array(
                        'success' => false,
                        'error' => $ex->getMessage(),
                        'exception' => true,
                        'errorCode' => $ex->getCode(),
                        'errorFile' => $ex->getFile(),
                        'errorLine' => $ex->getLine(),
                        'errorInfo' => $errorInfo,
                    )
            );
            
            Yii::app()->end();
        }
        
        // Data has been retrieved!
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => $data,
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array($data, "summary", "reservation");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            $this->renderPartial(
                    "_index",
                    array(
                        'data' => $data,
                        'headers' => Reservation::getSummaryAttributes()
                    ));
        }
    }
    
    protected function handleLocationIndex($outputFormat)
    {
        // First check to see if we are restricting by anything
        $aid = null;
        $tid = null;
        $sid = null;
        
        if(isset($_GET['aid']) && is_numeric($_GET['aid'])) {
            $aid = $_GET['aid'];
        }
        
        if(isset($_GET['tid']) && is_numeric($_GET['tid'])) {
            $tid = $_GET['tid'];
        }
        
        if(isset($_GET['sid']) && is_numeric($_GET['sid'])) {
            $sid = $_GET['sid'];
        }
        
        // Always restrict to the currently logged in user!
        $uid = Yii::app()->user->id;
        $data = null;
        
        // Try and get the data!
        try {
            $data = Location::getAssignedSummary($uid, $aid, $tid, $sid);
        } catch (Exception $ex) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(500);
            }
            
            $errorInfo = null;
            
            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorInfo = array(
                    "sqlState" => $ex->errorInfo[0],
                    "mysqlError" => $ex->errorInfo[1],
                    "message" => $ex->errorInfo[2],
                );
            }
            
            $this->sendResponseHeaders(500, 'json');

            echo json_encode(
                    array(
                        'success' => false,
                        'error' => $ex->getMessage(),
                        'exception' => true,
                        'errorCode' => $ex->getCode(),
                        'errorFile' => $ex->getFile(),
                        'errorLine' => $ex->getLine(),
                        'errorInfo' => $errorInfo,
                    )
            );
            
            Yii::app()->end();
        }
        
        // Data has been retrieved!
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => $data,
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array($data, "summary", "location");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            $this->renderPartial(
                    "_index",
                    array(
                        'data' => $data,
                        'headers' => Location::getSummaryAttributes()
                    ));
        }
    }
    
}