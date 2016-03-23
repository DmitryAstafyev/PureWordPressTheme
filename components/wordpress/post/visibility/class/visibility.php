<?php
namespace Pure\Components\WordPress\Post\Visibility{
    /*
    VALUES:: VISIBILITY===============================
        [community]
        [friends]
        [group]
    VALUES:: CLOSED ================================
        [0] - for all
        [1] - for associated object (community, friends or group)
    */
    class Data{
        static $visibility = array(
            'public'    =>0,
            'private'   =>1,
        );
        static $association = array(
            'community' =>'community',
            'friends'   =>'friends',
            'group'     =>'group',
        );
    }
    class Provider{
        private $table;
        public function defaults(){
            return (object)array(
                'visibility'    => Data::$visibility['public'],
                'association'   => Data::$association['community']
            );
        }
        public function get($post_id){
            if ((int)$post_id > 0){
                global $wpdb;
                $selector   =   'SELECT '.
                                    'association as association, '.
                                    'object_id as object_id, '.
                                    'closed as visibility '.
                                'FROM '.
                                    'wp_pure_post_visibility '.
                                'WHERE '.
                                    'post_id = '.(int)$post_id;
                $visibility = $wpdb->get_results($selector);
                if (is_array($visibility) !== false){
                    if (count($visibility) === 1){
                        return $visibility[0];
                    }
                }
                return $this->defaults();
            }
            return false;
        }
        public function set($post_id, $data, $object_id){
            if ((int)$post_id > 0){
                if (is_object($data) !== false){
                    if (isset($data->visibility) !== false && isset($data->association) !== false){
                        if (array_search($data->visibility, Data::$visibility) !== false && array_search($data->association, Data::$association) !== false){
                            global $wpdb;
                            $selector   =   'SELECT '.
                                                '* '.
                                            'FROM '.
                                                'wp_pure_post_visibility '.
                                            'WHERE '.
                                                'post_id = '.(int)$post_id;
                            $visibility = (int)$wpdb->query($selector);
                            if ($visibility === 0){
                                $result     = $wpdb->insert(
                                    $this->table,
                                    array(
                                        'post_id'       =>(int)$post_id,
                                        'association'   =>esc_sql($data->association),
                                        'object_id'     =>($data->association === 'group' ? (int)$object_id : 0),
                                        'closed'        =>(int)$data->visibility
                                    ),
                                    array('%d', '%s', '%d', '%d')
                                );
                                return ($result !== false ? true : false);
                            }else{
                                //Update
                                $result = $wpdb->update(
                                    $this->table,
                                    array(
                                        'association'   => esc_sql($data->association),
                                        'object_id'     =>(int)$object_id,
                                        'closed'        =>(int)$data->visibility
                                    ),
                                    array( 'post_id' => (int)$post_id),
                                    array( '%s', '%d', '%d'),
                                    array( '%d')
                                );
                                return ($result !== false ? true : false);
                            }
                        }
                    }
                }
            }
            return false;
        }
        function __construct(){
            $this->table = \Pure\DataBase\TablesNames::instance()->post_visibility;
        }
    }
    class Availability{
        public function viewPost($post_id){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $Provider   = new Provider();
            $user       = $WordPress->get_current_user();
            $visibility = $Provider->get($post_id);
            $Provider   = NULL;
            $WordPress  = NULL;
            if ($visibility !== false){
                if ((int)$visibility->visibility === 0){
                    //Public post
                    //Available for everybody
                    return true;
                }elseif((int)$visibility->visibility === 1){
                    //Private post
                    switch($visibility->association){
                        case 'community':
                            //Available for registered users
                            return ($user !== false ? true : false);
                        case 'friends':
                            //Available for friends
                            if ($user !== false){
                                $PostProvider   = \Pure\Providers\Posts\Initialization::instance()->getCommon();
                                $post           = $PostProvider->get($post_id, true);
                                $PostProvider   = NULL;
                                if ($post !== false){
                                    if ((int)$post->post_author === (int)$user->ID){
                                        return true;
                                    }
                                    \Pure\Components\BuddyPress\Friendship\Initialization::instance()->attach(true);
                                    $Friendship = new \Pure\Components\BuddyPress\Friendship\Core();
                                    $friendship = $Friendship->isFriends(
                                        (object)array(
                                            'memberIDA'=>(int)$post->post_author,
                                            'memberIDB'=>(int)$user->ID
                                        )
                                    );
                                    $Friendship = NULL;
                                    if ($friendship !== false){
                                        if ($friendship->accepted !== false){
                                            return true;
                                        }
                                    }
                                }
                            }
                            return false;
                        case 'group':
                            if ($user !== false){
                                $PostProvider   = \Pure\Providers\Posts\Initialization::instance()->getCommon();
                                $post           = $PostProvider->get($post_id, true);
                                $PostProvider   = NULL;
                                if ($post !== false) {
                                    if ((int)$post->post_author === (int)$user->ID) {
                                        return true;
                                    }
                                    \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach(true);
                                    $Membership = new \Pure\Components\BuddyPress\Groups\Core();
                                    $membership = $Membership->getMembershipData(
                                        (object)array(
                                            'group_id'  =>(int)$visibility->object_id,
                                            'user_id'   =>(int)$user->ID
                                        )
                                    );
                                    $Membership = NULL;
                                    if ($membership !== false && is_null($membership) === false){
                                        if ($membership->status === 'member'){
                                            return true;
                                        }
                                    }
                                }
                            }
                            return false;
                    }
                }
            }
            return false;
        }
        public function editPost($post_id){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $user       = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ($user !== false){
                $PostProvider   = \Pure\Providers\Posts\Initialization::instance()->getCommon();
                $post           = $PostProvider->get($post_id, true);
                $PostProvider   = NULL;
                if ($post !== false){
                    if ((int)$post->post_author === (int)$user->ID){
                        return true;
                    }
                }
            }
            return false;
        }
        public function createPost(){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $user       = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ($user !== false){
                return true;
            }
            return false;
        }
    }
}
?>