<?php
namespace Pure\Components\WordPress\Location\Module{
    \Pure\Components\WordPress\Location\Module\Initialization::instance()->attach();
    $Location = new \Pure\Components\WordPress\Location\Module\Core();
    $Location->proceed();
    $Location = NULL;
}
?>