<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('footer');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance          ::instance()->settings->footer->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance          ::instance()->settings->footer->id;
$templates              = \Pure\Templates\Layout\Page\ByScheme\Initialization   ::instance()->templates;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Footer settings", 'pure' ),
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
    <p data-type="Pure.Configuration.Info"><?php echo __('All settings here are about footer', 'pure');?></p>
    <table>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('After footer text', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('As usual here is something about copyrights' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <textarea data-type="Pure.Configuration.TextArea" id="<?php echo $properties->after->name; ?>" name="<?php echo $properties->after->name; ?>"><?php echo $properties->after->value; ?></textarea>
                <p data-type="Pure.Configuration.Accent"><?php echo __('You can use here HTML tags.', 'pure');?></p>
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