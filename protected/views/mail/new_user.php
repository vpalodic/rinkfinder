<table cellspacing="5" cellpadding="10" style="color:#666;font:13px Arial;line-height:1.4em;width:100%;">
    <tbody>
        <tr>
            <td>
                <h2 style="padding-bottom:0px;padding-right:0px;padding-top:0px;padding-right:0px;font-size:1.75em;font-weight:bold;color:#0d1b72;background:#ffffff;">
                    Welcome <?php echo CHtml::encode($fullName); ?> to <?php echo CHtml::encode(Yii::app()->name); ?>
                </h2>
                <h3 style="padding-bottom:0px;padding-right:0px;padding-top:0px;padding-right:0px;font-size:1.45em;font-weight:bold;color:#0d1b72;background:#ffffff;">
                    A Service of the Minnesota Ice Arena Manager's Association
                </h3>
                <p style="padding-bottom:0px;padding-right:0px;padding-top:0px;padding-right:0px;font-size:1.0em;font-weight:normal;color:#000000;background:#ffffff;">
                    Here are your account details<br />
                    <ul>
                        <li>
                            Username: <b><?php echo $username; ?></b>
                        </li>
                        <li>
                            Password: <b><?php echo $password; ?></b>
                        </li>
                        <li>
                            E-mail: <b><?php echo $email; ?></b>
                        </li>
                        <li>
                            User Key: <b><?php echo $user_key; ?></b>
                        </li>
                        <li>
                            Activation Link: <b><a href="<?php echo $activationUrl; ?>"><?php echo $activationUrl; ?></a></b>
                        </li>
                    </ul>
                </p>
                <p style="padding-bottom:0px;padding-right:0px;padding-top:0px;padding-right:0px;font-size:1.0em;font-weight:normal;color:#000000;background:#ffffff;">
                    Before you can start using your account, please click on the <b>Activation Link</b> above to activate your account.
                    <br />
                    <br />
                    You may need to copy and past the link directly in to your browser.
                    <br />
                    <br />
                    If you continue to have problems activating your account, you may manually activate your account.
                    <br />
                    <br />
                    Please click this link <b><a href="<?php echo $manualUrl; ?>"><?php echo $manualUrl; ?></a></b> and enter in your <b>E-mail</b> and <b>User Key</b> from above.
                    <br />
                    <br />
                    After activating your account, please login and <b>change your password!</b>
                    <br />
                    <br />
                    If you have any questions, please either contact the person who created your account or use the contact link on the site.
                </p>
            </td>
        </tr>
    </tbody>
</table>