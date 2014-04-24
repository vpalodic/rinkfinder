<?php
    /* @var $this ArenaController   */
    /* @var $data mixed[]           */
    /* @var $start_date string      */
    /* @var $doReady boolean        */
    /* @var $path string            */
?>

<?php 
    $this->renderPartial('_view', array(
        'data' => $data,
        'start_date' => $start_date,
        'doReady' => false,
        'path' => $path        
    ));