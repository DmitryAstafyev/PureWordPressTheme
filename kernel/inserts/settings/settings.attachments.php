<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('attachments');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->attachments->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->attachments->id;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Attachments module settings", 'pure' ),
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
    <p data-type="Pure.Configuration.Info"><?php echo __('In some places of site, members can attach any files to posts, comments and etc. For example, module "Questions & Answers", where members can attach files to question, additions and answers. So, here you can configure settings for attachments.', 'pure');?></p>
    <table>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Size of one attachment', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Size in bytes. Of course defined size should be less then it defines in your PHP settings.' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->max_size_attachment->value; ?>" id="<?php echo $properties->max_size_attachment->name; ?>" name="<?php echo $properties->max_size_attachment->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Number of attachments per one object', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('How many attachments can be attached to one object (post, comment, addition and etc.)' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->max_count_per_object->value; ?>" id="<?php echo $properties->max_count_per_object->name; ?>" name="<?php echo $properties->max_count_per_object->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Number of attachments per one month', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('How many attachments can be attached by member per one month. This setting can be useful to prevent DDOS attacks.' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->max_count_per_month->value; ?>" id="<?php echo $properties->max_count_per_month->name; ?>" name="<?php echo $properties->max_count_per_month->name; ?>" />
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