<?php
namespace Pure\Inserts\Header\Before {
    if (is_front_page() === true) {
        \Pure\Configuration::instance()->globals->styles->logo_mode = 'light';
    }else if(\Pure\Configuration::instance()->globals->requests->BYSCHEME !== false){
        \Pure\Configuration::instance()->globals->styles->logo_mode = 'light';
    } else {
        switch (\Pure\Configuration::instance()->globals->requests->type) {
            case 'BUDDY':
                \Pure\Configuration::instance()->globals->styles->logo_mode = 'dark';
                break;
            case 'SPECIAL':
                \Pure\Configuration::instance()->globals->styles->logo_mode = 'dark';
                break;
            case 'POST':
                \Pure\Configuration::instance()->globals->styles->logo_mode = 'dark';
                break;
            case 'PAGE':
                \Pure\Configuration::instance()->globals->styles->logo_mode = 'dark';
                break;
            case 'AUTHOR':
                \Pure\Configuration::instance()->globals->styles->logo_mode = 'dark';
                break;
            case 'CATEGORY':
                \Pure\Configuration::instance()->globals->styles->logo_mode = 'dark';
                break;
            case 'TAG':
                \Pure\Configuration::instance()->globals->styles->logo_mode = 'dark';
                break;
            case 'SEARCH':
                \Pure\Configuration::instance()->globals->styles->logo_mode = 'dark';
                break;
            case 'STREAM':
                \Pure\Configuration::instance()->globals->styles->logo_mode = 'dark';
                break;
        }
    }
    //Generate background page
    \Pure\Components\WordPress\PageBackground\Initialization::instance()->attach();
    $BackgroundPage = new \Pure\Components\WordPress\PageBackground\Core();
    $BackgroundPage->generate();
    $BackgroundPage = NULL;
    //Attach global openmenu
    $HeaderMenu = \Pure\Templates\HeaderMenu\Initialization::instance()->get('C');
    $HeaderMenu->get('','primary',true);
    require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/header.after.php'));
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('header.php');
}
?>