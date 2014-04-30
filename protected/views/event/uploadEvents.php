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
        CHtml::encode($arenaName) => array('/management/Arena', 'id' => $arenaId),
        'Upload Events'
    );
?>

<h2 class="sectionHeader">Upload Events</h2>

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

