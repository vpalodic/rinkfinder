<table cellspacing="5" cellpadding="10" style="color:#666;font:13px Arial;line-height:1.4em;width:100%;">
    <tbody>
        <tr>
            <td width="20%" style="vertical-align:text-top;text-align:right;padding-top: 5px;padding-left: 10px;padding-bottom: 5px;padding-right: 10px;font-size: 1.5em;font-weight: bold;color: #0d1b72;background: #ffffff;">
                <p>From:</p>
            </td>
            <td style="text-align:left;padding:15px 20px;padding-top: 5px;font-size: 1.5em;font-weight: normal;color: #000000;background: #ffffff;">
            	<?php if(isset($requester)) echo $requester['name'];  ?>
            </td>
        </tr>
        <tr>
            <td width="20%" style="vertical-align:text-top;text-align:right;padding-top: 5px;padding-left: 10px;padding-bottom: 5px;padding-right: 10px;font-size: 1.5em;font-weight: bold;color: #0d1b72;background: #ffffff;">
                <p>Subject:</p>
            </td>
            <td style="text-align:left;padding:15px 20px;padding-top: 5px;font-size: 1.5em;font-weight: normal;color: #000000;background: #ffffff;">
            	<?php if(isset($description)) echo $description;  ?>
            </td>
        </tr>
        <tr>
            <td width="20%" style="vertical-align:text-top;text-align:right;padding-top: 5px;padding-left: 10px;padding-bottom: 5px;padding-right: 10px;font-size: 1.5em;font-weight: bold;color: #0d1b72;background: #ffffff;">
                <p>
                    Message:
                </p>
            </td>
            <td style="text-align:left;padding:15px 20px;padding-top: 5px;font-size: 1.5em;font-weight: normal;color: #000000;background: #ffffff;">
                <p>
                    <?php echo $message ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>