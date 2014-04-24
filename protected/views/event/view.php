<?php
    /* @var $this EventController   */
    /* @var $data mixed[]           */
    /* @var $arena Arena            */
    /* @var $path string            */
    /* @var $doReady boolean        */
?>

<h2>Event #<?php echo $data['records'][0]['id']; ?></h2>

<?php 
    $this->renderPartial('_view', array(
        'data' => $data,
        'arena' => $arena,
        'doReady' => false,
        'path' => $path
    ));
?>