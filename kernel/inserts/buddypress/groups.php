<?php
namespace Pure\Inserts\BuddyPress\AllGroups {
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('groups.php');
    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
    $BuddyPressSettings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->buddypress->properties;
    //Layout of page
    $layoutClass        = \Pure\Templates\Layout\BuddyPress\AllGroups\Initialization::instance()->get($BuddyPressSettings->header_template->value);
    echo $layoutClass->get();
    $layoutClass        = NULL;
    $BuddyPressSettings = NULL;
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('groups.php');
}
?>