<?php
namespace Pure\Templates\Layout\Page\Footer\Page{
    class Configuration extends \Pure\Templates\Configuration{
        public $version = '0.01';
    }
    class Initialization extends \Pure\Templates\Initialization{
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