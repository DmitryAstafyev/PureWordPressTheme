<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('mailer');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->mailer->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->mailer->id;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Mailer settings", 'pure' ),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>true,
        "container_style"   =>'width:auto;',
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
?>
<form method="POST" action="">
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Where it is actual?', 'pure');?></strong></p>
    <p data-type="Pure.Configuration.Info"><?php echo __('To send some notification to your user we should do it from some mailbox. For example to make available conformation of email (during registration of new user). At least without it we cannot register new user. So, it will be better if you configure mailer.', 'pure');?></p>
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Basic settings', 'pure');?></strong></p>
    <table>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('STMP Host', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('For example: stmp.google.com', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->host->value; ?>" id="<?php echo $properties->host->name; ?>" name="<?php echo $properties->host->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('STMP Port', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->port->value; ?>" id="<?php echo $properties->port->name; ?>" name="<?php echo $properties->port->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Username', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('For example: administrator@gmail.com ', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->username->value; ?>" id="<?php echo $properties->username->name; ?>" name="<?php echo $properties->username->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Password', 'pure');?></p>
                <p data-type="Pure.Configuration.Attention"><?php echo __('It will be saved in data base of your site', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="password" value="<?php echo $properties->password->value; ?>" id="<?php echo $properties->password->name; ?>" name="<?php echo $properties->password->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Use SMTP authorization', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->SMTPAuth->name;?>" name="<?php echo $properties->SMTPAuth->name; ?>">
                    <option value="on" <?php selected( 'on', $properties->SMTPAuth->value ); ?>><?php echo __('use', 'pure' ); ?></option>
                    <option value="off" <?php selected( 'off', $properties->SMTPAuth->value ); ?>><?php echo __('do not use', 'pure' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('SMTP security protocol', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->SMTPSecure->name;?>" name="<?php echo $properties->SMTPSecure->name; ?>">
                    <option value="no" <?php selected( 'no', $properties->SMTPSecure->value ); ?>><?php echo __('do not use', 'pure' ); ?></option>
                    <option value="tls" <?php selected( 'tls', $properties->SMTPSecure->value ); ?>><?php echo __('TLS', 'pure' ); ?></option>
                    <option value="ssl" <?php selected( 'ssl', $properties->SMTPSecure->value ); ?>><?php echo __('SSL', 'pure' ); ?></option>
                </select>
            </td>
        </tr>
    </table>
    <input type="hidden" name="update_settings" value="Y" />
    <?php
        echo $status_of_saving->message;
    ?>
    <p>
        <input type="submit" value="Save settings" class="button-primary"/>
    </p>
    <?php
    $groups->close(false);
    ?>
</form>