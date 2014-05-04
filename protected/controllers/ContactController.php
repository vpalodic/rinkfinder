<?php

class ContactController extends Controller
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
            'accessControl',
            'ajaxOnly + updateAttribute createContact deleteContact',
            'postOnly + delete updateAttribute createContact deleteContact',
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
            array('allow',
                'actions' => array('view', 'updateAttribute', 'createContact', 'deleteContact'),
                'users' => array('@'),
            ),
/*            array('allow',
                'actions' => array('create', 'update', 'index', 'admin', 'delete'),
                'roles' => array('ApplicationAdministrator'),
            ),*/
            array('deny',
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        Yii::trace("In actionView.", "application.controllers.ContactController");
        
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
        $get_available = isset($_POST['get_available']) && is_numeric($_POST['get_available']) && $_POST['get_available'] > 0 ? (integer)$_POST['get_available'] : 0;
        $get_assigned = isset($_POST['$get_assigned']) && is_numeric($_POST['$get_assigned']) && $_POST['$get_assigned'] > 0 ? (integer)$_POST['$get_assigned'] : 0;
        
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
            $assignedArenas = null;
            $availableArenas = null;
            
            if($aid > 0) {
                $sql = 'SELECT c.*, aca.primary_contact '
                        . 'FROM contact c '
                        . 'INNER JOIN arena_contact_assignment aca '
                        . 'ON c.id = aca.contact_id AND c.id = :id AND aca.arena_id = :aid';
                
                $model = Contact::model()->findBySql($sql, array(':id' => $id, ':aid' => $aid));
                
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
                
                $attributes = $model->attributes;
                $attributes['primary_contact'] = $model->primary_contact;
            } else {
                $model = $this->loadModel($id, $outputFormat);
                
                $attributes = $model->attributes;
                
                $availableArenas = Arena::getAvailableAssignedForContact($model->id, Yii::app()->user->id);
                $assignedArenas = Arena::getAssignedAssignedForContact($model->id, Yii::app()->user->id);                
            }
            
            // Data has been retrieved or else we would have thrown an exception
            if($outputFormat == 'json') {
                $this->sendResponseHeaders(200, 'json');
                
                echo json_encode(
                        array(
                            'success' => true,
                            'error' => false,
                            'data' => $attributes,
                            'availableArenas' => $availableArenas,
                            'assignedArenas' => $assignedArenas
                        )
                );
                
                Yii::app()->end();
            } elseif($outputFormat == 'xml') {
                $this->sendResponseHeaders(200, 'xml');
            
                $xml = Controller::generate_valid_xml_from_array(array($attributes, $assignedArenas, $availableArenas), "view", "contact");
                echo $xml;
            
                Yii::app()->end();
            } else {
                // We default to html!
                // Publish and register our jQuery plugin
                $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));            

                if(defined('YII_DEBUG')) {
                    Yii::app()->clientScript->registerScriptFile($path . '/js/contact/view.js', CClientScript::POS_END);
                } else {
                    Yii::app()->clientScript->registerScriptFile($path . '/js/contact/view.min.js', CClientScript::POS_END);
                }
            
                $this->breadcrumbs = array(
                    'Contacts' => $this->createUrl('contact/index'),
                    isset($model->first_name) ? CHtml::encode($model->first_name . ' ' . $model->last_name) : ''
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
                                'availableArenas' => $availableArenas,
                                'assignedArenas' => $assignedArenas
                            ));
                } else {
                    $this->render(
                            "view",
                            array(
                                'model' => $model,
                                'aid' => $aid,
                                'doReady' => true,
                                'path' => $path,
                                'availableArenas' => $availableArenas,
                                'assignedArenas' => $assignedArenas
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
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new Contact;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['Contact'])) {
            $model->attributes = $_POST['Contact'];
            if ($model->save()) {
                $this->redirect(array('view','id' => $model->id));
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the primary key of the new contact is returned.
     * Otherwise if there are validation errors, those are returned as well.
     */
    public function actionCreateContact()
    {
        Yii::trace("In actionCreateContact.", "application.controllers.ContactController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
            $outputFormat = $_POST['output'];
        }
        
        // We only update via a POST and AJAX request!
        $aid = isset($_POST['aid']) && is_numeric($_POST['aid']) && $_POST['aid'] > 0 ? (integer)$_POST['aid'] : 0;
        $get_available = isset($_POST['get_available']) && is_numeric($_POST['get_available']) && $_POST['get_available'] > 0 ? (integer)$_POST['get_available'] : 0;
        
        // Verify we have a valid ID! We need either an arena ID or we need
        // to return the available arenas to assign the contact to.
        if($aid <= 0 && $get_available <= 0) {
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
        
        if($aid > 0) {
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
        } else {
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
        }
        
        // We need to grab and validate the rest of our parameters from the request body
        $model = new Contact;

        $model->first_name = isset($_POST['first_name']) && is_string($_POST['first_name']) && strlen($_POST['first_name']) > 0 ? $_POST['first_name'] : null;
        $model->last_name = isset($_POST['last_name']) && is_string($_POST['last_name']) && strlen($_POST['last_name']) > 0 ? $_POST['last_name'] : null;
        $model->active = isset($_POST['active']) && is_numeric($_POST['active']) ? (integer)$_POST['active'] : 1;
        $model->primary_contact = isset($_POST['primary_contact']) && is_numeric($_POST['primary_contact']) ? (integer)$_POST['primary_contact'] : 0;
        $model->email = isset($_POST['email']) && is_string($_POST['email']) && strlen($_POST['email']) > 0 ? $_POST['email'] : null;
        $model->phone = isset($_POST['phone']) && is_numeric($_POST['phone']) && strlen($_POST['phone']) == 10 ? $_POST['phone'] : null;
        $model->ext = isset($_POST['ext']) && is_numeric($_POST['ext']) ? $_POST['ext'] : null;
        $model->fax = isset($_POST['fax']) && is_numeric($_POST['fax']) && strlen($_POST['fax']) == 10 ? $_POST['fax'] : null;
        $model->fax_ext = isset($_POST['fax_ext']) && is_numeric($_POST['fax_ext']) ? $_POST['fax_ext'] : null;
        $model->created_by_id = $uid;
        $model->created_on = new CDbExpression('NOW()');
        $model->updated_by_id = $uid;
        $model->updated_on = new CDbExpression('NOW()');
        
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
            
            $availableArenas = array();
            
            if($aid > 0) {
                // Ok, we have added the contact, now assign it to the arena!
                $arena->assignContacts($uid, $model->id);
            
                if($model->primary_contact == 1) {
                    $model->makePrimaryContact($uid, $arena->id);
                }
            } else {
                $availableArenas = Arena::getAvailableAssignedForContact($model->id, Yii::app()->user->id);
            }
            
            if($outputFormat == "html") {
                echo json_encode(
                        array(
                            'success' => true,
                            'error' => false,
                            'id' => $model->id,
                            'availableArenas' => $availableArenas
                        )
                );
            } else if ($outputFormat == "xml") {
                $xmlout = array(
                    'success' => 'true',
                    'error' => 'false',
                    'id' => $model->id,
                    'availableArenas' => $availableArenas
                );
                
                $this->sendResponseHeaders(200, 'xml');
                $xml = Controller::generate_valid_xml_from_array($xmlout, "newContact", "contact");
                echo $xml;
                Yii::app()->end();
            } else {
                $this->sendResponseHeaders(200, 'json');
            
                echo json_encode(
                        array(
                            'success' => true,
                            'error' => false,
                            'id' => $model->id,
                            'availableArenas' => $availableArenas
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

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Contact'])) {
            $model->attributes = $_POST['Contact'];
            if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, there is no output, otherwise we output an error.
     */
    public function actionUpdateAttribute()
    {
        Yii::trace("In actionUpdateAttribute.", "application.controllers.ContactController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
            $outputFormat = $_POST['output'];
        }
        
        // We only update via a POST and AJAX request!
        $id = isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 ? (integer)$_POST['id'] : 0;
        $pk = isset($_POST['pk']) && is_numeric($_POST['pk']) && $_POST['pk'] > 0 ? (integer)$_POST['pk'] : 0;
        $aid = isset($_POST['aid']) && is_numeric($_POST['aid']) && $_POST['aid'] > 0 ? (integer)$_POST['aid'] : 0;
        $get_available = isset($_POST['get_available']) && is_numeric($_POST['get_available']) && $_POST['get_available'] > 0 ? (integer)$_POST['get_available'] : 0;
        $get_assigned = isset($_POST['$get_assigned']) && is_numeric($_POST['$get_assigned']) && $_POST['$get_assigned'] > 0 ? (integer)$_POST['$get_assigned'] : 0;
        
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
        
        // Always grab the currently logged in user's ID.
        $uid = Yii::app()->user->id;
        
        if($aid > 0) {
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
        } else {
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
        }
        
        // We need to grab and validate the rest of our parameters from the request body
        // We will update one attribute at a time!
        
        // Grab the remaining parameters!
        $name = isset($_POST['name']) ? $_POST['name'] : null;
        $value = isset($_POST['value']) ? $_POST['value'] : null;
        $aids = isset($_POST['aids']) ? $_POST['aids'] : null;

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
        if($name == 'assign' || $name == 'unassign') {
            
            if((is_array($value) && count($value) > 0 && $aid > 0) || (is_array($aids) && count($aids) > 0)) {
                $valid = 1;
                
                if($aid <= 0 ) {
                    $model = $this->loadModel($id, $outputFormat);
                }
            } else {
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
        } else {
            $model = $this->loadModel($id, $outputFormat);
            $model->$name = $value;
            
            $attribs = array($name);
            
            $valid = $model->validate($attribs);
        }
            
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
            if($name == 'assign') {
                if(is_array($value) && count($value) > 0 && isset($arena) && !is_null($arena)) {
                    if(!$arena->assignContacts($uid, $value)) {
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
                } elseif (is_array($aids) && count($aids) > 0) {
                    if(!$model->assignArenas($uid, $aids)) {
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
                }
            } elseif($name == 'unassign') {
                if(is_array($value) && count($value) > 0 && isset($arena) && !is_null($arena)) {
                    if(!$arena->unassignContacts($uid, $value)) {
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
                } elseif (is_array($aids) && count($aids) > 0) {
                    if(!$model->unassignArenas($uid, $aids)) {
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
                }
            } elseif($name == "primary_contact"  && isset($arena) && !is_null($arena)) { 
                // We either make the contact a primary or not
                // If we make it the primary, all other contacts are
                // removed as primary as there can be only one!
                $success = true;
                if($value == 1) {
                    $success = $model->makePrimaryContact($uid, $arena->id);
                } else {
                    $success = $model->makeSecondaryContact($uid, $arena->id);
                }
                
                if(!$success) {
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
            } else {
                if($value == null) {
                    $value = new CDbExpression('NULL');
                }
            
                $attributes = array(
                    $name => $value,
                    'updated_by_id' => $uid,
                    'updated_on' => new CDbExpression('NOW()')
                );
                
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
    public function actionDeleteContact()
    {
        Yii::trace("In actionDeleteContact.", "application.controllers.ContactController");
        
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
        if(Yii::app()->request->isPostRequest) {
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
		$dataProvider=new CActiveDataProvider('Contact');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Contact('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Contact'])) {
			$model->attributes=$_GET['Contact'];
		}

		$this->render('admin',array(
			'model'=>$model,
		));
	}

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Contact the loaded model
     * @throws CHttpException
     */
    public function loadModel($id, $outputFormat = 'html')
    {
        $model = Contact::model()->findByPk($id);
        
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
     * Performs the AJAX validation.
     * @param Contact $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax'] === 'contact-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}