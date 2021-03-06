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
                    'create',
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
        
        $model = isset($_GET['model']) && is_array($_GET['model']) && count($_GET['model']) > 0 ? $_GET['model'] : null;
        $from = isset($_GET['from']) && strtotime($_GET['from']) ? $_GET['from'] : null;
        $to = isset($_GET['to']) && strtotime($_GET['to']) ? $_GET['to'] : null;
        
        if(is_null($model)) {
            $this->sendResponseHeaders(400, 'json');
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Invalid parameters',
                    )
            );
            Yii::app()->end();
        }        

        // Always save the currently logged in user
        $user = Yii::app()->user->model;
        
        $dashData = null;
        
        try {
            $dashData = $user->getManagementDashboardCounts($model, $from, $to);
        } catch(Exception $ex) {
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
    public function actionCreate()
    {
        Yii::trace("In actionView.", "application.controllers.ManagementController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        if(!Yii::app()->user->isRestrictedArenaManager()) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(403, 'Permission denied. You are not authorized to perform this action.');
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
                $this->handleArenaCreate($outputFormat);
                break;
            case 'event':
                $this->handleEventCreate($outputFormat);
                break;
            case 'eventrequest':
                $this->handleEventRequestCreate($outputFormat);
                break;
            case 'reservation':
                $this->handleReservationCreate($outputFormat);
                break;
            case 'contact':
                $this->handleContactCreate($outputFormat);
                break;
            case 'location':
                $this->handleLocationCreate($outputFormat);
                break;
            case 'manager':
                $this->handleManagerCreate($outputFormat);
                break;
            case 'arenareservationpolicy':
                $this->handleArenaReservationPolicyCreate($outputFormat);
                break;
            case 'recurrence':
                $this->handleRecurrenceCreate($outputFormat);
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
    
    protected function handleEventCreate($outputFormat)
    {
        // Always restrict to the currently logged in user!
        $uid = Yii::app()->user->id;
        
        $params = array(
            'endpoints' => array(
                'event' => array(
                    'new' => $this->createUrl('/event/createEvent'),
                    'update' => $this->createUrl('/event/updateAttribute'),
                    'view' => $this->createUrl('/event/viewEvent'),
                    'delete' => $this->createUrl('/event/deleteEvent')
                )
            ),
            'data' => array(
                'output' => 'html'
            )
        );
            
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => array(
                            'model' => $model,
                            'params' => $params
                        )
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array(array('model' => $model, 'params' => $params), "details", "event");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
            
            $this->pageTitle = Yii::app()->name . ' - Event Management - New Event';
            $this->breadcrumbs = array(
                'Management' => array('/site/management'),
                'Events' => array('/management/index', 'model' => 'event'),
                'New Event'
            );

            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_event",
                        array(
                            'params' => $params,
                            'ownView' => true,
                            'newRecord' => 1,
                            'path' => $path,
                            'doReady' => 0
                        ));
            } else {
                $this->registerManagementScripts();
        
                $this->render(
                        "_event",
                        array(
                            'params' => $params,
                            'ownView' => true,
                            'newRecord' => 1,
                            'path' => $path,
                            'doReady' => 1
                        ));
            }
        }
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
                throw new CHttpException(403, 'Permission denied. You are not authorized to perform this action.');
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
    
    protected function handleArenaView($id, $outputFormat)
    {
        // Validate we have a valid Arena ID and if we don't throw a
        // 404 not found error!
        if($id === null || $id < 0) {
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
        
        // During the process of retrieving the arena model, we validate
        // that the user is authorized to view / update this arena!

        // Try and get the data!
        try {
            // First validate the user is authorized
            $model = $this->loadArenaModel($id, $outputFormat, false);
            
            if(!$model->isUserAssigned($uid)) {
                if($outputFormat == "html" || $outputFormat == "xml") {
                    throw new CHttpException(403, 'Permission denied. You are not authorized to perform this action.');
                }

                $this->sendResponseHeaders(403, 'json');
                echo json_encode(array(
                        'success' => false,
                        'error' => 'Permission denied. You are not authorized to perform this action.'
                    )
                );
                Yii::app()->end();
            }
        } catch (Exception $ex) {
            if($ex instanceof CHttpException) {
                throw $ex;
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
        
        // Data has been retrieved!
        $params = array(
            'endpoints' => array(
                'arena' => array(
                    'new' => $this->createUrl('/arena/createArena'),
                    'update' => $this->createUrl('/arena/updateAttribute'),
                    'view' => $this->createUrl('/arena/viewArena'),
                    'delete' => $this->createUrl('/arena/deleteArena')
                ),
                'contact' => array(
                    'new' => $this->createUrl('/contact/createContact'),
                    'update' => $this->createUrl('/contact/updateAttribute'),
                    'view' => $this->createUrl('/contact/view'),
                    'delete' => $this->createUrl('/contact/deleteContact')
                ),
                'location' => array(
                    'new' => $this->createUrl('/location/createLocation'),
                    'update' => $this->createUrl('/location/updateAttribute'),
                    'view' => $this->createUrl('/location/view'),
                    'delete' => $this->createUrl('/location/deleteLocation')
                ),
                'event' => array(
                    'new' => $this->createUrl('/management/create', array('model' => 'event')),
                    'update' => $this->createUrl('/management/index', array('model' => 'event', 'aid' => $model->id)),
                    'view' => $this->createUrl('/event/viewEvent'),
                    'delete' => $this->createUrl('/event/deleteEvent'),
                    'search' => $this->createUrl('/event/retrieveEvents'),
                    'import' => $this->createUrl('/event/uploadEvents', array('aid' => $model->id)),
                    'export' => $this->createUrl('/event/exportEvents'),
                    'deleteAll' => $this->createUrl('/event/deleteEvents')
                ),
                'eventRequest' => array(
                    'new' => $this->createUrl('/eventRequest/createEventRequest'),
                    'update' => $this->createUrl('/eventRequest/updateAttribute'),
                    'view' => $this->createUrl('/eventRequest/view'),
                    'delete' => $this->createUrl('/eventRequest/deleteEventRequest')
                ),
                'reservation' => array(
                    'new' => $this->createUrl('/reservation/createReservation'),
                    'update' => $this->createUrl('/reservation/updateAttribute'),
                    'view' => $this->createUrl('/reservation/view'),
                    'delete' => $this->createUrl('/reservation/deleteReservation')
                ),
                'manager' => array(
                    'new' => $this->createUrl('/user/create', array('arenaId' => $model->id, 'role' => 'RestrictedManager')),
                    'update' => $this->createUrl('/arena/assignManager'),
                    'view' => $this->createUrl('/user/view'),
                    'delete' => $this->createUrl('/user/deleteUser')
                )
            ),
            'data' => array(
                'id' => $model->id,
                'output' => 'html',
                'aid' => $model->id
            )
        );
            
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => array(
                            'model' => $model,
                            'params' => $params
                        )
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array(array('model' => $model, 'params' => $params), "details", "arena");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
            
            $this->pageTitle = Yii::app()->name . ' - Facility Management - ' . $model->name;
            $this->breadcrumbs = array(
                'Management' => array('/site/management'),
                'Facilities' => array('/management/index', 'model' => 'arena'),
                $model->name,
            );

            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_arena",
                        array(
                            'model' => $model,
                            'params' => $params,
                            'ownView' => true,
                            'newRecord' => 0,
                            'path' => $path,
                            'doReady' => 0
                        ));
            } else {
                $this->registerManagementScripts();
        
                $this->render(
                        "_arena",
                        array(
                            'model' => $model,
                            'params' => $params,
                            'ownView' => true,
                            'newRecord' => 0,
                            'path' => $path,
                            'doReady' => 1
                        ));
            }
        }
    }
    
    protected function handleContactView($id, $outputFormat)
    {
        // We do not need to ensure that we have an Arena ID as this method
        // is for loading a contact outside of an Arena context
        
        // Validate we have an Contact ID
        if($id === null || $id < 0) {
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
        
        // Try and get the data!
        try {
            $sql = 'SELECT c.* '
                    . 'FROM contact c '
                    . 'WHERE c.id = :id '
                    . 'ORDER BY last_name ASC, first_name ASC';
        

            $model = Contact::model()->findBySql($sql, array(':id' => $id));
            
            if($model === null) {
                if($outputFormat == "html" || $outputFormat == "xml") {
                    throw new CHttpException(404, 'Contact not found');
                }

                $this->sendResponseHeaders(404, 'json');
                
                echo json_encode(
                        array(
                            'success' => false,
                            'error' => 'Contact not found',
                        )
                );
                Yii::app()->end();
            }

        } catch (Exception $ex) {
            if($ex instanceof CHttpException) {
                throw $ex;
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
        
        // Data has been retrieved!
        $params = array(
            'endpoints' => array(
                'contact' => array(
                    'new' => $this->createUrl('/contact/createContact'),
                    'update' => $this->createUrl('/contact/updateAttribute'),
                    'view' => $this->createUrl('/contact/view'),
                    'delete' => $this->createUrl('/contact/deleteContact')
                )
            ),
            'data' => array(
                'id' => $model->id,
                'get_assigned' => 1,
                'get_available' => 1,
                'output' => 'html'
            )
        );
            
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => array(
                            'model' => $model,
                            'params' => $params
                        )
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array(array('model' => $model, 'params' => $params), "details", "contact");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
            
            $this->pageTitle = Yii::app()->name . ' - Contact Management - ' . $model->first_name . ' ' . $model->last_name;
            $this->breadcrumbs = array(
                'Management' => array('/site/management'),
                'Contacts' => array('/management/index', 'model' => 'contact'),
                $model->first_name . ' ' . $model->last_name
            );

            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_contact",
                        array(
                            'model' => $model,
                            'params' => $params,
                            'ownView' => true,
                            'newRecord' => 0,
                            'path' => $path,
                            'doReady' => 0
                        ));
            } else {
                $this->registerManagementScripts();
        
                $this->render(
                        "_contact",
                        array(
                            'model' => $model,
                            'params' => $params,
                            'ownView' => true,
                            'newRecord' => 0,
                            'path' => $path,
                            'doReady' => 1
                        ));
            }
        }
    }
    
    protected function handleLocationView($id, $outputFormat)
    {
        // We need to ensure that we have an Arena ID
        // We need this to ensure that the model isn't being edited/viewed out
        // of context!
        
        $aid = null;
        
        if(isset($_GET['aid']) && is_numeric($_GET['aid']) && $_GET['aid'] > 0) {
            $aid = $_GET['aid'];
        }
        
        // Validate we have an Arena ID and Location ID
        if($id === null || $id < 0 || $aid === null) {
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
        
        // During the process of retrieving the location model, we validate
        // that the user is authorized to view / update this location!

        // Try and get the data!
        try {
            // First validate the user is authorized
            $arena = $this->loadArenaModel($aid, $outputFormat, false);
            
            if(!$arena->isUserAssigned($uid)) {
                if($outputFormat == "html" || $outputFormat == "xml") {
                    throw new CHttpException(403, 'Permission denied. You are not authorized to perform this action.');
                }

                $this->sendResponseHeaders(403, 'json');
                echo json_encode(array(
                        'success' => false,
                        'error' => 'Permission denied. You are not authorized to perform this action.'
                    )
                );
                Yii::app()->end();
            }
            
            $sql = 'SELECT l.*, a.name AS arena_name, s.display_name AS status, t.display_name AS type '
                    . 'FROM location l '
                    . 'INNER JOIN arena a '
                    . 'ON l.arena_id = a.id AND l.id = :id AND a.id = :aid '
                    . 'INNER JOIN location_status s '
                    . 'ON l.status_id = s.id '
                    . 'INNER JOIN location_type t '
                    . 'ON l.type_id = t.id '
                    . 'ORDER BY l.name ASC';
        

            $model = Location::model()->findBySql($sql, array(':id' => $id, ':aid' => $aid));
            
            if($model === null) {
                if($outputFormat == "html" || $outputFormat == "xml") {
                    throw new CHttpException(404, 'Location not found');
                }

                $this->sendResponseHeaders(404, 'json');
                
                echo json_encode(
                        array(
                            'success' => false,
                            'error' => 'Location not found',
                        )
                );
                Yii::app()->end();
            }

        } catch (Exception $ex) {
            if($ex instanceof CHttpException) {
                throw $ex;
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
        
        // Data has been retrieved!
        $params = array(
            'endpoints' => array(
                'location' => array(
                    'new' => $this->createUrl('/location/createLocation'),
                    'update' => $this->createUrl('/location/updateAttribute'),
                    'view' => $this->createUrl('/location/view'),
                    'delete' => $this->createUrl('/location/deleteLocation')
                )
            ),
            'data' => array(
                'id' => $model->id,
                'output' => 'html',
                'aid' => $model->arena_id
            )
        );
            
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => array(
                            'model' => $model,
                            'params' => $params
                        )
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array(array('model' => $model, 'params' => $params), "details", "location");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
            
            $this->pageTitle = Yii::app()->name . ' - Venue Management - ' . $model->name;
            $this->breadcrumbs = array(
                'Management' => array('/site/management'),
                'Venues' => array('/management/index', 'model' => 'location'),
                $model->name
            );

            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_location",
                        array(
                            'model' => $model,
                            'params' => $params,
                            'ownView' => true,
                            'newRecord' => 0,
                            'path' => $path,
                            'doReady' => 0
                        ));
            } else {
                $this->registerManagementScripts();
        
                $this->render(
                        "_location",
                        array(
                            'model' => $model,
                            'params' => $params,
                            'ownView' => true,
                            'newRecord' => 0,
                            'path' => $path,
                            'doReady' => 1
                        ));
            }
        }
    }
    
    protected function handleEventView($id, $outputFormat)
    {
        // We need to ensure that we have an Arena ID
        // We need this to ensure that the model isn't being edited/viewed out
        // of context!
        
        $aid = null;
        
        if(isset($_GET['aid']) && is_numeric($_GET['aid']) && $_GET['aid'] > 0) {
            $aid = $_GET['aid'];
        }
        
        // Validate we have an Arena ID and Event ID
        if($id === null || $id < 0 || $aid === null) {
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
        
        // During the process of retrieving the location model, we validate
        // that the user is authorized to view / update this location!

        // Try and get the data!
        try {
            // First validate the user is authorized
            $arena = $this->loadArenaModel($aid, $outputFormat, false);
            
            if(!$arena->isUserAssigned($uid)) {
                if($outputFormat == "html" || $outputFormat == "xml") {
                    throw new CHttpException(403, 'Permission denied. You are not authorized to perform this action.');
                }

                $this->sendResponseHeaders(403, 'json');
                echo json_encode(array(
                        'success' => false,
                        'error' => 'Permission denied. You are not authorized to perform this action.'
                    )
                );
                Yii::app()->end();
            }
            
            $sql = 'SELECT e.*, '
                    . "DATE_FORMAT(e.start_date, '%c/%e/%Y') AS startDate, "
                    . "DATE_FORMAT(e.start_time, '%l:%i %p') AS startTime, "
                    . 'a.name AS arena_name, '
                    . 'l.name AS location_name, '
                    . "CASE WHEN l.name IS NULL THEN CONCAT(DATE_FORMAT(e.start_date, '%c/%e/%Y'), ' ', DATE_FORMAT(e.start_time, '%l:%i %p'), ' @ ', a.name) "
                    . "ELSE CONCAT(DATE_FORMAT(e.start_date, '%c/%e/%Y'), ' ', DATE_FORMAT(e.start_time, '%l:%i %p'), ' @ ', a.name, ' ', l.name) "
                    . "END AS eventName, "
                    . 's.display_name AS estatus, '
                    . 't.display_name AS etype '
                    . 'FROM event e '
                    . 'INNER JOIN arena a '
                    . 'ON e.arena_id = a.id AND e.id = :id AND a.id = :aid '
                    . 'INNER JOIN event_status s '
                    . 'ON e.status_id = s.id '
                    . 'INNER JOIN event_type t '
                    . 'ON e.type_id = t.id '
                    . 'LEFT OUTER JOIN location l '
                    . 'ON e.location_id = l.id '
                    . 'ORDER BY e.start_date ASC, e.start_time';
        

            $model = Event::model()->findBySql($sql, array(':id' => $id, ':aid' => $aid));
            
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

        } catch (Exception $ex) {
            if($ex instanceof CHttpException) {
                throw $ex;
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
        
        // Data has been retrieved!
        $params = array(
            'endpoints' => array(
                'event' => array(
                    'new' => $this->createUrl('/event/createEvent'),
                    'update' => $this->createUrl('/event/updateAttribute'),
                    'view' => $this->createUrl('/event/viewEvent'),
                    'delete' => $this->createUrl('/event/deleteEvent')
                )
            ),
            'data' => array(
                'id' => $model->id,
                'output' => 'html',
                'aid' => $model->arena_id,
                'lid' => $model->location_id
            )
        );
            
        if($outputFormat == 'json') {
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => array(
                            'model' => $model,
                            'params' => $params
                        )
                    )
            );
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array(array('model' => $model, 'params' => $params), "details", "event");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
            
            $this->pageTitle = Yii::app()->name . ' - Event Management - Event: #' . $model->id;
            $this->breadcrumbs = array(
                'Management' => array('/site/management'),
                'Events' => array('/management/index', 'model' => 'event'),
                $model->etype . ' Event: #' . $model->id
            );

            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_event",
                        array(
                            'model' => $model,
                            'params' => $params,
                            'ownView' => true,
                            'newRecord' => 0,
                            'path' => $path,
                            'doReady' => 0
                        ));
            } else {
                $this->registerManagementScripts();
        
                $this->render(
                        "_event",
                        array(
                            'model' => $model,
                            'params' => $params,
                            'ownView' => true,
                            'newRecord' => 0,
                            'path' => $path,
                            'doReady' => 1
                        ));
            }
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
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
            
            $this->pageTitle = Yii::app()->name . ' - Event Request Management - Request: #' . $data['item']['fields']['id']['value'];
            $this->breadcrumbs = array(
                'Management' => array('/site/management'),
                'Event Requests' => array('/management/index', 'model' => 'eventRequest'),
                $data['item']['fields']['type_id']['value'] . ' Request: #' . $data['item']['fields']['id']['value']
            );

            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_eventRequest",
                        array(
                            'model' => new EventRequest(),
                            'data' => $data,
                            'ownView' => true,
                            'newRecord' => 0,
                            'path' => $path,
                            'doReady' => false
                        ));
            } else {
                $this->registerManagementScripts();
        
                $this->render(
                        "_eventRequest",
                        array(
                            'model' => new EventRequest(),
                            'data' => $data,
                            'ownView' => true,
                            'newRecord' => 0,
                            'path' => $path,
                            'doReady' => true
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
                throw new CHttpException(403, 'Permission denied. You are not authorized to perform this action.');
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
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
            
            $this->pageTitle = Yii::app()->name . ' - Facility Management';
            $this->breadcrumbs = array(
                'Management' => array('/site/management'),
                'Facilities'
            );

            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_index",
                        array(
                            'data' => $data,
                            'headers' => Arena::getSummaryAttributes(),
                            'doReady' => false,
                            'path' => $path,
                            'jsFile' => ""
                        ));
            } else {
                $this->registerManagementScripts();
            
                $this->render(
                        "_index",
                        array(
                            'data' => $data,
                            'headers' => Arena::getSummaryAttributes(),
                            'doReady' => true,
                            'path' => $path,
                            'jsFile' => ""
                        ));
            }
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
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));            

            $this->pageTitle = Yii::app()->name . ' - Contact Management';
            $this->breadcrumbs = array(
                'Management' => array('/site/management'),
                'Contacts'
            );

            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_index",
                        array(
                            'data' => $data,
                            'headers' => Contact::getSummaryAttributes(),
                            'doReady' => false,
                            'path' => $path,
                            'jsFile' => ""
                        ));
            } else {
                $this->registerManagementScripts();
            
                $this->render(
                        "_index",
                        array(
                            'data' => $data,
                            'headers' => Contact::getSummaryAttributes(),
                            'doReady' => true,
                            'path' => $path,
                            'jsFile' => ""
                        ));
            }
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

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => $data
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
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));            

            $this->pageTitle = Yii::app()->name . ' - Event Management';
            $this->breadcrumbs = array(
                'Management' => array('/site/management'),
                'Events'
            );

            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_index",
                        array(
                            'data' => $data,
                            'headers' => Event::getSummaryAttributes(),
                            'doReady' => false,
                            'path' => $path,
                            'jsFile' => ""
                        ));
            } else {
                $this->registerManagementScripts();
            
                $this->render(
                        "_index",
                        array(
                            'data' => $data,
                            'headers' => Event::getSummaryAttributes(),
                            'doReady' => true,
                            'path' => $path,
                            'jsFile' => ""
                        ));
            }
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
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));            

            $this->pageTitle = Yii::app()->name . ' - Event Request Management';
            $this->breadcrumbs = array(
                'Management' => array('/site/management'),
                'Event Requests'
            );

            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_index",
                        array(
                            'data' => $data,
                            'headers' => EventRequest::getSummaryAttributes(),
                            'doReady' => false,
                            'path' => $path,
                            'jsFile' => ""
                        ));
            } else {
                $this->registerManagementScripts();
            
                $this->render(
                        "_index",
                        array(
                            'data' => $data,
                            'headers' => EventRequest::getSummaryAttributes(),
                            'doReady' => true,
                            'path' => $path,
                            'jsFile' => ""
                        ));
            }
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
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));            

            $this->pageTitle = Yii::app()->name . ' - Reservation Management';
            $this->breadcrumbs = array(
                'Management' => array('/site/management'),
                'Reservations'
            );

            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_index",
                        array(
                            'data' => $data,
                            'headers' => Reservation::getSummaryAttributes(),
                            'doReady' => false,
                            'path' => $path,
                            'jsFile' => ""
                        ));
            } else {
                $this->registerManagementScripts();
            
                $this->render(
                        "_index",
                        array(
                            'data' => $data,
                            'headers' => Reservation::getSummaryAttributes(),
                            'doReady' => true,
                            'path' => $path,
                            'jsFile' => ""
                        ));
            }
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
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));            

            $this->pageTitle = Yii::app()->name . ' - Venue Management';
            $this->breadcrumbs = array(
                'Management' => array('/site/management'),
                'Venues'
            );

            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_index",
                        array(
                            'data' => $data,
                            'headers' => Location::getSummaryAttributes(),
                            'doReady' => false,
                            'path' => $path,
                            'jsFile' => ""
                        ));
            } else {
                $this->registerManagementScripts();
            
                $this->render(
                        "_index",
                        array(
                            'data' => $data,
                            'headers' => Location::getSummaryAttributes(),
                            'doReady' => true,
                            'path' => $path,
                            'jsFile' => ""
                        ));
            }
        }
    }

    /**
     * Returns the Arena data model based on the primary key given in the GET variable.
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

}