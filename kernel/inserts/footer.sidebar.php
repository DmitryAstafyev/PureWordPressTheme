<?php
namespace Pure\Inserts\Content\Footer {
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('footer.php');
    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
    $BuddyPressSettings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->buddypress->properties;
    switch (\Pure\Configuration::instance()->globals->requests->type) {
        case 'BUDDY':
            $layoutClass        = \Pure\Templates\Layout\Page\Footer\Standard\Initialization::instance()->get($BuddyPressSettings->header_template->value);
            echo $layoutClass->get('footer-standard');
            break;
        case 'SPECIAL':
            switch (\Pure\Configuration::instance()->globals->requests->SPECIAL->request) {
                case 'CREATEPOST':
                    $layoutClass        = \Pure\Templates\Layout\Page\Footer\Standard\Initialization::instance()->get($BuddyPressSettings->header_template->value);
                    echo $layoutClass->get('footer-standard');
                    break;
                case 'CREATEEVENT':
                    $layoutClass        = \Pure\Templates\Layout\Page\Footer\Standard\Initialization::instance()->get($BuddyPressSettings->header_template->value);
                    echo $layoutClass->get('footer-standard');
                    break;
                case 'EDITPOST':
                    $layoutClass        = \Pure\Templates\Layout\Page\Footer\Standard\Initialization::instance()->get($BuddyPressSettings->header_template->value);
                    echo $layoutClass->get('footer-standard');
                    break;
                case 'EDITEVENT':
                    $layoutClass        = \Pure\Templates\Layout\Page\Footer\Standard\Initialization::instance()->get($BuddyPressSettings->header_template->value);
                    echo $layoutClass->get('footer-standard');
                    break;
                case 'TOP':
                    $layoutClass        = \Pure\Templates\Layout\Page\Footer\Page\Initialization::instance()->get($BuddyPressSettings->header_template->value);
                    echo $layoutClass->get('footer-page');
                    break;
                case 'STREAM':
                    $layoutClass        = \Pure\Templates\Layout\Page\Footer\Standard\Initialization::instance()->get($BuddyPressSettings->header_template->value);
                    echo $layoutClass->get('footer-standard');
                    break;
                case 'SEARCH':
                    $layoutClass        = \Pure\Templates\Layout\Page\Footer\Standard\Initialization::instance()->get($BuddyPressSettings->header_template->value);
                    echo $layoutClass->get('footer-standard');
                    break;
                case 'ASEARCH':
                    $layoutClass        = \Pure\Templates\Layout\Page\Footer\Page\Initialization::instance()->get($BuddyPressSettings->header_template->value);
                    echo $layoutClass->get('footer-page');
                    break;
                case 'DRAFTS':
                    $layoutClass        = \Pure\Templates\Layout\Page\Footer\Standard\Initialization::instance()->get($BuddyPressSettings->header_template->value);
                    echo $layoutClass->get('footer-standard');
                    break;
                case 'GROUPCONTENT':
                    $layoutClass        = \Pure\Templates\Layout\Page\Footer\Standard\Initialization::instance()->get($BuddyPressSettings->header_template->value);
                    echo $layoutClass->get('footer-standard');
                    break;
            }
            break;
        case 'POST':
            $layoutClass        = \Pure\Templates\Layout\Page\Footer\Standard\Initialization::instance()->get($BuddyPressSettings->header_template->value);
            echo $layoutClass->get('footer-standard');
            break;
        case 'PAGE':
            $layoutClass        = \Pure\Templates\Layout\Page\Footer\Page\Initialization::instance()->get($BuddyPressSettings->header_template->value);
            echo $layoutClass->get('footer-page');
            break;
        case 'AUTHOR':
            $layoutClass        = \Pure\Templates\Layout\Page\Footer\Standard\Initialization::instance()->get($BuddyPressSettings->header_template->value);
            echo $layoutClass->get('footer-standard');
            break;
        case 'CATEGORY':
            $layoutClass        = \Pure\Templates\Layout\Page\Footer\Page\Initialization::instance()->get($BuddyPressSettings->header_template->value);
            echo $layoutClass->get('footer-page');
            break;
        case 'TAG':
            $layoutClass        = \Pure\Templates\Layout\Page\Footer\Page\Initialization::instance()->get($BuddyPressSettings->header_template->value);
            echo $layoutClass->get('footer-page');
            break;
        case 'SEARCH':
            $layoutClass        = \Pure\Templates\Layout\Page\Footer\Page\Initialization::instance()->get($BuddyPressSettings->header_template->value);
            echo $layoutClass->get('footer-page');
            break;
    }
    $layoutClass        = NULL;
    $BuddyPressSettings = NULL;
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('footer.php');
}
?>