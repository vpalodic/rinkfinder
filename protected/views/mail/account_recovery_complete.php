<table cellspacing="5" cellpadding="10" style="color:#666;font:13px Arial;line-height:1.4em;width:100%;">
    <tbody>
        <tr>
            <td>
                <h2 style="padding-bottom:0px;padding-right:0px;padding-top:0px;padding-right:0px;font-size:1.75em;font-weight:bold;color:#0d1b72;background:#ffffff;">
                    <?php echo CHtml::encode(Yii::app()->name); ?> Account Recovery Completed
                </h2>
                <p style="padding-bottom:0px;padding-right:0px;padding-top:0px;padding-right:0px;font-size:1.0em;font-weight:normal;color:#000000;background:#ffffff;">
                    <p style="padding-bottom:0px;padding-right:0px;padding-top:0px;padding-right:0px;font-size:1.0em;font-weight:normal;color:#000000;background:#ffffff;">
                        Hello <?php echo CHtml::encode($fullName); ?>:<br />
                        <br />
                        <br />
                        The account recovery process for your account is now complete.
                        <br />
                        You may login to your account with your username: <b><?php echo CHtml::encode($username); ?></b> and new password.
                        <br />
                        If you did not request this change and are unable to login to your account, please follow the instructions below to recover your account.
                    </p>
                    <ul>
                        <li>
                            Click the recovery link or copy and paste it in to your browser: <b><a href="<?php echo $recoveryUrl; ?>"><?php echo $recoveryUrl; ?></a></b>
                        </li>
                        <li>
                            You will then be able to set a new password.
                        </li>
                        <li>
                            After setting a new password you will then be able to login to your account.
                        </li>
                        <li>
                            If you have problems recovering your account, you may manually recover your account by clicking on this link: <b><a href="<?php echo $manualUrl; ?>"><?php echo $manualUrl; ?></a></b> and entering in the User Key listed below.
                        </li>
                        <li>
                            User Key: <b><?php echo $user_key; ?></b>
                        </li>
                    </ul>
                </p>
                <p style="padding-bottom:0px;padding-right:0px;padding-top:0px;padding-right:0px;font-size:1.0em;font-weight:normal;color:#000000;background:#ffffff;">
                    <br />
                    <br />
                    Happy rink finding!
                </p>
            </td>
        </tr>
    </tbody>
</table>