<?php
namespace Pure\Components\WordPress\UserData{
    class Data{
        public function user_avatar_url($user_id){
            $cache = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, func_get_args());
            if (! $result = $cache->value){
                if (get_userdata($user_id) !== false){
                    //Get avatar
                    preg_match('/src="(.*?)"/i', get_avatar($user_id), $avatar);
                    if (count($avatar) === 0){
                        preg_match("/src='(.*?)'/i", get_avatar($user_id), $avatar);
                    }else{
                        if (is_string($avatar[0]) === false){
                            preg_match("/src='(.*?)'/i", get_avatar($user_id), $avatar);
                        }
                    }
                    $result = mb_substr($avatar[0], mb_strlen('src="'), mb_strlen($avatar[0]) - mb_strlen('src="') - 1);
                    \Pure\Components\Tools\Cache\Cache::set($cache->key, $result);
                }
            }
            return $result;
        }
        public function has_user_avatar($user_id, $user_email = ''){
            if (get_userdata($user_id) !== false){
                $avatar_url = $this->user_avatar_url($user_id);
                if (strpos($avatar_url, home_url()) !== false ){
                    return true;
                }
                if (strpos($avatar_url, 'gravatar') !== false){
                    if (strlen($user_email) > 0){
                        $user_email_hash = md5($user_email);
                        if (strpos($avatar_url, $user_email_hash) !== false){
                            return true;
                        }
                    }
                }
            }
            return false;
        }
        public function get_name($user_data){//$user_data is ID of user || Object data of user
            $user_data		= (is_int($user_data) === true ? get_userdata($user_data) : $user_data);
            $name           = '';
            if (isset($user_data->first_name) === true && isset($user_data->last_name)=== true){
                if ($user_data->first_name !== '' && $user_data->last_name !== ''){
                    $name     = $user_data->first_name.' '.$user_data->last_name;
                }
            }
            if ($name === ''){
                if (isset($user_data->display_name) === true){
                    if ($user_data->display_name !== ''){
                        $name = $user_data->display_name;
                    }
                }
            }
            if ($name === ''){
                if (isset($user_data->user_login) === true){
                    if ($user_data->user_login !== ''){
                        $name = $user_data->user_login;
                    }
                }
            }
            return $name;
        }
        public function get_current_user($memberships = false, $friends = false, $avatar = false){
            if (is_user_logged_in() === true){
                $cache = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, func_get_args());
                if (! $user = $cache->value){
                    global $current_user;
                    get_currentuserinfo();
                    $user                   = $current_user;
                    $user->role             = new \stdClass();
                    $user->role->is_admin   = in_array( 'administrator',    (array) $user->roles );
                    $user->role->is_editor  = in_array( 'editor',           (array) $user->roles );
                    $user->name             = $this->get_name($user->ID);
                    if ($avatar !== false){
                        $user->avatar = $this->user_avatar_url($user->ID);
                    }
                    if ($memberships !== false){
                        global $wpdb;
                        $_memberships   = array();
                        $memberships    = $wpdb->get_results(   'SELECT group_id AS id '.
                                                                    'FROM wp_bp_groups_members '.
                                                                        'WHERE user_id='.(int)$user->ID);
                        foreach($memberships as $membership){
                            $_memberships[] = (int)$membership->id;
                        }
                        $user->memberships = $_memberships;
                    }
                    if ($friends !== false){
                        global $wpdb;
                        $_friends   = array();
                        $friends    = $wpdb->get_results(   'SELECT friend_user_id AS id '.
                                                                'FROM wp_bp_friends '.
                                                                    'WHERE initiator_user_id = '.(int)$user->ID.' '.
                                                            'UNION '.
                                                            'SELECT initiator_user_id AS id '.
                                                                'FROM wp_bp_friends '.
                                                                    'WHERE friend_user_id = '.(int)$user->ID.';');
                        foreach($friends as $friend){
                            $_friends[] = (int)$friend->id;
                        }
                        $user->friends = $_friends;
                    }
                    \Pure\Components\Tools\Cache\Cache::set($cache->key, $user);
                }
                return $user;
            }else{
                return false;
            }
        }
        public function how_long_on_site($user_id){
            if ((int)$user_id > 0){
                $cache = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, func_get_args());
                if (!$result = $cache->value){
                    $user = get_userdata((int)$user_id);
                    if ($user !== false){
                        \Pure\Components\Tools\Dates\Initialization::instance()->attach(true);
                        $DateTools  = new \Pure\Components\Tools\Dates\Dates();
                        $result     = $DateTools->fromNow($user->user_registered);
                        $DateTools  = NULL;
                    }else{
                        $result = false;
                    }
                    \Pure\Components\Tools\Cache\Cache::set($cache->key, $result);
                }
                return $result;
            }
            return false;
        }
        public function getFriendsOfUser($user_id){
            if ((int)$user_id > 0){
                $cache = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, func_get_args());
                if (!$friends = $cache->value){
                    global $wpdb;
                    $_friends   = array();
                    $friends    = $wpdb->get_results(   'SELECT friend_user_id AS id '.
                                                            'FROM wp_bp_friends '.
                                                                'WHERE initiator_user_id = '.(int)$user_id.' AND is_confirmed = 1 '.
                                                        'UNION '.
                                                        'SELECT initiator_user_id AS id '.
                                                            'FROM wp_bp_friends '.
                                                                'WHERE friend_user_id = '.(int)$user_id.' AND is_confirmed = 1;');
                    foreach($friends as $friend){
                        $_friends[] = (int)$friend->id;
                    }
                    $friends = $_friends;
                    \Pure\Components\Tools\Cache\Cache::set($cache->key, $friends);
                }
                return $friends;
            }
            return false;
        }
        public function can_register(){
            $option = get_option('users_can_register');
            return ($option === false ? false : ((int)$option === 1 ? true : false));
        }
    }
}
?>