<?php
namespace Pure\Providers\Activities{
    class Configuration extends \Pure\Providers\Configuration{
        public $version     = '0.01';
    }
    class Initialization extends \Pure\Providers\Initialization{
        static private $self;
        static function instance(){
            if (!self::$self){
                $namespace = preg_split('/(\\\)/', __NAMESPACE__);
                $namespace = array_splice($namespace, 2);
                self::$self = new self(new Configuration($namespace), self::$descriptions);
            }
            return self::$self;
        }
        static private $descriptions = array(
            'last'      =>'Last activities from common steam',
            'of_group'  =>'Activities1 of group',
            'of_user'   =>'Activities1 of user'
        );
    }
}
?>