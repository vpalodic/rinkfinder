<?php

class LocationController extends Controller
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
            'ajaxOnly + updateAttribute createLocation deleteLocation',
            'postOnly + delete updateAttribute createLocation deleteLocation',
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
                'actions' => array('view', 'updateAttribute', 'createLocation', 'deleteLocation'),
                'users' => array('@'),
            ),
            array('allow',
                'actions' => array('create', 'update', 'index', 'admin', 'delete'),
                'roles' => array('ApplicationAdministrator'),
            ),
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
        $model = new Location;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['Location'])) {
            $model->attributes = $_POST['Location'];
            if($model->save()) {
                $this->redirect(array('view','id'=>$model->id));
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the primary key of the new location is returned
     * along with the tags!
     * Otherwise if there are validation errors, those are returned as well.
     */
    public function actionCreateLocation()
    {
        Yii::trace("In actionCreateLocation.", "application.controllers.LocationController");
        
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
        
        // Always grab the currently logged in user's ID.
        $uid = Yii::app()->user->id;
        
        // Load the Arena model and ensure that the user is assigned to it!
        $arena = $this->loadArenaModel($aid, $outputFormat);
        
        // And that the user has permission to update it!
        if(!Yii::app()->user->isRestrictedArenaManager() || !$arena->isUserAssigned($uid)) {
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
        $model = new Location;

        $model->arena_id = $aid;
        $model->external_id = isset($_POST['external_id']) && is_string($_POST['external_id']) && strlen($_POST['external_id']) > 0 ? $_POST['external_id'] : null;
        $model->name = isset($_POST['name']) && is_string($_POST['name']) && strlen($_POST['name']) > 0 ? $_POST['name'] : null;
        $model->description = isset($_POST['description']) && is_string($_POST['description']) && strlen($_POST['description']) > 0 ? $_POST['description'] : null;
        $model->notes = isset($_POST['notes']) && is_string($_POST['notes']) && strlen($_POST['notes']) > 0 ? $_POST['notes'] : null;
        $model->tags = isset($_POST['tags']) && is_string($_POST['tags']) && strlen($_POST['tags']) > 0 ? $_POST['tags'] : null;
        $model->type_id = isset($_POST['type_id']) && is_numeric($_POST['type_id']) ? (integer)$_POST['type_id'] : 1;
        $model->status_id = isset($_POST['status_id']) && is_numeric($_POST['status_id']) ? (integer)$_POST['status_id'] : 1;
        $model->length = isset($_POST['length']) && is_numeric($_POST['length']) ? (float)$_POST['length'] : null;
        $model->width = isset($_POST['width']) && is_numeric($_POST['width']) ? (float)$_POST['width'] : null;
        $model->radius = isset($_POST['radius']) && is_numeric($_POST['radius']) ? (float)$_POST['radius'] : null;
        $model->seating = isset($_POST['seating']) && is_numeric($_POST['seating']) ? (integer)$_POST['seating'] : null;
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
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Location'])) {
			$model->attributes=$_POST['Location'];
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
		$dataProvider=new CActiveDataProvider('Location');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Location('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Location'])) {
			$model->attributes=$_GET['Location'];
		}

		$this->render('admin',array(
			'model'=>$model,
		));
	}

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Location the loaded model
     * @throws CHttpException
     */
    public function loadModel($id, $outputFormat = 'html')
    {
        $model = Location::model()->findByPk($id);
        
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
     * @param Location $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax'] === 'location-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}