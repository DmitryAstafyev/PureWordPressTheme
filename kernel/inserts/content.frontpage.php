<?php
namespace Pure\Inserts\Front {
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('front.content.php');
    //Get settings
    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
    $parameters     = \Pure\Components\WordPress\Settings\Instance::instance()->settings->front_page->properties;
    $parameters     = \Pure\Components\WordPress\Settings\Instance::instance()->less($parameters);
    $TemplateLayout = \Pure\Templates\Layout\Page\ByScheme\Initialization::instance()->get($parameters->template);
    $TemplateLayout->get();
    $TemplateLayout = NULL;
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('front.content.php');
}
?>