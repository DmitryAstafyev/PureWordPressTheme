<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('counts');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->counts->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->counts->id;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Count of items", 'pure' ),
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
    <p data-type="Pure.Configuration.Info"><?php echo __('Here you can configure count of items to render. For example, you can define count of posts as 5. It means that on page will be shown 5 thumbnails of posts, but user can load more for sure.', 'pure');?></p>
    <table>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Post', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Count of post\'s thumbnails on different pages, like: user\'s content, content of friends, content of category and ect.' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->posts->value; ?>" id="<?php echo $properties->posts->name; ?>" name="<?php echo $properties->posts->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Items in sidebar', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Here you can define count of items of each group of sidebar. For example, if some sidebar shows members of site you can define here count of member to show. For sure, user can load more.' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->items_on_sidebars->value; ?>" id="<?php echo $properties->items_on_sidebars->name; ?>" name="<?php echo $properties->items_on_sidebars->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Groups on groups page', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('How much groups we should show on groups page (where list of site\'s groups is).' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->groups_on_groups_page->value; ?>" id="<?php echo $properties->groups_on_groups_page->name; ?>" name="<?php echo $properties->groups_on_groups_page->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Members on members page', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('How much members we should show on members page (where list of site\'s members is).' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->members_on_members_page->value; ?>" id="<?php echo $properties->members_on_members_page->name; ?>" name="<?php echo $properties->members_on_members_page->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Groups on some members page', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('How much groups we should show on page of some member of site. This is list of member\'s groups.' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->groups_on_member_page->value; ?>" id="<?php echo $properties->groups_on_member_page->name; ?>" name="<?php echo $properties->groups_on_member_page->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Members on some members page', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('How much members (friends) we should show on page of some member of site. This is list of member\'s friends.' , 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->members_on_member_page->value; ?>" id="<?php echo $properties->members_on_member_page->name; ?>" name="<?php echo $properties->members_on_member_page->name; ?>" />
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