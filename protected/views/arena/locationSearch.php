<?php
    /* @var $this ArenaController   */
    /* @var $path string            */
    /* @var $types []               */
    /* @var $searchUrl []           */
    /* @var $doReady boolean        */

    $this->pageTitle = Yii::app()->name . ' - Find a Facility Near You!';
    $this->breadcrumbs = array(
        'Facility Search',
    );
?>
<?php
?>

<h2 class="sectionHeader">Facility Search</h2>

<?php
    $this->renderPartial('/arena/_locationSearch', array(
        'types' => $types,
        'path' => $path,
        'searchUrl' => $searchUrl,
        'doReady' => $doReady
    ));
?>