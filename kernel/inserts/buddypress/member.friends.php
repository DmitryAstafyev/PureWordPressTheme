<?php
namespace Pure\Inserts\BuddyPress\Friends {
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('member.friends.php');
    //Get data about member
    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
    $WordPress          = new \Pure\Components\WordPress\UserData\Data();
    $BuddyPressSettings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->buddypress->properties;
    $member             = new \stdClass();
    $member->id         = \Pure\Configuration::instance()->globals->IDs->user_id;
    $member->avatar     = $WordPress->user_avatar_url($member->id);
    $member->name       = $WordPress->get_name($member->id);
    $WordPress          = NULL;
    //Layout of page
    $layoutClass        = \Pure\Templates\Layout\BuddyPress\Friends\Initialization::instance()->get($BuddyPressSettings->header_template->value);
    echo $layoutClass->get($member);
    $layoutClass        = NULL;
    $BuddyPressSettings = NULL;
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('member.friends.php');
}
?>