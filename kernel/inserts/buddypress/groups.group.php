<?php
namespace Pure\Inserts\BuddyPress\Group {
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('group.php');
    //Get data about group
    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
    $BuddyPressSettings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->buddypress->properties;
    //Layout of page
    $layoutClass        = \Pure\Templates\Layout\BuddyPress\Group\Initialization::instance()->get($BuddyPressSettings->header_template->value);
    echo $layoutClass->get(\Pure\Configuration::instance()->globals->IDs->group_id);
    $layoutClass        = NULL;
    $BuddyPressSettings = NULL;
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('group.php');
}
?>