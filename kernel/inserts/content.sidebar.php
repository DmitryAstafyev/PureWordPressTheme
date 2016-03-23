<?php
namespace Pure\Inserts\Content\SideBar {
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('content.sidebar.php');
    \Pure\Components\WordPress\Sidebars\Initialization::instance()->attach();
    $SideBars   = new \Pure\Components\WordPress\Sidebars\Render();
    $added      = false;
    if (is_front_page() === true) {
    } else {
        switch (\Pure\Configuration::instance()->globals->requests->type) {
            case 'BUDDY':
                $added = $SideBars->by_location('BUDDY',     \Pure\Configuration::instance()->globals->requests->BUDDY);
                break;
            case 'SPECIAL':
                $added = $SideBars->by_location('SPECIAL',   \Pure\Configuration::instance()->globals->requests->SPECIAL->request);
                break;
            case 'POST':
                $added = $SideBars->by_location('POST',      false);
                break;
            case 'PAGE':
                $added = $SideBars->by_location('PAGE',      false);
                break;
            case 'AUTHOR':
                $added = $SideBars->by_location('AUTHOR',    false);
                break;
            case 'CATEGORY':
                $added = $SideBars->by_location('CATEGORY',  false);
                break;
            case 'TAG':
                $added = $SideBars->by_location('TAG',       false);
                break;
            case 'SEARCH':
                $added = $SideBars->by_location('SEARCH',    false);
                break;
        }
    }
    $SideBars = NULL;
    if ($added !== false){
        $SideBarTemplate = \Pure\Templates\Layout\SideBar\Initialization::instance()->get('A');
        $SideBarTemplate->attach();
        $SideBarTemplate = NULL;
    }
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('content.sidebar.php');
}
?>