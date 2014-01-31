<?php

class SiteController extends Controller
{
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
                'allow', // allow all users to perform all actions
                'actions' => array('index', 'captcha', 'page', 'contact', 'login', 'logout', 'register'),
                'users' => array('*'),
            ),
            array(
                'deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        $this->render('index');
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if($error = Yii::app()->errorHandler->error) {
            if(Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                $this->render('error', $error);
            }
        }
    }

    /**
     * Displays the contact page
     */
    public function actionContact()
    {
        $model = new ContactForm;

    	// ajax validator
        if(isset($_POST['ajax']) && $_POST['ajax'] === 'contact-form') {
            echo CActiveForm::validate(array($model));
            Yii::app()->end();
        }

        if(isset($_POST['ContactForm']))
        {
            $model->attributes = $_POST['ContactForm'];

            if($model->validate()) {
                $model->body = "Request from: " . CHtml::encode($model->name) . CHtml::encode(" <") . CHtml::encode($model->email) . CHtml::encode("> ") . "<br /><br />" . nl2br(CHtml::encode($model->body));
                
                $mailSent = Yii::app()->sendMail('',
                        'vj.palodichuk@gmail.com',
                        'Rinkfinder.com contact request: ' . CHtml::encode($model->subject),
                        $model->body);
                if($mailSent === true) {
                    Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_SUCCESS,
                            'Thank you for contacting us. We will respond to you as soon as possible.');
                    
                    if($model->copyMe) {
                        $mailSent = Yii::app()->sendMail('',
                                $model->email,
                                'Rinkfinder.com contact request copy: ' . CHtml::encode($model->subject),
                                $model->body);
                        
                        if($mailSent === true) {
                            Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_INFO,
                                    'The copy you requested has been e-mailed to you.');
                        } else {
                            Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_ERROR,
                                    'We tried to e-mail you a copy as a requested. An error occurred, please try again later.<br><br>Error: ' . $mailSent);
                        }
                    }
                } else {
                    Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_ERROR,
                            'Thank you for trying to contact us. An error occurred, please try again later.<br><br>Error: ' . $mailSent);
                }
    		$this->refresh();
            }
        }

        // Preload the form if the user is logged in!
        if(!Yii::app()->user->isGuest) {
            if(($names = Yii::app()->user->getState('_names')) !== null) {
                if(isset($names['fullName'])) {
                    $model->name = $names['fullName'];
                }
            }
            
            if(($email = Yii::app()->user->getState('_email')) !== null) {
                $model->email = $email;
            }
        }

        $this->render('contact', array('model' => $model));
    }

    /**
     * Displays the registration page
     */
    function actionRegister()
    {
        $model = new User;
        $profile = new Profile;

        // ajax validator
        if(isset($_POST['ajax']) && $_POST['ajax'] === 'registration-form') {
            echo CActiveForm::validate(array($model, $profile));
            Yii::app()->end();
        }
        
        // If user is already logged in, send them to their profile page!!!
        if(Yii::app()->user->id) {
            $this->redirect(array('profile/view', 'id' => Yii::app()->user->id));
        } else {
            // collect user input data
            if(isset($_POST['User'])) {
                $model->attributes = $_POST['User'];
                $profile->attributes = ((isset($_POST['Profile']) ? $_POST['Profile'] : array()));

                // validate user input and redirect to the welcome! page if valid
                if($model->save()) {
                    //optional
                    $login = new LoginForm;
                    $login->username = $_POST['User']['username'];
                    $login->password = $_POST['User']['passwordSave'];
                
                    if($login->validate() && $login->login()) {
                        $this->render('welcome');
                    }
                    else {
                        $this->redirect('/site/error', $login->getErrors());
                    }
                }
            } else {
                // display the registration form
                $this->render('register', array('model' => $model));
            }
        }
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $model = new LoginForm;

        // if it is ajax validation request
        if(isset($_POST['ajax']) && $_POST['ajax']==='login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if(isset($_POST['LoginForm']))
        {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if($model->validate() && $model->login()) {
                $this->redirect(Yii::app()->user->returnUrl);
            }
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }
}