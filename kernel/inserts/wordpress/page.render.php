<?php
namespace Pure\Inserts\WordPress\Page {
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('page.content.php');
    //Get data about member
    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
    $BuddyPressSettings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->buddypress->properties;
    //Layout of page
    $layoutClass        = \Pure\Templates\Layout\WordPress\Page\Initialization::instance()->get($BuddyPressSettings->header_template->value);
    echo $layoutClass->get(\Pure\Configuration::instance()->globals->requests->PAGE);
    $layoutClass        = NULL;
    $BuddyPressSettings = NULL;
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('page.content.php');
}
?>