<?php
namespace Pure\Inserts\WordPress\SearchResults {
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('search.results.php');
//Get data about member
    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
    $BuddyPressSettings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->buddypress->properties;
//Get IDs of posts
    $IDs = [];
    foreach (\Pure\Configuration::instance()->globals->requests->SEARCH->posts as $post) {
        $IDs[] = $post->ID;
    }
//Layout of page
    $layoutClass = \Pure\Templates\Layout\WordPress\SearchResults\Initialization::instance()->get($BuddyPressSettings->header_template->value);
    echo $layoutClass->get($IDs);
    $layoutClass = NULL;
    $BuddyPressSettings = NULL;
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('search.results.php');
}
?>