<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('basic');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->basic->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->basic->id;
$templatesHeaderMenu    = \Pure\Templates\HeaderMenu\Initialization     ::instance()->templates;
$templates404Page       = \Pure\Templates\Pages\Error\Initialization    ::instance()->templates;
$groups                 = \Pure\Templates\Admin\Groups\Initialization   ::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Basic templates", 'pure' ),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>true,
        "container_style"   =>'width:auto;',
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
?>
<form method="POST" action="">
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Header menu', 'pure');?></strong></p>
    <?php
    foreach ($templatesHeaderMenu as $templateHeaderMenu){
        ?>
        <p>
            <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $templateHeaderMenu->key; ?>" <?php checked( $templateHeaderMenu->key, $properties->header_menu_template->value ); ?> id="<?php echo $prefix.$properties->header_menu_template->name.'template'.$templateHeaderMenu->key; ?>" name="<?php echo $properties->header_menu_template->name; ?>" />
            <label for="<?php echo $prefix.$properties->header_menu_template->name.'template'.$templateHeaderMenu->key; ?>">Template <?php echo $templateHeaderMenu->key; ?> <br />
                <img alt="" data-type="Pure.Configuration.Input.Fader" width="50%" style="margin-left: 25%;" src="<?php echo $templateHeaderMenu->thumbnail; ?>">
            </label>
            <?php
            \Pure\Templates\HeaderMenu\Initialization::instance()->description($templateHeaderMenu->key);
            ?>
        </p>
    <?php
    }
    ?>
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('404 page template', 'pure');?></strong></p>
    <?php
    foreach ($templates404Page as $template404Page){
        ?>
        <p>
            <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $template404Page->key; ?>" <?php checked( $template404Page->key, $properties->error_page_template->value ); ?> id="<?php echo $prefix.$properties->error_page_template->name.'template'.$template404Page->key; ?>" name="<?php echo $properties->error_page_template->name; ?>" />
            <label for="<?php echo $prefix.$properties->error_page_template->name.'template'.$template404Page->key; ?>">Template <?php echo $template404Page->key; ?> <br />
                <img alt="" data-type="Pure.Configuration.Input.Fader" width="50%" style="margin-left: 25%;" src="<?php echo $template404Page->thumbnail; ?>">
            </label>
            <?php
            \Pure\Templates\Pages\Error\Initialization::instance()->description($template404Page->key);
            ?>
        </p>
    <?php
    }
    ?>
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Security', 'pure');?></strong></p>
    <table>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Can registered users see WordPress console or not. ', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->console_access->name;?>" name="<?php echo $properties->console_access->name; ?>">
                    <option value="yes" <?php selected( 'yes', $properties->console_access->value ); ?>><?php echo __('Registered users have access to console', 'pure' ); ?></option>
                    <option value="no" <?php selected( 'no', $properties->console_access->value ); ?>><?php echo __('Only admin of site has access to console', 'pure' ); ?></option>
                </select>
                <p data-type="Pure.Configuration.Accent"><?php echo __('We are strongly recommend you, deny access anybody except admin.', 'pure');?></p>
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