<?php
namespace Pure\Components\BuddyPress\URLs{
    class Configuration extends \Pure\Components\Configuration{
        public $version     = '0.01';
        public $name        = 'URLs for BuddyPress groups component';
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
    }
}
?>