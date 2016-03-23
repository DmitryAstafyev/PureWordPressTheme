<?php
namespace Pure\Inserts\WordPress\Post {
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('post.content.php');
    //Get data about member
    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
    $BuddyPressSettings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->buddypress->properties;
    //Layout of page
    $layoutClass        = \Pure\Templates\Layout\WordPress\Post\Initialization::instance()->get($BuddyPressSettings->header_template->value);
    echo $layoutClass->get(\Pure\Configuration::instance()->globals->requests->POST);
    $layoutClass        = NULL;
    $BuddyPressSettings = NULL;
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('post.content.php');
}
?>