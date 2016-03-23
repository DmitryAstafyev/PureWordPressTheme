<?php
namespace Pure\Components\WordPress\Media\Separator{
    class Configuration extends \Pure\Components\Configuration{
        public $version     = '0.01';
        public $name        = 'Separator media files (by owners) component for WordPress';
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