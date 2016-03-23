<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('mana');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->mana->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->mana->id;
$templatesManaIcons     = \Pure\Templates\Mana\Icon\Initialization      ::instance()->templates;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__('Settings of mana scheme for Pure theme', 'pure'),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>true,
        "container_style"   =>'width:auto;',
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
?>
<form method="POST" action="">
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Use mana for manage permissions of users (members)', 'pure');?></strong></p>
    <p data-type="Pure.Configuration.Info"><?php echo __('If you switch off mana, users can rate comments, posts and etc in any case. But in such case, value of mana does not mater for user\'s permissions.', 'pure');?></p>
    <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->mana_using->name;?>" name="<?php echo $properties->mana_using->name; ?>">
        <option value="on" <?php selected( 'on', $properties->mana_using->value ); ?>><?php echo __('Use mana', 'pure' ); ?></option>
        <option value="off" <?php selected( 'off', $properties->mana_using->value ); ?>><?php echo __('Do not use mana', 'pure' ); ?></option>
    </select>
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('If mana management is active', 'pure');?></strong></p>
    <table>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Create post', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('User should have defined value of mana (positive value) to create post. If user has not necessary value of mana, he will not create any post.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_threshold_create_post->value; ?>" id="<?php echo $properties->mana_threshold_create_post->name; ?>" name="<?php echo $properties->mana_threshold_create_post->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Create event', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('User should have defined value of mana (positive value) to create event.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_threshold_create_event->value; ?>" id="<?php echo $properties->mana_threshold_create_event->name; ?>" name="<?php echo $properties->mana_threshold_create_event->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Create report', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('User should have defined value of mana (positive value) to create report.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_threshold_create_report->value; ?>" id="<?php echo $properties->mana_threshold_create_report->name; ?>" name="<?php echo $properties->mana_threshold_create_report->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Create question and add answers', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('User should have defined value of mana (positive value) to create question and add answers.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_threshold_create_question->value; ?>" id="<?php echo $properties->mana_threshold_create_question->name; ?>" name="<?php echo $properties->mana_threshold_create_question->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Post comments', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('User should have defined value of mana (positive value) to add comments in any place of site.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_threshold_create_comment->value; ?>" id="<?php echo $properties->mana_threshold_create_comment->name; ?>" name="<?php echo $properties->mana_threshold_create_comment->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Update status (add activity)', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('User should have defined value of mana (positive value) to add new activity (short post in his activities ("Life" section).', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_threshold_create_activity->value; ?>" id="<?php echo $properties->mana_threshold_create_activity->name; ?>" name="<?php echo $properties->mana_threshold_create_activity->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Remove status (remove activity)', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('User should have defined value of mana (positive value) to remove his activity (short post in his activities ("Life" section).', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_threshold_do_activity_remove->value; ?>" id="<?php echo $properties->mana_threshold_do_activity_remove->name; ?>" name="<?php echo $properties->mana_threshold_do_activity_remove->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Remove comment (in activities)', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('User should have defined value of mana (positive value) to remove comments to his activity (short post in his activities ("Life" section).', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_threshold_do_comment_remove->value; ?>" id="<?php echo $properties->mana_threshold_do_comment_remove->name; ?>" name="<?php echo $properties->mana_threshold_do_comment_remove->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Vote comment', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('User should have defined value of mana (positive value) to vote any comment.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_threshold_vote_comment->value; ?>" id="<?php echo $properties->mana_threshold_vote_comment->name; ?>" name="<?php echo $properties->mana_threshold_vote_comment->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Vote post', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('User should have defined value of mana (positive value) to vote post.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_threshold_vote_post->value; ?>" id="<?php echo $properties->mana_threshold_vote_post->name; ?>" name="<?php echo $properties->mana_threshold_vote_post->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Go out from sandbox', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('If user has not enough value of mana, any his post will be placed in sandbox (special category). When user get necessary value of mana, he can go out from sandbox.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_threshold_manage_categories->value; ?>" id="<?php echo $properties->mana_threshold_manage_categories->name; ?>" name="<?php echo $properties->mana_threshold_manage_categories->name; ?>" />
                <p data-type="Pure.Configuration.Info"><?php echo __('Choose category which will be used as sandbox.', 'pure');?></p>
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->mana_threshold_manage_categories_sandbox->name;?>" name="<?php echo $properties->mana_threshold_manage_categories_sandbox->name; ?>">
                <?php
                $categories = get_terms('category', 'orderby=count&hide_empty=0');
                foreach($categories as $category) {
                    ?>
                        <option value="<?php echo $category->term_id;?>" <?php selected( $category->term_id, $properties->mana_threshold_manage_categories_sandbox->value ); ?>><?php echo $category->name; ?></option>
                    <?php
                }
                ?>
                </select>
                <p data-type="Pure.Configuration.Attention"><?php echo __('If you use mana you have to define sandbox category, or can be unexpected errors.', 'pure');?></p>
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Allow or deny comments', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('If user has enough value of mana, he can allow or deny comments to his post. If not - comments will be allowed.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_threshold_manage_comments->value; ?>" id="<?php echo $properties->mana_threshold_manage_comments->name; ?>" name="<?php echo $properties->mana_threshold_manage_comments->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Allow or deny vote of post', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('If user has enough value of mana, he can allow or deny vote to his post. If not - vote will be allowed.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_threshold_manage_vote->value; ?>" id="<?php echo $properties->mana_threshold_manage_vote->name; ?>" name="<?php echo $properties->mana_threshold_manage_vote->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Maximum gift', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Users can give own mana to other users. Define here value of maximum gift.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->mana_maximum_gift->value; ?>" id="<?php echo $properties->mana_maximum_gift->name; ?>" name="<?php echo $properties->mana_maximum_gift->name; ?>" />
            </td>
        </tr>
    </table>
    <p data-type="Pure.Configuration.Attention"><?php echo __('Default value of mana for new users is 0.', 'pure');?></p>
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Template of mana icons', 'pure');?></strong></p>
    <?php
    foreach ($templatesManaIcons as $templateManaIcons){
        ?>
        <p>
            <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $templateManaIcons->key; ?>" <?php checked( $templateManaIcons->key, $properties->mana_icon_template->value ); ?> id="<?php echo $prefix.$properties->mana_icon_template->name.'template'.$templateManaIcons->key; ?>" name="<?php echo $properties->mana_icon_template->name; ?>" />
            <label for="<?php echo $prefix.$properties->mana_icon_template->name.'template'.$templateManaIcons->key; ?>">Template <?php echo $templateManaIcons->key; ?> <br />
                <img alt="" data-type="Pure.Configuration.Input.Fader" width="50%" style="margin-left: 25%;" src="<?php echo $templateManaIcons->thumbnail; ?>">
            </label>
            <?php
            \Pure\Templates\BuddyPress\Activities\Initialization::instance()->description($templateManaIcons->key);
            ?>
        </p>
    <?php
    }
    ?>
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

