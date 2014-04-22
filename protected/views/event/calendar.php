<?php
    /* @var $this EventController */
    /* @var $data mixed[] */
    /* @var $start_date string */
    /* @var $path string */
    /* @var $doReady boolean */
?>

<?php
    $this->renderPartial('/event/_calendar', array(
        'data' => $data,
        '$start_date' => $start_date,
        'path' => $path,
        'doReady' => $doReady,
    ));
?>

