<?php

class ArenaController extends Controller
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
            'postOnly + delete assignManager updateAttribute deleteArena createArena',
            'ajaxOnly + uploadArenasFileDelete uploadArenasProcessCSV assignManager updateAttribute deleteArena createArena',
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
                    'index',
                    'view',
                    'mapMarkers',
                ),
                'users' => array(
                    '*'
                ),
            ),
            array(
                'allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(
                    'update',
                    'updateAttribute',
                    'assignManager'
                ),
                'users' => array(
                    '@'
                ),
            ),
            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array(
                    'create',
                    'createArena',
                    'viewArena',
                    'uploadArenas',
                    'uploadArenasFile',
                    'uploadArenasFileDelete',
                    'uploadArenasProcessCSV',
                    'admin',
                    'delete',
                    'deleteArena'
                ),                
                'roles' => array('ApplicationAdministrator'),
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
     * Displays a particular model.
     */
    public function actionViewArena()
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
        
        $model = null;
        
        // Always restrict to the currently logged in user!
        $uid = Yii::app()->user->id;
        
        $data = null;
        
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
                    'new' => $this->createUrl('/event/createEvent'),
                    'update' => $this->createUrl('/event/updateAttribute'),
                    'view' => $this->createUrl('/event/view'),
                    'delete' => $this->createUrl('/event/deleteEvent')
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
                'user' => array(
                    'new' => $this->createUrl('/user/createUser'),
                    'update' => $this->createUrl('/user/updateAttribute'),
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
            $this->pageTitle = Yii::app()->name . ' - Account & Profile!';
            $this->breadcrumbs = array(
                'Management' => array('/management/index'),
                'Facilities' => array('/management/index'),
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
                            'newRecord' => false,
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
                            'newRecord' => false,
                            'path' => $path,
                            'doReady' => 1
                        ));
            }
        }
    }
    
    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        Yii::trace("In actionView.", "application.controllers.ArenaController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        $open = isset($_GET['open']) &&  isset($_GET['open']) == 'false' ? false : true;
        $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 0;
        $price = isset($_GET['price']) ? $_GET['price'] : null;
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d", time());
        $end_date = date('Y-m-t', strtotime($start_date));
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : null;
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : null;
        $types = isset($_GET['types']) ? $_GET['types'] : array();
        
        if(is_null($id) || !is_numeric($id) || $id <= 0) {
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
            $data = Arena::getViewWithContactsLocations($id, array(
                'offset' => $offset,
                'limit' => $limit,
                'open' => $open,
                'price' => $price,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'types' => $types
               )
            );
            
            // Ensure we got results. If no results then throw a 404!
            if(!isset($data['id']) || !isset($data['arena_name'])) {
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
            
            $xml = Controller::generate_valid_xml_from_array($data, "view", "arena");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));            

            if(YII_DEBUG) {
                Yii::app()->clientScript->registerScriptFile($path . '/js/arena/view.js', CClientScript::POS_END);
            } else {
                Yii::app()->clientScript->registerScriptFile($path . '/js/arena/view.min.js', CClientScript::POS_END);
            }
            
            $this->breadcrumbs = array(
                'Facilities' => $this->createUrl('arena/index'),
                isset($data['arena_name']) ? CHtml::encode($data['arena_name']) : ''
            );

            $this->registerUserScripts();
            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "_view",
                        array(
                            'data' => $data,
                            'start_date' => $start_date,
                            'doReady' => false,
                            'path' => $path,
                        ));
            } else {
                $this->render(
                        "view",
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
        // Always grab the currently logged in user's ID.
        $uid = Yii::app()->user->id;
        
        // Ensure that the user has permission to create it!
        if(!Yii::app()->user->isApplicationAdministrator()) {
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
        
        $model = new Arena;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Arena'])) {
            $model->attributes = $_POST['Arena'];
            $model->autoTag();
            if(isset($model->external_id) && $model->external_id == '') {
                $model->external_id = null;
            }
            
            if ($model->save()) {
                $model->assignUsers($uid, User::getAllAdminIds());
                $this->redirect(array('view','id'=>$model->id));
            }
        }

        $this->render('create',array(
                'model' => $model,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the primary key of the new location is returned
     * along with the tags!
     * Otherwise if there are validation errors, those are returned as well.
     */
    public function actionCreateArena()
    {
        Yii::trace("In actionCreateArena.", "application.controllers.ArenaController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
            $outputFormat = $_POST['output'];
        }
        
        // Always grab the currently logged in user's ID.
        $uid = Yii::app()->user->id;
        
        // Ensure that the user has permission to create it!
        if(!Yii::app()->user->isApplicationAdministrator()) {
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
        $model = new Arena;

        $model->external_id = isset($_POST['external_id']) && is_string($_POST['external_id']) && strlen($_POST['external_id']) > 0 ? $_POST['external_id'] : null;
        $model->name = isset($_POST['name']) && is_string($_POST['name']) && strlen($_POST['name']) > 0 ? $_POST['name'] : null;
        $model->address_line1 = isset($_POST['address_line1']) && is_string($_POST['address_line1']) && strlen($_POST['address_line1']) > 0 ? $_POST['address_line1'] : null;
        $model->address_line2 = isset($_POST['address_line2']) && is_string($_POST['address_line2']) && strlen($_POST['address_line2']) > 0 ? $_POST['address_line2'] : null;
        $model->city = isset($_POST['city']) && is_string($_POST['city']) && strlen($_POST['city']) > 0 ? $_POST['city'] : null;
        $model->state = isset($_POST['state']) && is_string($_POST['state']) && strlen($_POST['state']) == 2 ? $_POST['state'] : null;
        $model->zip = isset($_POST['zip']) && is_numeric($_POST['zip']) && strlen($_POST['zip']) == 5 ? $_POST['zip'] : null;
        $model->lat = isset($_POST['lat']) && is_numeric($_POST['lat']) ? (float)$_POST['lat'] : null;
        $model->lng = isset($_POST['lng']) && is_numeric($_POST['lng']) ? (float)$_POST['lng'] : null;
        $model->logo = isset($_POST['logo']) && is_string($_POST['logo']) && strlen($_POST['logo']) > 0 ? $_POST['logo'] : null;
        $model->url = isset($_POST['url']) && is_string($_POST['url']) && strlen($_POST['url']) > 0 ? $_POST['url'] : null;
        $model->description = isset($_POST['description']) && is_string($_POST['description']) && strlen($_POST['description']) > 0 ? $_POST['description'] : null;
        $model->notes = isset($_POST['notes']) && is_string($_POST['notes']) && strlen($_POST['notes']) > 0 ? $_POST['notes'] : null;
        $model->tags = isset($_POST['tags']) && is_string($_POST['tags']) && strlen($_POST['tags']) > 0 ? $_POST['tags'] : null;
        $model->status_id = isset($_POST['status_id']) && is_numeric($_POST['status_id']) ? (integer)$_POST['status_id'] : 1;
        $model->phone = isset($_POST['phone']) && is_numeric($_POST['phone']) && strlen($_POST['phone']) == 10 ? $_POST['phone'] : null;
        $model->ext = isset($_POST['ext']) && is_numeric($_POST['ext']) ? $_POST['ext'] : null;
        $model->fax = isset($_POST['fax']) && is_numeric($_POST['fax']) && strlen($_POST['fax']) == 10 ? $_POST['fax'] : null;
        $model->fax_ext = isset($_POST['fax_ext']) && is_numeric($_POST['fax_ext']) ? $_POST['fax_ext'] : null;
        $model->created_by_id = $uid;
        $model->created_on = new CDbExpression('NOW()');
        $model->updated_by_id = $uid;
        $model->updated_on = new CDbExpression('NOW()');
        
        // Ok, we auto-tag it before we save!
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
                echo 'id:' . $model->id . ',tags:' . $model->tags;
                Yii::app()->end();
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
                            'tags' => $model->tags
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
        $model = $this->loadModel($id);

        // Always grab the currently logged in user's ID.
        $uid = Yii::app()->user->id;
        
        // Ensure that the user has permission to create it!
        if(!Yii::app()->user->isApplicationAdministrator() || !$model->isUserAssigned($uid)) {
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
        
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Arena'])) {
            $model->attributes = $_POST['Arena'];
            if(isset($model->external_id) && $model->external_id == '') {
                $model->external_id = new CDbExpression('NULL');
            }
            if($model->save()) {
                $this->redirect(array('view','id' => $model->id));
            }
        }

        $this->render('update',array(
                'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful there is no output
     */
    public function actionUpdateAttribute()
    {
        Yii::trace("In actionUpdateAttribute.", "application.controllers.ArenaController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
            $outputFormat = $_POST['output'];
        }
        
        // We only update via a POST and AJAX request!
        $id = isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 ? (integer)$_POST['id'] : 0;
        $pk = isset($_POST['pk']) && is_numeric($_POST['pk']) && $_POST['pk'] > 0 ? (integer)$_POST['pk'] : 0;
        
        if($id == 0) {
            // Work around an editable side-effect after adding a new record.
            $id = $pk;
        }
        
        // Verify we have a valid ID!
        if($id <= 0 || $pk <= 0 || $id !== $pk) {
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
        
        // Parameters look good so now verify that the arena model exists!
        $model = $this->loadModel($id, $outputFormat);
        
        // Always grab the currently logged in user's ID.
        $uid = Yii::app()->user->id;

        
        // And that the user has permission to update it!
        if(!Yii::app()->user->isRestrictedArenaManager() || !$model->isUserAssigned($uid)) {
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

        if($value == '') {
            $value = null;
        }
        
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
            
        // Ok, we have what appear to be valid parameters and so
        // it is time to validate and then update the value!
        if($name == 'geocoding') {
            $model->lat = $value[0];
            $model->lng = $value[1];
            
            $attribs = array('lat', 'lng');
        } else {
            $model->$name = $value;
            
            $attribs = array($name);
        }        

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
            // the user is a restricted manager and they are assigned to
            // the arena. If it affects zero rows, then the user
            // wasn't authorized and we will throw a 403 error!
            if($name == 'geocoding') {
                $attributes = array(
                    'lat' => $value[0],
                    'lng' => $value[1],
                    'updated_by_id' => $uid,
                    'updated_on' => new CDbExpression('NOW()')
                );
            } else {
                if($value == null) {
                    $value = new CDbExpression('NULL');
                }

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
            
            if($name == 'tags') {
                $model->normalizeTags();
                Tag::model()->updateFrequency($model->oldTags, $model->tags);
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
     * Assigns or unassigns managers from a Facility
     * If update is successful there is no output.
     */
    public function actionAssignManager()
    {
        Yii::trace("In actionAssignManager.", "application.controllers.ArenaController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
            $outputFormat = $_POST['output'];
        }
        
        // We only update via a POST and AJAX request!
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
        
        // Parameters look good so now verify that the arena model exists!
        $model = $this->loadModel($aid, $outputFormat);
        
        // Always grab the currently logged in user's ID.
        $uid = Yii::app()->user->id;

        
        // And that the user has permission to update it!
        if(!Yii::app()->user->isArenaManager() || !$model->isUserAssigned($uid)) {
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
            
        try {
            $success = false;
            
            if($name == 'assign') {
                $success = $model->assignUsers($uid, $value);
            } elseif($name == 'unassign') {
                $success = $model->unassignUsers($uid, $value);
            }

            if(!$success) {
                $output = 'Unknown action or the requested action was unauthorized.';

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
    public function actionDeleteArena()
    {
        Yii::trace("In actionDeleteArena.", "application.controllers.ArenaController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
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
        
        // Ensure that the user has permission to delete it!
        if(!Yii::app()->user->isApplicationAdministrator()) {
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
            $model = $this->loadModel($id, $outputFormat);
            
//            $model->tags = "";
            
            $transaction = Yii::app()->db->beginTransaction();
            
            // Before we go and delete the facility, we need to iterate through
            // each venue and their events so that the tags can be updated!
            foreach($model->locations as $location) {
//                $location->tags = "";
//                Tag::model()->updateFrequency($location->oldTags, $location->tags);
                
                foreach($location->events as $event) {
//                    $event->tags = "";
//                    Tag::model()->updateFrequency($event->oldTags, $event->tags);
                    $event->delete();
                }
                $location->delete();
            }
            
            // Now that the locations are deleted, check for any dangling events!
            foreach($model->events as $event) {
//                $event->tags = "";
//                Tag::model()->updateFrequency($event->oldTags, $event->tags);
                $event->delete();
            }
            
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
        // Always grab the currently logged in user's ID.
        $uid = Yii::app()->user->id;
        
        // Ensure that the user has permission to create it!
        if(!Yii::app()->user->isApplicationAdministrator()) {
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
        
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $transaction = null;
            
            try
            {
                $model = $this->loadModel($id);
            
//                $model->tags = "";
            
                $transaction = Yii::app()->db->beginTransaction();
                
                // Before we go and delete the facility, we need to iterate through
                // each venue and their events so that the tags can be updated!
                foreach($model->locations as $location) {
//                    $location->tags = "";
//                    Tag::model()->updateFrequency($location->oldTags, $location->tags);
                
                    foreach($location->events as $event) {
//                        $event->tags = "";
//                        Tag::model()->updateFrequency($event->oldTags, $event->tags);
                        $event->delete();
                    }
                    $location->delete();
                }
            
                // Now that the locations are deleted, check for any dangling events!
                foreach($model->events as $event) {
//                    $event->tags = "";
//                    Tag::model()->updateFrequency($event->oldTags, $event->tags);
                    $event->delete();
                }
            
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
        Yii::trace("In actionIndex.", "application.controllers.ArenaController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d", time());
        $end_date = date('Y-m-t', strtotime($start_date));
        
        $data = null;
        
        // Try and get the data!
        try {
            $data = Arena::getIndexWithContactsEvents(array(
                'start_date' => $start_date,
                'end_date' => $end_date
                )
            );
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
            
            $xml = Controller::generate_valid_xml_from_array($data, "index", "arena");
            echo $xml;
            
            Yii::app()->end();
        } else {
            // We default to html!
            // Publish and register our jQuery plugin
            $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));            

            if(YII_DEBUG) {
                Yii::app()->clientScript->registerScriptFile($path . '/js/arena/index.js', CClientScript::POS_END);
            } else {
                Yii::app()->clientScript->registerScriptFile($path . '/js/arena/index.min.js', CClientScript::POS_END);
            }
            
            $this->breadcrumbs = array(
                'Facilities',
            );

            $this->registerUserScripts();
            $this->includeCss = true;
            $this->navigation = true;

            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial(
                        "index",
                        array(
                            'start_date' => $start_date,
                            'data' => $data,
                            'doReady' => false,
                            'path' => $path,
                        ));
            } else {
                $this->render(
                        "index",
                        array(
                            'start_date' => $start_date,
                            'data' => $data,
                            'doReady' => true,
                            'path' => $path,
                        ));
            }
        }
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        // Always grab the currently logged in user's ID.
        $uid = Yii::app()->user->id;
        
        // Ensure that the user has permission to create it!
        if(!Yii::app()->user->isApplicationAdministrator()) {
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
        
        $model = new Arena('search');
        $model->unsetAttributes();  // clear any default values
        
        if(isset($_GET['Arena'])) {
            $model->attributes = $_GET['Arena'];
        }
        
        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Main page to upload many areans through a data file
     */
    public function actionUploadArenas()
    {
        Yii::trace("In actionUploadArenas.", "application.controllers.ArenaController");
        
        if(!Yii::app()->user->checkAccess('uploadArena')) {
            throw new CHttpException(
                    403,
                    'Permission denied. You are not authorized to perform this action.'
            );
        }
        
        $model = new ArenaUploadForm();
                
        $model->unsetAttributes();  // clear any default values
		
        if(isset($_POST['ArenaUploadForm'])) {
            $model->attributes = $_POST['ArenaUploadForm'];
        }

        // Publish and register our jQuery plugin
        $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
        
        if(Yii::app()->request->isAjaxRequest) {
            $this->renderPartial(
                    'uploadArenas',
                    array(
                        'model' => $model,
                        'fields' => Arena::getImportAttributes(),
                        'path' => $path,
                        'doReady' => false
                    )
            );
        } else {
            $this->registerAdministrationScripts();
            
            $this->includeCss = true;

            $this->render(
                    'uploadArenas',
                    array(
                        'model' => $model,
                        'fields' => Arena::getImportAttributes(),
                        'path' => $path,
                        'doReady' => true
                    )
            );
        }
    }

    /**
     * Action method to upload a file.
     */
    public function actionUploadArenasFile()
    {
        if(!Yii::app()->user->checkAccess('uploadArena')) {
            $this->sendResponseHeaders(403);
            echo json_encode(array(
                    'success' => false,
                    'error' => 'Permission denied. You are not authorized to perform this action.'
                )
            );
            Yii::app()->end();
        }
        
        $model = new ArenaUploadForm();
        
        $instanceRetrieved = $model->getUploadFileInstance();
        
        if($instanceRetrieved !== true) {
            $this->sendResponseHeaders(400);
            echo $instanceRetrieved;
            Yii::app()->end();
        }
        
        // The file has been uploaded.
        // Before we save it off, validate with what the uploader sent
        $model->fileSize = isset($_GET['qqtotalfilesize']) ? (integer)$_GET['qqtotalfilesize'] : 0;
        $model->fileName = isset($_GET['ArenaUploadForm']['fileName']) ? $_GET['ArenaUploadForm']['fileName'] : '';

        $isValid = $model->isValidUploadedFile();
            
        if($isValid !== true) {
            // What was suppose to be uploaded doesn't match what we got
            $this->sendResponseHeaders(400);
            echo $isValid;                
            Yii::app()->end();
        }

        $isDirPrepared = $model->prepareUploadDirectory(
                FileUpload::TYPE_ARENA_CSV,
                Yii::app()->user->id
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
                $this->createUrl('arena/uploadArenasFileDelete'),
                $this->createUrl('arena/uploadArenasProcessCSV')
        );
        Yii::app()->end();
    }
    
    public function actionUploadArenasFileDelete()
    {
        if(!Yii::app()->user->checkAccess('uploadArena')) {
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
        echo RinkfinderUploadForm::deleteUploadedFile($fid, $name, FileUpload::TYPE_ARENA_CSV);
        Yii::app()->end();
    }
    
    public function actionUploadArenasProcessCSV()
    {
        if(!Yii::app()->user->checkAccess('uploadArena')) {
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
                $this->uploadArenasProcessCSVStep2();
                break;
            case 3:
                $this->uploadArenasProcessCSVStep3();
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
     * @return Arena the loaded model
     * @throws CHttpException
     */
    public function loadModel($id, $outputFormat = 'html', $with = false)
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
     * Performs the AJAX validation.
     * @param Arena $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax'] === 'arena-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
    
    protected function uploadArenasProcessCSVStep2()
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
        $skipRows = isset($_GET['csvOptions']['skipRows']) ? (integer)$_GET['csvOptions']['skipRows'] : null;
        $delimiter = isset($_GET['csvOptions']['delimiter']) ? $_GET['csvOptions']['delimiter'] : null;
        $enclosure = isset($_GET['csvOptions']['enclosure']) ? $_GET['csvOptions']['enclosure'] : null;
        $escapeChar = isset($_GET['csvOptions']['escapeChar']) ? $_GET['csvOptions']['escapeChar'] : null;
        
        // ensure all parameters have been passed in.
        if(!isset($fid) || !isset($name) || !isset($upload_type_id) ||
           !isset($skipRows) || !isset($delimiter) || !isset($enclosure) ||
           !isset($escapeChar)) {
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
                'id = :fid AND upload_type_id = :upload_type_id AND name = :name',
                array(
                    ':fid' => $fid,
                    ':upload_type_id' => $upload_type_id,
                    ':name' => $name
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
        $tableFields = Arena::getImportAttributes();
        
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
    
    protected function uploadArenasProcessCSVStep3()
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
           !isset($mappings)) {
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
                'id = :fid AND upload_type_id = :upload_type_id AND name = :name',
                array(
                    ':fid' => $fileUpload['id'],
                    ':upload_type_id' => $fileUpload['upload_type_id'],
                    ':name' => $fileUpload['name']
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
        
        $tableName = 'arena';
        $selectFields = array('id', 'name', 'city', 'state');
        $inFields = array('name', 'city', 'state');
        $createFields = array(
            'created_by_id' => Yii::app()->user->id,
            'created_on' => new CDbExpression("NOW()")
        );
        $updateFields = array(
            'updated_by_id' => Yii::app()->user->id,
            'updated_on' => new CDbExpression("NOW()") //date('Y-m-d H:i:s')
        );
        
        $tableImporter = new TableImporter(
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
        
        $transaction = Yii::app()->db->beginTransaction();
        
        try {
            // Auto tag the updated records!!!
            $arenas = Arena::model()->findAll(
                    array(
                        'condition' => 'updated_by_id = :uid',
                        'order' => 'updated_on DESC',
                        'limit' => $tableImporter->getRowsInserted() + $tableImporter->getRowsUpdated(),
                        'params' => array(
                            ':uid' => Yii::app()->user->id
                        )
                    )
            );
            
            foreach($arenas as $arena) {
                $arena->autoTag();
                
                if(!$arena->save()) {
                    Yii::log('Unable to save auto tags for Arena', CLogger::LEVEL_ERROR, 'application.controllers');
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
            
            Yii::log(
                    'Exception during auto tags for Arena: ' . 
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
                    CLogger::LEVEL_ERROR, 'application.controllers.ArenaController');
        }
        
        $transaction = Yii::app()->db->beginTransaction();
        
        // Ensure that the admins are assigned to the new arenas!
        try {
            User::assignAllAdminsToAllArenas(Yii::app()->user->id);
            $transaction->commit();
        } catch (Exception $ex) {

            if($transaction->getActive()) {
                $transaction->rollback();
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
            
            // Just log the error for now!
            Yii::log(
                    'Exception during assignAllAdminsToAllArenas: ' . 
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
                    CLogger::LEVEL_ERROR, 'application.controllers.ArenaController');
        }
        
        // Save the file import information to the database
        try {
            $fileImport = new FileImport();
            
            $fileImport->file_upload_id = $tableImporter->getFileUploadId();
            $fileImport->table_count = 1;
            $fileImport->tables = 'arena';
            $fileImport->total_records = $tableImporter->getRowsTotal();
            $fileImport->total_updated = $tableImporter->getRowsUpdated();
            $fileImport->total_created = $tableImporter->getRowsInserted();
            $fileImport->auto_tagged = $autoTagged;
            
            if(!$fileImport->save()) {
                Yii::log('Unable to save fileImport for Arena', CLogger::LEVEL_ERROR, 'application.controllers');
            }
        } catch (Exception $ex) {
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
            
            Yii::log(
                    'Exception saving fileImport for Arena: ' . 
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
                    CLogger::LEVEL_ERROR, 'application.controllers.ArenaController');
        }
        
        // Data has been imported so let the user know!
        $this->sendResponseHeaders(200);
        $response = json_decode($tableImporter->getJsonSuccessResponse(), true);
        $response['importSummary']['autoTagged'] = $autoTagged;
        
        echo json_encode($response);
        
        Yii::app()->end();
    }
    
    /**
     * Returns a list of facilities based on the provided coordinates.
     * Can be further restricted by providing a radius, limit, and if only
     * open arenas should be included. Additionally, output is restricted to XML
     * or JSON formats only!
     */
    public function actionMapMarkers()
    {
        Yii::trace("In actionMapMarkers.", "application.controllers.ArenaController");
        
        // Default to XML output!
        $output = "xml";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'json')) {
            $output = $_GET['output'];
        }
        
        // There are two required parameters: lat and lng.
        // Defaults will be used for the others if not provided
        $lat = isset($_GET['lat']) ? $_GET['lat'] : null;
        $lng = isset($_GET['lng']) ? $_GET['lng'] : null;
        $radius = isset($_GET['radius']) ? $_GET['radius'] : 15;
        $open = isset($_GET['open']) &&  isset($_GET['open']) == 'false' ? false : true;
        $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
        $price = isset($_GET['price']) ? $_GET['price'] : null;
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : null;
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : null;
        $types = isset($_GET['types']) ? $_GET['types'] : array();
        
        if(is_null($lat) || !is_numeric($lat) || is_null($lng) || !is_numeric($lng)) {
            if($output == "xml") {
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
        
        // Our parameters are set so lets get us some data!
        try {
            $markers = Arena::getMarkersWithContactsEvents($lat, $lng, $radius, array(
                'offset' => $offset,
                'limit' => $limit,
                'open' => $open,
                'price' => $price,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'types' => $types
                )
            );
            if($output == "xml") {
                $this->sendResponseHeaders(200, 'xml');
            
                $xml = Controller::generate_valid_xml_from_array($markers, "markers", "marker");
                echo $xml;
            
                Yii::app()->end();
            }

            $this->sendResponseHeaders(200, 'json');

            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'data' => $markers,
                    )
            );
            
            Yii::app()->end();
        } catch (Exception $ex) {
            if($ex instanceof CHttpException) {
                throw $ex;
            }
            
            if($output == "xml") {
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