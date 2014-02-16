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
            'ajaxOnly + uploadArenasFile', // we only upload files via ajax!
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
				'actions'=>array('create','update', 'uploadArenas', 'uploadArenasFile'),
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
        // Here we define the paths where the files will be stored temporarily
//        $path = realpath(dirname(Yii::app()->request->scriptFile) . Yii::app()->params['uploads']) . DIRECTORY_SEPARATOR;
//        $publicPath = Yii::app()->getBaseUrl() . Yii::app()->params['uploads'] . "/";

        // This is for IE which doens't handle 'Content-type: application/json' correctly
        header('Vary: Accept');
        if(isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
        
        $model = new ArenaUploadForm();
        
        $file = CUploadedFile::getInstance($model, 'fileName');
        
        if($file !== null) {
            // The file has been uploaded.
            // We need to save it so we can process it later!
            $uploadDir = Yii::app()->params['uploads']['path'];
            $uploadDir .= DIRECTORY_SEPARATOR;
            $uploadDir .= Yii::app()->params['uploads']['directory'];
            
            // Each user gets it's own directory
            $uploadDir .= DIRECTORY_SEPARATOR;
            $uploadDir .= (string)Yii::app()->user->id;

            // Each type gets it's own subdirectory
            $uploadDir .= DIRECTORY_SEPARATOR;
            $uploadDir .= (string)FileUpload::TYPE_ARENA_CSV;
            
            // Check if the path exists. If it doesn't, then create it!
            if(!file_exists($uploadDir)) {
                // Attempt to create the directory path
                if(!mkdir($uploadDir, 0777, true)) {
                    // Something went horribly wrong!
                    echo json_encode(
                            array(
                                'success' => false,
                                'error' => 'Failed to create upload path: ' . $uploadDir,
                            )
                    );
                    
                    Yii::app()->end(1);
                }
            }
            
            // Our path is valid, now we must save our temp file to it!
            $uploadFile = $uploadDir . DIRECTORY_SEPARATOR;
            $uploadFile .= $file->getName();
            
            // Check for an existing record
            $fileUpload = FileUpload::model()->find(
                    'upload_type_id = :upload_type_id AND name = :name',
                    array(
                        ':upload_type_id' => FileUpload::TYPE_ARENA_CSV,
                        ':name' => $file->getName()
                    )
            );

            if($fileUpload !== null) {
                // Ok, we have an existing record, so we abort!
                echo json_encode(
                        array(
                            'success' => false,
                            'error' => 'File already has been uploaded: ' . $uploadFile,
                        )
                );
                    
               Yii::app()->end();
            }
            
            // Ok, we can safely save off the file!
            if(!$file->saveAs($uploadFile)) {
                // Something went horribly wrong!!!
                echo json_encode(
                        array(
                            'success' => false,
                            'error' => 'Unable to save the temp file to the filesystem: ' . $uploadFile,
                        )
                );
                    
                Yii::app()->end(1);
            }
            
            // File has been saved, now we make a record of it!!
            $fileUpload = new FileUpload();
            $fileUpload->upload_type_id = FileUpload::TYPE_ARENA_CSV;
            $fileUpload->user_id = Yii::app()->user->id;
            $fileUpload->name = $file->getName();
            $fileUpload->path = $uploadDir;
            $fileUpload->extension = $file->getExtensionName();
            $fileUpload->mime_type = $file->getType();
            $fileUpload->size = $file->getSize();
            $fileUpload->error_code = $file->getError();

            if(!$fileUpload->save()) {
                // Something went horribly wrong!!!
                echo json_encode(
                        array(
                            'success' => false,
                            'error' => 'Unable to save file details to the database: ' . $uploadDir,
                        )
                );
                    
                Yii::app()->end(1);
            }
            
            // We are ready to process the file so let the UI know that we are ready for it!
            echo json_encode(
                    array(
                        'success' => true,
                        'error' => false,
                        'fileUpload' => array(
                            'uploadType' => FileUpload::itemAlias('UploadType', $fileUpload->upload_type_id),
                            'id' => (integer)$fileUpload->id,
                            'user' => Yii::app()->user->fullName,
                            'name' => $fileUpload->name,
                            'path' => $fileUpload->path,
                            'extension' => $fileUpload->extension,
                            'mimeType' => $fileUpload->mime_type,
                            'size' => (integer)$fileUpload->size,
                            'errorCode' => (integer)$fileUpload->error_code,
                        ),
                    )
            );
            
            Yii::app()->end();
        } else {
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Failed to retrieve uploaded file.',
                    )
            );
            
            Yii::app()->end(1);
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
		$model=Arena::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Arena $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax']==='arena-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}