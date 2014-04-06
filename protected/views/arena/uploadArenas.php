<?php
    /* @var $this ArenaController */
    /* @var $model ArenaUploadForm */
    /* @var $form TbActiveForm */
    /* @var $fields array[][] */
    /* @var $path string */
    /* @var $doReady boolean */

    $this->pageTitle = Yii::app()->name . ' - Upload Arenas';
    $this->breadcrumbs = array(
        'Administration' => array('/site/administration'),
        'Upload Arenas',
    );
?>
<?php
?>

<h2 class="sectionHeader">Upload Arenas</h2>

<?php
    $this->renderPartial('/arena/_uploadArenas', array(
        'model' => $model,
        'fields' => $fields,
        'path' => $path,
        'doReady' => $doReady
    ));
?>