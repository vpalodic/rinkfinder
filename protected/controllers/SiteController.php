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
                'actions' => array('error', 'index', 'captcha', 'page', 'contact', 'login', 'logout', 'register', 'activateAccount'),
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
                $this->sendContactEmail($model);
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
        $model = new User('registration');
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
                
                // Preregister the new user!!!
                $model->preRegisterNewUser();
                
                // validate user input and redirect to the welcome! page if valid
                if($model->save()) {
                    $model->postRegisterNewUser();
                    
                    $profile->user_id = $model->id;
                    $profile->save();
                    $profile->postRegisterNewUser();
                    
                    $this->sendWelcomeEmail($model);
                    $this->sendActivationEmail($model);
                    
                    //optional
                    $login = new LoginForm;
                    $login->username = $_POST['User']['username'];
                    $login->password = $_POST['User']['passwordSave'];
                
                    if($login->validate() && $login->login()) {
                        $this->render('welcome');
                    }
                    else {
                        $this->render(
                                'error',
                                array(
                                    'message' => $login->getErrors(),
                                    'code' => 400,
                                )
                        );
                    }
                }
            } else {
                // display the registration form
                $this->render(
                        'register',
                        array(
                            'model' => $model,
                            'profile' => $profile,
                        )
                );
            }
        }
    }

    /**
     * Displays the registration page
     */
    function actionActivateAccount()
    {
        $email = (isset($_GET['email']) ? $_GET['email'] : false);
        $user_key = (isset($_GET['user_key']) ? $_GET['user_key'] : false);
        $activated = false;
        $message = '';
        
        if($email && $user_key) {
            $email = strtolower($email);
            
            // Both $email and $user_key must be valid
            $user = User::model()->find(
                    'LOWER(email) = :email AND user_key = :user_key',
                    array(
                        ':email' => $email,
                        ':user_key' => $user_key,
                    )
            );
            
            if($user) {
                // We found the user account. Now activate it!
                if($user->activateAccount($user_key)) {
                    // The account has been activated!
                    $activated = true;
                    $message = 'Your account has been successfully activated and you may now login!';
                    Yii::app()->user->setFlash(
                            TbHtml::ALERT_COLOR_SUCCESS,
                            $message
                    );
                } else {
                    // Oops, something went wrong!!!
                    $message = 'Unable to activate your account';
                    Yii::app()->user->setFlash(
                            TbHtml::ALERT_COLOR_ERROR,
                            $message
                    );
                }
            } else {
                $message = 'Unable to activate your account';
                Yii::app()->user->setFlash(
                        TbHtml::ALERT_COLOR_ERROR,
                        $message
                );
            }
        } else {
            $message = 'Please enter your E-mail address and User Key as was e-mailed to you.';
        }

        // Display the activation form
        $this->render(
                'activateAccount',
                array(
                    'email' => $email,
                    'user_key' => $user_key,
                    'activated' => $activated,
                    'message' => $message,
                )
        );
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
    
    protected function sendContactEmail($model)
    {
        $data = array();
        $data['description'] = CHtml::encode($model->subject);
        $data['requester'] = array(
            'name' => $model->name,
            'email' => $model->email,
        );
        $to = array('vj.palodichuk@gmail.com' => 'Vincent J. Palodichuk');
        $model->subject = CHtml::encode(Yii::app()->name) . ' contact request: ' . CHtml::encode($model->subject);
        $model->body = nl2br(CHtml::encode($model->body));
        
        $mailSent = Yii::app()->sendMail(
                '',
                $to,
                $model->subject,
                $model->body,
                $data,
                'contact'
        );
        
        if($mailSent == true) {
            Yii::app()->user->setFlash(
                    TbHtml::ALERT_COLOR_SUCCESS,
                    'Thank you for contacting us. We will respond to you as soon as possible.'
            );
        } else {
            Yii::app()->user->setFlash(
                    TbHtml::ALERT_COLOR_ERROR,
                    'Thank you for trying to contact us. An error occurred, please try again later.<br><br>Error: ' . $mailSent
            );
        }
        
        if($model->copyMe) {
            $to = array($model->email => $model->name);
            
            $mailSent = Yii::app()->sendMail(
                    '',
                    $to,
                    'REQUESTED COPY OF: ' . $model->subject,
                    $model->body,
                    $data,
                    'contact'
            );
            
            if($mailSent == true) {
                Yii::app()->user->setFlash(
                        TbHtml::ALERT_COLOR_INFO,
                        'The copy you requested has been e-mailed to you.'
                );
            } else {
                Yii::app()->user->setFlash(
                        TbHtml::ALERT_COLOR_WARNING,
                        'We tried to e-mail you a copy as a requested. An error occurred, please try again later.<br><br>Error: ' . $mailSent
                );
            }
        }
    }
    
    protected function sendWelcomeEmail($user)
    {
        $data = array();
        $data['fullName'] = $user->fullName;
        $to = array($user->email => $user->fullName);
        $subject = 'Welcome ' . CHtml::encode($user->fullName) . ' to ' . CHtml::encode(Yii::app()->name);
        
        $mailSent = Yii::app()->sendMail(
                '',
                $to,
                $subject,
                $subject,
                $data,
                'welcome'
        );
        
        return $mailSent;
    }
    
    protected function sendActivationEmail($user)
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
        $data['user_key'] = $user->user_key;
        
        $to = array($user->email => $user->fullName);
        $subject = CHtml::encode(Yii::app()->name) . ': Account Registration Confirmation';
        
        $mailSent = Yii::app()->sendMail(
                '',
                $to,
                $subject,
                $subject,
                $data,
                'registration'
        );
        
        return $mailSent;
    }
}