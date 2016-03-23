<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('comments');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->comments->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->comments->id;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Comments block settings", 'pure' ),
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
    <p data-type="Pure.Configuration.Info"><?php echo __('These settings actual for comments in <strong>post</strong> and in <strong>page</strong>.', 'pure');?></p>
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Basic settings', 'pure');?></strong></p>
    <table>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Attachments in comments', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('You can allow attach files (from media library of user) to comment', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->allow_attachment->name;?>" name="<?php echo $properties->allow_attachment->name; ?>">
                    <option value="on" <?php selected( 'on', $properties->allow_attachment->value ); ?>><?php echo __('Allow', 'pure' ); ?></option>
                    <option value="off" <?php selected( 'off', $properties->allow_attachment->value ); ?>><?php echo __('Deny', 'pure' ); ?></option>
                </select>
                <p data-type="Pure.Configuration.Title"><?php echo __('User always can attach only one file per one comment', 'pure');?></p>
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Allow memes in comments', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Memes is a simple collection of some images. It can be memes, smiles or something else. You as administrator can make any collection of memes.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->allow_memes->name;?>" name="<?php echo $properties->allow_memes->name; ?>">
                    <option value="on" <?php selected( 'on', $properties->allow_memes->value ); ?>><?php echo __('Allow', 'pure' ); ?></option>
                    <option value="off" <?php selected( 'off', $properties->allow_memes->value ); ?>><?php echo __('Deny', 'pure' ); ?></option>
                </select>
                <p data-type="Pure.Configuration.Title"><?php echo __('Folder of memes on server. This is sub folder in "../wp-content/uploads"', 'pure');?></p>
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->memes_folder->value; ?>" id="<?php echo $properties->memes_folder->name; ?>" name="<?php echo $properties->memes_folder->name; ?>" />
                <p data-type="Pure.Configuration.Attention"><?php echo __('To create collection of memes you should create any folder inside "../wp-content/uploads", place images (*.gif, *.png, *.jpg, *.jpeg) into and define name of this folder here.', 'pure');?></p>
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Maximum size of one comment message', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Size in symbols', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->max_length->value; ?>" id="<?php echo $properties->max_length->name; ?>" name="<?php echo $properties->max_length->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Max count of comments on page (only root comments)', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->show_on_page->value; ?>" id="<?php echo $properties->show_on_page->name; ?>" name="<?php echo $properties->show_on_page->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Allow hot update', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Hot update based on WebSocket server. If you switch on it, comments will be updating in real-time mode and all users will see changes immediately.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->hot_update->name;?>" name="<?php echo $properties->hot_update->name; ?>">
                    <option value="on" <?php selected( 'on', $properties->hot_update->value ); ?>><?php echo __('Allow', 'pure' ); ?></option>
                    <option value="off" <?php selected( 'off', $properties->hot_update->value ); ?>><?php echo __('Deny', 'pure' ); ?></option>
                </select>
                <p data-type="Pure.Configuration.Attention"><?php echo __('For sure you should activate <strong>WebSocket server</strong> to use hot updating.', 'pure');?></p>
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