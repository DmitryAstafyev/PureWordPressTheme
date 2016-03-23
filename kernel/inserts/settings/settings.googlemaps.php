<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('googlemaps');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->googlemaps->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->googlemaps->id;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Google maps settings", 'pure' ),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>true,
        "container_style"   =>'width:auto;',
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
/*
''        =>(object)array('value' =>'https://maps.googleapis.com/maps/api/js?key='                            ),
''        =>(object)array('value' =>'AIzaSyC9QMynKPiEnrdcnQjrT703igxoNp32nvM'                                 ),
''         =>(object)array('value' =>'746210815561-2irvpt0u2jba7psc5p3r93oo1jftgtav.apps.googleusercontent.com'),

*/
?>
<form method="POST" action="">
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Where it is actual?', 'pure');?></strong></p>
    <p data-type="Pure.Configuration.Info"><?php echo __('As default Google maps are used in <strong>events</strong>. So if you do not configure it, your users will not see map with place of event.', 'pure');?></p>
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Basic settings', 'pure');?></strong></p>
    <table>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Link to script with Google maps.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->script_url->value; ?>" id="<?php echo $properties->script_url->name; ?>" name="<?php echo $properties->script_url->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Your access key to Google maps API', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->access_key->value; ?>" id="<?php echo $properties->access_key->name; ?>" name="<?php echo $properties->access_key->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Your client ID for Google maps API', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->client_id->value; ?>" id="<?php echo $properties->client_id->name; ?>" name="<?php echo $properties->client_id->name; ?>" />
            </td>
        </tr>
        <tr>
            <td  colspan="2">
                <p data-type="Pure.Configuration.Accent"><?php echo __('To get more information about how you can get <strong>key</strong> and <strong>clientID</strong>, please visit <a href="https://developers.google.com/maps/documentation/javascript/tutorial">Google Developer page</a>. We use <strong>Google Maps JavaScript API v3</strong>.', 'pure');?></p>
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