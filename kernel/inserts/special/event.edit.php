<?php
namespace Pure\Inserts\Special\Event\Edit{
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('event.edit.php');
//Get data about member
    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
    $BuddyPressSettings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->buddypress->properties;
//Layout of page
    $post_id = false;
    if (isset(\Pure\Configuration::instance()->globals->requests->SPECIAL->parameters) !== false){
        if (isset(\Pure\Configuration::instance()->globals->requests->SPECIAL->parameters->post_id) !== false){
            $post_id = \Pure\Configuration::instance()->globals->requests->SPECIAL->parameters->post_id;
        }
    }
    $layoutClass        = \Pure\Templates\Layout\Special\EventEdit\Initialization::instance()->get($BuddyPressSettings->header_template->value);
    echo $layoutClass->get($post_id);
    $layoutClass        = NULL;
    $BuddyPressSettings = NULL;
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('event.edit.php');
}
?>