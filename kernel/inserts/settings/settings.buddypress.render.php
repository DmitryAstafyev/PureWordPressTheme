<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('buddypress');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance          ::instance()->settings->buddypress->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance          ::instance()->settings->buddypress->id;
$templatesHeaders       = \Pure\Templates\BuddyPress\Headers\Initialization     ::instance()->templates;
$templatesActivities    = \Pure\Templates\BuddyPress\Activities\Initialization  ::instance()->templates;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Visual settings", 'pure' ),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>true,
        "container_style"   =>'width:auto;',
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
?>
<form method="POST" action="">
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Template of personal user\'s page header', 'pure');?></strong></p>
    <?php
    foreach ($templatesHeaders as $templatesHeader){
        ?>
        <p>
            <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $templatesHeader->key; ?>" <?php checked( $templatesHeader->key, $properties->header_template->value ); ?> id="<?php echo $prefix.$properties->header_template->name.'template'.$templatesHeader->key; ?>" name="<?php echo $properties->header_template->name; ?>" />
            <label for="<?php echo $prefix.$properties->header_template->name.'template'.$templatesHeader->key; ?>">Template <?php echo $templatesHeader->key; ?> <br />
                <img alt="" data-type="Pure.Configuration.Input.Fader" width="50%" style="margin-left: 25%;" src="<?php echo $templatesHeader->thumbnail; ?>">
            </label>
            <?php
            \Pure\Templates\BuddyPress\Headers\Initialization::instance()->description($templatesHeader->key);
            ?>
        </p>
    <?php
    }
    ?>
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Template of user\'s activities', 'pure');?></strong></p>
    <?php
    foreach ($templatesActivities as $templatesActivity){
        ?>
        <p>
            <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $templatesActivity->key; ?>" <?php checked( $templatesActivity->key, $properties->activity_template->value ); ?> id="<?php echo $prefix.$properties->activity_template->name.'template'.$templatesActivity->key; ?>" name="<?php echo $properties->activity_template->name; ?>" />
            <label for="<?php echo $prefix.$properties->activity_template->name.'template'.$templatesActivity->key; ?>">Template <?php echo $templatesActivity->key; ?> <br />
                <img alt="" data-type="Pure.Configuration.Input.Fader" width="50%" style="margin-left: 25%;" src="<?php echo $templatesActivity->thumbnail; ?>">
            </label>
            <?php
            \Pure\Templates\BuddyPress\Activities\Initialization::instance()->description($templatesActivity->key);
            ?>
        </p>
    <?php
    }
    ?>
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Other', 'pure');?></strong></p>
    <table>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Count of records on page', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="widefat" type="number" value="<?php echo esc_attr($properties->records_on_page->value); ?>" name="<?php echo $properties->records_on_page->name; ?>" style="width: 10em;" />
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
</form>
<?php
$groups->close(array("echo"=>true));
?>
