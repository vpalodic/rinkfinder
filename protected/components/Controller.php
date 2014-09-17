<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/column1';
    
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
	
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();

    /**
     * @property boolean controls if the main layout file loads custom CSS files
     */
    public $includeCss = true;

    /**
     * @property boolean controls if the main layout file should include navigation
     */
    public $navigation = true;

    public function registerUserScripts()
    {
        
    }
    public function registerManagementScripts()
    {
        // Publish and register our jQuery plugin
        $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
        
        if(YII_DEBUG && Yii::app()->user->isRestrictedArenaManager()) {
            Yii::app()->clientScript->registerScriptFile($path . '/js/site/management.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/management/_index.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/management/arenaManagementView.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/management/locationManagementView.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/management/contactManagementView.js', CClientScript::POS_END);
//            Yii::app()->clientScript->registerScriptFile($path . '/js/management/_manager.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/management/eventManagementView.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/management/_eventRequest.js', CClientScript::POS_END);
            //Yii::app()->clientScript->registerScriptFile($path . '/js/management/_reservation.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/event/uploadEvents.js', CClientScript::POS_END);
        } elseif(Yii::app()->user->isRestrictedArenaManager()) {
            Yii::app()->clientScript->registerScriptFile($path . '/js/site/management.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/management/_index.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/management/arenaManagementView.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/management/locationManagementView.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/management/contactManagementView.min.js', CClientScript::POS_END);
//            Yii::app()->clientScript->registerScriptFile($path . '/js/management/_manager.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/management/eventManagementView.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/management/_eventRequest.min.js', CClientScript::POS_END);
            //Yii::app()->clientScript->registerScriptFile($path . '/js/management/_reservation.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/event/uploadEvents.min.js', CClientScript::POS_END);
        }
        
    }
    
    public function registerAdministrationScripts()
    {
        // Publish and register our jQuery plugin
        $path = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'));
        
        if(YII_DEBUG && Yii::app()->user->isRestrictedArenaManager()) {
            //Yii::app()->clientScript->registerScriptFile($path . '/js/site/administration.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/arena/uploadArenas.js', CClientScript::POS_END);
        } elseif(Yii::app()->user->isRestrictedArenaManager()) {
            //Yii::app()->clientScript->registerScriptFile($path . '/js/site/administration.min.js', CClientScript::POS_END);
            Yii::app()->clientScript->registerScriptFile($path . '/js/arena/uploadArenas.min.js', CClientScript::POS_END);
        }
        
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
                'allow',  // allow all authenticated users to perform 'index' and 'view' actions
                'controllers' => array('issue', 'project', 'user'),
                'actions' => array('index', 'view', 'addUser'),
                'users' => array('@'),
            ),
            array(
                'allow', // allow authenticated user to perform 'create' and 'update' actions
                'controllers' => array('issue', 'project', 'user'),
                'actions' => array('create', 'update'),
                'users' => array('@'),
            ),
            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions
                'controllers' => array('issue', 'project', 'user'),
                'actions' => array('admin', 'delete'),
                'roles' => array('admin'),
            ),
            array(
                'deny',  // deny all users
                'controllers' => array('issue', 'project', 'user'),
                'users' => array('*'),
            ),
        );
    }
        
    /**
     * Sends Content-type header to be application/json if supported
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
     * Sends Content-type header to be application/json if supported
     * @param integer $status The HTTP 1.1 Status Code to send
     */
    protected function sendResponseHeaders($status, $contentType = null)
    {
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->getStatusCodeMessage($status);
        
        header($status_header);
        
        if($contentType !== null) {
            // This is for IE which doens't handle 'Content-type: application/json' correctly
            header('Vary: Accept');
            switch($contentType) {
                case 'json':
                    if(isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                        header('Content-type: application/json');
                    } elseif (isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/json') !== false)) {
                        header('Content-type: text/json');
                    } else {
                        header('Content-type: text/plain');
                    }        
                    break;
                case 'xml':
                    if(isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/xml') !== false)) {
                        header('Content-Type: application/xml; charset=utf-8');
                    } elseif (isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/xml') !== false)) {
                        header('Content-type: text/xml');
                    } else {
                        header('Content-type: text/plain');
                    }        
                    break;
                case 'html':
                    break;
            }
        }
    }
    
    /**
     * Retrieve a description of the status code
     * @param integer $status The HTTP 1.1 Status Code
     * @return string Returns a string that describes the status code
     */
    protected function getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
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
     * Checks that the PUT method is used
     * @return mixed true if the PUT is being used or else
     * a JSON encoded error string
     */
    public function isPutMethod()
    {
        if(isset($_SERVER['REQUEST_METHOD']) &&
            strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT') != 0) {
            
            return json_encode(
                    array(
                        'success' => false,
                        'error' => 'Request Method must be PUT.',
                    )
            );
        }
        
        return true;        
    }
    
    /**
     * Checks that the POST method is used
     * @return mixed true if the POST is being used or else
     * a JSON encoded error string
     */
    public function isPostMethod()
    {
        if(isset($_SERVER['REQUEST_METHOD']) &&
            strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) {
            
            return json_encode(
                    array(
                        'success' => false,
                        'error' => 'Request Method must be POST.',
                    )
            );
        }
        
        return true;        
    }
    
    /**
     * Checks that the GET method is used
     * @return mixed true if the GET is being used or else
     * a JSON encoded error string
     */
    public function isGetMethod()
    {
        if(isset($_SERVER['REQUEST_METHOD']) &&
            strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') != 0) {
            
            return json_encode(
                    array(
                        'success' => false,
                        'error' => 'Request Method must be GET.',
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
    
    public static function generate_xml_from_array($array, $node_name, $tab = "    ")
    {
        $xml = '';
        
        if (is_array($array) || is_object($array)) {
            foreach ($array as $key=>$value) {
                if (is_numeric($key)) {
                    $key = $node_name;
                }
//                $xml .= $tab . '' . "\n";
                $xml .= $tab . '<' . $key . '>' . "\n" . Controller::generate_xml_from_array($value, $node_name, $tab . "    ") . $tab . '</' . $key . '>' . "\n";
//                $xml .= $tab . '' . "\n";
            }
	} else {
            $xml = $tab . htmlspecialchars($array, ENT_QUOTES) . "\n";
        }
        
        return $xml;
    }

    public static function generate_valid_xml_from_array($array, $node_block='nodes', $node_name='node')
    {
	$xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

	$xml .= '<' . $node_block . '>' . "\n";
	$xml .= Controller::generate_xml_from_array($array, $node_name);
	$xml .= '</' . $node_block . '>' . "\n";

	return $xml;
    }

    /**
     * Formats a line (passed as a fields  array) as CSV and returns the CSV as a string.
     * Adapted from http://us3.php.net/manual/en/function.fputcsv.php#87120
     */
    public function arrayToCsv( array &$fields, $delimiter = ',', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false )
    {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');

        $output = array();
        foreach ( $fields as $field ) {
            if ($field === null && $nullToMysqlNull) {
                $output[] = 'NULL';
                continue;
            }

            // Enclose fields containing $delimiter, $enclosure or whitespace
            if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
                $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
            }
            else {
                $output[] = $field;
            }
        }

        return implode( $delimiter, $output );
    }
}