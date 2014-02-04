<?php

class SiteController extends Controller
{
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
        $registered = false;
        $message = '';

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
                
                if($model->validate() && $profile->validate()) {
                    // Preregister the new user!!!
                    $model->preRegisterNewUser();
                
                    // Register the account!!!!
                    if($model->save()) {
                        $model->postRegisterNewUser();
                    
                        $profile->user_id = $model->id;
                        $profile->save();
                        $profile->postRegisterNewUser();
                    
                        $this->sendWelcomeEmail($model);
                        $this->sendActivationEmail($model);
                    
                        $registered = true;
                        $message = '<h4>Registration Completed!</h4>';
                        $message .= 'Thank you for registering with ' . CHtml::encode(Yii::app()->name) . '. ';
                        $message .= 'In order to login and start using the site, you must first activate your account. ';
                        $message .= 'Please check your e-mail for instructions on how to activate your account.';
                        Yii::app()->user->setFlash(
                                TbHtml::ALERT_COLOR_SUCCESS,
                                $message
                        );
                    }
                } else {
                    $profile->validate();
                }
            }
        }
        
        // display the registration form
        $this->render(
                'register',
                array(
                    'model' => $model,
                    'profile' => $profile,
                    'registered' => $registered,
                )
        );
    }

    /**
     * Displays the activation page
     */
    function actionActivateAccount()
    {
        $email = (isset($_GET['email']) ? $_GET['email'] : false);
        $user_key = (isset($_GET['user_key']) ? $_GET['user_key'] : false);
        $resendEmail = (isset($_GET['resendEmail']) ? $_GET['resendEmail'] : false);
        $activated = false;
        $message = '';
        
        if($email) {
            $email = strtolower($email);
            
            // Both $email and $user_key must be valid but, we will only
            // search by $email to find the account
            $user = User::model()->find(
                    'LOWER(email) = :email',
                    array(
                        ':email' => $email,
                    )
            );
            
            if($user !== null) {
                if($user_key) {
                    // We found the user account. Now activate it!
                    if($user->activateAccount($user_key)) {
                        // The account has been activated!
                        $activated = true;
                        $message = '<h4>Account Activated!</h4>';
                        $message .= 'Your account has been successfully activated and you may now login!';
                        Yii::app()->user->setFlash(
                                TbHtml::ALERT_COLOR_SUCCESS,
                                $message
                        );
                    } elseif($user->isNotActivated()) {
                        // Oops, something went wrong!!!
                        $message = '<h4>Account Not Activated!</h4>';
                        $message .= 'Unable to activate your account as the User Key entered is no longer valid.';
                        Yii::app()->user->setFlash(
                                TbHtml::ALERT_COLOR_ERROR,
                                $message
                        );
                    } elseif($user->isActive()) {
                        // Oops, something went wrong!!!
                        $activated = true;
                        $message = '<h4>Account Already Activated!</h4>';
                        $message .= 'Your account is active and has already been activated. Please ';
                        $message .= '<a href="' . $this->createUrl('site/login') . '">login.</a>';
                        Yii::app()->user->setFlash(
                                TbHtml::ALERT_COLOR_SUCCESS,
                                $message
                        );
                    } else {
                        // Oops, something went wrong!!!
                        $activated = true;
                        $message = '<h4>Account Already Activated!</h4>';
                        $message .= 'Your account has already been activated but it is ' . $user->itemAlias('UserStatus', $user->status_id) . '!';
                        Yii::app()->user->setFlash(
                                TbHtml::ALERT_COLOR_ERROR,
                                $message
                        );
                    }
                }
                
                if($resendEmail) {
                    if($this->sendActivationEmail($user) == true) {
                        Yii::app()->user->setFlash(
                                TbHtml::ALERT_COLOR_INFO,
                                '<h4>Activation E-mail Sent Successfully</h4>'
                        );
                    } else {
                        Yii::app()->user->setFlash(
                                TbHtml::ALERT_COLOR_WARNING,
                                '<h4>An error occurred while sending the Activation E-mail.</h>Please try again later.<br><br>Error: ' . $mailSent
                        );
                    }
                }
            } else {
                $message = '<h4>Account Not Found!</h4>';
                $message .= 'Unable to locate an account with the provided e-mail address.';
                Yii::app()->user->setFlash(
                        TbHtml::ALERT_COLOR_ERROR,
                        $message
                );
            }
        } else {
            $message = 'Please enter your E-mail Address and User Key as was e-mailed to you.';
            Yii::app()->user->setFlash(
                    TbHtml::ALERT_COLOR_WARNING,
                    $message
            );
        }

        // Display the activation form
        $this->render(
                'activateAccount',
                array(
                    'email' => $email,
                    'user_key' => $user_key,
                    'resendEmail' => $resendEmail,
                    'activated' => $activated,
                    'message' => $message,
                )
        );
    }

    /**
     * Displays the activation page
     */
    function actionResetAccount()
    {
        // If the user is logged in, then no need to be here!
        if(Yii::app()->user->id) {
            $this->redirect(Yii::app()->user->returnUrl);
        }
                
        $email = (isset($_GET['email']) ? $_GET['email'] : false);
        $user_key = (isset($_GET['user_key']) ? $_GET['user_key'] : false);
        $sendEmail = (isset($_GET['sendEmail']) ? $_GET['sendEmail'] : false);
        $reset = false;
        $message = '';
        
        if($email && $user_key) {
            $email = strtolower($email);
            
            // We will only search by $email to find the account
            $user = User::model()->find(
                    'LOWER(email) = :email',
                    array(
                        ':email' => $email,
                    )
            );
            
            if(isset($user) && $user->user_key == $user_key) {
                $model = new UserChangePassword;
                if(isset($_POST['UserChangePassword'])) {
                    $model->attributes = $_POST['UserChangePassword'];
                    if($model->validate()) {
                        if($user->activateAccount($user_key)) {
                            if($user->setPassword($model->passwordSave)) {
                                $message = '<h4>New password has been saved!</h4>';
                                $message .= 'You may now login to your account using your new password.';
                                
                                Yii::app()->user->setFlash(
                                        TbHtml::ALERT_COLOR_SUCCESS,
                                        $message
                                );
                            } else {
                                $message = '<h4>Error saving password!</h4>';
                                $message .= 'Your new password has not been saved. Please restart the process.';
                                
                                Yii::app()->user->setFlash(
                                        TbHtml::ALERT_COLOR_ERROR,
                                        $message
                                );
                            }
                        } else {
                            $message = '<h4>Error recovering account!</h4>';
                            $message .= 'Unable to recover your account. Your account status is ';
                            $message .= $user->itemAlias('UserStatus', $user->status_id) . '.';
                                
                            Yii::app()->user->setFlash(
                                    TbHtml::ALERT_COLOR_ERROR,
                                    $message
                            );
                        }
			$this->redirect('resetAccount');
                    }
                }
                $this->render(
                        'resetAccount',
                        array(
                            'model' => $model
                        )
                );
            } else {
                $message = "<h4>Invalid recovery link!</h4>";
                
                Yii::app()->user->setFlash(
                        TbHtml::ALERT_COLOR_ERROR,
                        $message
                );
                
                $this->redirect('resetAccount');
            }
        } elseif($email && $sendEmail) {
            $email = strtolower($email);
            
            // We will only search by $email to find the account
            $user = User::model()->find(
                    'LOWER(email) = :email',
                    array(
                        ':email' => $email,
                    )
            );
            
            if(isset($user)) {
                if($user->resetUser() == true) {
                    if($this->sendRecoveryEmail($user) == true) {
                        $reset = true;
                        $message = '<h4>Recovery E-mail Sent Successfully</h4>';
                        $message .= 'Instructions on recoverying your account have been e-mailed to you.';
                        Yii::app()->user->setFlash(
                                TbHtml::ALERT_COLOR_INFO,
                                $message
                        );
                    } else {
                        $message = '<h4>An error occurred while sending the Recovery E-mail.</h>';
                        $message .= 'Please try again later.<br><br>Error: ' . $mailSent;
                        Yii::app()->user->setFlash(
                                TbHtml::ALERT_COLOR_WARNING,
                                $message
                        );
                    }
                } else {
                    $message = '<h4>Error recovering account!</h4>';
                    $message .= 'Unable to recover your account. Your account status is ';
                    $message .= $user->itemAlias('UserStatus', $user->status_id) . '.';
                    
                    Yii::app()->user->setFlash(
                            TbHtml::ALERT_COLOR_ERROR,
                            $message
                    );
                }
            } else {
                $message = '<h4>Account Not Found!</h4>';
                $message .= 'Unable to locate an account with the provided e-mail address.';
                Yii::app()->user->setFlash(
                        TbHtml::ALERT_COLOR_ERROR,
                        $message
                );
            }

            // Display the reset account form
            $this->render(
                    'resetAccount',
                    array(
                        'email' => $email,
                        'user_key' => $user_key,
                        'reset' => $reset,
                        'message' => $message,
                    )
            );
        } else {
            $message = 'Please enter the E-mail Address you registered with to begin the Account Recovery process.';
            Yii::app()->user->setFlash(
                    TbHtml::ALERT_COLOR_WARNING,
                    $message
            );

            // Display the reset account form
            $this->render(
                    'resetAccount',
                    array(
                        'email' => $email,
                        'user_key' => $user_key,
                        'reset' => $reset,
                        'message' => $message,
                    )
            );
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
                        'We tried to e-mail you a copy as requested. An error occurred, please try again later.<br><br>Error: ' . $mailSent
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
    
    protected function sendRecoveryEmail($user)
    {
        $data = array();
        $data['fullName'] = $user->fullName;
        $data['recoveryUrl'] = $this->createAbsoluteUrl(
                'site/resetAccount',
                array(
                    'user_key' => $user->user_key,
                    'email' => $user->email
                )
        );
        $data['manualUrl'] = $this->createAbsoluteUrl(
                'site/resetAccount',
                array(
                    'email' => $user->email
                )
        );
        $data['username'] = $user->username;
        $data['email'] = $user->email;
        $data['user_key'] = $user->user_key;
        
        $to = array($user->email => $user->fullName);
        $subject = CHtml::encode(Yii::app()->name) . ': Account Recovery Instructions';
        
        $mailSent = Yii::app()->sendMail(
                '',
                $to,
                $subject,
                $subject,
                $data,
                'account_recovery'
        );
        
        return $mailSent;
    }
}