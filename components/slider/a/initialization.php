<?php
namespace Pure\Components\Slider\A{
    class Configuration extends \Pure\Components\Configuration{
        public $version     = '0.01';
        public $name        = 'Slider component of WordPress';
    }
    class Initialization extends \Pure\Components\Initialization{
        static private $self;
        static function instance(){
            if (!self::$self){
                $namespace  = preg_split('/(\\\)/', __NAMESPACE__);
                $namespace  = array_splice($namespace, 2);
                self::$self = new self(new Configuration($namespace));
            }
            return self::$self;
        }
        public function call_scripts($echo = false){
            $innerHTML = '';
            if (\Pure\Configuration::instance()->globals->requests->AJAX === true){
                $innerHTML =    '<!--JS:['.\Pure\Components\Slider\A\Initialization::instance()->configuration->urls->js.'/slider.js'.']-->'.
                                '<!--INIT:[pure.components.slider.A]-->';
            }
            if ($echo === true){
                echo $innerHTML;
            }else{
                return $innerHTML;
            }
        }
    }
}
?>