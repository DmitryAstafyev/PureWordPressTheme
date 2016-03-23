<?php
namespace Pure\Inserts\WordPress\Category {
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('category.content.php');
//Get data about member
    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
    $BuddyPressSettings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->buddypress->properties;
//Layout of page
    $layoutClass = \Pure\Templates\Layout\WordPress\Category\Initialization::instance()->get($BuddyPressSettings->header_template->value);
    echo $layoutClass->get((int)\Pure\Configuration::instance()->globals->requests->CATEGORY->cat_ID);
    $layoutClass = NULL;
    $BuddyPressSettings = NULL;
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('category.content.php');
}
?>