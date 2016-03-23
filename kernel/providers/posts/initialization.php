<?php
namespace Pure\Providers\Posts{
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
            'author'            =>'Posts of defined author(s)',
            'friends_author'    =>'Posts of friends of defined author(s)',
            'category'          =>'Posts of defined category(s)',
            'last'              =>'Last posts',
            'popular'           =>'Most popular posts',
            'group'             =>'Posts of defined group(s)',
            'tag'               =>'Posts of defined tag(s)',
            'defined'           =>'Post(s) by ID',
            'questions_solved'  =>'Question with solution(s)',
            'questions_unsolved'=>'Question without solution'
        );
    }
}
?>