<?php
    /**
     * This doubles as both a view/edit form for existing records
     * 
     * @var $this UserController
     * @var $model User
     * @var $path string
     * @var $doReady boolean
     * @var $newRecord
     * @var $params []
     */

?>

<?php
    $this->widget('bootstrap.widgets.TbAlert', array('htmlOptions' => array('class' => 'fade-message')));
?>

<?php if($newRecord === 1): ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Account</h3>
    </div>
    <div class="panel-body">
        <div class="row-fluid">
            <div class="span3">
                <img class="img-circle"
                     src="<?php echo Yii::app()->request->baseUrl; ?>/images/blank_avatar.png"
                     alt="Generic User Pic" />
            </div>
            <div class="span6">
                <h3>No Data Found!</h3><br />
            </div>
        </div>
    </div>
    <div class="panel-footer">
    </div>
</div>

<?php else: ?>
<?php $attributeLabels = $model->attributeLabels(); ?>
<div id="userAccountProfileView" class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?php echo $model->fullName; ?>
        </h3>
    </div>
    <div class="panel-body">
        <div class="row-fluid">
            <div class="span3">
                <img class="img-circle"
                     src="<?php echo Yii::app()->request->baseUrl; ?>/images/blank_avatar.png"
                     alt="Generic User Pic" />
            </div>
            <div class="span9">
                <strong>Account Details</strong><br />
                <table class="table table-condensed table-information">
                    <tbody>
                        <tr>
                            <td style="width:33%">
                                <?php echo $attributeLabels['username']; ?>
                            </td>
                            <td>
                                <a href="#" id="username" data-type="text"
                                   data-pk="user"
                                   data-url="<?php echo $params['endpoints']['update']; ?>"
                                   data-mode="inline"
                                   title="Unique username">
                                   <?php echo $model->username; ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:33%">
                                <?php echo $attributeLabels['email']; ?>
                            </td>
                            <td>
                                <a href="#" id="email" data-type="text"
                                   data-pk="user"
                                   data-url="<?php echo $params['endpoints']['update']; ?>"
                                   data-mode="inline"
                                   title="Unique e-mail address">
                                   <?php echo $model->email; ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:33%">
                                <?php echo $attributeLabels['password']; ?>
                            </td>
                            <td>
                                <i class="fa fa-fw fa-pencil"></i> 
                                <a id="password" href="<?php echo $this->createUrl('user/changePassword', array('id' => $model->id)); ?>">
                                    Change Password
                                </a>
                            </td>
                        </tr>
        <?php if(Yii::app()->user->isApplicationAdministrator()) : ?>
                        <tr>
                            <td style="width:33%">
                                <?php echo $attributeLabels['status_id']; ?>
                            </td>
                            <td>
                                <a id="status_id" href="#" id="status_id" data-type="select"
                                   data-pk="user"
                                   data-url="<?php echo $params['endpoints']['update']; ?>"
                                   data-mode="inline"
                                   data-value="<?php echo $model->status_id; ?>"
                                   title="Account Status">
                                   <?php echo $model->itemAlias('UserStatus', $model->status_id); ?>
                                </a>
                            </td>
                        </tr>
        <?php endif; ?>
                    </tbody>
                </table>
                <strong>Profile Information</strong><br />
                <table class="table table-condensed table-information">
                    <tbody>
        <?php
            $profile = $model->profile;
            $profileFields = $profile->getFields();
        ?>
        <?php if($profileFields) : ?>
            <?php foreach($profileFields as $field) : ?>
                <?php if($field->varname == 'birth_day') : ?>
                        <tr>
                            <td style="width:33%">
                                <?php echo $field->title; ?>
                            </td>
                            <td>
                                <a id="birth_day" href="#" data-type="date"
                                   data-pk="profile"
                                   data-url="<?php echo $params['endpoints']['update']; ?>"
                                   data-mode="inline"
                                   data-format="yyyy-mm-dd"
                                   data-viewFormat="mm/dd/yyyy"
                                   data-value="<?php echo $profile->birth_day; ?>"
                                   title="Birthday">
                                   <?php echo $profile->birth_day; ?>
                                </a>
                            </td>
                        </tr>
                <?php elseif($field->varname == "phone") : ?>
                        <tr>
                            <td style="width:33%">
                                <?php echo $field->title; ?>
                            </td>
                            <td>
                                <a href="#" id="phone" data-type="text"
                                   data-pk="profile"
                                   data-url="<?php echo $params['endpoints']['update']; ?>"
                                   data-mode="inline"
                                   title="Ten digit phone number">
                                   <?php echo $profile->phone; ?>
                                </a>
                            </td>
                        </tr>
                <?php elseif($field->varname == "state") : ?>
                    <?php $states = UnitedStatesNames::$states; ?>
                        <tr>
                            <td style="width:33%">
                                <?php echo $field->title; ?>
                            </td>
                            <td>
                                <a id="state" href="#" data-type="select"
                                   data-pk="profile"
                                   data-url="<?php echo $params['endpoints']['update']; ?>"
                                   data-mode="inline"
                                   data-value="<?php echo $profile->state; ?>"
                                   title="State">
                                   <?php echo UnitedStatesNames::getName($profile->state); ?>
                                </a>
                            </td>
                        </tr>
                <?php elseif($field->field_type == "TEXT") : ?>
                        <tr>
                            <td style="width:33%">
                                <?php echo $field->title; ?>
                            </td>
                            <td>
                                <a id="<?php echo $field->varname; ?>" href="#" data-type="textarea"
                                   data-pk="profile"
                                   data-url="<?php echo $params['endpoints']['update']; ?>"
                                   data-mode="inline"
                                   data-value="<?php $varname = $field->varname; echo $profile->$varname; ?>"
                                   title="<?php echo $field->title; ?>">
                                   <?php $varname = $field->varname; echo $profile->$varname; ?>
                                </a>
                            </td>
                        </tr>
                <?php else : ?>
                        <tr>
                            <td style="width:33%">
                                <?php echo $field->title; ?>
                            </td>
                            <td>
                                <a id="<?php echo $field->varname; ?>" href="#" data-type="text"
                                   data-pk="profile"
                                   data-url="<?php echo $params['endpoints']['update']; ?>"
                                   data-mode="inline"
                                   data-value="<?php $varname = $field->varname; echo $profile->$varname; ?>"
                                   title="<?php echo $field->title; ?>">
                                   <?php $varname = $field->varname; echo $profile->$varname; ?>
                                </a>
                            </td>
                        </tr>
                <?php endif; endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php if($doReady) : ?>
