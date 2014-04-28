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
            'ajaxOnly + updateAttribute',
            'postOnly + delete updateAttribute',
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
                'actions' => array('view'),
                'users' => array('*'),
            ),
            array('allow',
                'actions' => array('index', 'create','update'),
                'users' => array('@'),
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
		$model=new Contact;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Contact'])) {
			$model->attributes=$_POST['Contact'];
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

		if (isset($_POST['Contact'])) {
			$model->attributes=$_POST['Contact'];
			if ($model->save()) {
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

    public function actionUpdateAttribute()
    {
        Yii::trace("In actionUpdateAttribute.", "application.controllers.ArenaController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        } elseif(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
            $outputFormat = $_POST['output'];
        }
        
        // We only update via a POST and AJAX request!
        $id = isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 ? (integer)$_POST['id'] : 0;
        $pk = isset($_POST['pk']) && is_numeric($_POST['pk']) && $_POST['pk'] > 0 ? (integer)$_POST['pk'] : 0;
        
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
        }
        
        // The attribute is valid and so we should save it!!
        try {
            // We don't blindly save it even though we validated that
            // the user is a restricted manager. We could do another
            // check to see if the user is assigned to the arena but,
            // we are going to do that check during the update!
            // So, we will know if the user is valid if our update query
            // affects one row. If it affects zero rows, then the user
            // wasn't authorized and we will throw a 403 error!
            if($value == null) {
                $value = new CDbExpression('NULL');
            }
            
            if($name == 'geocoding') {
                $attributes = array(
                    'lat' => $value[0],
                    'lng' => $value[1],
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
            
            if($name == 'tags') {
                $model->normalizeTags();
                Tag::model()->updateFrequency($model->oldTags, $model->tags);
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
	public function loadModel($id)
	{
		$model=Contact::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Contact $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax']==='contact-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}