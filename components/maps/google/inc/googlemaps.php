<?php
namespace Pure\Components\Maps\Google{
    class Loader{
        public function attach(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->googlemaps->properties;
            $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addJS(
                $settings->script_url.$settings->access_key.'&signed_in=false&callback=pure.safelyHandles.mapsGoogleInit',
                false,
                true
            );
            $settings = NULL;
        }
    }
    $Loader = new Loader();
    $Loader->attach();
    $Loader = NULL;
}
?>