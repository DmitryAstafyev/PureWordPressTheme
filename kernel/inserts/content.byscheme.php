<?php
namespace Pure\Inserts\Front {
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('front.content.php');
    $TemplateLayout = \Pure\Templates\Layout\Page\ByScheme\Initialization::instance()->get(
        \Pure\Configuration::instance()->globals->requests->SPECIAL->parameters->scheme
    );
    if ($TemplateLayout !== false){
        $TemplateLayout->get();
    }else{
        $Location = new \Pure\Components\WordPress\Location\Module\Core();
        $Location->force404();
        //Here PHP did exit;
    }
    $TemplateLayout = NULL;
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('front.content.php');
}
?>