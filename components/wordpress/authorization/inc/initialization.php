<?php
namespace Pure\Components\WordPress\Authorization{
    require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->requests.'/settings/request.authorization.requests.php'));
    $Settings = new \Pure\Requests\Authorization\Requests\Settings\Initialization();
    $Settings->init(false);
    $Settings = NULL;
}
?>