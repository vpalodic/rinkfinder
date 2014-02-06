<?php

/**
 * Resposive Multilevel Flat Menu
 *
 * @author turi
 */
class EFlatMenu extends CWidget {

    public $items = array();

    /**
     * Init widget
     */
    public function init() {
        parent::init();
 
        $cs=Yii::app()->getClientScript();
        $scriptUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.extensions.eflatmenu.resources'));
 
        $cs->registerCssFile($scriptUrl . '/eflatmenu.css');
        if(defined('YII_DEBUG')) {
            $cs->registerCssFile($scriptUrl . '/font-awesome/css/font-awesome.css');
        } else {
            $cs->registerCssFile($scriptUrl . '/font-awesome/css/font-awesome.min.css');
        }
        
        $cs->registerScriptFile($scriptUrl . '/eflatmenu.js');
    }

    protected function registerClientScript() {
        $cs = Yii::app()->clientScript;

        $cs->registerCssFile($this->cssFile);
        $cs->registerCssFile($this->fontawesomeCSSFile);
        $cs->registerCoreScript('jquery');
        $cs->registerScriptFile($this->jsFile, CClientScript::POS_END);
    }

    public function run() {
        $items = $this->items;

        $this->render('view', array('items' => $items));
    }

}

?>
