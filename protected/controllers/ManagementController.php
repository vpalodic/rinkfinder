<?php

class ManagementController extends Controller
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
            'ajaxOnly', // we only allow ajax calls!
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
                'allow', // allow authenticated users only!!!
                'actions' => array(
                    'getCounts',
                ),
                'users' => array(
                    '@'
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
     * Action method to upload a file.
     */
    public function actionGetCounts()
    {
        Yii::trace("In actionGetCounts.", "application.controllers.ManagementController");
        
        if(!Yii::app()->user->isRestrictedArenaManager()) {
            $this->sendResponseHeaders(403);
            echo json_encode(array(
                    'success' => false,
                    'error' => 'Permission denied. You are not authorized to perform this action.'
                )
            );
            Yii::app()->end();
        }

        if(!isset($_GET['for']) && !is_array($_GET['for'])) {
            $this->sendResponseHeaders(400);
            echo json_encode(
                    array(
                        'success' => false,
                        'error' => 'Missing expected parameters',
                    )
            );
            Yii::app()->end();
        }        

        $user = Yii::app()->user->model;
        $dashData = null;
        
        try {
            $dashData = $user->getManagementDashboardCounts($_GET['for']);
        } catch(Exception $ex) {
            $errorInfo = null;
            
            if(isset($ex->errorInfo) && !empty($ex->errorInfo)) {
                $errorInfo = array(
                    "sqlState" => $ex->errorInfo[0],
                    "mysqlError" => $ex->errorInfo[1],
                    "message" => $ex->errorInfo[2],
                );
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
        
        // Data has been retrieved
        $this->sendResponseHeaders(200);

        echo json_encode(
                array(
                    'succes' => true,
                    'error' => false,
                    'for' => $dashData,
                )
        );
        
        Yii::app()->end();
    }
}