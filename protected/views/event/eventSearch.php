<?php
    /* @var $this EventController   */
    /* @var $data mixed[]           */
    /* @var $arena Arena            */
    /* @var $start_date string      */
    /* @var $path string            */
    /* @var $doReady boolean        */
?>

<?php
    $this->renderPartial('/event/_eventSearch', array(
        'data' => $data,
        'arena' => $arena,
        '$start_date' => $start_date,
        'path' => $path,
        'doReady' => $doReady,
    ));
?>

