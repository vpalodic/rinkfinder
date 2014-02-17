<?php

class ArenaController extends Controller
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
            'ajaxOnly + uploadArenasFile actionUploadArenasFileDelete', // we only upload and delete files via ajax!
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
                    'view'
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
		$dataProvider=new CActiveDataProvider('Arena');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
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
	 * Manages all models.
	 */
    public function actionUploadArenas()
    {
        $model = new ArenaUploadForm();
                
        $model->unsetAttributes();  // clear any default values
		
        if(isset($_POST['ArenaUploadForm'])) {
            $model->attributes = $_POST['ArenaUploadForm'];
        }

        // Publish and register our jQuery plugin
        $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets.js'));
        Yii::app()->clientScript->registerScriptFile($path.'/uploadArenas.js');
        
        $this->render(
                'uploadArenas',
                array(
                    'model' => $model,
                )
        );
    }

    public function actionUploadArenasFile()
    {
        $this->sendJSONHeaders();
        
        $model = new ArenaUploadForm();
        
        $instanceRetrieved = $model->getUploadFileInstance();
        
        if($instanceRetrieved !== true) {
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
            echo $isValid;                
            Yii::app()->end();
        }

        $isDirPrepared = $model->prepareUploadDirectory(
                FileUpload::TYPE_ARENA_CSV,
                Yii::app()->user->id
        );

        if($isDirPrepared !== true) {
            // Unable to prepare the upload directory
            echo $isDirPrepared;                
            Yii::app()->end();
        }

        $hasExistingRecord = $model->getHasFileRecord(
                $this->createUrl('arena/uploadArenasFileDelete')
        );
            
        if($hasExistingRecord !== false) {
            // Ok, we have an existing record, so we abort!
            echo $hasExistingRecord;                    
            Yii::app()->end();
        }

        // We need to save it so we can process it later!
        // Our path is valid, now we must save our temp file to it!
        $fileSaved = $model->saveUploadedFile();
            
        // Ok, we can safely save off the file!
        if($fileSaved !== true) {
            // Something went horribly wrong!!!
            echo $fileSaved;                    
            Yii::app()->end();
        }

        // File has been saved, now we make a record of it!!
        $fileRecordSaved = $model->saveUploadedFileRecord();

        if($fileRecordSaved !== true) {
            // Something went horribly wrong!!!
            echo $fileRecordSaved;
            Yii::app()->end();
        }

        // We are ready to process the file so let the UI know that we are ready for it!
        echo $model->getJsonSuccessResponse(
                $this->createUrl('arena/uploadArenasFileDelete')
        );
        Yii::app()->end();
    }
    
    public function actionUploadArenasFileDelete()
    {
        $this->sendJSONHeaders();
        
        $isDeleteMethod = $this->isDeleteMethod();
        
        // This needs to come through as an actual DELETE request!!!
        if($isDeleteMethod !== true) {
            echo $isDeleteMethod;
            Yii::app()->end();
        }
        
        $paramstr =  $this->getParamsFromPhp();

        if($paramstr === false) {
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
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Missing expected parameters',
                    )
            );
            Yii::app()->end();
        }
        
        // Delete the file and send the response!
        echo RinkfinderUploadForm::deleteUploadedFile($fid, $name, FileUpload::TYPE_ARENA_CSV);
        Yii::app()->end();
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
     * Sends the headers
     */
    protected function sendJSONHeaders()
    {
        // This is for IE which doens't handle 'Content-type: application/json' correctly
        header('Vary: Accept');
        if(isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }        
    }

    /**
     * Checks that the DELETE method is used
     * @return mixed true if the DELETE is being used or else
     * a JSON encoded error string
     */
    public function isDeleteMethod()
    {
        if(isset($_SERVER['REQUEST_METHOD']) &&
            strcasecmp($_SERVER['REQUEST_METHOD'], 'DELETE') != 0) {
            
            return json_encode(
                    array(
                        'success' => false,
                        'error' => 'Request Method must be DELETE.',
                    )
            );
        }
        
        return true;        
    }
    
    /**
     * Read the request parameters directly for php://input
     * @return mixed an unparsed string containing the parameters
     * read from php://input or false if unable to read them
     */
    public function getParamsFromPhp()
    {
        // We need to read the contents of the DELETE request
        // via the php://input file
        if(is_resource('php://input')) {
            rewind('php://input');
            // For now just echo!
            $paramstr =  stream_get_contents('php://input');
        } else {
            // For now just echo!
            $paramstr =  file_get_contents('php://input');
        }
        
        return $paramstr;
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
}