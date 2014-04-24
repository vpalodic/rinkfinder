<?php
    /* @var $this Site              */
    /* @var $path string            */
    /* @var $types []               */
    /* @var $searchUrl []           */
    /* @var $doReady boolean        */

?>

<?php
    $this->renderPartial('/site/_locationSearch', array(
        'types' => $types,
        'path' => $path,
        'searchUrl' => $searchUrl,
        'doReady' => $doReady
    ));
?>