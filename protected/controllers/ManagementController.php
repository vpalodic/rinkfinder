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
            'ajaxOnly', // we only allow ajax calls!
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
                    'getDetails',
                    'index',
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
            $this->sendResponseHeaders(403);
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
            $this->sendResponseHeaders(400);
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
            
            $this->sendResponseHeaders(500);

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
        $this->sendResponseHeaders(200);

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
     * Action method to upload a file.
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
            
            $this->sendResponseHeaders(403);
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
                throw new CHttpException(400);
            }
            
            $this->sendResponseHeaders(400);
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
            default:
                if($outputFormat == "html" || $outputFormat == "xml") {
                    throw new CHttpException(400);
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
    
    protected function handleArenaIndex($outputFormat) {
        // First check to see if we are restricting by a 
        // status code
        $sid = null;
        
        if(isset($_GET['sid']) && is_numeric($_GET['sid'])) {
            $sid = $_GET['sid'];
        }
        
        // Always restrict to the currently logged in user!
        $uid = Yii::app()->user->id;
        $data = null;
        
        // Try and get the data!
        try {
            $data = Arena::getAssignedArenasSummary($uid, $sid);
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
            
            $this->sendResponseHeaders(500);

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
            $this->sendResponseHeaders(200);

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => $data,
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200);
            
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
    
    protected function handleEventIndex($outputFormat) {
        // First check to see if we are restricting by a 
        // status code
        $sid = null;
        
        if(isset($_GET['sid']) && is_numeric($_GET['sid'])) {
            $sid = $_GET['sid'];
        }
        
        // Always restrict to the currently logged in user!
        $uid = Yii::app()->user->id;
        $data = null;
        
        // Try and get the data!
        try {
            $data = Arena::getAssignedArenasSummary($uid, $sid);
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
            
            $this->sendResponseHeaders(500);

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
            $this->sendResponseHeaders(200);

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => $data,
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200);
            
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
}