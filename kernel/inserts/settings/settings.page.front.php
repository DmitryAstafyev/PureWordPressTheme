<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('front_page');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance          ::instance()->settings->front_page->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance          ::instance()->settings->front_page->id;
$templates              = \Pure\Templates\Layout\Page\ByScheme\Initialization   ::instance()->templates;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Front page template", 'pure' ),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>true,
        "container_style"   =>'width:auto;',
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
?>
<form method="POST" action="">
    <?php
    foreach($templates as $template){
        ?>
        <p>
            <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $template->key; ?>" <?php checked($properties->template->value, $template->key ); ?> id="<?php echo $properties->template->name.'_'.$template->key; ?>" name="<?php echo $properties->template->name; ?>" />
            <label for="LayoutScheme'.$template->key.'">Template <?php echo $template->key; ?><br />
                <img alt="" data-type="Pure.Configuration.Input.Fader"  style="position:relative;width:10rem;left:50%;margin-left: -5rem;" src="<?php echo $template->thumbnail; ?>">
                </label>
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
    <?php
    $groups->close(false);
    ?>
</form>