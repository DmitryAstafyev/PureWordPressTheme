<?php
namespace Pure\Providers\Comments{
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
            'last'              => 'last comments',
            'last_in_popular'   => 'last comments in popular',
            'last_in_posts'     => 'last from posts',
            'last_of_user'      => 'last of user',
            'last_of_category'  => 'last of category',
            'where_post_author' => 'comments of posts of defined author(s)',
        );
    }
}
?>