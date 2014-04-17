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
            'postOnly + delete', // we only allow deletion via POST request
            'ajaxOnly + uploadArenasFileDelete uploadArenasProcessCSV', // we only delete and process files via ajax!
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
                    'locationSearch',
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
                    'uploadArenas',
                    'uploadArenasFile',
                    'uploadArenasFileDelete',
                    'uploadArenasProcessCSV',
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
		$model=new Arena;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Arena'])) {
			$model->attributes=$_POST['Arena'];
			if ($model->save()) {
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
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

		if (isset($_POST['Arena'])) {
			$model->attributes=$_POST['Arena'];
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
        $dataProvider = new CActiveDataProvider('Arena');

        $this->registerUserScripts();
        $this->includeCss = true;
        $this->navigation = true;

        $this->render(
                'index',
                array(
                    'dataProvider' => $dataProvider,
                )
        );
    }

    /**
     * Lists all models.
     */
    public function actionLocationSearch()
    {
        // Publish and register our jQuery plugin
        $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
        
        if(defined('YII_DEBUG')) {
            Yii::app()->clientScript->registerScriptFile($path . '/js/arena/locationSearch.js', CClientScript::POS_END);
        } else {
            Yii::app()->clientScript->registerScriptFile($path . '/js/arena/locationSearch.min.js', CClientScript::POS_END);
            
        }
        
        $this->registerUserScripts();
        $this->includeCss = true;
        $this->navigation = true;
        $doReady = true;
        
        if(Yii::app()->request->isAjaxRequest) {
            $doReady = false;
        }
        
        $this->render(
                '/arena/locationSearch',
                array(
                    'path' => $path,
                    'types' => Event::getTypes(true),
                    'searchUrl' => $this->createUrl('arena/mapMarkers', array('output' => 'json')),
                    'doReady' => $doReady,
                )
        );
    }

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Arena('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Arena'])) {
			$model->attributes=$_GET['Arena'];
		}

		$this->render('admin',array(
			'model'=>$model,
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
            if(defined('YII_DEBUG')) {
                Yii::app()->clientScript->registerScriptFile($path . '/js/utilities.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/footable.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/footable.paginate.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/arena/uploadArenas.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/jquery.fineuploader-3.2.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-switch.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modalmanager.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modal.js', CClientScript::POS_END);
            } else {
                Yii::app()->clientScript->registerScriptFile($path . '/js/utilities.min.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/footable.min.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/footable.paginate.min.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/arena/uploadArenas.min.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/jquery.fineuploader-3.2.min.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-switch.min.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modalmanager.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerScriptFile($path . '/js/bootstrap-modal.js', CClientScript::POS_END);
            }
            
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
    public function loadModel($id)
    {
        $model = Arena::model()->findByPk($id);

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
//            $markers = Arena::getAddressWithEventsMarkers($lat, $lng, $radius, $offset, $limit, $open, $start_date, $end_date, $start_time, $end_time, $types);
//            $markers = Arena::getMarkersWithContactsEvents($lat, $lng, $radius, $offset, $limit, $open, $start_date, $end_date, $start_time, $end_time, $types);
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
                throw new CHttpException(500, "Internal Server Error");
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