<?php

class UserController extends Controller
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
            'ajaxOnly + updateAttribute',
            'postOnly + delete updateAttribute', // we only allow deletion via POST request
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
                'allow',
                'actions' => array(
                    'view',
                    'create',
                    'update',
                    'updateAttribute',
                    'changePassword',
                ),
                'users' => array('@'),
            ),
            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array(
                    'admin',
                    'delete',
                    'index',
                ),
                'roles' => array('ApplicationAdministrator'),
            ),
            array(
                'deny',  // deny all users
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
        Yii::trace("In actionView.", "application.controllers.UserController");
        
        // Publish and register our jQuery plugin
        $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
        
        if(defined('YII_DEBUG')) {
            Yii::app()->clientScript->registerScriptFile($path . '/js/user/userAccountProfile.js', CClientScript::POS_END);
        } else {
            Yii::app()->clientScript->registerScriptFile($path . '/js/user/userAccountProfile.min.js', CClientScript::POS_END);
            
        }
        
        $model = $this->loadModel($id);
            
        if(!Yii::app()->user->checkAccess('viewUser', array('user' => $model))) {
            throw new CHttpException(
                    403,
                    'Permission denied. You are not authorized to perform this action.'
            );
        }
        
        $this->registerUserScripts();
        $this->includeCss = true;
        $this->navigation = true;

        $doReady = 1;
        $newRecord = 0;
        
        if(Yii::app()->request->isAjaxRequest) {
            $doReady = 0;
        }

        $this->pageTitle = Yii::app()->name . ' - Account & Profile!';
        $this->breadcrumbs = array(
            $model->fullName,
        );
        
        $params = array(
            'endpoints' => array(
                'new' => Yii::app()->createUrl('user/create'),
                'update' => Yii::app()->createUrl('user/updateAttribute')
            ),
            'data' => array(
                'id' => $model->id,
                'output' => 'html'
            )
        );

        $this->render(
                'view',
                array(
                    'model' => $model,
                    'path' => $path,
                    'params' => $params,
                    'doReady' => $doReady,
                    'newRecord' => $newRecord
                )
        );
    }

    /**
     * Creates a new user with the specified role.
     * If creation is successful, the form will be reset so that another user
     * can be added
     * @param string $role the name of the user type to create
     * @param string $arenaId the ID of the Arena this new user will be assigned to.
     */
    public function actionCreate($role, $arenaId = null)
    {
        $arena = (isset($arenaId) && $arenaId > 0) ? Arena::model()->findByPk($arenaId) : null;
        
        if(Yii::app()->user->checkAccess('createUser', array('arena' => $arena, 'role' => $role))) {
            $roles = Yii::app()->authManager->getRoles();
            $roleKeys = array_keys($roles);
        
            if(!in_array($role, $roleKeys) || (isset($arenaId) && $arenaId <= 0)) {
                throw new CHttpException(
                        400,
                        'Bad request. The parameters are invalid!'
                );
            }
        
            // Arena should only be specified if we are creating a Manager
            // or Restricted Manager
            if($role != 'Manager' && $role != 'RestrictedManager' && $arenaId !== null) {
                throw new CHttpException(
                        400,
                        'Bad request. Only Arena Managers and Restricted Arena Managers can be assigned to an Arena'
                );
            }

            $model = new User;
            $profile = new Profile;
            
            // Uncomment the following line if AJAX validation is needed
            $this->performAjaxValidation($model, $profile);

            switch($role) {
                case 'ApplicationAdministrator':
                    $displayRole = 'Application Administrator';
                    break;
                case 'Administrator':
                    $displayRole = 'Site Administrator';
                    break;
                case 'Manager':
                    $displayRole = 'Arena Manager';
                    break;
                case 'RestrictedManager':
                    $displayRole = 'Restricted Arena Manager';
                    break;
                case 'User':
                    $displayRole = 'Site User';
                    break;
                default:
                    $displayRole = 'Unknown Role';
                    break;
            }
        
            $description = $roles[$role]->description;
        
            if(isset($_POST['User'])) {
                $model->attributes = $_POST['User'];
                $profile->attributes = ((isset($_POST['Profile']) ? $_POST['Profile'] : array()));
                
                if($model->validate() && $profile->validate()) {
                    // Preregister the new user!!!
                    $model->preRegisterNewUser($model->status_id);
                
                    // Register the account!!!!
                    if($model->save()) {
                        $model->postRegisterNewUser($role);
                    
                        $profile->user_id = $model->id;
                        $profile->save();
                        $profile->postRegisterNewUser();
                        
                        if($arena !== null) {
                            $arena->assignUser($model->id);
                        }
                    
                        $emailSent = $this->sendNewUserEmail($model);
                    
                        $message = '<h4>New User Created Successfully!</h4>';
                        
                        if($emailSent === true) {
                            $message .= 'The new user has been e-mailed instructions on how to login to their account. ';
                        } else {
                            $message .= 'We attempted to e-mail the new user their account information and login instructions ';
                            $message .= 'but an error prevented us from doing so. Please besure to provide them with their ';
                            $message .= 'credentials. <br /><br /> <b>E-mail Error:</b><br /><br />' . $emailSent;
                        }
                        
                        Yii::app()->user->setFlash(
                                TbHtml::ALERT_COLOR_SUCCESS,
                                $message
                        );
                        
                        // Do not completely reset the models!
                        // Allows many users to be created quickly!
                        $model->username = '';
                        $model->email = '';
                        $model->passwordSave = '';
                        $model->passwordRepeat = '';
                        $profile->first_name = '';
                        $profile->last_name = '';
                    }
                } else {
                    $profile->validate();
                }
            }

            $this->render(
                    'create',
                    array(
                        'model' => $model,
                        'profile' => $profile,
                        'role' => $role,
                        'arena' => $arena,
                        'displayRole' => $displayRole,
                        'description' => $description,
                    )
            );
        } else {
            throw new CHttpException(
                    403,
                    'Permission denied. You are not authorized to perform this action.'
            );
        }
    }

    /**
     * Updates a user's model. Well, actually just displays the user's profile
     * as the update is done on a per field basis and via ajax / json
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);
        $profile = $model->profile;

        if(Yii::app()->user->checkAccess('updateUser', array('user' => $model))) {
            if(isset($_POST['ajax']) && $_POST['ajax'] === 'user-form') {
                echo CActiveForm::validate(array($model, $profile));
                Yii::app()->end();
            }
            if(isset($_POST['User'])) {
                $model->attributes = $_POST['User'];
                $profile->attributes = ((isset($_POST['Profile']) ? $_POST['Profile'] : array()));
                
                if($model->validate() && $profile->validate()) {
                    if($model->save() && $profile->save()) {
                        $this->redirect(array(
                            'view',
                            'id' => $model->id,
                            )
                        );
                    }
                }
            }

            $this->render(
                    'update',
                    array(
                        'model' => $model,
                        'profile' => $profile
                    )
            );
        } else {
            throw new CHttpException(
                    403,
                    'Permission denied. You are not authorized to perform this action.'
            );
        }
    }

    public function actionUpdateAttribute()
    {
        Yii::trace("In actionUpdateAttribute.", "application.controllers.UserController");
        
        // Default to HTML output!
        $outputFormat = "html";
        
        if(isset($_GET['output']) && ($_GET['output'] == 'xml' || $_GET['output'] == 'json')) {
            $outputFormat = $_GET['output'];
        } elseif(isset($_POST['output']) && ($_POST['output'] == 'xml' || $_POST['output'] == 'json')) {
            $outputFormat = $_POST['output'];
        }
        
        // We only update via a POST and AJAX request!
        $id = isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 ? (integer)$_POST['id'] : 0;
        $pk = isset($_POST['pk']) && is_string($_POST['pk']) ? $_POST['pk'] : null;
        
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
        
        // Parameters look good so now verify that the user model exists!
        $model = $this->loadModel($id, $outputFormat);
        
        // And that the user has permission to update it!
        if(!Yii::app()->user->checkAccess('updateUser', array('user' => $model))) {
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
        $action = $pk;
        $name = isset($_POST['name']) ? $_POST['name'] : null;
        $value = isset($_POST['value']) ? $_POST['value'] : null;

        // Always grab the currently logged in user's ID.
        $uid = Yii::app()->user->id;

        // Validate our remaining parameters!
        if($name === null || $action === null) {
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
            
        // The $action will either be 'user' or 'profile' and it determines
        // which model we are going to update!
        $updateModel = null;
        
        if($action === 'user') {
            $updateModel = $model;
        } elseif($action === 'profile') {
            $updateModel = $model->profile;
        } else {
            // Unknown action so send an error response!
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(400, 'Unknown action request.');
            }
            
            $this->sendResponseHeaders(400, 'json');
            echo json_encode(array(
                    'success' => false,
                    'error' => 'Unknown action request.'
                )
            );
            Yii::app()->end();
        }

        // Ok, we have what appear to be valid parameters and so
        // it is time to validate and then update the value!
        $updateModel->$name = $value;

        $valid = $updateModel->validate(array($name));
            
        if(!$valid) {
            $errors = $updateModel->getErrors($name);

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
            // the user is a restricted manager. We could do another
            // check to see if the user is assigned to the arena but,
            // we are going to do that check during the update!
            // So, we will know if the user is valid if our update query
            // affects one row. If it affects zero rows, then the user
            // wasn't authorized and we will throw a 403 error!
            if($value == null) {
                $value = new CDbExpression('NULL');
            }
            
            $attributes = array(
                $name => $value,
                'updated_by_id' => $uid,
                'updated_on' => new CDbExpression('NOW()')
            );

            if(!$updateModel->saveAttributes($attributes)) {
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
        $model = $this->loadModel($id);

        if(Yii::app()->user->checkAccess('deleteUser', array('user' => $model))) {
//            if(Yii::app()->request->isPostRequest) {
                // we only allow deletion via POST request
                $model->adminDeleteAccount();
                
                // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                if(!isset($_GET['ajax'])) {
                    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
                }
//            } else {
//                throw new CHttpException(
//                        400,
//                        'Invalid request. Please do not repeat this request again.'
//                );
//            }
        } else {
            throw new CHttpException(
                    403,
                    'Permission denied. You are not authorized to perform this action.'
            );
        }
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        if(Yii::app()->user->checkAccess('indexUser')) {
            $criteria = new CDbCriteria();
            
            $criteria->with = array('profile', 'createdBy', 'updatedBy');
            $criteria->together = true;
            
            $dataProvider = new CActiveDataProvider(
                    'User',
                    array(
                        'criteria' => $criteria
                    )
            );
            
            $this->render(
                    'index',
                    array(
                        'dataProvider' => $dataProvider,
                    )
            );
        } elseif(!Yii::app()->user->isGuest) {
            $this->redirect(array('user/view', 'id' => Yii::app()->user->id));
        } else {
            throw new CHttpException(
                    403,
                    'Permission denied. You are not authorized to perform this action.'
            );
        }
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        if(Yii::app()->user->checkAccess('adminUser')) {
            $model = new User('search');
            $model->unsetAttributes();  // clear any default values
            
            if(isset($_GET['User'])) {
                $model->attributes = $_GET['User'];
            }

            $this->render(
                    'admin',
                    array(
                        'model' => $model,
                    )
            );
        } else {
            throw new CHttpException(
                    403,
                    'Permission denied. You are not authorized to perform this action.'
            );
        }
    }

    /**
     * Updates a particular model's password.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionChangePassword($id)
    {
        $model = $this->loadModel($id);

        if(Yii::app()->user->checkAccess('updateUser', array('user' => $model))) {
            $model->scenario = 'changePassword';
            // Uncomment the following line if AJAX validation is needed
            //$this->performAjaxValidation($model);

            if(isset($_POST['User'])) {
                $model->attributes = $_POST['User'];
                if($model->save()) {
                    $message = '<h4>New password has been saved!</h4>';
                    $message .= 'The next time you login, please use your new password.';
                    
                    Yii::app()->user->setFlash(
                            TbHtml::ALERT_COLOR_SUCCESS,
                            $message
                    );
                    
                    $this->redirect(array('user/view', 'id' => $model->id));
                }
            }

            $this->render(
                    'changePassword',
                    array(
                        'model' => $model,
                    )
            );
        } else {
            throw new CHttpException(
                    403,
                    'Permission denied. You are not authorized to perform this action.'
            );
        }
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return User the loaded model
     * @throws CHttpException
     */
    public function loadModel($id, $outputFormat = 'html')
    {
        $model = User::model()->with(array('profile' => array('together' => true)))->findByPk($id);
        
        if($model === null) {
            if($outputFormat == "html" || $outputFormat == "xml") {
                throw new CHttpException(404, 'User not found');
            }

            $this->sendResponseHeaders(404, 'json');
                
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'User not found',
                    )
            );
            Yii::app()->end();
        }
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param User $model the model to be validated
     */
    protected function performAjaxValidation($model, $profile)
    {
        if(isset($_POST['ajax']) && $_POST['ajax'] === 'user-form') {
            echo CActiveForm::validate(array($model, $profile));
            Yii::app()->end();
        }
    }
    
    /**
     * Sends the new user e-mail to the user
     * @param User $user
     * @return mixed True if mail was sent, otherwise the error information
     */
    protected function sendNewUserEmail($user)
    {
        $data = array();
        $data['fullName'] = $user->fullName;
        $data['activationUrl'] = $this->createAbsoluteUrl(
                'site/activateAccount',
                array(
                    'user_key' => $user->user_key,
                    'email' => $user->email
                )
        );
        $data['manualUrl'] = $this->createAbsoluteUrl('site/activateAccount');
        $data['username'] = $user->username;
        $data['email'] = $user->email;
        $data['password'] = $user->passwordSave;
        $data['user_key'] = $user->user_key;
        
        $to = array($user->email => $user->fullName);

        $subject = 'Welcome ' . CHtml::encode($user->fullName) . ' to ' . CHtml::encode(Yii::app()->name . ' (Account Information Enclosed)');
        
        $mailSent = Yii::app()->sendMail(
                '',
                $to,
                $subject,
                $subject,
                $data,
                'new_user'
        );
        
        return $mailSent;
    }
    
    public function buildBreadcrumbs($action, $displayRole, $arena = null)
    {
        switch($action) {
            case 'create':
                if($arena !== null) {
                    $this->breadcrumbs = array(
                        'Arena Management' => array(
                            'arenaManagement/index'
                        ),
                        CHtml::encode($arena->name) => array(
                            'arenaManagement/arenas/view',
                            'aid' => $arena->id,
                        ),
                        'Managers' => array(
                            'arenaManagement/managers/index',
                            'aid' => $arena->id,
                        ),
                        'Create '. $displayRole,
                    );
                } else {
                    $this->breadcrumbs = array(
                        'Administration' => array(
                            '/admin',
                        ),
                        'Users' => array(
                            '/admin/users',
                        ),
                        'Create '. $displayRole,
                    );
                }
                break;
            default:
                break;
        }
    }
    
    public function buildMenu($action, $arena = null)
    {
        
    }
}