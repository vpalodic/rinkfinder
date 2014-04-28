<?php
    /**
     * @var $this UserController
     * @var $model User
     * @var $path string
     * @var $doReady boolean
     * @var $newRecord
     * @var $params []
     */

    $this->renderPartial('_view', array(
        'model' => $model,
        'path' => $path,
        'doReady' => $doReady,
        'newRecord' => $newRecord,
        'params' => $params
    ));            
?>