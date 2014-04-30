<?php

class EventRequestController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='//layouts/column1';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'ajaxOnly + type status deleteEventRequest',
            'postOnly + delete purchase info deleteEventRequest',
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
                'allow',
                'actions' => array('index', 'view', 'type', 'status', 'purchase', 'info'),
                'users' => array('*'),
            ),
            array(
                'allow',
                'actions' => array('create', 'update', 'deleteEventRequest'),
                'users' => array('@'),
            ),
            array('allow',
                'actions' => array('admin', 'delete'),
                'users' => array('@'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view',array(
            'model'=>$this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new EventRequest;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['EventRequest'])) {
            $model->attributes=$_POST['EventRequest'];
            if ($model->save()) {
                $this->redirect(array('view','id'=>$model->id));
            }
        }

        $this->render('create',array(
            'model'=>$model,
        ));
    }

    /**
     * Creates a "Purchase" request and sends e-mails to arena managers and
     * the requester
     */
    public function actionPurchase()
    {
        Yii::trace("In actionPurchase.", "application.controllers.EventRequestController");
        
        // Default to JSON output!
        $outputFormat = "json";
        $data = array();
        
        if(isset($_REQUEST['output']) && ($_REQUEST['output'] == 'xml' || $_REQUEST['output'] == 'json')) {
            $outputFormat = $_REQUEST['output'];
        }
        
        $requester_id = Yii::app()->user->isGuest ? 3 : Yii::app()->user->id;
        $requester_name = isset($_POST['requester_name']) ? $_POST['requester_name'] : null;
        $requester_email = isset($_POST['requester_email']) ? $_POST['requester_email'] : null;
        $requester_phone = isset($_POST['requester_phone']) ? $_POST['requester_phone'] : null;
        $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
        $aid = isset($_REQUEST['aid']) ? $_REQUEST['aid'] : null;
        $eid = isset($_REQUEST['eid']) ? $_REQUEST['eid'] : null;
        $type_id = EventRequestType::model()->find('name = "PURCHASE"')->id;
        
        if(is_null($aid) || is_null($type_id) || is_null($eid) || 
                is_null($requester_phone) || is_null($requester_email) || 
                is_null($requester_name) || is_null($requester_id)) {
            if($outputFormat == "xml" || $outputFormat == "html") {
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
        
        // Try and save the data!
        try {
            $model = new EventRequest();
            
            $model->event_id = $eid;
            $model->requester_id = $requester_id;
            $model->created_by_id = $requester_id;
            $model->updated_by_id = $requester_id;
            $model->requester_name = $requester_name;
            $model->requester_email = $requester_email;
            $model->requester_phone = $requester_phone;
            $model->notes = ($notes == '' ? null : $notes . "\r\n\r\n");
            $model->type_id = $type_id;
            
            // validate and save the model!!
            $saved = $model->save();
            
            if(!$saved) {
                throw new CHttpException(500, "Failed to save the request");
            }
            
            $emailsSent = EventRequest::sendNewEmailNotifications($requester_name, $requester_email, $requester_phone, $notes, "Reserve / Purchase", $model->id, $eid, $aid);
            
            // log any e-mail failures!
            if($emailsSent !== true) {
                Yii::log($emailsSent, CLogger::LEVEL_ERROR, 'application.models.EventRequest');
            }
        } catch (Exception $ex) {
            if($ex instanceof CHttpException) {
                if($outputFormat == "html" || $outputFormat == "xml") {
                    throw $ex;
                }
            }
            
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(500);
            }
            
            $errorInfo = null;
            
            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorParms = array();

                if(isset($ex->errorInfo[0])) {
                    $errorParms['sqlState'] = $ex->errorInfo[0];
                } else {
                    $errorParms['sqlState'] = "Unknown";
                }

                if(isset($ex->errorInfo[1])) {
                    $errorParms['mysqlError'] = $ex->errorInfo[1];
                } else {
                    $errorParms['mysqlError'] = "Unknown";
                }

                if(isset($ex->errorInfo[2])) {
                    $errorParms['message'] = $ex->errorInfo[2];
                } else {
                    $errorParms['message'] = "Unknown";
                }

                $errorInfo = array($errorParms);
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
        
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(array('success' => true));
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array(array('success' => true), "response", "node");
            echo $xml;
            
            Yii::app()->end();
        } else {
        }
    }

    /**
     * Creates a "Purchase" request and sends e-mails to arena managers and
     * the requester
     */
    public function actionInfo()
    {
        Yii::trace("In actionInfo.", "application.controllers.EventRequestController");
        
        // Default to JSON output!
        $outputFormat = "json";
        $data = array();
        
        if(isset($_REQUEST['output']) && ($_REQUEST['output'] == 'xml' || $_REQUEST['output'] == 'json')) {
            $outputFormat = $_REQUEST['output'];
        }
        
        $requester_id = Yii::app()->user->isGuest ? 3 : Yii::app()->user->id;
        $requester_name = isset($_POST['requester_name']) ? $_POST['requester_name'] : null;
        $requester_email = isset($_POST['requester_email']) ? $_POST['requester_email'] : null;
        $requester_phone = isset($_POST['requester_phone']) ? $_POST['requester_phone'] : null;
        $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
        $aid = isset($_REQUEST['aid']) ? $_REQUEST['aid'] : null;
        $eid = isset($_REQUEST['eid']) ? $_REQUEST['eid'] : null;
        $type_id = EventRequestType::model()->find('name = "INFORMATION"')->id;
        
        if(is_null($aid) || is_null($type_id) || is_null($eid) || 
                is_null($requester_phone) || is_null($requester_email) || 
                is_null($requester_name) || is_null($requester_id)) {
            if($outputFormat == "xml" || $outputFormat == "html") {
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
        
        // Try and save the data!
        try {
            $model = new EventRequest();
            
            $model->event_id = $eid;
            $model->requester_id = $requester_id;
            $model->created_by_id = $requester_id;
            $model->updated_by_id = $requester_id;
            $model->requester_name = $requester_name;
            $model->requester_email = $requester_email;
            $model->requester_phone = $requester_phone;
            $model->notes = ($notes == '' ? null : $notes . "\r\n\r\n");
            $model->type_id = $type_id;
            
            // validate and save the model!!
            $saved = $model->save();
            
            if(!$saved) {
                throw new CHttpException(500, "Failed to save the request");
            }
            
            $emailsSent = EventRequest::sendNewEmailNotifications($requester_name, $requester_email, $requester_phone, $notes, "Information", $model->id, $eid, $aid);
            
            // log any e-mail failures!
            if($emailsSent !== true) {
                Yii::log($emailsSent, CLogger::LEVEL_ERROR, 'application.models.EventRequest');
            }
        } catch (Exception $ex) {
            if($ex instanceof CHttpException) {
                if($outputFormat == "html" || $outputFormat == "xml") {
                    throw $ex;
                }
            }
            
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(500);
            }
            
            $errorInfo = null;
            
            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorParms = array();

                if(isset($ex->errorInfo[0])) {
                    $errorParms['sqlState'] = $ex->errorInfo[0];
                } else {
                    $errorParms['sqlState'] = "Unknown";
                }

                if(isset($ex->errorInfo[1])) {
                    $errorParms['mysqlError'] = $ex->errorInfo[1];
                } else {
                    $errorParms['mysqlError'] = "Unknown";
                }

                if(isset($ex->errorInfo[2])) {
                    $errorParms['message'] = $ex->errorInfo[2];
                } else {
                    $errorParms['message'] = "Unknown";
                }

                $errorInfo = array($errorParms);
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
        
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(array('success' => true));
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array(array('success' => true), "response", "node");
            echo $xml;
            
            Yii::app()->end();
        } else {
        }
    }

    /**
     * Gets a list of available types for the event request
     */
    public function actionType()
    {
        Yii::trace("In actionType.", "application.controllers.EventRequestController");
        
        // Default to XML output!
        $outputFormat = "json";
        $data = array();
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        // Try and get the data!
        try {
            $dataTemp = EventRequest::getTypes(true);
            
            foreach($dataTemp as $record) {
                $data[] = array(
                    'value' => $record['id'],
                    'text' => $record['display_name']
                );
            }
            
        } catch (Exception $ex) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(500);
            }
            
            $errorInfo = null;
            
            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorParms = array();

                if(isset($ex->errorInfo[0])) {
                    $errorParms['sqlState'] = $ex->errorInfo[0];
                } else {
                    $errorParms['sqlState'] = "Unknown";
                }

                if(isset($ex->errorInfo[1])) {
                    $errorParms['mysqlError'] = $ex->errorInfo[1];
                } else {
                    $errorParms['mysqlError'] = "Unknown";
                }

                if(isset($ex->errorInfo[2])) {
                    $errorParms['message'] = $ex->errorInfo[2];
                } else {
                    $errorParms['message'] = "Unknown";
                }

                $errorInfo = array($errorParms);
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

            echo json_encode($data);
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array($data, "eventRequestTypes", "eventRequestType");
            echo $xml;
            
            Yii::app()->end();
        } else {
        }
    }

    /**
     * Gets a list of available status values for the event request
     */
    public function actionStatus()
    {
        Yii::trace("In actionStatus.", "application.controllers.EventRequestController");
        
        // Default to XML output!
        $outputFormat = "json";
        $data = array();
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        // Try and get the data!
        try {
            $dataTemp = EventRequest::getStatuses(true);
            
            foreach($dataTemp as $record) {
                $data[] = array(
                    'value' => $record['id'],
                    'text' => $record['display_name']
                );
            }
            
        } catch (Exception $ex) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(500);
            }
            
            $errorInfo = null;
            
            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorParms = array();

                if(isset($ex->errorInfo[0])) {
                    $errorParms['sqlState'] = $ex->errorInfo[0];
                } else {
                    $errorParms['sqlState'] = "Unknown";
                }

                if(isset($ex->errorInfo[1])) {
                    $errorParms['mysqlError'] = $ex->errorInfo[1];
                } else {
                    $errorParms['mysqlError'] = "Unknown";
                }

                if(isset($ex->errorInfo[2])) {
                    $errorParms['message'] = $ex->errorInfo[2];
                } else {
                    $errorParms['message'] = "Unknown";
                }

                $errorInfo = array($errorParms);
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

            echo json_encode($data);
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array($data, "eventRequestStatuses", "eventRequestStatus");
            echo $xml;
            
            Yii::app()->end();
        } else {
        }
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id = null)
    {
        Yii::trace("In actionUpdate.", "application.controllers.EventRequestController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        } elseif(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
            $outputFormat = $_POST['output'];
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
        
        // We need to grab and validate our parameters from the request body
        // The parameters will be different depending if this is an Ajax
        // request or not

        // If we are updating via ajax, then we do something a little bit
        // differently as we will update one attribute at a time!
        if(Yii::app()->request->isAjaxRequest && Yii::app()->request->isPostRequest) {
            // Grab all of the parameters!
            $action = isset($_POST['action']) ? $_POST['action'] : null;
            $requesterName = isset($_POST['requester_name']) ? $_POST['requester_name'] : null;
            $requesterEmail = isset($_POST['requester_email']) ? $_POST['requester_email'] : null;
            $acknowledged = isset($_POST['acknowledged']) ? $_POST['acknowledged'] : null;
            $acknowledged = ($acknowledged == "false") ? false : true;
            $accepted = isset($_POST['accepted']) ? $_POST['accepted'] : null;
            $accepted = ($accepted == "false") ? false : true;
            $rejected = isset($_POST['rejected']) ? $_POST['rejected'] : null;
            $rejected = ($rejected == "false") ? false : true;
            $rejectedReason = isset($_POST['rejected_reason']) ? $_POST['rejected_reason'] : null;
            $message = isset($_POST['message']) ? $_POST['message'] : null;
            $name = isset($_POST['name']) ? $_POST['name'] : null;
            $value = isset($_POST['value']) ? $_POST['value'] : null;
            $pk = isset($_POST['pk']) ? $_POST['pk'] : null;
            $id = isset($_POST['id']) ? $_POST['id'] : null;
            $eid = isset($_POST['eid']) ? $_POST['eid'] : null;
            $aid = isset($_POST['aid']) ? $_POST['aid'] : null;
            $lid = isset($_POST['lid']) ? $_POST['lid'] : null;
            // Always restrict to the currently logged in user!
            $uid = Yii::app()->user->id;
            
            if($message && $action == 'message') {
                $value = $message;
            } elseif($rejectedReason && $action == 'reject') {
                $value = $rejectedReason;
            }
            
            if((($name === null || $value === null) && $action === null) || $id === null ||
                    $eid === null || $aid === null || $pk === null || $pk != $id) {
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
            
            // Check for an action!!!
            if(isset($action) && !empty($action)) {
                if($action == 'reject' || $action == 'message') {
                    $rejectedReason = $value;
                    // ensure that we have a reason!
                    if(!isset($rejectedReason)) {
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
                }
                
                if(!isset($requesterEmail) || !isset($requesterName)) {
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
                
                return $this->handleAction(array(
                    'output' => $outputFormat,
                    'action' => $action,
                    'rejected_reason' => $rejectedReason,
                    'rejected' => $rejected,
                    'acknowledged' => $acknowledged,
                    'accepted' => $accepted,
                    'requester_name' => $requesterName,
                    'requester_email' => $requesterEmail,
                    'id' => $id,
                    'uid' => $uid,
                    'eid' => $eid,
                    'aid' => $aid,
                    'lid' => $lid
                ));
            }
            
            // Ok, we have what appear to be valid parameters and so
            // it is time to validate and then update the value!
            
            $model = $this->loadModel($id, $outputFormat);
            
            if($name == 'notes') {
                $orig = ($model->$name ? $model->$name : '');
                $value = $orig . $value;
                $model->$name = $value;
            } else {
                $model->$name = $value;
            }
            
            $valid = $model->validate(array($name));
            
            if(!$valid) {
                $errors = $model->getErrors($name);
                
                if($outputFormat == "html" || $outputFormat == "xml") {
                    $output = '';

                    foreach($errors as $error) {
                        if($output == '') {
                            $output = $error;
                        } else {
                            $output .= '<br>' . $error;
                        }
                    }
                    
                    throw new CHttpException(400, $output);
                }

                $this->sendResponseHeaders(400, 'json');
                
                echo json_encode(
                        array(
                            'success' => false,
                            'error' => json_encode($errors),
                        )
                );
                Yii::app()->end();
            } else {
                // The attribute is valid and so we should save it!!
                try {
                    // We don't blindly save it even though we validated that
                    // the user is a restricted manager. We could do another
                    // check to see if the user is assigned to the arena but,
                    // we are going to do that check during the update!
                    // So, we will know if the user is valid if our update query
                    // affects one row. If it affects zero rows, then the user
                    // wasn't authorized and we will throw a 403 error!
                    if(EventRequest::saveAssignedRecordAttributes(array($name => $value), $id, $uid, $eid, $aid, $lid) == false) {
                        $output = 'Failed to save record as the update was either unauthorized or because too many rows would be updated.';

                        if($outputFormat == "html" || $outputFormat == "xml") {
                            throw new CHttpException(400, $output);
                        }
                        
                        $this->sendResponseHeaders(400, 'json');
                        
                        echo json_encode(
                                array(
                                    'success' => false,
                                    'error' => json_encode($output),
                                )
                        );
                        Yii::app()->end();
                    }
                    
                } catch (Exception $ex) {
                    if($ex instanceof CHttpException) {
                        throw $ex;
                    }
                    
                    if($outputFormat == "html" || $outputFormat == "xml") {
                        throw new CHttpException(500, $ex->getMessage());
                    }
                    
                    $errorInfo = null;
                    
                    if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                        $errorParms = array();

                        if(isset($ex->errorInfo[0])) {
                            $errorParms['sqlState'] = $ex->errorInfo[0];
                        } else {
                            $errorParms['sqlState'] = "Unknown";
                        }

                        if(isset($ex->errorInfo[1])) {
                            $errorParms['mysqlError'] = $ex->errorInfo[1];
                        } else {
                            $errorParms['mysqlError'] = "Unknown";
                        }

                        if(isset($ex->errorInfo[2])) {
                            $errorParms['message'] = $ex->errorInfo[2];
                        } else {
                            $errorParms['message'] = "Unknown";
                        }

                        $errorInfo = array($errorParms);
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
            }
        }
    }

    /**
     * Deletes a particular model.
     * If delete is successful, there is no output, otherwise we output an error.
     */
    public function actionDeleteEventRequest()
    {
        Yii::trace("In actionDeleteEventRequest.", "application.controllers.EventRequestController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        } elseif(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
            $outputFormat = $_POST['output'];
        }
        
        // We only delete via a POST and AJAX request!
        $id = isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 ? (integer)$_POST['id'] : 0;
        $pk = isset($_POST['pk']) && is_numeric($_POST['pk']) && $_POST['pk'] > 0 ? (integer)$_POST['pk'] : 0;
        $aid = isset($_POST['aid']) && is_numeric($_POST['aid']) && $_POST['aid'] > 0 ? (integer)$_POST['aid'] : 0;
        $eid = isset($_POST['eid']) && is_numeric($_POST['eid']) && $_POST['eid'] > 0 ? (integer)$_POST['eid'] : 0;
        
        // Verify we have a valid ID!
        if($aid <= 0 || $eid <= 0 || $id <= 0 || $pk <= 0 || $id !== $pk) {
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
        
        
        // Always grab the currently logged in user's ID.
        $uid = Yii::app()->user->id;
        
        // Load the Arena model and ensure that the user is assigned to it!
        $arena = $this->loadArenaModel($aid, $outputFormat);
        
        // And that the user has permission to update it!
        if(!Yii::app()->user->isRestrictedArenaManager() || !$arena->isUserAssigned($uid)) {
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
        
        try {
            $model = $this->loadModel($id, $outputFormat);
            
            if(!$model->delete()) {
                $output = 'Failed to delete the record as the update was either unauthorized or because too many rows would be updated.';

                if($outputFormat == "html" || $outputFormat == "xml") {
                    throw new CHttpException(400, $output);
                }

                $this->sendResponseHeaders(400, 'json');

                echo json_encode(
                        array(
                            'success' => false,
                            'error' => json_encode($output),
                        )
                );
                Yii::app()->end();
            }
        } catch (Exception $ex) {
            if($ex instanceof CHttpException) {
                throw $ex;
            }
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(500, $ex->getMessage());
            }

            $errorInfo = null;

            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorParms = array();

                if(isset($ex->errorInfo[0])) {
                    $errorParms['sqlState'] = $ex->errorInfo[0];
                } else {
                    $errorParms['sqlState'] = "Unknown";
                }

                if(isset($ex->errorInfo[1])) {
                    $errorParms['mysqlError'] = $ex->errorInfo[1];
                } else {
                    $errorParms['mysqlError'] = "Unknown";
                }

                if(isset($ex->errorInfo[2])) {
                    $errorParms['message'] = $ex->errorInfo[2];
                } else {
                    $errorParms['message'] = "Unknown";
                }

                $errorInfo = array($errorParms);
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
    }

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if (Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax'])) {
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
			}
		} else {
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('EventRequest');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new EventRequest('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['EventRequest'])) {
			$model->attributes=$_GET['EventRequest'];
		}

		$this->render('admin',array(
			'model'=>$model,
		));
	}

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return EventRequest the loaded model
     * @throws CHttpException
     */
    public function loadModel($id, $outputFormat = 'html')
    {
        $model = EventRequest::model()->findByPk($id);
        
        if($model === null) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(404, 'Facility not found');
            }

            $this->sendResponseHeaders(404, 'json');
                
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Facility not found',
                    )
            );
            Yii::app()->end();
        }
        
        return $model;
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Arena the loaded model
     * @throws CHttpException
     */
    public function loadArenaModel($id, $outputFormat = 'html', $with = false)
    {
        if($with === true) {
            $model = Arena::model()->with('locations', 'contacts')->findByPk($id);
        } else {
            $model = Arena::model()->findByPk($id);
        }
        
        if($model === null) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(404, 'Facility not found');
            }

            $this->sendResponseHeaders(404, 'json');
                
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Facility not found',
                    )
            );
            Yii::app()->end();
        }
        return $model;
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Event the loaded model
     * @throws CHttpException
     */
    public function loadEventModel($id, $outputFormat = 'html')
    {
        $model = Event::model()->findByPk($id);
        
        if($model === null) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(404, 'Event not found');
            }

            $this->sendResponseHeaders(404, 'json');
                
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Event not found',
                    )
            );
            Yii::app()->end();
        }
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param EventRequest $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax'] === 'event-request-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
        
    protected function handleAction($params)
    {
        $bRet = false;
        // Ensure we have a valid action!
        // and delegate to the appropriate model handler!
        try {
            switch($params['action']) {
                case 'acknowledge':
                    $bRet = EventRequest::acknowledgeAssignedRecord($params['requester_name'], $params['requester_email'], $params['acknowledged'], $params['accepted'], $params['rejected'], $params['id'], $params['uid'], $params['eid'], $params['aid'], $params['lid']);
                    break;
                case 'accept':
                    $bRet = EventRequest::acceptAssignedRecord($params['requester_name'], $params['requester_email'], $params['acknowledged'], $params['accepted'], $params['rejected'], $params['id'], $params['uid'], $params['eid'], $params['aid'], $params['lid']);
                    break;
                case 'reject':
                    $bRet = EventRequest::rejectAssignedRecord($params['requester_name'], $params['requester_email'], $params['acknowledged'], $params['accepted'], $params['rejected'], $params['rejected_reason'], $params['id'], $params['uid'], $params['eid'], $params['aid'], $params['lid']);
                    break;
                case 'message':
                    $bRet = EventRequest::sendEmail(array("Message", $params['rejected_reason']), $params['requester_name'], $params['requester_email'], $params['id'], $params['uid'], $params['eid'], $params['aid'], $params['lid']);
                    break;
                default:
                    if($params['output'] == "html" || $params['output'] == "xml") {
                        throw new CHttpException(400, 'Invalid action');
                    }

                    $this->sendResponseHeaders(400, 'json');

                    echo json_encode(
                            array(
                                'success' => false,
                                'error' => 'Invalid action',
                            )
                    );
                    Yii::app()->end();
            }

            if($bRet !== true && !is_string($bRet)) {
                // we didn't perform the record action, let the user know this
                $output = 'Failed to send the message or save record as the update was unauthorized, '
                        . 'too many rows would be updated, or someone '
                        . 'else has already updated the record.';

                if($params['output'] == "html" || $params['output'] == "xml") {
                    throw new CHttpException(400, $output);
                }

                $this->sendResponseHeaders(400, 'json');

                echo json_encode(
                        array(
                            'success' => false,
                            'error' => json_encode($output),
                        )
                );
                Yii::app()->end();
            } elseif($bRet !== true && is_string($bRet)) {
                // we didn't perform the record action, let the user know this
                $output = $bRet;

                if($params['output'] == "html" || $params['output'] == "xml") {
                    throw new CHttpException(500, $output);
                }

                $this->sendResponseHeaders(500, 'json');

                echo json_encode(
                        array(
                            'success' => false,
                            'error' => json_encode($output),
                        )
                );
                Yii::app()->end();
            }
            
            // At this point, the request has either been acknowledged,
            // rejected, or accepted. We simply need to send a single e-mail to the requester to
            // let them know the status update of their request.
            // Thanksfully, the EventRequest model handles all of this for us
            // in a nice big transaction. If anything errors during the DB
            // updates or when sending the e-mail, the transaction will be rolled back.
            // so, we are all done here and can simply return as no output
            // means all is well!!!
            return;
        } catch (Exception $ex) {
            if($ex instanceof CHttpException) {
                throw $ex;
            }
                    
            if($params['output'] == "html" || $params['output'] == "xml") {
                throw new CHttpException(500, $ex->getMessage());
            }

            $errorInfo = null;

            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorParms = array();

                if(isset($ex->errorInfo[0])) {
                    $errorParms['sqlState'] = $ex->errorInfo[0];
                } else {
                    $errorParms['sqlState'] = "Unknown";
                }

                if(isset($ex->errorInfo[1])) {
                    $errorParms['mysqlError'] = $ex->errorInfo[1];
                } else {
                    $errorParms['mysqlError'] = "Unknown";
                }

                if(isset($ex->errorInfo[2])) {
                    $errorParms['message'] = $ex->errorInfo[2];
                } else {
                    $errorParms['message'] = "Unknown";
                }

                $errorInfo = array($errorParms);
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
    }
}