<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('information');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->information->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->information->id;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Support information", 'pure' ),
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
    <p data-type="Pure.Configuration.Info"><?php echo __('This information will be show before menu', 'pure');?></p>
    <table>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Facebook', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Link to your official facebook account' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->facebook->value; ?>" id="<?php echo $properties->facebook->name; ?>" name="<?php echo $properties->facebook->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Google +', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Link to your official google + account' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->google->value; ?>" id="<?php echo $properties->google->name; ?>" name="<?php echo $properties->google->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('LinkIn', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Link to your official linkIn account' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->linkin->value; ?>" id="<?php echo $properties->linkin->name; ?>" name="<?php echo $properties->linkin->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Twitter', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Link to your official twitter account' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->twitter->value; ?>" id="<?php echo $properties->twitter->name; ?>" name="<?php echo $properties->twitter->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Phone number', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Define here support phone number' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->phone->value; ?>" id="<?php echo $properties->phone->name; ?>" name="<?php echo $properties->phone->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Mail', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Define here support mail' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->mail->value; ?>" id="<?php echo $properties->mail->name; ?>" name="<?php echo $properties->mail->name; ?>" />
            </td>
        </tr>
    </table>
    <p data-type="Pure.Configuration.Accent"><?php echo __('To remove some button (or phone, or mail), just leave empty field.', 'pure');?></p>
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