<?php
    $myScript = 'utilities.urls.login = "' . $this->createUrl('site/login') . '";'
            . 'utilities.urls.logout = "' . $this->createUrl('site/logout') . '";'
            . 'utilities.urls.base = "' . Yii::app()->request->baseUrl . '";'
            . 'utilities.urls.assets = "' . $path . '";'
            . 'utilities.debug = ' . (YII_DEBUG ? 'true' : 'false') . ';'
            . 'userAccountProfile.endpoints.newRecord = "' . $params['endpoints']['new'] . '";'
            . 'userAccountProfile.endpoints.updateRecord = "' . $params['endpoints']['update'] . '";'
            . 'userAccountProfile.params = ' . json_encode($params['data']) . ';'
            . 'userAccountProfile.account = ' . json_encode($model->attributes) . ';'
            . 'userAccountProfile.profile = ' . json_encode($model->profile->attributes) . ';'
            . 'userAccountProfile.isArenaManager = ' . (Yii::app()->user->isApplicationAdministrator() ? 1 : 0) . ';'
            . 'userAccountProfile.statusList = ' . json_encode($model->itemAlias('UserStatus')) . ';'
            . 'userAccountProfile.stateList = ' . json_encode(UnitedStatesNames::$states) . ';'
            . 'userAccountProfile.Id = ' . (integer)Yii::app()->user->id . ';'
            . 'userAccountProfile.Name = "' . Yii::app()->user->fullName . '";'
            . 'userAccountProfile.onReady();';
    
    Yii::app()->clientScript->registerScript(
            'doReady_UserAccountProfile',
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
    
    if(typeof userAccountProfile === "undefined")
    {
        var scriptName = utilities.urls.assets + '/js/user/userAccountProfile.' + (utilities.debug ? 'js' : 'min.js');
        
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
            if (typeof userAccountProfile !== "undefined") {
                clearInterval(interval);
                userAccountProfile.endpoints.newRecord = "<?php echo $params['endpoints']['new']; ?>";
                userAccountProfile.endpoints.updateRecord = "<?php echo $params['endpoints']['update']; ?>";
                userAccountProfile.params = <?php echo json_encode($params['data']); ?>;
                userAccountProfile.account = <?php echo json_encode($model->attributes); ?>;
                userAccountProfile.profile = <?php echo json_encode($model->profile->attributes); ?>;
                userAccountProfile.isArenaManager = <?php echo (Yii::app()->user->isApplicationAdministrator()) ? 1 : 0; ?>;
                userAccountProfile.statusList = <?php echo json_encode($model->itemAlias('UserStatus')); ?>;
                userAccountProfile.stateList = <?php echo json_encode(UnitedStatesNames::$states); ?>;
                userAccountProfile.Id = <?php echo Yii::app()->user->id; ?>;
                userAccountProfile.Name = "<?php echo Yii::app()->user->fullName; ?>";
                userAccountProfile.onReady();
            } else if (console && console.log) {
                console.log("Loading... " + scriptName);
            }
        }, 500);
    }
    else
    {
        userAccountProfile.endpoints.newRecord = "<?php echo $params['endpoints']['new']; ?>";
        userAccountProfile.endpoints.updateRecord = "<?php echo $params['endpoints']['update']; ?>";
        userAccountProfile.params = <?php echo json_encode($params['data']); ?>;
        userAccountProfile.account = <?php echo json_encode($model->attributes); ?>;
        userAccountProfile.profile = <?php echo json_encode($model->profile->attributes); ?>;
        userAccountProfile.isArenaManager = <?php echo (Yii::app()->user->isApplicationAdministrator()) ? 1 : 0; ?>;
        userAccountProfile.statusList = <?php echo json_encode($model->itemAlias('UserStatus')); ?>;
        userAccountProfile.stateList = <?php echo json_encode(UnitedStatesNames::$states); ?>;
        userAccountProfile.Id = <?php echo Yii::app()->user->id; ?>;
        userAccountProfile.Name = "<?php echo Yii::app()->user->fullName; ?>";
        userAccountProfile.onReady();
    }
});
</script>
<?php endif; ?>