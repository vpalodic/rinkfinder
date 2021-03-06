<?php
    /**
     * This doubles as both a view/edit form for existing records
     * 
     * @var $this ManagementController
     * @var $model Arena
     * @var $path string
     * @var $doReady boolean
     * @var $newRecord
     * @var $params []
     */

    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<?php $attributeLabels = $model->attributeLabels(); ?>
<ul class="nav nav-tabs">
    <li class="active">
        <a href="#arenaTabPane" data-toggle="tab">Facility</a>
    </li>
    <li>
        <a href="#contactsTabPane" data-toggle="tab">Contacts</a>
    </li>
    <li>
        <a href="#locationsTabPane" data-toggle="tab">Venues</a>
    </li>
    <li>
        <a href="#eventsTabPane" data-toggle="tab">Events</a>
    </li>
<?php if(Yii::app()->user->isArenaManager()) : ?>
    <li>
        <a href="#managersTabPane" data-toggle="tab">Managers</a>
    </li>
<?php endif; ?>
</ul>

<div id="arenaTabContent" class="tab-content">
    <div id="arenaTabPane" class="tab-pane active in">
        <div id="arenaManagementView" class="panel panel-primary">
            <div class="panel-heading">
                <h3 style="background-color: #FFFFFF">
                    <a href="#"
                       id="Arena_name"
                       data-name="name"
                       data-type="text"
                       data-pk="<?php echo $model->id; ?>"
                       data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                       data-mode="inline"
                       data-value="<?php echo $model->name; ?>"
                       title="Unique Facility Name"
                       class="arena-editable">
                       <?php echo $model->name; ?>
                    </a>
                </h3>
            </div>
            <div class="panel-body">
                <div class="row-fluid">
                    <div class="span3">
                        Logo: 
                        <a href="#"
                           id="Arena_logo"
                           data-name="logo"
                           data-type="text"
                           data-pk="<?php echo $model->id; ?>"
                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                           data-mode="inline"
                           data-value="<?php echo $model->logo; ?>"
                           title="Facility Logo"
                           class="arena-editable"
                           style="word-break:break-all;word-wrap:break-word">
                           <?php echo $model->logo; ?>
                        </a>
                        <br />
                        <img id="Arena_logo_img"
                             class="img-circle"
                             src="<?php echo $model->logo; ?>"
                             alt="Facility Logo" />
                    </div>
                    <div class="span9">
                        <strong>Details</strong><br />
                        <table class="table table-condensed table-information">
                            <tbody>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['url']; ?>
                                        (Must prefix with http://)
                                    </td>
                                    <td>
                                        <a href="#"
                                           id="Arena_url"
                                           data-name="url"
                                           data-type="text"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-value="<?php echo $model->url; ?>"
                                           data-mode="inline"
                                           title="Facility Home Page"
                                           class="arena-editable"
                                           style="word-break:break-all;word-wrap:break-word">
                                           <?php echo $model->url; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['external_id']; ?>
                                    </td>
                                    <td>
                                        <a href="#"
                                           id="Arena_external_id"
                                           data-name="external_id"
                                           data-type="text"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-value="<?php echo $model->external_id; ?>"
                                           data-mode="inline"
                                           title="Your unique ID for the Facility"
                                           class="arena-editable">
                                           <?php echo $model->external_id; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['tags']; ?>
                                    </td>
                                    <td>
                                        <a href="#"
                                           id="Arena_tags"
                                           data-name="tags"
                                           data-type="text"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-value="<?php echo $model->tags; ?>"
                                           data-mode="inline"
                                           title="Facility Tags"
                                           class="arena-editable">
                                           <?php echo $model->tags; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['status_id']; ?>
                                    </td>
                                    <td>
                                        <a href="#"
                                           id="Arena_status_id"
                                           data-name="status_id"
                                           data-type="select"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-value="<?php echo $model->status_id; ?>"
                                           data-mode="inline"
                                           title="Facility Status"
                                           class="arena-editable">
                                           <?php echo $model->getStatusAlias(); ?>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <strong>Address & Phone</strong><br />
                        <table class="table table-condensed table-information">
                            <tbody>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['address_line1']; ?>
                                    </td>
                                    <td>
                                        <a href="#"
                                           id="Arena_address_line1"
                                           data-name="address_line1"
                                           data-type="text"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-mode="inline"
                                           data-value="<?php echo $model->address_line1; ?>"
                                           title="Address Line 1"
                                           class="arena-editable">
                                           <?php echo $model->address_line1; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['address_line2']; ?>
                                    </td>
                                    <td>
                                        <a href="#"
                                           id="Arena_address_line2"
                                           data-name="address_line2"
                                           data-type="text"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-mode="inline"
                                           data-value="<?php echo $model->address_line2; ?>"
                                           title="Address Line 2"
                                           class="arena-editable">
                                           <?php echo $model->address_line2; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['city']; ?>
                                    </td>
                                    <td>
                                        <a href="#"
                                           id="Arena_city"
                                           data-name="city"
                                           data-type="text"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-mode="inline"
                                           data-value="<?php echo $model->city; ?>"
                                           title="City"
                                           class="arena-editable">
                                           <?php echo $model->city; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['state']; ?>
                                    </td>
                                    <td>
                                        <a id="Arena_state"
                                           href="#"
                                           data-name ="state"
                                           data-type="select"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-mode="inline"
                                           data-value="<?php echo $model->state; ?>"
                                           title="State"
                                           class="arena-editable">
                                           <?php echo UnitedStatesNames::getName($model->state); ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['zip']; ?>
                                    </td>
                                    <td>
                                        <a href="#"
                                           id="Arena_zip"
                                           data-name="zip"
                                           data-type="text"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-mode="inline"
                                           data-value="<?php echo $model->zip; ?>"
                                           title="Five digit zip-code"
                                           class="arena-editable">
                                           <?php echo $model->zip; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:33%">
                                        Geocoding
                                    </td>
                                    <td>
                                    <?php if(isset($model->address_line2) && !empty($model->address_line2)) : ?>
                                        <a id="Arena_directions" target="_blank" href="http://maps.google.com/maps?daddr=<?php echo urlencode($model->address_line1 . ', ' . $model->address_line2 . ', ' . $model->city . ', ' . $model->state . ' ' . $model->zip); ?>">
                                            Driving Directions
                                        </a><br />
                                    <?php elseif(isset($model->address_line1) && isset($model->city)) : ?>
                                        <a id="Arena_directions" target="_blank" href="http://maps.google.com/maps?daddr=<?php echo urlencode($model->address_line1 . ', ' . $model->city . ', ' . $model->state . ' ' . $model->zip); ?>">
                                            Driving Directions
                                        </a><br />
                                    <?php endif; ?>
                                        <button id="Arena_geocode_btn"
                                                type="button"
                                                class="btn btn-success">
                                            <i class="fa fa-fw fa-lg fa-map-marker">
                                                
                                            </i>
                                            <span>Update</span>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['phone']; ?>
                                    </td>
                                    <td>
                                        <a href="#"
                                           id="Arena_phone"
                                           data-name="phone"
                                           data-type="text"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-mode="inline"
                                           data-value="<?php echo $model->phone; ?>"
                                           title="Ten digit phone number"
                                           class="arena-editable">
                                           <?php echo $model->phone; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['ext']; ?>
                                    </td>
                                    <td>
                                        <a href="#"
                                           id="Arena_ext"
                                           data-name="ext"
                                           data-type="text"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-mode="inline"
                                           data-value="<?php echo $model->ext; ?>"
                                           title="Phone extension"
                                           class="arena-editable">
                                           <?php echo $model->ext; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['fax']; ?>
                                    </td>
                                    <td>
                                        <a href="#"
                                           id="Arena_fax"
                                           data-name="fax"
                                           data-type="text"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-mode="inline"
                                           data-value="<?php echo $model->fax; ?>"
                                           title="Ten digit fax number"
                                           class="arena-editable">
                                           <?php echo $model->fax; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['fax_ext']; ?>
                                    </td>
                                    <td>
                                        <a href="#"
                                           id="Arena_fax_ext"
                                           data-name="fax_ext"
                                           data-type="text"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-mode="inline"
                                           data-value="<?php echo $model->fax_ext; ?>"
                                           title="Fax extension"
                                           class="arena-editable">
                                           <?php echo $model->fax_ext; ?>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <strong>Description & Notes</strong><br />
                        <table class="table table-condensed table-information">
                            <tbody>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['description']; ?>
                                        <i class="fa fa-lg fa-fw fa-pencil" style="padding-right: 5px"></i>
                                        <a href="#" id="Arena_description_edit">
                                            <span>[edit]</span>
                                        </a>
                                    </td>
                                    <td>
                                        <div id="Arena_description"
                                           data-name="description"
                                           data-type="wysihtml5"
                                           data-toggle="manual"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-mode="inline"
                                           data-title="Description of the facility"
                                           title="Description of the facility"
                                           class="arena-editable">
                                           <?php echo $model->description; ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:33%">
                                        <?php echo $attributeLabels['notes']; ?>
                                        <i class="fa fa-lg fa-fw fa-pencil" style="padding-right: 5px"></i>
                                        <a href="#" id="Arena_notes_edit">
                                            <span>[edit]</span>
                                        </a>
                                    </td>
                                    <td>
                                        <div id="Arena_notes"
                                           data-name="notes"
                                           data-type="wysihtml5"
                                           data-toggle="manual"
                                           data-pk="<?php echo $model->id; ?>"
                                           data-url="<?php echo $params['endpoints']['arena']['update']; ?>"
                                           data-mode="inline"
                                           data-title="Additional notes"
                                           title="Additional notes"
                                           class="arena-editable">
                                           <?php echo $model->notes; ?>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    // We are going to grab two lists of contacts for the list views
    // We only need the contact name and id, we don't need anything else
    $availableContacts = Contact::getAvailable(Yii::app()->user->id, $model->id, 0);
    $assignedContacts = Contact::getAssigned(Yii::app()->user->id, $model->id, 0);
    ?>
    <div id="contactsTabPane" class="tab-pane fade">
        <div id="contactManagementView" class="panel panel-primary">
            <div class="panel-heading">
                <h3>
                    Contacts
                </h3>
            </div>
            <div class="panel-body">
                <div class="row-fluid">
                    <div class="span6">
                        <strong>Available Contacts</strong><br />
                        <select id="availableContactsMSelect"
                                multiple
                                class="span12">
                            <?php foreach($availableContacts as $aavc) : ?>
                            <option value="<?php echo $aavc['id']; ?>">
                                <?php echo $aavc['last_name'] . ', ' . $aavc['first_name'] . ' - ' . $aavc['email'] . ($aavc['active'] == 1 ? ' (Active)' : ' (Inactive)'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="span6">
                        <strong>Assigned Contacts</strong><br />
                        <select id="assignedContactsMSelect"
                                multiple
                                class="span12">
                            <?php foreach($assignedContacts as $aac) : ?>
                            <option value="<?php echo $aac['id']; ?>">
                                <?php echo $aac['last_name'] . ', ' . $aac['first_name'] . ' - ' . $aac['email'] . ($aac['active'] == 1 ? ' (Active)' : ' (Inactive)'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <strong>Assignment Actions</strong><br />
                        <div class="well">
                            <div class="row-fluid">
                                <div class="span3 offset3">
                                    <button class="btn btn-block btn-success"
                                            type="button"
                                            data-toggle="tooltip"
                                            data-original-title="Assign contact to this facility"
                                            id="assignContactButton">
                                        <i class="fa fa-lg fa-fw fa-chevron-right"></i> <br />
                                        <span>Assign</span>
                                    </button>
                                </div>
                                <div class="span3">
                                    <button class="btn btn-block btn-warning"
                                            type="button"
                                            data-toggle="tooltip"
                                            data-original-title="Unassign contact from this facility"
                                            id="unassignContactButton">
                                        <i class="fa fa-lg fa-fw fa-chevron-left"></i> <br />
                                        <span>Unassign</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <strong>Select a contact to view or edit</strong><br />
                        <select id="assignedContactsSelect"
                                class="span12">
                            <option value="none"></option>
                            <?php foreach($assignedContacts as $aac) : ?>
                            <option value="<?php echo $aac['id']; ?>">
                                <?php echo $aac['last_name'] . ', ' . $aac['first_name'] . ' - ' . $aac['email'] . ($aac['active'] == 1 ? ' (Active)' : ' (Inactive)'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <strong>Contact Actions</strong><br />
                        <div class="well">
                            <div class="row-fluid">
                                <div class="span3 offset3">
                                    <button class="btn btn-block btn-primary"
                                            type="button"
                                            data-toggle="tooltip"
                                            data-original-title="Create a new contact"
                                            id="newContactButton">
                                        <i class="fa fa-lg fa-plus-square"></i> <br />
                                        <span>New</span>
                                    </button>
                                </div>
                                <div class="span3">
                                    <button class="btn btn-block btn-danger"
                                            type="button"
                                            data-toggle="tooltip"
                                            data-original-title="Delete this contact"
                                            id="deleteContactButton">
                                        <i class="fa fa-lg fa-minus-square"></i> <br />
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                    
                <div class="row-fluid">
                    <div class="span8 offset2">
                        <div id="contactDetails">
                            
                        </div>
                        <div id="newContactButtons">
                            <button id="saveContactButton"
                                    class="btn btn-large btn-primary"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Save the new contact">
                                <i class="fa fa-lg fa-fw fa-check"></i>
                                <span>Save</span>
                            </button>
                            <button id="cancelContactButton"
                                    class="btn btn-large pull-right"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Cancel adding a new contact">
                                <i class="fa fa-lg fa-fw fa-times"></i>
                                <span>Cancel</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    // We are going to grab a single list for the list view
    // We only need the location name and id, we don't need anything else
    $locations = Location::getAvailable($model->id);
    $locationsStatuses = Location::getStatusesList();
    $locationsTypes = Location::getTypesList();
    ?>
    <div id="locationsTabPane" class="tab-pane fade">
        <div id="locationManagementView" class="panel panel-primary">
            <div class="panel-heading">
                <h3>
                    Venues
                </h3>
            </div>
            <div class="panel-body">
                <div class="row-fluid">
                    <div class="span12">
                        <strong>Select a venue to view or edit</strong><br />
                        <select id="locationsSelect"
                                class="span12">
                            <option value="none"></option>
                            <?php foreach($locations as $location) : ?>
                            <option value="<?php echo $location['id']; ?>">
                                <?php echo $location['name'] . ' - ' . $location['type'] . ' (' . $location['status'] . ')'; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <strong>Venue Actions</strong><br />
                        <div class="well">
                            <div class="row-fluid">
                                <div class="span3 offset3">
                                    <button class="btn btn-block btn-primary"
                                            type="button"
                                            data-toggle="tooltip"
                                            data-original-title="Create a new venue"
                                            id="newLocationButton">
                                        <i class="fa fa-lg fa-plus-square"></i> <br />
                                        <span>New</span>
                                    </button>
                                </div>
                                <div class="span3">
                                    <button class="btn btn-block btn-danger"
                                            type="button"
                                            data-toggle="tooltip"
                                            data-original-title="Delete this venue"
                                            id="deleteLocationButton">
                                        <i class="fa fa-lg fa-minus-square"></i> <br />
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span8 offset2">
                        <div id="locationDetails">
                            
                        </div>
                        <div id="newLocationButtons">
                            <button id="saveLocationButton"
                                    class="btn btn-large btn-primary"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Save the new venue">
                                <i class="fa fa-lg fa-fw fa-check"></i>
                                <span>Save</span>
                            </button>
                            <button id="cancelLocationButton"
                                    class="btn btn-large pull-right"
                                    type="button"
                                    data-toggle="tooltip"
                                    data-original-title="Cancel adding a new venue">
                                <i class="fa fa-lg fa-fw fa-times"></i>
                                <span>Cancel</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    // Grab the event status and types. We don't pre-load any events
    // as that would be too time consuming and very wasteful!
    $eventsStatuses = Event::getStatusesList();
    $eventsTypes = Event::getTypesList();
    ?>
    <div id="eventsTabPane" class="tab-pane fade">
        <div id="eventManagementView" class="panel panel-primary">
            <div class="panel-heading">
                <h3>
                    Events
                </h3>
            </div>
            <div class="panel-body">
                <div class="row-fluid">
                    <div class="span12">
                        <strong>Event List</strong><br />
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="span9">
                            <button id="retrieveEventButton" class="btn"
                                    type="button" data-toggle="tooltip"
                                    data-original-title="Retrieve all events for this Facility">
                                <i class="fa fa-fw fa-lg fa-search"></i><br />
                                <span>Retrieve Events</span>                            
                            </button>
                            <span> </span>
                            <a class="btn btn-success"
                               href="<?php echo $params['endpoints']['event']['import']; ?>"
                               target="_blank"
                               id="importEventButton"
                               data-toggle="tooltip"
                               data-original-title="Import Events (Opens in a new window)">
                                <i class="fa fa-lg fa-fw fa-upload"></i><br />
                                <span>Import Events</span>
                            </a>
                            <span> </span>
                            <button id="exportEventButton" class="btn btn-warning"
                                    type="button" data-toggle="tooltip"
                                    data-original-title="Export all of the events in the events list">
                                <i class="fa fa-fw fa-lg fa-download"></i><br />
                                <span>Export All Events</span>                            
                            </button>
                        </div>
                        <div class="span3">
                            <button id="deleteEventButton" class="btn btn-danger"
                                    type="button" data-toggle="tooltip"
                                    data-original-title="Delete all of the events in the events list">
                                <i class="fa fa-fw fa-lg fa-times-circle"></i><br />
                                <span>Delete All Events</span>                            
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <br />
                        <form class="form-search">
                            <div class="input-prepend">
                                <span class="add-on">Filter</span>
                                <input id="tableEventFilter" type="text" class="input-medium search-query" placeholder="Filter" />
                            </div>
                            <span> </span>
                            <div id="tableEventVenueFilterGroup" class="input-prepend">
                                <span class="add-on">Venue:</span>
                                <select class="input-medium search-query" id="tableEventVenueFilter" placeholder="Venue">
                                    <option value=""></option>
                                    <?php
                                    foreach($locations as $location) {
                                        echo '<option value="' . $location['id'] . '">' . str_replace(' ', '_', $location['name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <span> </span>
                            <div id="tableEventTypeFilterGroup" class="input-prepend">
                                <span class="add-on">Type:</span>
                                <select class="input-medium search-query" id="tableEventTypeFilter" placeholder="Type">
                                    <option value=""></option>
                                    <?php
                                    foreach($eventsTypes as $type) {
                                        echo '<option value="' . $type['value'] . '">' . $type['text'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <span> </span>
                            <div id="tableEventStatusFilterGroup" class="input-prepend">
                                <span class="add-on">Status:</span>
                                <select class="input-medium search-query" id="tableEventStatusFilter" placeholder="Status">
                                    <option value=""></option>
                                    <?php
                                    foreach($eventsStatuses as $status) {
                                        echo '<option value="' . $status['value'] . '">' . $status['text'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="well well-small">
                            <div id="eventsTableContainer">
                                <table id="eventFootable"
                                       class="items table table-striped table-bordered table-condensed table-hover footable"
                                       data-filter="#tableEventFilter"
                                       data-sort="false"
                                       data-filter-text-only="true"
                                       data-filter-minimum="2">
                                    <thead>
                                        <tr>
                                            <th data-type="numeric" data-class="event-arena-id">
                                                Event
                                            </th>
                                            <th data-type="alpha" data-class="event-location_id" data-hide="phone,tablet">
                                                Venue
                                            </th>
                                            <th data-type="alpha" data-class="event-type_id" data-hide="phone,tablet">
                                                Type
                                            </th>
                                            <th data-type="alpha" data-class="event-status_id" data-hide="phone,tablet">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="eventFootableBody">
                                        
                                    </tbody>
                                    <tfoot class="hide-if-no-paging">
                                        <tr>
                                            <td colspan="4">
                                                <div class="pagination pagination-centered">
                                                    
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <strong>Event Actions</strong><br />
                        <div class="well">
                            <div class="row-fluid">
                                <div class="span4 offset2">
                                    <a class="btn btn-block btn-success"
                                       href="<?php echo $params['endpoints']['event']['update']; ?>"
                                       target="_blank"
                                       data-toggle="tooltip"
                                       data-original-title="Editable Events (Opens in a new window)"
                                       id="editEventButton">
                                        <i class="fa fa-lg fa-fw fa-pencil"></i> <br />
                                        <span>Editable Events</span>
                                    </a>
                                </div>
                                <div class="span4">
                                    <a class="btn btn-block btn-warning"
                                       href="<?php echo $params['endpoints']['event']['new']; ?>"
                                       target="_blank"
                                       data-toggle="tooltip"
                                       data-original-title="New Event (Opens in a new window)"
                                       id="newEventButton">
                                        <i class="fa fa-lg fa-fw fa-plus-square"></i> <br />
                                        <span>New Event</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // We are going to grab two lists of managers for the list views
    // We only need the manager name and id, we don't need anything else
    if(Yii::app()->user->isApplicationAdministrator()) {
        $availableManagers = User::getAvailableManagersForAdmins(Yii::app()->user->id, $model->id);
    } else {
        $availableManagers = User::getAvailableManagers(Yii::app()->user->id, $model->id);
    }
    
    $assignedManagers = User::getAssignedManagers(Yii::app()->user->id, $model->id);
    ?>
<?php if(Yii::app()->user->isArenaManager()) : ?>
    <div id="managersTabPane" class="tab-pane fade">
        <div id="managerManagementView" class="panel panel-primary">
            <div class="panel-heading">
                <h3>
                    Managers
                </h3>
            </div>
            <div class="panel-body">
                <div class="row-fluid">
                    <div class="span6">
                        <strong>Available Managers</strong><br />
                        <select id="availableManagersMSelect"
                                multiple
                                class="span12">
                            <?php foreach($availableManagers as $aavc) : ?>
                            <option value="<?php echo $aavc['id']; ?>">
                                <?php echo $aavc['last_name'] . ', ' . $aavc['first_name'] . ' - ' . $aavc['email']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="span6">
                        <strong>Assigned Managers</strong><br />
                        <select id="assignedManagersMSelect"
                                multiple
                                class="span12">
                            <?php foreach($assignedManagers as $aac) : ?>
                            <option value="<?php echo $aac['id']; ?>">
                                <?php echo $aac['last_name'] . ', ' . $aac['first_name'] . ' - ' . $aac['email']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <strong>Assignment Actions</strong><br />
                        <div class="well">
                            <div class="row-fluid">
                                <div class="span3 offset3">
                                    <button class="btn btn-block btn-success"
                                            type="button"
                                            data-toggle="tooltip"
                                            data-original-title="Assign manager to this facility"
                                            id="assignManagerButton">
                                        <i class="fa fa-lg fa-fw fa-chevron-right"></i> <br />
                                        <span>Assign</span>
                                    </button>
                                </div>
                                <div class="span3">
                                    <button class="btn btn-block btn-warning"
                                            type="button"
                                            data-toggle="tooltip"
                                            data-original-title="Unassign manager from this facility"
                                            id="unassignManagerButton">
                                        <i class="fa fa-lg fa-fw fa-chevron-left"></i> <br />
                                        <span>Unassign</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <strong>Manager Actions</strong><br />
                        <div class="well">
                            <div class="row-fluid">
                                <div class="span2 offset5">
                                    <a class="btn btn-block btn-warning"
                                       href="<?php echo $params['endpoints']['manager']['new']; ?>"
                                       target="_blank"
                                       data-toggle="tooltip"
                                       data-original-title="New Manager (Opens in a new window)"
                                       id="newManagerButton">
                                        <i class="fa fa-lg fa-fw fa-plus-square"></i> <br />
                                        <span>New Manager</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                    
            </div>
        </div>
    </div>
<?php endif; ?>
        
</div>

<?php if($doReady) : ?>
<?php
    $myScript = 'utilities.urls.login = "' . $this->createUrl('site/login') . '";'
            . 'utilities.urls.logout = "' . $this->createUrl('site/logout') . '";'
            . 'utilities.urls.base = "' . Yii::app()->request->baseUrl . '";'
            . 'utilities.urls.assets = "' . $path . '";'
            . 'utilities.debug = ' . (YII_DEBUG ? 'true' : 'false') . ';'
            . 'arenaManagementView.endpoints.arena.newRecord = "' . $params['endpoints']['arena']['new'] . '";'
            . 'arenaManagementView.endpoints.arena.updateRecord = "' . $params['endpoints']['arena']['update'] . '";'
            . 'arenaManagementView.endpoints.contact.newRecord = "' . $params['endpoints']['contact']['new'] . '";'
            . 'arenaManagementView.endpoints.contact.updateRecord = "' . $params['endpoints']['contact']['update'] . '";'
            . 'arenaManagementView.endpoints.contact.viewRecord = "' . $params['endpoints']['contact']['view'] . '";'
            . 'arenaManagementView.endpoints.contact.deleteRecord = "' . $params['endpoints']['contact']['delete'] . '";'
            . 'arenaManagementView.endpoints.location.newRecord = "' . $params['endpoints']['location']['new'] . '";'
            . 'arenaManagementView.endpoints.location.updateRecord = "' . $params['endpoints']['location']['update'] . '";'
            . 'arenaManagementView.endpoints.location.viewRecord = "' . $params['endpoints']['location']['view'] . '";'
            . 'arenaManagementView.endpoints.location.deleteRecord = "' . $params['endpoints']['location']['delete'] . '";'
            . 'arenaManagementView.endpoints.event.newRecord = "' . $params['endpoints']['event']['new'] . '";'
            . 'arenaManagementView.endpoints.event.updateRecord = "' . $params['endpoints']['event']['update'] . '";'
            . 'arenaManagementView.endpoints.event.viewRecord = "' . $params['endpoints']['event']['view'] . '";'
            . 'arenaManagementView.endpoints.event.deleteRecord = "' . $params['endpoints']['event']['delete'] . '";'
            . 'arenaManagementView.endpoints.event.searchRecord = "' . $params['endpoints']['event']['search'] . '";'
            . 'arenaManagementView.endpoints.event.exportRecord = "' . $params['endpoints']['event']['export'] . '";'
            . 'arenaManagementView.endpoints.event.deleteAllRecord = "' . $params['endpoints']['event']['deleteAll'] . '";'
            . 'arenaManagementView.endpoints.manager.newRecord = "' . $params['endpoints']['manager']['new'] . '";'
            . 'arenaManagementView.endpoints.manager.updateRecord = "' . $params['endpoints']['manager']['update'] . '";'
            . 'arenaManagementView.endpoints.manager.viewRecord = "' . $params['endpoints']['manager']['view'] . '";'
            . 'arenaManagementView.endpoints.manager.deleteRecord = "' . $params['endpoints']['manager']['delete'] . '";'
            . 'arenaManagementView.params = ' . json_encode($params['data']) . ';'
            . 'arenaManagementView.arena = ' . json_encode($model->attributes) . ';'
            . 'arenaManagementView.locations = ' . json_encode($locations) . ';'
            . 'arenaManagementView.locationTypes = ' . json_encode($locationsTypes) . ';'
            . 'arenaManagementView.locationStatuses = ' . json_encode($locationsStatuses) . ';'
            . 'arenaManagementView.contacts = ' . json_encode($assignedContacts) . ';'
            //. 'arenaManagementView.events = ' . json_encode($model->events) . ';'
            . 'arenaManagementView.eventTypes = ' . json_encode($eventsTypes) . ';'
            . 'arenaManagementView.eventStatuses = ' . json_encode($eventsStatuses) . ';'
            . 'arenaManagementView.managers = ' . json_encode($assignedManagers) . ';'
            . 'arenaManagementView.isArenaManager = ' . (Yii::app()->user->isArenaManager() ? 1 : 0) . ';'
            . 'arenaManagementView.statusList = ' . json_encode(Arena::getActiveStatusList()) . ';'
            . 'arenaManagementView.stateList = ' . json_encode(UnitedStatesNames::$states) . ';'
            . 'arenaManagementView.Id = ' . (integer)Yii::app()->user->id . ';'
            . 'arenaManagementView.Name = "' . Yii::app()->user->fullName . '";'
            . 'arenaManagementView.onReady();';
    
    Yii::app()->clientScript->registerScript(
            'doReady_ArenaManagementView',
            $myScript,
            CClientScript::POS_READY
    );
?>
<?php else: ?>
<script type="text/javascript">
$(document).ready(function() {
    utilities.urls.login = "<?php echo $this->createUrl('site/login'); ?>";
    utilities.urls.logout = "<?php echo $this->createUrl('site/logout'); ?>";
    utilities.urls.base = "<?php echo Yii::app()->request->baseUrl; ?>";
    utilities.urls.assets = "<?php echo $path; ?>";
    utilities.debug = <?php echo (YII_DEBUG ? 'true' : 'false'); ?>;
    
    if(typeof arenaManagementView === "undefined")
    {
        var scriptName = utilities.urls.assets + '/js/management/arenaManagementView.' + (utilities.debug ? 'js' : 'min.js');
        
        $.ajax({
            url: scriptName,
            dataType: "script",
            cache: true,
            success: function () {
                console.log("Loaded: " + scriptName);
            },
            error: function(xhr, status, errorThrown) {
                utilities.ajaxError.show(
                    "Error",
                    "Failed to retrieve javsScript file",
                    xhr,
                    status,
                    errorThrown
                );
            }
        });
        
        var interval = setInterval(function () {
            if (typeof arenaManagementView !== "undefined") {
                clearInterval(interval);
                arenaManagementView.endpoints.arena.newRecord = "<?php echo $params['endpoints']['arena']['new']; ?>";
                arenaManagementView.endpoints.arena.updateRecord = "<?php echo $params['endpoints']['arena']['update']; ?>";
                arenaManagementView.endpoints.contact.newRecord = "<?php echo $params['endpoints']['contact']['new']; ?>";
                arenaManagementView.endpoints.contact.updateRecord = "<?php echo $params['endpoints']['contact']['update']; ?>";
                arenaManagementView.endpoints.contact.viewRecord = "<?php echo $params['endpoints']['contact']['view']; ?>";
                arenaManagementView.endpoints.contact.deleteRecord = "<?php echo $params['endpoints']['contact']['delete']; ?>";
                arenaManagementView.endpoints.location.newRecord = "<?php echo $params['endpoints']['location']['new']; ?>";
                arenaManagementView.endpoints.location.updateRecord = "<?php echo $params['endpoints']['location']['update']; ?>";
                arenaManagementView.endpoints.location.viewRecord = "<?php echo $params['endpoints']['location']['view']; ?>";
                arenaManagementView.endpoints.location.deleteRecord = "<?php echo $params['endpoints']['location']['delete']; ?>";
                arenaManagementView.endpoints.event.newRecord = "<?php echo $params['endpoints']['event']['new']; ?>";
                arenaManagementView.endpoints.event.updateRecord = "<?php echo $params['endpoints']['event']['update']; ?>";
                arenaManagementView.endpoints.event.viewRecord = "<?php echo $params['endpoints']['event']['view']; ?>";
                arenaManagementView.endpoints.event.deleteRecord = "<?php echo $params['endpoints']['event']['delete']; ?>";
                arenaManagementView.endpoints.event.searchRecord = "<?php echo $params['endpoints']['event']['search']; ?>";
                arenaManagementView.endpoints.event.exportRecord = "<?php echo $params['endpoints']['event']['export']; ?>";
                arenaManagementView.endpoints.event.deleteAllRecord = "<?php echo $params['endpoints']['event']['deleteAll']; ?>";
                arenaManagementView.endpoints.manager.newRecord = "<?php echo $params['endpoints']['manager']['new']; ?>";
                arenaManagementView.endpoints.manager.updateRecord = "<?php echo $params['endpoints']['manager']['update']; ?>";
                arenaManagementView.endpoints.manager.viewRecord = "<?php echo $params['endpoints']['manager']['view']; ?>";
                arenaManagementView.endpoints.manager.deleteRecord = "<?php echo $params['endpoints']['manager']['delete']; ?>";
                arenaManagementView.params = <?php echo json_encode($params['data']); ?>;
                arenaManagementView.arena = <?php echo json_encode($model->attributes); ?>;
                arenaManagementView.locations = <?php echo json_encode($locations); ?>;
                arenaManagementView.locationTypes = <?php echo json_encode($locationsTypes); ?>;
                arenaManagementView.locationStatuses = <?php echo json_encode($locationsStatuses); ?>;
                arenaManagementView.contacts = <?php echo json_encode($assignedContacts); ?>;
                arenaManagementView.eventTypes = <?php echo json_encode($eventsTypes); ?>;
                arenaManagementView.eventStatuses = <?php echo json_encode($eventsStatuses); ?>;
                arenaManagementView.isArenaManager = <?php echo (Yii::app()->user->isArenaManager()) ? 1 : 0; ?>;
                arenaManagementView.statusList = <?php echo json_encode(Arena::getActiveStatusList()); ?>;
                arenaManagementView.stateList = <?php echo json_encode(UnitedStatesNames::$states); ?>;
                arenaManagementView.Id = <?php echo Yii::app()->user->id; ?>;
                arenaManagementView.Name = "<?php echo Yii::app()->user->fullName; ?>";
                arenaManagementView.onReady();
            } else if (console && console.log) {
                console.log("Loading... " + scriptName);
            }
        }, 500);
    }
    else
    {
        arenaManagementView.endpoints.arena.newRecord = "<?php echo $params['endpoints']['arena']['new']; ?>";
        arenaManagementView.endpoints.arena.updateRecord = "<?php echo $params['endpoints']['arena']['update']; ?>";
        arenaManagementView.endpoints.contact.newRecord = "<?php echo $params['endpoints']['contact']['new']; ?>";
        arenaManagementView.endpoints.contact.updateRecord = "<?php echo $params['endpoints']['contact']['update']; ?>";
        arenaManagementView.endpoints.contact.viewRecord = "<?php echo $params['endpoints']['contact']['view']; ?>";
        arenaManagementView.endpoints.contact.deleteRecord = "<?php echo $params['endpoints']['contact']['delete']; ?>";
        arenaManagementView.endpoints.location.newRecord = "<?php echo $params['endpoints']['location']['new']; ?>";
        arenaManagementView.endpoints.location.updateRecord = "<?php echo $params['endpoints']['location']['update']; ?>";
        arenaManagementView.endpoints.location.viewRecord = "<?php echo $params['endpoints']['location']['view']; ?>";
        arenaManagementView.endpoints.location.deleteRecord = "<?php echo $params['endpoints']['location']['delete']; ?>";
        arenaManagementView.endpoints.event.newRecord = "<?php echo $params['endpoints']['event']['new']; ?>";
        arenaManagementView.endpoints.event.updateRecord = "<?php echo $params['endpoints']['event']['update']; ?>";
        arenaManagementView.endpoints.event.viewRecord = "<?php echo $params['endpoints']['event']['view']; ?>";
        arenaManagementView.endpoints.event.deleteRecord = "<?php echo $params['endpoints']['event']['delete']; ?>";
        arenaManagementView.endpoints.event.searchRecord = "<?php echo $params['endpoints']['event']['search']; ?>";
        arenaManagementView.endpoints.event.exportRecord = "<?php echo $params['endpoints']['event']['export']; ?>";
        arenaManagementView.endpoints.event.deleteAllRecord = "<?php echo $params['endpoints']['event']['deleteAll']; ?>";
        arenaManagementView.endpoints.manager.newRecord = "<?php echo $params['endpoints']['manager']['new']; ?>";
        arenaManagementView.endpoints.manager.updateRecord = "<?php echo $params['endpoints']['manager']['update']; ?>";
        arenaManagementView.endpoints.manager.viewRecord = "<?php echo $params['endpoints']['manager']['view']; ?>";
        arenaManagementView.endpoints.manager.deleteRecord = "<?php echo $params['endpoints']['manager']['delete']; ?>";
        arenaManagementView.params = <?php echo json_encode($params['data']); ?>;
        arenaManagementView.arena = <?php echo json_encode($model->attributes); ?>;
        arenaManagementView.locations = <?php echo json_encode($locations); ?>;
        arenaManagementView.locationTypes = <?php echo json_encode($locationsTypes); ?>;
        arenaManagementView.locationStatuses = <?php echo json_encode($locationsStatuses); ?>;
        arenaManagementView.contacts = <?php echo json_encode($assignedContacts); ?>;
        arenaManagementView.eventTypes = <?php echo json_encode($eventsTypes); ?>;
        arenaManagementView.eventStatuses = <?php echo json_encode($eventsStatuses); ?>;
        arenaManagementView.isArenaManager = <?php echo (Yii::app()->user->isArenaManager()) ? 1 : 0; ?>;
        arenaManagementView.statusList = <?php echo json_encode(Arena::getActiveStatusList()); ?>;
        arenaManagementView.stateList = <?php echo json_encode(UnitedStatesNames::$states); ?>;
        arenaManagementView.Id = <?php echo Yii::app()->user->id; ?>;
        arenaManagementView.Name = "<?php echo Yii::app()->user->fullName; ?>";
        arenaManagementView.onReady();
    }
});
</script>
<?php endif; ?>