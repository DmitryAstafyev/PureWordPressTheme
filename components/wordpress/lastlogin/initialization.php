<?php
namespace Pure\Components\WordPress\LastLogin{
    class Configuration extends \Pure\Components\Configuration{
        public $version     = '0.01';
        public $name        = 'LastLogin component of WordPress';
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