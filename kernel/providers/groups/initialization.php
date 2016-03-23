<?php
namespace Pure\Providers\Groups{
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
            'last'          =>'last created',
            'popular'       =>'most popular',
            'users'         =>'where member(s) is',
            'creator'       =>'where creator(s) is',
            'administrator' =>'where administrator(s) is',
            'moderator'     =>'where moderator(s) is',
            'groups'        =>'defined groups',
        );
    }
}
?>