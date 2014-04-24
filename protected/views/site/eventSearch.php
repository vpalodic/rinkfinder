<?php
    /* @var $this Site              */
    /* @var $path string            */
    /* @var $arenas []              */
    /* @var $types []               */
    /* @var $searchUrl []           */
    /* @var $doReady boolean        */
?>

<?php
    $this->renderPartial('/site/_eventSearch', array(
        'types' => $types,
        'arenas' => $arenas,
        'path' => $path,
        'searchUrl' => $searchUrl,
        'doReady' => $doReady
    ));
?>