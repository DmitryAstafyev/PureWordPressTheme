<?php
namespace Pure\Inserts\WordPress\Tag {
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('tag.content.php');
//Get data about member
    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
    $BuddyPressSettings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->buddypress->properties;
//Layout of page
    $layoutClass = \Pure\Templates\Layout\WordPress\Tag\Initialization::instance()->get($BuddyPressSettings->header_template->value);
    echo $layoutClass->get((int)\Pure\Configuration::instance()->globals->requests->TAG->term_id);
    $layoutClass = NULL;
    $BuddyPressSettings = NULL;
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('tag.content.php');
}
?>