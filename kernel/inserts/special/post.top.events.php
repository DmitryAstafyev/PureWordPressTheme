<?php
\Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
\Pure\Components\Tools\DebugMarks\Marks::instance()->open('top.events.php');
//Get data about member
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$BuddyPressSettings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->buddypress->properties;
//Layout of page
$layoutClass        = \Pure\Templates\Layout\Special\Top\Events\Initialization::instance()->get($BuddyPressSettings->header_template->value);
echo $layoutClass->get();
$layoutClass        = NULL;
$BuddyPressSettings = NULL;
\Pure\Components\Tools\DebugMarks\Marks::instance()->close('top.events.php');
?>