<?php
/* @var $this ArenaController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php
    $this->breadcrumbs = array(
        'Facilities',
    );

    $this->menu = array(
        array(
            'label' => 'Create Facility',
            'url' => array('create')
        ),
        array(
            'label' => 'Manage Facility',
            'url' => array('admin')
        ),
    );
?>

<h2>Facilities</h2>

<div class="well well-large">
    <?php $dataProvider->setPagination(false); foreach($dataProvider->getData() as $record) : ?>
    <div class="row-fluid info-row">
        <div class="span1">
            <img class="img-circle"
                 src="<?php echo Yii::app()->request->baseUrl . '/images/generic_facility_sm.jpg'; ?>"
                 alt="User Pic">
        </div>
        <div class="span10">
            <strong><?php echo $record->name; ?></strong><br>
            <span class="text-muted"><?php echo $record->city . ' ' . UnitedStatesNames::getName($record->state); ?></span>
        </div>
        <div class="span1 dropdown-info" data-for=".<?php echo preg_replace("/[^A-Za-z0-9]/", "", $record->name) . $record->id; ?>">
            <i class="fa fa-lg fa-chevron-down text-muted"></i>
        </div>
    </div>
    <div class="row-fluid info-infos <?php echo preg_replace("/[^A-Za-z0-9]/", "", $record->name) . $record->id; ?>">
        <div class="span10 offset1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Facility information</h3>
                </div>
                <div class="panel-body">
                    <div class="row-fluid">
                        <div class="span3">
                            <img class="img-circle"
                                 src="<?php echo Yii::app()->request->baseUrl . '/images/generic_facility.jpg'; ?>"
                                 alt="User Pic">
                        </div>
                        <div class="span6">
                            <strong><?php echo $record->name; ?></strong><br>
                            <table class="table table-condensed table-responsive table-information">
                                <tbody>
                                    <tr>
                                        <td>Address:</td>
                                        <td>
                                            <address>
                                                <?php echo $record->address_line1; ?><br />
                                                <?php if(isset($record->address_line2) && !empty($record->address_line2)) {
                                                    echo $record->address_line2 . '<br />';
                                                    } ?>
                                                <?php echo $record->city; ?>, <?php echo $record->state; ?> <?php echo $record->zip; ?>
                                            </address>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Get Directions:</td>
                                        <td>
                                            <a target="_blank" href="http://maps.google.com/maps?daddr=<?php echo urlencode($record->address_line1 . ', ' . $record->city . ', ' . $record->state . ' ' . $record->zip); ?>">
                                                Google Maps
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Topics</td>
                                        <td>15</td>
                                    </tr>
                                    <tr>
                                        <td>Warnings</td>
                                        <td>0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button class="btn  btn-primary" type="button"
                            data-toggle="tooltip"
                            data-original-title="Send message to user"><i class="icon-envelope icon-white"></i></button>
                    <span class="pull-right">
                        <button class="btn btn-warning" type="button"
                                data-toggle="tooltip"
                                data-original-title="Edit this user"><i class="icon-edit icon-white"></i></button>
                        <button class="btn btn-danger" type="button"
                                data-toggle="tooltip"
                                data-original-title="Remove this user"><i class="icon-remove icon-white"></i></button>
                    </span>
                </div>
            </div>
        </div>
    </div>
     <?php endforeach; ?>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var panels = $('.info-infos');
        var panelsButton = $('.info-row');
        panels.hide();
        
        //Click dropdown
        panelsButton.click(function() {
            //get data-for attribute
            var currentButton = $(this).find('.dropdown-info');
            var dataFor = currentButton.attr('data-for');
            var idFor = $(dataFor);
            
            //current button
            idFor.slideToggle(400, function() {
                //Completed slidetoggle
                if(idFor.is(':visible'))
                {
                    currentButton.html('<i class="fa fa-lg fa-chevron-up text-muted"></i>');
                }
                else
                {
                    currentButton.html('<i class="fa fa-lg fa-chevron-down text-muted"></i>');
                }
            });
        });
        
        $('[data-toggle="tooltip"]').tooltip();

        $('button').click(function(e) {
            e.preventDefault();
            alert("This is a demo.\n :-)");
        });
    });
</script>