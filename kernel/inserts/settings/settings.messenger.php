<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('messenger');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->messenger->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->messenger->id;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__('Global messenger setting for Pure theme', 'pure'),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>true,
        "container_style"   =>'width:auto;',
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
?>
<form method="POST" action="">
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Basic settings', 'pure');?></strong></p>
    <table>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Max count of mails on page', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mails_max_count->value; ?>" id="<?php echo $properties->mails_max_count->name; ?>" name="<?php echo $properties->mails_max_count->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Max size of one message', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Size on one message in symbols.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mail_max_size->value; ?>" id="<?php echo $properties->mail_max_size->name; ?>" name="<?php echo $properties->mail_max_size->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Max size of subject', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Size of message\'s subject in symbols', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mail_subject_max_size->value; ?>" id="<?php echo $properties->mail_subject_max_size->name; ?>" name="<?php echo $properties->mail_subject_max_size->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Attachments in mails', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Can user attach file to message or not.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->allow_attachment_in_mail->name;?>" name="<?php echo $properties->allow_attachment_in_mail->name; ?>">
                    <option value="on" <?php selected( 'on', $properties->allow_attachment_in_mail->value ); ?>><?php echo __('Allow', 'pure' ); ?></option>
                    <option value="off" <?php selected( 'off', $properties->allow_attachment_in_mail->value ); ?>><?php echo __('Deny', 'pure' ); ?></option>
                </select>
                <p data-type="Pure.Configuration.Title"><?php echo __('Size of one attachment in bytes', 'pure');?></p>
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->attachment_max_size->value; ?>" id="<?php echo $properties->attachment_max_size->name; ?>" name="<?php echo $properties->attachment_max_size->name; ?>" />
                <p data-type="Pure.Configuration.Title"><?php echo __('Allowed count of attachments per one message', 'pure');?></p>
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->attachment_max_count->value; ?>" id="<?php echo $properties->attachment_max_count->name; ?>" name="<?php echo $properties->attachment_max_count->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Max count of chat messages on page', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->chats_max_count_messages->value; ?>" id="<?php echo $properties->chats_max_count_messages->name; ?>" name="<?php echo $properties->chats_max_count_messages->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Maximum size of one chat message', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Size in symbols', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->chat_message_max_size->value; ?>" id="<?php echo $properties->chat_message_max_size->name; ?>" name="<?php echo $properties->chat_message_max_size->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Size of one attachment (in chat message) in bytes', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Count of attachments per message in chat always is 1', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->chat_attachment_max_size->value; ?>" id="<?php echo $properties->chat_attachment_max_size->name; ?>" name="<?php echo $properties->chat_attachment_max_size->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Allow memes in chat', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Memes is a simple collection of some images. It can be memes, smiles or something else. You as administrator can make any collection of memes.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->chat_allow_memes->name;?>" name="<?php echo $properties->chat_allow_memes->name; ?>">
                    <option value="yes" <?php selected( 'yes', $properties->chat_allow_memes->value ); ?>><?php echo __('Allow', 'pure' ); ?></option>
                    <option value="no" <?php selected( 'no', $properties->chat_allow_memes->value ); ?>><?php echo __('Deny', 'pure' ); ?></option>
                </select>
                <p data-type="Pure.Configuration.Title"><?php echo __('Folder of memes on server. This is sub folder in "../wp-content/uploads"', 'pure');?></p>
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->chat_memes_folder->value; ?>" id="<?php echo $properties->chat_memes_folder->name; ?>" name="<?php echo $properties->chat_memes_folder->name; ?>" />
                <p data-type="Pure.Configuration.Attention"><?php echo __('To create collection of memes you should create any folder inside "../wp-content/uploads", place images (*.gif, *.png, *.jpg, *.jpeg) into and define name of this folder here.', 'pure');?></p>
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Max count of notifications messages on page', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->notifications_max_count->value; ?>" id="<?php echo $properties->notifications_max_count->name; ?>" name="<?php echo $properties->notifications_max_count->name; ?>" />
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
$groups->close(false);
?>

