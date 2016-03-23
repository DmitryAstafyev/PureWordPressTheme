<?php
namespace Pure\Providers\Members{
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
            'last'                  =>'last users',
            'users_of_popular'      =>'authors of popular posts',
            'users_of_posts'        =>'authors of defined posts',
            'users'                 =>'defined authors (users)',
            'users_active'          =>'most active commentators',
            'users_of_group'        =>'members of defined groups',
            'users_creative'        =>'most active creators',
            'admins_of_group'       =>'administrators of defined groups',
            'moderators_of_group'   =>'moderators of defined groups',
            'friends_of_user'       =>'friends of defined users',
            'users_of_category'     =>'active creators of category',
            'recipients_of_user'    =>'shows users what wrote some to user',
            'in_stream_of_users'    =>'shows users from stream(s) of defined user(s)'
        );
    }
}
?>