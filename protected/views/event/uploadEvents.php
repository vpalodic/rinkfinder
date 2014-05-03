<?php
    /* @var $this EventController */
    /* @var $model EventUploadForm */
    /* @var $form TbActiveForm */
    /* @var $fields array[][] */
    /* @var $path string */
    /* @var $doReady boolean */
    /* @var $arenaId integer */
    /* @var $arenaName string */
    /* @var $eventTypes array[] */

    $this->pageTitle = Yii::app()->name . ' - Upload Events';
    $this->breadcrumbs = array(
        'Management' => array('/site/management'),
        'Facilities' => array('//management/index', 'model' => 'arena'),
        CHtml::encode($arenaName) => array('/management/Arena', 'id' => $arenaId),
        'Import Events'
    );
?>

<h2 class="sectionHeader">Import Events</h2>

<?php
    $this->renderPartial('/event/_uploadEvents', array(
        'model' => $model,
        'fields' => $fields,
        'path' => $path,
        'doReady' => $doReady,
        'arenaId' => $arenaId,
        'arenaName' => $arenaName,
        'eventTypes' => $eventTypes
    ));
?>

