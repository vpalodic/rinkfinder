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
			'postOnly + delete', // we only allow deletion via POST request
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update', 'type', 'status'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
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
		$model=new EventRequest;

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
        
        if(!Yii::app()->user->isSiteUser()) {
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

            echo json_encode($data);
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array($data, "details", "eventrequest");
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
        Yii::trace("In actionType.", "application.controllers.EventRequestController");
        
        // Default to XML output!
        $outputFormat = "json";
        $data = array();
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        }
        
        if(!Yii::app()->user->isSiteUser()) {
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

            echo json_encode($data);
        
            Yii::app()->end();
        } elseif($outputFormat == 'xml') {
            $this->sendResponseHeaders(200, 'xml');
            
            $xml = Controller::generate_valid_xml_from_array($data, "details", "eventrequest");
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
        if(Yii::app()->request->isAjaxRequest) {
            // Grab all of the parameters!
            $name = isset($_POST['name']) ? $_POST['name'] : null;
            $value = isset($_POST['value']) ? $_POST['value'] : null;
            $pk = isset($_POST['pk']) ? $_POST['pk'] : null;
            $id = isset($_POST['id']) ? $_POST['id'] : null;
            $eid = isset($_POST['eid']) ? $_POST['eid'] : null;
            $aid = isset($_POST['aid']) ? $_POST['aid'] : null;
            $lid = isset($_POST['lid']) ? $_POST['lid'] : null;
            // Always restrict to the currently logged in user!
            $uid = Yii::app()->user->id;
            
            if($name === null || $value === null || $id === null ||
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
            
            // Ok, we have what appear to be valid parameters and so
            // it is time to validate and then update the value!
            $model = new EventRequest();
            
            $model->$name = $value;
            
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
                        throw new CHttpException(500, "Internal Server Error");
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
            }
        } else {
            $model = $this->loadModel($id);

            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);

            if (isset($_POST['EventRequest'])) {
                $model->attributes=$_POST['EventRequest'];
                if ($model->save()) {
                    $this->redirect(array('view','id' => $model->id));
                }
            }

            $this->render('update',array(
                'model' => $model,
            ));
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
	public function loadModel($id)
	{
		$model=EventRequest::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param EventRequest $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax']==='event-request-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}