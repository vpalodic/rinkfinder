<?php

class EventController extends Controller
{
    /**
     * @var private property containing the associated Arena model
     * instance.
     */
    private $arena = null;

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
            'accessControl',
            'postOnly + delete deleteEvent deleteEvents updateAttribute createEvent exportEvents',
            'arenaContext + create admin uploadEvents delete',
            'ajaxOnly + retrieveEvents viewEvent deleteEvent updateAttribute createEvent uploadEventsFileDelete uploadEventsProcessCSV type status',
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
                'allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array(
                    'getMonth',
                    'getSearch',
                    'index',
                    'view',
                    'type',
                    'status',
                ),
                'users' => array(
                    '*'
                ),
            ),
            array(
                'allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(
                    'createEvent',
                    'deleteEvent',
                    'deleteEvents',
                    'retrieveEvents',
                    'exportEvents',
                    'updateAttribute',
                    'uploadEvents',
                    'uploadEventsFile',
                    'uploadEventsFileDelete',
                    'uploadEventsProcessCSV',
                    'viewEvent'
                ),
                'users' => array(
                    '@'
                ),
            ),
/*            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array(
                    'create',
                    'admin',
                    'update',
                    'delete'
                ),                
                'roles' => array(
                    'ApplicationAdministrator'
                ),
            ),*/
            array(
                'deny',  // deny all users
                'users' => array(
                    '*'
                ),
            ),
        );
    }

    public function actionViewEvent()
    {
        Yii::trace("In actionViewEvent.", "application.controllers.EventController");
        
        // Default to HTML output!
        $outputFormat = 'html';
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        // And that the user has permission to update it!
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
        
         // We only view via GET requests!
        $id = isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0 ? (integer)$_GET['id'] : 0;
        $aid = isset($_GET['aid']) && is_numeric($_GET['aid']) && $_GET['aid'] > 0 ? (integer)$_GET['aid'] : 0;
        $uid = Yii::app()->user->id;
        $aid = 0;
        
        // Honestly, we don't need the aid here as events are publicly available
        // and we validate in the updateAttribute action that the user has 
        // authorization to edit the event.
        
        // Verify we have a valid ID!
        if($id <= 0) {
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
        
        // Try and get the data!
        try {
            if($aid > 0) {
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
                
                $attributes = $model->attributes;
                $attributes['arena_name'] = $model->arena_name;
                $attributes['location_name'] = $model->location_name;
                $attributes['eventName'] = $model->eventName;
                $attributes['startDate'] = $model->startDate;
                $attributes['startTime'] = $model->startTime;
                $attributes['type'] = $model->etype;
                $attributes['status'] = $model->estatus;
            } else {
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
                    . 'ON e.arena_id = a.id AND e.id = :id '
                    . 'INNER JOIN event_status s '
                    . 'ON e.status_id = s.id '
                    . 'INNER JOIN event_type t '
                    . 'ON e.type_id = t.id '
                    . 'LEFT OUTER JOIN location l '
                    . 'ON e.location_id = l.id '
                    . 'ORDER BY e.start_date ASC, e.start_time';

                $model = Event::model()->findBySql($sql, array(':id' => $id));
                
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
                
                $attributes = $model->attributes;
                $attributes['arena_name'] = $model->arena_name;
                $attributes['location_name'] = $model->location_name;
                $attributes['eventName'] = $model->eventName;
                $attributes['startDate'] = $model->startDate;
                $attributes['startTime'] = $model->startTime;
                $attributes['type'] = $model->etype;
                $attributes['status'] = $model->estatus;
            }
            
            // Data has been retrieved or else we would have thrown an exception
            if($outputFormat == 'json') {
                $this->sendResponseHeaders(200, 'json');
                
                echo json_encode(
                        array(
                            'success' => true,
                            'error' => false,
                            'data' => $attributes,
                        )
                );
                
                Yii::app()->end();
            } elseif($outputFormat == 'xml') {
                $this->sendResponseHeaders(200, 'xml');
            
                $xml = Controller::generate_valid_xml_from_array($attributes, "view", "event");
                echo $xml;
            
                Yii::app()->end();
            } else {
                // We default to html!
                // Publish and register our jQuery plugin
                $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));            

                if(defined('YII_DEBUG')) {
                    Yii::app()->clientScript->registerScriptFile($path . '/js/event/view.js', CClientScript::POS_END);
                } else {
                    Yii::app()->clientScript->registerScriptFile($path . '/js/event/view.min.js', CClientScript::POS_END);
                }
            
                $this->breadcrumbs = array(
                    'Events' => $this->createUrl('event/index'),
                    isset($model->eventName) ? CHtml::encode($model->eventName) : ''
                );

                $this->registerUserScripts();
                $this->includeCss = true;
                $this->navigation = true;

                if(Yii::app()->request->isAjaxRequest) {
                    $this->renderPartial(
                            "_view",
                            array(
                                'model' => $model,
                                'aid' => $aid,
                                'doReady' => false,
                                'path' => $path,
                            ));
                } else {
                    $this->render(
                            "view",
                            array(
                                'model' => $model,
                                'aid' => $aid,
                                'doReady' => true,
                                'path' => $path,
                            ));
                }
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
    }
    
    /**
     * Displays a particular model.
     */
    public function actionView()
    {
        Yii::trace("In actionView.", "application.controllers.EventController");
        
        // Send some headers that will allow cross-domain requests
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type');
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $aid = isset($_GET['aid']) ? $_GET['aid'] : null;
        $lid = isset($_GET['lid']) ? $_GET['lid'] : null;
        $open = isset($_GET['open']) &&  ($_GET['open'] == 'false' || $_GET['open'] <= 0 || empty($_GET['open'])) ? 0 : 1;
        $navigation = isset($_GET['nav']) && ($_GET['nav'] == 'false' || $_GET['nav'] <= 0 || empty($_GET['nav'])) ? 0 : 1;
        
        // We are pretty flexible in what we will accept as we can either send
        // lat & lng pair to search by distance, a single arena id, or an array
        // of arena IDs. At least one of these must be passed in to us or else
        // we will bomb out.
        if(is_null($aid) || !is_numeric($aid) || $aid <= 0 ||
                is_null($id) || !is_numeric($id) || $id <= 0) {
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
        
        // Try and get the data!
        try {
            $data = Event::getSingleEventView($id, $aid, array(
                    'open' => $open,
                    'lid' => $lid
                    )
                );
            
            $data['requestUrl'] = '/event/view';
            $data['params']['output'] = $outputFormat;
            $data['params']['nav'] = $navigation;
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
            
            $xml = Controller::generate_valid_xml_from_array($data, "events", "event");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));            

            $arena = null;
            
            if(count($data['records']) == 1)
            {
                $arena = Arena::model()->findByPk($data['records'][0]['arena_id']);
            }
            
            if(Yii::app()->request->isAjaxRequest) {
                //$this->registerUserScripts();
                $this->includeCss = true;
                $this->navigation = false;
                
                $this->renderPartial(
                        "_view",
                        array(
                            'data' => $data,
                            'arena' => $arena,
                            'doReady' => false,
                            'path' => $path,
                        ));
            } else {
                if(defined('YII_DEBUG')) {
                    Yii::app()->clientScript->registerScriptFile($path . '/js/event/calendar.js', CClientScript::POS_END);
                } else {
                    Yii::app()->clientScript->registerScriptFile($path . '/js/event/calendar.min.js', CClientScript::POS_END);
                }

                $this->breadcrumbs = array(
                    'Facilities' => array('/arena/index'),
                    $data['records'][0]['arena_name'] => array('/arena/view', 'id' => $data['records'][0]['arena_id']),
                    'Events' => array('/event/index', 'aid' => $data['records'][0]['arena_id'], 'limit' => 100),
                    $data['records'][0]['type'] => array('/event/index', 'aid' => $data['records'][0]['arena_id'], 'limit' => 100, 'types' => array($data['records'][0]['type_id']))
                );

                $this->registerUserScripts();
                $this->includeCss = true;
                $this->navigation = $navigation;

                $this->render(
                        "view",
                        array(
                            'data' => $data,
                            'arena' => $arena,
                            'doReady' => true,
                            'path' => $path,
                        ));
            }
        }
    }

    /**
     * Displays a particular arena's events one month at a time.
     */
    public function actionRetrieveEvents()
    {
        Yii::trace("In actionRetrieveEvents.", "application.controllers.EventController");
        
        // We output json or XML only for this one!
        $outputFormat = "json";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        $aid = 0;
        $lid = null;
        $from = null;
        $to = null;
        $tid = null;
        $sid = null;
        
        if(isset($_GET['aid']) && is_numeric($_GET['aid']) && $_GET['aid'] > 0) {
            $aid = $_GET['aid'];
        }
        
        if(isset($_GET['lid']) && is_numeric($_GET['lid']) && $_GET['lid'] > 0) {
            $aid = $_GET['lid'];
        }
        
        if(isset($_GET['from']) && strtotime($_GET['from'])) {
            $from = $_GET['from'];
        }
        
        if(isset($_GET['to']) && strtotime($_GET['to'])) {
            $to = $_GET['to'];
        }
        
        if(isset($_GET['tid']) && is_numeric($_GET['tid']) && $_GET['tid'] > 0) {
            $tid = $_GET['tid'];
        }
        
        if(isset($_GET['sid']) && is_numeric($_GET['sid']) && $_GET['sid'] > 0) {
            $sid = $_GET['sid'];
        }
        
        // Always restrict to the currently logged in user!
        $uid = Yii::app()->user->id;
        $data = null;
        
        // We are pretty flexible in what we will accept as we can either send
        // lat & lng pair to search by distance, a single arena id, or an array
        // of arena IDs. At least one of these must be passed in to us or else
        // we will bomb out.
        if($aid <= 0) {
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
        
        // Load the Arena model and ensure that the user is assigned to it!
        $arena = $this->loadArenaModel($aid, $outputFormat);
        
        // And that the user has permission to update it!
        if(!Yii::app()->user->isRestrictedArenaManager() || !$arena->isUserAssigned($uid)) {
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
        
        $data = null;
        
        // Try and get the data!
        try {
            $data = Event::getAssignedArenaView($uid, $aid, $lid, $from, $to, $tid, $sid);
            
            // Data has been retrieved!
            if($outputFormat == 'json') {
                $this->sendResponseHeaders(200, 'json');

                echo json_encode(array(
                    'success' => true,
                    'error' => false,
                    'data' => $data
                ));
                Yii::app()->end();
            } elseif($outputFormat == 'xml') {
                $this->sendResponseHeaders(200, 'xml');
            
                $xml = Controller::generate_valid_xml_from_array($data, "events", "event");
                echo $xml;
            
                Yii::app()->end();
            } else {
                Yii::app()->end();
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
    }

    /**
     * Displays a particular arena's events one month at a time.
     */
    public function actionGetSearch()
    {
        Yii::trace("In actionGetSearch.", "application.controllers.EventController");
        
        // Send some headers that will allow cross-domain requests
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type');
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        $aid = isset($_GET['aid']) ? $_GET['aid'] : null;
        $aids = isset($_GET['aids']) ? $_GET['aids'] : array();
        $lat = isset($_GET['lat']) ? $_GET['lat'] : null;
        $lng = isset($_GET['lng']) ? $_GET['lng'] : null;
        $radius = isset($_GET['radius']) ? $_GET['radius'] : 20;
        $lid = isset($_GET['lid']) ? $_GET['lid'] : null;
        $open = isset($_GET['open']) &&  ($_GET['open'] == 'false' || $_GET['open'] <= 0 || empty($_GET['open'])) ? 0 : 1;
        $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
        $price = isset($_GET['price']) ? $_GET['price'] : null;
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : null;
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : null;
        $types = isset($_GET['types']) ? $_GET['types'] : array();
        $navigation = isset($_GET['nav']) && ($_GET['nav'] == 'false' || $_GET['nav'] <= 0 || empty($_GET['nav'])) ? 0 : 1;
        
        // We are pretty flexible in what we will accept as we can either send
        // lat & lng pair to search by distance, a single arena id, or an array
        // of arena IDs. At least one of these must be passed in to us or else
        // we will bomb out.
        if((is_null($aid) || !is_numeric($aid) || $aid <= 0) && count($aids) <= 0 &&
                (is_null($lat) || !is_numeric($lat) || is_null($lng) || !is_numeric($lng))) {
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
        
        // To ease the pain, we will always search with $aids. so, we will simply
        // add the single $aid to the array if it has been set
        if(!is_null($aid) && is_numeric($aid) && $aid > 0) {
            $aids[] = $aid;
        }
        
        $data = null;
        
        // Try and get the data!
        try {
            // We will do one of two searches, if count($aids) is greater than 0
            // we will search by the selected arenas, otherwise we will search
            // by $lat and $lng
            if(count($aids) > 0) {
                $data = Event::getEventsSearchByArenas($aids, array(
                    'offset' => $offset,
                    'limit' => $limit,
                    'open' => $open,
                    'lid' => $lid,
                    'price' => $price,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'types' => $types
                    )
                );
            } else {
                $data = Event::getEventsSearchByLatLng($lat, $lng, $radius, array(
                    'offset' => $offset,
                    'limit' => $limit,
                    'open' => $open,
                    'lid' => $lid,
                    'price' => $price,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'types' => $types
                    )
                );
            }
            
            $data['requestUrl'] = '/event/getSearch';
            $data['params']['output'] = $outputFormat;
            $data['params']['nav'] = $navigation;
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
            
            $xml = Controller::generate_valid_xml_from_array($data, "events", "event");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));            

            $arena = null;
            
            if(count($aids) == 1)
            {
                $arena = Arena::model()->findByPk($aids[0]);
            }
            
            if(Yii::app()->request->isAjaxRequest) {
                //$this->registerUserScripts();
                $this->includeCss = true;
                $this->navigation = false;
                
                $this->renderPartial(
                        "_eventSearch",
                        array(
                            'data' => $data,
                            'arena' => $arena,
                            'start_date' => $start_date,
                            'doReady' => false,
                            'path' => $path,
                        ));
            } else {
                if(defined('YII_DEBUG')) {
                    Yii::app()->clientScript->registerScriptFile($path . '/js/event/calendar.js', CClientScript::POS_END);
                } else {
                    Yii::app()->clientScript->registerScriptFile($path . '/js/event/calendar.min.js', CClientScript::POS_END);
                }
            
                $this->breadcrumbs = array(
                    'Events'
                );

                $this->registerUserScripts();
                $this->includeCss = true;
                $this->navigation = $navigation;

                $this->render(
                        "eventSearch",
                        array(
                            'data' => $data,
                            'arena' => $arena,
                            'start_date' => $start_date,
                            'doReady' => true,
                            'path' => $path,
                        ));
            }
        }
    }

    /**
     * Displays a particular arena's events one month at a time.
     */
    public function actionGetMonth()
    {
        Yii::trace("In actionGetMonth.", "application.controllers.EventController");
        
        // Send some headers that will allow cross-domain requests
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type');
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        $aid = isset($_GET['aid']) ? $_GET['aid'] : null;
        $lid = isset($_GET['lid']) ? $_GET['lid'] : null;
        $open = isset($_GET['open']) &&  ($_GET['open'] == 'false' || $_GET['open'] <= 0 || empty($_GET['open'])) ? 0 : 1;
        $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 0;
        $price = isset($_GET['price']) ? $_GET['price'] : null;
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d", time());
        $end_date = date('Y-m-t', strtotime($start_date));
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : null;
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : null;
        $types = isset($_GET['types']) ? $_GET['types'] : array();
        $navigation = isset($_GET['nav']) && ($_GET['nav'] == 'false' || $_GET['nav'] <= 0 || empty($_GET['nav'])) ? 0 : 1;
        
        if(is_null($aid) || !is_numeric($aid) || $aid <= 0) {
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
        
        $data = null;
        
        // Try and get the data!
        try {
            $data = Event::getEventsMonthCalendar($aid, array(
                'offset' => $offset,
                'limit' => $limit,
                'open' => $open,
                'lid' => $lid,
                'price' => $price,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'types' => $types
               )
            );
            
            $data['requestUrl'] = '/event/getMonth';
            $data['params']['output'] = $outputFormat;
            $data['params']['nav'] = $navigation;
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
            
            $xml = Controller::generate_valid_xml_from_array($data, "events", "event");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));            

            if(Yii::app()->request->isAjaxRequest) {
                $this->includeCss = true;
                $this->navigation = false;
                
                $this->renderPartial(
                        "_calendar",
                        array(
                            'data' => $data,
                            'start_date' => $start_date,
                            'doReady' => false,
                            'path' => $path,
                        ));
            } else {
                if(defined('YII_DEBUG')) {
                    Yii::app()->clientScript->registerScriptFile($path . '/js/event/calendar.js', CClientScript::POS_END);
                } else {
                    Yii::app()->clientScript->registerScriptFile($path . '/js/event/calendar.min.js', CClientScript::POS_END);
                }
            
                $this->breadcrumbs = array(
                    'Events'
                );

                $this->registerUserScripts();
                $this->includeCss = true;
                $this->navigation = $navigation;

                $this->render(
                        "calendar",
                        array(
                            'data' => $data,
                            'start_date' => $start_date,
                            'doReady' => true,
                            'path' => $path,
                        ));
            }
        }
    }

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Event;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Event'])) {
			$model->attributes=$_POST['Event'];
			if ($model->save()) {
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

    /**
     * Gets a list of available types for the event request
     */
    public function actionType()
    {
        Yii::trace("In actionType.", "application.controllers.EventController");
        
        // Default to XML output!
        $outputFormat = "json";
        $data = array();
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        // Try and get the data!
        try {
            $dataTemp = Event::getTypes(true);
            
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
            
            $xml = Controller::generate_valid_xml_from_array($data, "eventTypes", "eventType");
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
        Yii::trace("In actionStatus.", "application.controllers.EventController");
        
        // Default to XML output!
        $outputFormat = "json";
        $data = array();
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        // Try and get the data!
        try {
            $dataTemp = Event::getStatuses(true);
            
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
            
            $xml = Controller::generate_valid_xml_from_array($data, "eventStatuses", "eventStatus");
            echo $xml;
            
            Yii::app()->end();
        } else {
        }
    }

    /**
     * Creates a new model.
     * If creation is successful, the primary key of the new event is returned
     * along with the tags!
     * Otherwise if there are validation errors, those are returned as well.
     */
    public function actionCreateEvent()
    {
        Yii::trace("In actionCreateLocation.", "application.controllers.LocationController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
            $outputFormat = $_POST['output'];
        }
        
        // We only create via a POST and AJAX request!
        $aid = isset($_POST['aid']) && is_numeric($_POST['aid']) && $_POST['aid'] > 0 ? (integer)$_POST['aid'] : 0;
        
        // Verify we have a valid ID!
        if($aid <= 0) {
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
        
        // We need to grab and validate the rest of our parameters from the request body
        $model = new Event;

        $model->arena_id = $aid;
        $model->location_id = isset($_POST['location_id']) && is_numeric($_POST['location_id']) ? (integer)$_POST['location_id'] : null;
        $model->external_id = isset($_POST['external_id']) && is_string($_POST['external_id']) && strlen($_POST['external_id']) > 0 ? $_POST['external_id'] : null;
        $model->recurrence_id = isset($_POST['recurrence_id']) && is_numeric($_POST['recurrence_id']) ? (integer)$_POST['recurrence_id'] : null;
        $model->name = isset($_POST['name']) && is_string($_POST['name']) && strlen($_POST['name']) > 0 ? $_POST['name'] : null;
        $model->description = isset($_POST['description']) && is_string($_POST['description']) && strlen($_POST['description']) > 0 ? $_POST['description'] : null;
        $model->notes = isset($_POST['notes']) && is_string($_POST['notes']) && strlen($_POST['notes']) > 0 ? $_POST['notes'] : null;
        $model->tags = isset($_POST['tags']) && is_string($_POST['tags']) && strlen($_POST['tags']) > 0 ? $_POST['tags'] : null;
        $model->type_id = isset($_POST['type_id']) && is_numeric($_POST['type_id']) ? (integer)$_POST['type_id'] : 1;
        $model->status_id = isset($_POST['status_id']) && is_numeric($_POST['status_id']) ? (integer)$_POST['status_id'] : 1;
        $model->all_day = isset($_POST['all_day']) && is_numeric($_POST['all_day']) ? (integer)$_POST['all_day'] : null;
        $model->duration = isset($_POST['duration']) && is_numeric($_POST['duration']) ? (integer)$_POST['duration'] : null;
        $model->price = isset($_POST['price']) && is_numeric($_POST['price']) ? (float)$_POST['price'] : 0.00;
        $model->start_date = isset($_POST['start_date']) && is_string($_POST['start_date']) && strlen($_POST['start_date']) > 0 ? $_POST['start_date'] : null;
        $model->start_time = isset($_POST['start_time']) && is_string($_POST['start_time']) && strlen($_POST['start_time']) > 0 ? $_POST['start_time'] : null;
        $model->end_date = isset($_POST['end_date']) && is_string($_POST['end_date']) && strlen($_POST['end_date']) > 0 ? $_POST['end_date'] : null;
        $model->end_time = isset($_POST['end_time']) && is_string($_POST['end_time']) && strlen($_POST['end_time']) > 0 ? $_POST['end_time'] : null;
        $model->created_by_id = $uid;
        $model->created_on = new CDbExpression('NOW()');
        $model->updated_by_id = $uid;
        $model->updated_on = new CDbExpression('NOW()');
        
        // Ok, we auto-tag it before we save!
        $model->autoDurationEndDateTimeStatus(0);
        $model->autoTag();
        
        try {
            if(!$model->save()) {
                $errors = $model->getErrors();

                if($outputFormat == "html" || $outputFormat == "xml") {
                    $output = '';

                    foreach($errors as $error) {
                        if($output == '') {
                            $output = $error;
                        } else {
                            $output .= "\n" . $error;
                        }
                    }
                    throw new CHttpException(200, $output);
                }
            
                $this->sendResponseHeaders(200, 'json');
            
                echo json_encode(
                        array(
                            'success' => false,
                            'error' => true,
                            'errors' => json_encode($errors)
                        )
                );
                Yii::app()->end();
            }
            
            if($outputFormat == "html") {
                echo json_encode(
                        array(
                            'success' => true,
                            'error' => false,
                            'id' => $model->id,
                            'tags' => $model->tags
                        )
                );
            } else if ($outputFormat == "xml") {
                $xmlout = array(
                    'success' => 'true',
                    'error' => 'false',
                    'id' => $model->id,
                    'tags' => $model->tags
                );
                
                $this->sendResponseHeaders(200, 'xml');
                $xml = Controller::generate_valid_xml_from_array($xmlout, "newLocation", "location");
                echo $xml;
                Yii::app()->end();
            } else {
                $this->sendResponseHeaders(200, 'json');
            
                echo json_encode(
                        array(
                            'success' => true,
                            'error' => false,
                            'id' => $model->id,
                            'aid' => $model->arena_id,
                            'tags' => $model->tags,
                            'all_day' => $model->all_day,
                            'start_date' => $model->start_date,
                            'start_time' => $model->start_time,
                            'end_date' => $model->end_date,
                            'end_time' => $model->end_time,
                            'duration' => $model->duration
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
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Event'])) {
			$model->attributes=$_POST['Event'];
			if ($model->save()) {
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

    /**
     * Updates a particular model.
     * If update is successful, there is no output, otherwise we output an error.
     */
    public function actionUpdateAttribute()
    {
        Yii::trace("In actionUpdateAttribute.", "application.controllers.EventController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
            $outputFormat = $_POST['output'];
        }
        
        // We only update via a POST and AJAX request!
        $id = isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 ? (integer)$_POST['id'] : 0;
        $pk = isset($_POST['pk']) && is_numeric($_POST['pk']) && $_POST['pk'] > 0 ? (integer)$_POST['pk'] : 0;
        $aid = isset($_POST['aid']) && is_numeric($_POST['aid']) && $_POST['aid'] > 0 ? (integer)$_POST['aid'] : 0;
        $lid = isset($_POST['lid']) && is_numeric($_POST['lid']) && $_POST['lid'] > 0 ? (integer)$_POST['lid'] : 0;
        $newRecord = isset($_POST['newRecord']) && is_numeric($_POST['newRecord']) && $_POST['newRecord'] > 0 ? (integer)$_POST['newRecord'] : 0;
        
        if($id == 0) {
            // Work around an editable side-effect after adding a new record.
            $id = $pk;
        }
        
        // Verify we have a valid ID!
        if(($aid <= 0 || $id <= 0 || $pk <= 0 || $id !== $pk) && $newRecord == 0) {
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
        
        // Ok, we have what appear to be valid parameters and so
        // it is time to validate and then update the value!
        if (!$newRecord) {
            $model = $this->loadModel($id, $outputFormat);
            $aid = $model->arena_id;
        }
        
        // Load the Arena model and ensure that the user is assigned to it!
        $arena = $this->loadArenaModel($aid, $outputFormat);
        
        // And that the user has permission to update it!
        if(!Yii::app()->user->isRestrictedArenaManager() || !$arena->isUserAssigned($uid)) {
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
        
        // We need to grab and validate the rest of our parameters from the request body
        // We will update one attribute at a time!
        
        // Grab the remaining parameters!
        $name = isset($_POST['name']) ? $_POST['name'] : null;
        $value = isset($_POST['value']) ? $_POST['value'] : null;

        // Validate our remaining parameters!
        if($name === null) {
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
        
        // If it is for a new record, grab and return the arena locations
        if($newRecord >= 1 && $name == 'arena_id') {
            $locations = Arena::getLocationsList($value);
                
            // regardless of the output method, we are going to send a json
            // encoded response!!
            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'locations' => $locations,
                    )
            );
            Yii::app()->end();
        }
        
        // Ok, we have what appear to be valid parameters and so
        // it is time to validate and then update the value!
        $model->$name = $value;
            
        $attribs = array($name);
            
        $valid = $model->validate($attribs);
            
        if(!$valid) {
            $errors = $model->getErrors($name);

            if($outputFormat == "html" || $outputFormat == "xml") {
                $output = '';

                foreach($errors as $error) {
                    if($output == '') {
                        $output = $error;
                    } else {
                        $output .= "\n" . $error;
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
        }
        
        // The attribute is valid and so we should save it!!
        try {
            // We don't blindly save it even though we validated that
            // the user is a restricted manager and that they are
            // assigned to the arena. If it affects zero rows, then the user
            // wasn't authorized and we will throw a 403 error!
            if($value == null) {
                $value = new CDbExpression('NULL');
            }

            if($name == 'arena_id') {
                // Load the new Arena model and ensure that the user is assigned to it!
                $arena = $this->loadArenaModel($value, $outputFormat);
        
                // And that the user has permission to update it!
                if(!Yii::app()->user->isRestrictedArenaManager() || !$arena->isUserAssigned($uid)) {
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
                
                $attributes = array(
                    $name => $value,
                    'location_id' => new CDbExpression('NULL'),
                    'updated_by_id' => $uid,
                    'updated_on' => new CDbExpression('NOW()')
                );
            } elseif($name == 'start_date' || $name == 'start_time' || 
                    $name == 'duration' || $name == 'all_day' ||
                    $name == 'end_date' || $name == 'end_time') {
                $model->autoDurationEndDateTimeStatus(0);
                
                $attributes = array(
                    'all_day' => $model->all_day,
                    'start_date' => $model->start_date,
                    'start_time' => $model->start_time,
                    'end_date' => $model->end_date,
                    'end_time' => $model->end_time,
                    'duration' => $model->duration,
                    'updated_by_id' => $uid,
                    'updated_on' => new CDbExpression('NOW()')
                );
            } else {
                $attributes = array(
                    $name => $value,
                    'updated_by_id' => $uid,
                    'updated_on' => new CDbExpression('NOW()')
                );
            }

            if(!$model->saveAttributes($attributes)) {
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
            
            if($name == 'start_date' || $name == 'start_time' || 
                    $name == 'duration' || $name == 'all_day' ||
                    $name == 'end_date' || $name == 'end_time') {
                // Data has been saved or else we would have thrown an exception
                // Regardless of the output method, we are going to send a json
                // encoded response!!
                $this->sendResponseHeaders(200, 'json');

                echo json_encode(
                        array(
                            'success' => true,
                            'error' => false,
                            'attributes' => $attributes,
                        )
                );
            } elseif($name == 'tags') {
                $model->normalizeTags();
                Tag::model()->updateFrequency($model->oldTags, $model->tags);
            } elseif($name == 'arena_id') {
                // Data has been saved or else we would have thrown an exception
                $locations = Arena::getLocationsList($value);
                
                // regardless of the output method, we are going to send a json
                // encoded response!!
                $this->sendResponseHeaders(200, 'json');

                echo json_encode(
                        array(
                            'success' => true,
                            'error' => false,
                            'locations' => $locations,
                        )
                );
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
     * Deletes an array of models.
     * If delete is successful, there is no output, otherwise we output an error.
     */
    public function actionExportEvents()
    {
        Yii::trace("In actionExportEvents.", "application.controllers.EventController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
            $outputFormat = $_POST['output'];
        }
        
        // We only delete via a POST and AJAX request!
        
        $events = isset($_POST['events']) && is_array($_POST['events']) && count($_POST['events']) > 0 ? $_POST['events'] : array();
        $count = count($events);
        
        // Verify we have a valid array of ids and aids!
        if($count <= 0) {
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
        
        // We will have to do this in a way that ensures that the events
        // being exported are associated with arenas that the user is assigned to.
        // We do this as the external_id of the events will be updated with the 
        // event ID so that the user can later re-import them.
        // To do this, we will build a custom SELECT and UPDATE query that the Event model
        // will contain as a static method. 
        // Before we get to that point, we will at least ensure that the user
        // is in fact authorized to even make the request.
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
        
        try {
            $data = null;
            $data = Event::exportByArray($uid, $events);
            if($data == null || count($data) == 0) {
                $output = 'Failed to export the records as the update was either unauthorized or because too many rows would be exported.';

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
            
            // Ok, we have our data so we need to build up a CSV record and then
            // send that to the browser in a manner that will force it to download
            // the CSV file!
            $header = array_keys($data[0]);
            
            $csv = $this->arrayToCsv($header);
            $csv .= "\r\n";
            
            foreach($data as $event) {
                $csv .= $this->arrayToCsv($event);
                $csv .= "\r\n";
            }
            
            header("Cache-Control: ");
            //header("Contact-type: application/download");
            header("Content-type: text/plain");
            header('Content-Disposition: attachment; filename="EventExport_' . time() . '_.csv"');

            echo $csv;
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
     * Deletes an array of models.
     * If delete is successful, there is no output, otherwise we output an error.
     */
    public function actionDeleteEvents()
    {
        Yii::trace("In actionDeleteEvents.", "application.controllers.EventController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
            $outputFormat = $_POST['output'];
        }
        
        // We only delete via a POST and AJAX request!
        
        $events = isset($_POST['events']) && is_array($_POST['events']) && count($_POST['events']) > 0 ? $_POST['events'] : array();
        $count = count($events);
        
        // Verify we have a valid array of ids and aids!
        if($count <= 0) {
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
        
        // We will have to do this in a way that ensures that the events
        // being deleted are associated with arenas that the user is assigned to.
        // To do this, we will build a custom DELETE query that the Event model
        // will contain as a static method. 
        // Before we get to that point, we will at least ensure that the user
        // is in fact authorized to even make the request.
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
        
        try {
            if(!Event::deleteByArray($uid, $events)) {
                $output = 'Failed to delete the records as the update was either unauthorized or because too many rows would be deleted.';

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
     * If delete is successful, there is no output, otherwise we output an error.
     */
    public function actionDeleteEvent()
    {
        Yii::trace("In actionDeleteEvent.", "application.controllers.EventController");
        
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
        
        // Verify we have a valid ID!
        if($aid <= 0 || $id <= 0 || $pk <= 0 || $id !== $pk) {
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
        
        $model = $this->loadModel($id, $outputFormat);
        
        $aid = $model->arena_id;
        
        // Load the Arena model and ensure that the user is assigned to it!
        $arena = $this->loadArenaModel($aid, $outputFormat);
        
        // And that the user has permission to update it!
        if(!Yii::app()->user->isRestrictedArenaManager() || !$arena->isUserAssigned($uid)) {
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
        
        $transaction = null;
        
        try {
            $model = $this->loadModel($id);
            
//            $model->tags = "";
            
            $transaction = Yii::app()->db->beginTransaction();
            
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
            
//            Tag::model()->updateFrequency($model->oldTags, $model->tags);
            $transaction->commit();
        } catch (Exception $ex) {
            if($transaction && $transaction->active) {
                $transaction->rollback();
            }
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
        if(Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $transaction = null;
            
            try
            {
                $model = $this->loadModel($id);
            
//                $model->tags = "";
            
                $transaction = Yii::app()->db->beginTransaction();
            
                $model->delete();

//                Tag::model()->updateFrequency($model->oldTags, $model->tags);
            
                $transaction->commit();
                
                // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                if (!isset($_GET['ajax'])) {
                    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
                }
            } catch (Exception $ex) {
                if($transaction && $transaction->active) {
                    $transaction->rollback();
                }
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
        } else {
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
        }
    }

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->actionGetSearch();
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Event('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Event'])) {
			$model->attributes=$_GET['Event'];
		}

		$this->render('admin',array(
			'model'=>$model,
		));
	}

    /**
     * Main page to upload many events through a data file
     */
    public function actionUploadEvents()
    {
        if(!Yii::app()->user->isRestrictedArenaManager() || !$this->arena->isUserAssigned(Yii::app()->user->id)) {
            throw new CHttpException(
                    403,
                    'Permission denied. You are not authorized to perform this action.'
            );
        }
        
        $model = new EventUploadForm();
                
        $model->unsetAttributes();  // clear any default values
		
        if(isset($_POST['EventUploadForm'])) {
            $model->attributes = $_POST['EventUploadForm'];
        }

        // Publish and register our jQuery plugin
        $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
        
        if(Yii::app()->request->isAjaxRequest) {
            $this->renderPartial(
                    '_uploadEvents',
                    array(
                        'model' => $model,
                        'fields' => Event::getImportAttributes(),
                        'arenaId' => $this->arena->id,
                        'arenaName' => $this->arena->name,
                        'eventTypes' => CHtml::listData(Event::getTypes(), 'id', 'display_name'),
                        'path' => $path,
                        'doReady' => false
                    )
            );
        } else {
            $this->registerManagementScripts();
            $this->includeCss = true;
        
            $this->render(
                    'uploadEvents',
                    array(
                        'model' => $model,
                        'fields' => Event::getImportAttributes(),
                        'arenaId' => $this->arena->id,
                        'arenaName' => $this->arena->name,
                        'eventTypes' => CHtml::listData(Event::getTypes(), 'id', 'display_name'),
                        'path' => $path,
                        'doReady' => true
                    )
            );
        }
    }

    public function actionUploadEventsFile()
    {
        if(!Yii::app()->user->isRestrictedArenaManager()) {
            $this->sendResponseHeaders(403);
            echo json_encode(array(
                    'success' => false,
                    'error' => 'Permission denied. You are not authorized to perform this action.'
                )
            );
            Yii::app()->end();
        }
        
        $arenaId = isset($_GET['aid']) ? (integer)$_GET['aid'] : null;

        // Ensure we have a valid Arena!!!
        if($arenaId === null || $arenaId <= 0) {
            $this->sendResponseHeaders(400);
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Missing or invalid Arena ID.'
                    )
            );
            Yii::app()->end();
        }

        $this->arena = Arena::model()->findByPk($arenaId);
	
        if($this->arena === null || !$this->arena->isUserAssigned(Yii::app()->user->id)) {
            $this->sendResponseHeaders(400);
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Invalid Arena ID: ' . $arenaId . '.'
                    )
            );
            Yii::app()->end();
        }

        $model = new EventUploadForm();
        
        $instanceRetrieved = $model->getUploadFileInstance();
        
        if($instanceRetrieved !== true) {
            $this->sendResponseHeaders(400);
            echo $instanceRetrieved;
            Yii::app()->end();
        }
        
        // The file has been uploaded.
        // Before we save it off, validate with what the uploader sent
        $model->fileSize = isset($_GET['qqtotalfilesize']) ? (integer)$_GET['qqtotalfilesize'] : 0;
        $model->fileName = isset($_GET['EventUploadForm']['fileName']) ? $_GET['EventUploadForm']['fileName'] : '';

        $isValid = $model->isValidUploadedFile();
            
        if($isValid !== true) {
            // What was suppose to be uploaded doesn't match what we got
            $this->sendResponseHeaders(400);
            echo $isValid;                
            Yii::app()->end();
        }

        $isDirPrepared = $model->prepareUploadDirectory(
                FileUpload::TYPE_EVENT_CSV,
                Yii::app()->user->id,
                $arenaId
        );

        if($isDirPrepared !== true) {
            // Unable to prepare the upload directory
            $this->sendResponseHeaders(500);
            echo $isDirPrepared;                
            Yii::app()->end();
        }

        // We need to save it so we can process it later!
        // Our path is valid, now we must save our temp file to it!
        $fileSaved = $model->saveUploadedFile();
            
        // Ok, we can safely save off the file!
        if($fileSaved !== true) {
            // Something went horribly wrong!!!
            $this->sendResponseHeaders(500);
            echo $fileSaved;                    
            Yii::app()->end();
        }

        // File has been saved, now we make a record of it!!
        try {
            $fileRecordSaved = $model->saveUploadedFileRecord();
            
            if($fileRecordSaved !== true) {
                // Something went horribly wrong!!!
                $this->sendResponseHeaders(500);
                echo $fileRecordSaved;
                Yii::app()->end();
            }
        } catch (CDbException $ex) {
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
        
        // We are ready to process the file so let the UI know that we are ready for it!
        $this->sendResponseHeaders(200);
        echo $model->getJsonSuccessResponse(
                $this->createUrl('event/uploadEventsFileDelete'),
                $this->createUrl('event/uploadEventsProcessCSV')
        );
        Yii::app()->end();
    }
    
    public function actionUploadEventsFileDelete()
    {
        if(!Yii::app()->user->isRestrictedArenaManager()) {
            $this->sendResponseHeaders(403);
            echo json_encode(array(
                    'success' => false,
                    'error' => 'Permission denied. You are not authorized to perform this action.'
                )
            );
            Yii::app()->end();
        }
        
        $isDeleteMethod = $this->isDeleteMethod();
        
        // This needs to come through as an actual DELETE request!!!
        if($isDeleteMethod !== true) {
            $this->sendResponseHeaders(400);
            echo $isDeleteMethod;
            Yii::app()->end();
        }
        
        $paramstr =  $this->getParamsFromPhp();

        if($paramstr === false) {
            $this->sendResponseHeaders(400);
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Unable to read the parameters',
                    )
            );
            Yii::app()->end();
        }
        
        // explode the parameters!
        parse_str($paramstr);
        
        // We expect the database id of the file_upload record (fid)
        // to be sent in the delete request. We also expect the file name
        // (name) to be sent in the delete request.

        if(!isset($fid) || !isset($name)) {
            $this->sendResponseHeaders(400);
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Missing expected parameters',
                    )
            );
            Yii::app()->end();
        }
        
        // Delete the file and send the response!
        $this->sendResponseHeaders(200);
        echo RinkfinderUploadForm::deleteUploadedFile($fid, $name, FileUpload::TYPE_EVENT_CSV);
        Yii::app()->end();
    }
    
    public function actionUploadEventsProcessCSV()
    {
        if(!Yii::app()->user->isRestrictedArenaManager()) {
            $this->sendResponseHeaders(403);
            echo json_encode(array(
                    'success' => false,
                    'error' => 'Permission denied. You are not authorized to perform this action.'
                )
            );
            Yii::app()->end();
        }
        
        $step = isset($_GET['step']) ? (integer)$_GET['step'] : null;
 
        if(!isset($step)) {
            $this->sendResponseHeaders(400);
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Missing expected parameters',
                    )
            );
            Yii::app()->end();
        }
        
        switch ($step) {
            case 2:
                // This is a request to set the processing options and return
                // the database fields, the csv header fields, and a preview
                // row based on the options sent.
                $this->uploadEventsProcessCSVStep2();
                break;
            case 3:
                $this->uploadEventsProcessCSVStep3();
                break;
            default:
                $this->sendResponseHeaders(400);
                echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Invalid step specified.',
                    )
                );
                Yii::app()->end();
        }
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Event the loaded model
     * @throws CHttpException
     */
    public function loadModel($id, $outputFormat = 'html')
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
     * @param Event $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax'] === 'event-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
    
    protected function uploadEventsProcessCSVStep2()
    {
        $isGetMethod = $this->isGetMethod();
        
        // This needs to come through as an actual GET request!!!
        if($isGetMethod !== true) {
            echo $isGetMethod;
            Yii::app()->end();
        }

        // We know we are on step 2, so grab the parameters we need
        $fid = isset($_GET['fileUpload']['id']) ? (integer)$_GET['fileUpload']['id'] : null;
        $name = isset($_GET['fileUpload']['name']) ? $_GET['fileUpload']['name'] : null;
        $upload_type_id = isset($_GET['fileUpload']['upload_type_id']) ? $_GET['fileUpload']['upload_type_id'] : null;
        $arenaId = isset($_GET['arenaId']) ? $_GET['arenaId'] : null;
        $eventType = isset($_GET['eventType']) ? $_GET['eventType'] : null;
        $skipRows = isset($_GET['csvOptions']['skipRows']) ? (integer)$_GET['csvOptions']['skipRows'] : null;
        $delimiter = isset($_GET['csvOptions']['delimiter']) ? $_GET['csvOptions']['delimiter'] : null;
        $enclosure = isset($_GET['csvOptions']['enclosure']) ? $_GET['csvOptions']['enclosure'] : null;
        $escapeChar = isset($_GET['csvOptions']['escapeChar']) ? $_GET['csvOptions']['escapeChar'] : null;
        
        // ensure all parameters have been passed in.
        if(!isset($fid) || !isset($name) || !isset($upload_type_id) ||
           !isset($skipRows) || !isset($delimiter) || !isset($enclosure) ||
           !isset($escapeChar) || !isset($arenaId) || !isset($eventType)) {
            $this->sendResponseHeaders(400);
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Missing expected parameters',
                    )
            );
            Yii::app()->end();
        }
        
        // Find our uploaded record
        $fileUpload = FileUpload::model()->find(
                'id = :fid AND upload_type_id = :upload_type_id '
                . 'AND name = :name AND arena_id = :arenaId',
                array(
                    ':fid' => $fid,
                    ':upload_type_id' => $upload_type_id,
                    ':name' => $name,
                    ':arenaId' => $arenaId
                )
        );
        
        if($fileUpload === null) {
            $this->sendResponseHeaders(400);
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Unable to locate existing database file record',
                    )
            );
            Yii::app()->end();
        }
        
        // We have the database record, now open the CSV file and process it
        // based on the options.

        // In case tab is selected as delimiter
        $delimiter = str_replace("\\t", "\t", $delimiter);
        
        $csvImporter = new CsvImporter(
                $fileUpload->path . DIRECTORY_SEPARATOR . $fileUpload->name,
                true,
                $skipRows,
                $delimiter,
                $enclosure,
                $escapeChar
        );

        $csvFile = $csvImporter->open();
        
        if($csvFile !== true) {
            $this->sendResponseHeaders(400);
            echo $csvFile;
            Yii::app()->end();
        }
        
        $csvHeader = $csvImporter->getHeader();
        $csvRow = $csvImporter->getRows(1);
        $csvImporter->close();
        $tableFields = Event::getImportAttributes();
        
        $this->sendResponseHeaders(200);
        
        echo json_encode(
                array(
                    'success' => true,
                    'error' => false,
                    'csvFields' => $csvHeader,
                    'csvRows' => $csvRow,
                    'tableFields' => $tableFields,
                )
        );
        Yii::app()->end();
    }
    
    protected function uploadEventsProcessCSVStep3()
    {
        $isPostMethod = $this->isPostMethod();
        
        // This needs to come through as an actual PUT request!!!
        if($isPostMethod !== true) {
            $this->sendResponseHeaders(400);
            echo $isPostMethod;
            Yii::app()->end();
        }

        $paramstr =  $this->getParamsFromPhp();

        if($paramstr === false) {
            $this->sendResponseHeaders(400);
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Unable to read the parameters',
                    )
            );
            Yii::app()->end();
        }
        
        // explode the parameters!
        parse_str($paramstr);
        
        // ensure all parameters have been passed in.
        if(!isset($step) || !isset($fileUpload) || !isset($csvOptions) ||
           !isset($mappings) || !isset($arenaId) || !isset($eventType)) {
            $this->sendResponseHeaders(400);
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Missing expected parameters',
                    )
            );
            Yii::app()->end();
        }
        
        // Find our uploaded record
        $fileUploadRecord = FileUpload::model()->find(
                'id = :fid AND upload_type_id = :upload_type_id '
                . 'AND name = :name AND arena_id = :arenaId',
                array(
                    ':fid' => $fileUpload['id'],
                    ':upload_type_id' => $fileUpload['upload_type_id'],
                    ':name' => $fileUpload['name'],
                    ':arenaId' => $arenaId
                )
        );
        
        if($fileUploadRecord === null) {
            $this->sendResponseHeaders(400);
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Unable to locate existing database file record',
                    )
            );
            Yii::app()->end();
        }
        
        // We have the database record, now open the CSV file and process it
        // based on the options.

        // In case tab is selected as delimiter
        $csvOptions['delimiter'] = str_replace("\\t", "\t", $csvOptions['delimiter']);
        
        $tableName = 'event';
        $selectFields = array('id', 'external_id');
        $inFields = array('external_id');
        $createFields = array(
            'created_by_id' => Yii::app()->user->id,
            'created_on' => new CDbExpression("NOW()")
        );
        $updateFields = array(
            'updated_by_id' => Yii::app()->user->id,
            'updated_on' => new CDbExpression("NOW()") //date('Y-m-d H:i:s')
        );
        
        $tableImporter = new EventTableImporter(
                $tableName,
                $selectFields,
                $inFields,
                $createFields,
                $updateFields,
                $csvOptions,
                $fileUploadRecord,
                $mappings
        );
        
        try {
            $tableImporter->setArenaId($arenaId);
            $tableImporter->setTypeId($eventType);
            $tableImporter->doImport();
        } catch (CDbException $ex) {
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
       
        try {
            // Auto tag the updated records!!!
            $transaction = Yii::app()->db->beginTransaction();
            $events = Event::model()->findAll(
                    array(
                        'condition' => 't.updated_by_id = :uid AND t.arena_id = :arenaId',
                        'order' => 't.updated_on DESC',
                        'limit' => $tableImporter->getRowsInserted() + $tableImporter->getRowsUpdated(),
                        'params' => array(
                            ':uid' => Yii::app()->user->id,
                            ':arenaId' => $arenaId
                        ),
                        'with' => array(
                            'arena' => array('select' => 'name, city, state'),
                            'type' => array('select' => 'display_name')
                        ),
                        'together' => true,
                    )
            );
            
            $sql = 'SELECT id FROM event_status WHERE name = :name';
            $command = Yii::app()->db->createCommand($sql);
            $tempid = $command->queryScalar(array(':name' => 'EXPIRED'));
                
            foreach($events as $event) {
                $event->autoTag();
                $event->autoDurationEndDateTimeStatus($tempid);
                
                if(!$event->save()) {
                    Yii::log('Unable to save auto tags for Event', CLogger::LEVEL_ERROR, 'application.controllers');
                }
            }
            $transaction->commit();
            $autoTagged = true;
        } catch (Exception $ex) {
            $autoTagged = false;
            if($transaction->getActive()) {
                $transaction->rollback();
            }

            $errorInfo = null;
            
            Yii::log(
                    'Exception during auto tags for Event: ' . 
                    json_encode(
                            array(
                                'success' => false,
                                'error' => $ex->getMessage(),
                                'exception' => true,
                                'errorCode' => $ex->getCode(),
                                'errorFile' => $ex->getFile(),
                                'errorLine' => $ex->getLine(),
                                'errorInfo' => $errorInfo,
                            )),
                    CLogger::LEVEL_ERROR, 'application.controllers');
        }
        
        // Save the file import information to the database
        try {
            $fileImport = new FileImport();
            
            $fileImport->file_upload_id = $tableImporter->getFileUploadId();
            $fileImport->table_count = 1;
            $fileImport->tables = 'event';
            $fileImport->total_records = $tableImporter->getRowsTotal();
            $fileImport->total_updated = $tableImporter->getRowsUpdated();
            $fileImport->total_created = $tableImporter->getRowsInserted();
            $fileImport->auto_tagged = $autoTagged;
            
            if(!$fileImport->save()) {
                Yii::log('Unable to save fileImport for Event', CLogger::LEVEL_ERROR, 'application.controllers');
            }
        } catch (Exception $ex) {
            $errorInfo = null;
            
            Yii::log(
                    'Exception saving fileImport for Event: ' . 
                    json_encode(
                            array(
                                'success' => false,
                                'error' => $ex->getMessage(),
                                'exception' => true,
                                'errorCode' => $ex->getCode(),
                                'errorFile' => $ex->getFile(),
                                'errorLine' => $ex->getLine(),
                                'errorInfo' => $errorInfo,
                            )),
                    CLogger::LEVEL_ERROR, 'application.controllers');
        }
        
        // Data has been imported so let the user know!
        $this->sendResponseHeaders(200);
        $response = json_decode($tableImporter->getJsonSuccessResponse(), true);
        $response['importSummary']['autoTagged'] = $autoTagged;
        
        echo json_encode($response);
        
        Yii::app()->end();
    }
    
    /**
     * @desc Returns the arena model based on the primary key given in the GET variable.
     * If the arena model is not found, an HTTP exception will be raised.
     * @param integer $arenaId the ID of the arena to be loaded
     * @return Arena the loaded arena
     * @throws CHttpException
     */
    protected function loadArena($arenaId)
    {
        // If the arena property is null, created based on the passed in id
        if($this->arena === null) {
            $this->arena = Arena::model()->findByPk($arenaId);
	
            if($this->arena === null) {
                throw new CHttpException(
                        404,
                        'The requested arena does not exist.'
                );
            }
        }

        return $this->arena;
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
     * @desc In-class defined filter method, configured for use in the above
     * filters() method. It is called before the admin, create, uploadEvents,
     * and index actions are run in order to ensures a proper arena context
     * has been set.
     * @param FilterChain $filterChain the chain of filters
     */
    public function filterArenaContext($filterChain)
    {
        // Set the arena identifier based on GET input request variables
        if(isset($_GET['aid'])) {
            $this->loadArena($_GET['aid']);
        } else {
            throw new CHttpException(
                    400,
                    'Bad request. You must specify a valid arena before'
                    . ' performing this action'
            );
        }

        // Run the other filters and execute the requested action
        $filterChain->run();
    }

}