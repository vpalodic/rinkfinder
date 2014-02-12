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
            array(
                'allow',
                'actions' => array(
                    'index',
                    'view',
                    'create',
                    'update',
                    'changePassword',
                ),
                'users' => array('@'),
            ),
            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array(
                    'admin',
                    'delete',
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
        $model = $this->loadModel($id);
            
        if(Yii::app()->user->checkAccess('viewUser', array('user' => $model))) {
            $this->render(
                    'view',
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
     * Creates a new user with the specified role.
     * If creation is successful, the form will be reset so that another user
     * can be added
     * @param string $role the name of the user type to create
     * @param string $arenaId the ID of the Arena this new use will be assigned to.
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
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        if(Yii::app()->user->checkAccess('updateUser', array('user' => $model))) {
            // Uncomment the following line if AJAX validation is needed
            //$this->performAjaxValidation($model);

            if(isset($_POST['User'])) {
                $model->attributes = $_POST['User'];
                if($model->save()) {
                    $this->redirect(array('view','id'=>$model->id));
                }
            }

            $this->render(
                    'update',
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
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $model = $this->loadModel($id);

        if(Yii::app()->user->checkAccess('deleteUser', array('user' => $model))) {
            if(Yii::app()->request->isPostRequest) {
                // we only allow deletion via POST request
                $model->deleteAccount();
                
                // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                if(!isset($_GET['ajax'])) {
                    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
                }
            } else {
                throw new CHttpException(
                        400,
                        'Invalid request. Please do not repeat this request again.'
                );
            }
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
            $this->redirect(array('profile/view', 'id' => Yii::app()->user->id));
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
                    
                    $this->redirect(array('profile/view', 'id' => $model->id));
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
    public function loadModel($id)
    {
        $model = User::model()->with(array('profile' => array('together' => true)))->findByPk($id);
        
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
    
}