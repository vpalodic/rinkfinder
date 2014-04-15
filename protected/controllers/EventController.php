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
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
            'arenaContext + index create admin uploadEvents', // check to ensure proper arena context
            'ajaxOnly + uploadEventsFileDelete uploadEventsProcessCSV type status', // we only delete and process files via ajax!
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
                    'create',
                    'update',
                    'uploadEvents',
                    'uploadEventsFile',
                    'uploadEventsFileDelete',
                    'uploadEventsProcessCSV',
                ),
                'users' => array(
                    '@'
                ),
            ),
            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array(
                    'admin',
                    'delete'
                ),                
                'users' => array(
                    'admin'
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
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render(
                'view',
                array(
                    'model' => $this->loadModel($id),
		)
        );
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
		$dataProvider=new CActiveDataProvider('Event');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
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
        if(!Yii::app()->user->checkAccess('uploadEvent')) {
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
            if(defined('YII_DEBUG')) {
                Yii::app()->clientScript->registerScriptFile($path . '/js/utilities.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/footable.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/footable.paginate.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/event/uploadEvents.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/jquery.fineuploader-3.2.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-switch.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modalmanager.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modal.js', CClientScript::POS_END);
            } else {
                Yii::app()->clientScript->registerScriptFile($path . '/js/utilities.min.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/footable.min.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/footable.paginate.min.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/event/uploadEvents.min.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/jquery.fineuploader-3.2.min.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-switch.min.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modalmanager.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modal.js', CClientScript::POS_END);
            }
            
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
        if(!Yii::app()->user->checkAccess('uploadEvent')) {
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
	
        if($this->arena === null) {
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
        if(!Yii::app()->user->checkAccess('uploadEvent')) {
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
        if(!Yii::app()->user->checkAccess('uploadEvent')) {
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
    public function loadModel($id)
    {
        $model = Event::model()->findByPk($id);
        
        if($model === null) {
            throw new CHttpException(
                    404,
                    'The requested page does not exist.'
            );
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
                            'arena' => array('select' => 'name'),
                            'type' => array('select' => 'display_name'),
                            'status' => array('select' => 'name'),
                        ),
                        'together' => true,
                    )
            );
            
            foreach($events as $event) {
                $event->autoTag();
                $event->autoDurationEndDateTimeStatus();
                
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