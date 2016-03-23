<?php
namespace Pure\Providers\Members{
    class Common{
        public function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['shown'            ]));
                $result = ($result === false ? false : isset($parameters['only_with_avatar' ]));
                $result = ($result === false ? false : isset($parameters['maxcount'         ]));
                $result = ($result === false ? false : isset($parameters['profile'          ]));
                return $result;
            }
            return false;
        }
        private function add_email($user){
            if (isset($user->user_email) === false){
                $record             = new \stdClass();
                $record->ID         = (isset($user->ID) === true ? $user->ID : (isset($user->post_author) === true ? $user->post_author : $user->user_id)) ;
                $user_data          = get_userdata($record->ID);
                $record->user_email = $user_data->user_email;
                return $record;
            }else{
                return $user;
            }
        }
        private function get_comment_count($member_id){
            $comments = get_comments('user_id='.$member_id);
            return count($comments);
        }
        public function select($users_data, $parameters, $format = 'full', $leave_properties = false){
            $result             = new \stdClass();
            $result->members    = array();
            $result->shown      = -1;
            $result->total      = -1;
            if (count($users_data) > $parameters['shown']){
                $index  = 0;
                $count  = 0;
                $data   = new \Pure\Components\WordPress\UserData\Data();
                foreach ($users_data as $user){
                    switch((bool)$parameters['only_with_avatar']){
                        case true:
                            if ($index >= $parameters['shown']){
                                $record = $this->add_email($user);
                                $record = ($leave_properties !== false ? Helpers::instance()->copyProperties($record, $user, $leave_properties) : $record);
                                if ($data->has_user_avatar($record->ID, $record->user_email) === true){
                                    $result->members[]   = $record;
                                    $count ++;
                                }
                            }
                            break;
                        case false:
                            if ($index >= $parameters['shown']){
                                $record = $this->add_email($user);
                                $record = ($leave_properties !== false ? Helpers::instance()->copyProperties($record, $user, $leave_properties) : $record);
                                $result->members[]  = $record;
                                $count ++;
                            }
                            break;
                    }
                    $index ++;
                    if ($count >= $parameters['maxcount']){
                        break;
                    }
                }
                $result->shown      = $count;
                $result->total      = count($users_data);
                $result->members    = $this->get_records($result->members, $parameters, $format, $leave_properties);
            }
            $data = NULL;
            return $result;
        }
        private function get_records($members, $parameters, $format = 'full', $leave_properties = false){
            $result = array();
            foreach($members as $member){
                switch($format){
                    case 'full':
                        $record     = $this->get_record_full($member, $parameters);
                        $record     = ($leave_properties !== false ? Helpers::instance()->copyProperties($record, $member, $leave_properties) : $record);
                        $result[]   = $record;
                        break;
                    case 'name_avatar_id':
                        $record     = $this->get_record_short($member, $parameters);
                        $record     = ($leave_properties !== false ? Helpers::instance()->copyProperties($record, $member, $leave_properties) : $record);
                        $result[]   = $record;
                        break;
                }
            }
            return $result;
        }
        private function get_record_full($member, $parameters){
            $cache = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, array($member->ID, $parameters));
            if (!$data = $cache->value){
                $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                \Pure\Components\Tools\Dates\Initialization::instance()->attach(true);
                $DateTools      = new \Pure\Components\Tools\Dates\Dates();
                \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach();
                $BuddyPressURLs = new \Pure\Components\BuddyPress\URLs\Core();
                $current_user   = $WordPress->get_current_user();
                $user_data      = get_userdata($member->ID);
                $data           = (object)array(
                    'posts'     =>(object)array(
                        'count'=>(count_user_posts($member->ID, 'post') + count_user_posts($member->ID, 'event'))
                    ),
                    'author'    =>(object)array(
                        'id'            =>$member->ID,
                        'avatar'        =>$WordPress->user_avatar_url($member->ID),
                        'login'         =>$user_data->user_login,
                        'name'          =>$WordPress->get_name($user_data),
                        'date'          =>$user_data->user_registered,
                        'friends'       =>(function_exists('friends_get_total_friend_count' ) === true ? friends_get_total_friend_count ($member->ID) : false),
                        'groups'        =>(function_exists('groups_total_groups_for_user'   ) === true ? groups_total_groups_for_user   ($member->ID) : false),
                        'email'         =>$member->user_email,
                        'how_long'      =>$DateTools->fromNow($user_data->user_registered),
                        'urls'          =>(object)array(
                            'personal'      =>$user_data->user_url,
                            'posts'         =>get_author_posts_url($member->ID),
                            'member'        =>$BuddyPressURLs->member($user_data->user_login, $parameters['profile']),
                            'profile'       =>$BuddyPressURLs->profile($user_data->user_login),
                            'friends'       =>$BuddyPressURLs->userFriends($user_data->user_login),
                            'groups'        =>$BuddyPressURLs->userGroups($user_data->user_login),
                        )
                    ),
                    'comments'  =>(object)array(
                        'count'         =>$this->get_comment_count($member->ID),
                    ),
                    'friendship'=>(object)array(
                        'created'       =>false,
                        'accepted'      =>false,
                        'is_initiator'  =>false,
                        'how_long'      =>false
                    )
                );
                if ($current_user !== false){
                    \Pure\Components\BuddyPress\Friendship\Initialization::instance()->attach();
                    $Friendship = new \Pure\Components\BuddyPress\Friendship\Core();
                    $friendship = $Friendship->isFriends((object)array(
                        'memberIDA'=>(int)$current_user->ID,
                        'memberIDB'=>(int)$member->ID
                    ));
                    $Friendship = NULL;
                    if ($friendship !== false){
                        $data->friendship->created      = $friendship->created;
                        $data->friendship->accepted     = $friendship->accepted;
                        $data->friendship->is_initiator = ((int)$friendship->initiator === (int)$member->ID ? true : false);
                        $data->friendship->how_long     = $DateTools->fromNow($data->friendship->created);
                    }
                }
                if (isset($parameters['addition_request']) === true){
                    if (is_array($parameters['addition_request']) === true){
                        $addition_params = $parameters['addition_request'];
                        if (isset($addition_params['request']) === true){
                            $addition_data = false;
                            switch($addition_params['request']){
                                case 'status_in_groups':
                                    if (isset($addition_params['groups']) === true){
                                        $addition_data = $this->get_status_in_group($member->ID, $addition_params['groups']);
                                    }
                                    break;
                            }
                            $data->$addition_params['request'] = $addition_data;
                            //echo var_dump(array('_id_'=>$member->ID, '_groups_'=>$addition_params['groups'], 'add_data'=>$data->$addition_params['request']));
                        }
                    }
                }
                $BuddyPressURLs = NULL;
                $DateTools      = NULL;
                $WordPress      = NULL;
                //echo var_dump($cache->key);
                \Pure\Components\Tools\Cache\Cache::set($cache->key, $data);
            }
            return $data;
        }
        private function get_record_short($member, $parameters){
            \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach();
            $BuddyPressURLs = new \Pure\Components\BuddyPress\URLs\Core();
            $WordPress      = new \Pure\Components\WordPress\UserData\Data();
            $user_data      = get_userdata($member->ID);
            $data           = (object)array(
                'id'            =>$member->ID,
                'avatar'        =>$WordPress->user_avatar_url($member->ID),
                'login'         =>$user_data->user_login,
                'profile'       =>$BuddyPressURLs->member($user_data->user_login, $parameters['profile']),
                'name'          =>$WordPress->get_name($user_data),
                'date'          =>$user_data->user_registered,
            );
            $WordPress      = NULL;
            $BuddyPressURLs = NULL;
            return $data;
        }
        public function get($user_id, $format = 'full'){
            if ((int)$user_id > 0){
                $result = $this->select(
                    array((object)array('ID'=>(int)$user_id)),
                    array(
                        'shown'             =>0,
                        'only_with_avatar'  =>false,
                        'maxcount'          =>1,
                        'profile'           =>'',
                        'from_date'         =>date("Y-m-d"),
                        'days'              =>9999
                    ),
                    $format
                );
                return ($result !== false ? (count($result->members) === 1 ? $result->members[0] : false) : false);
            }
            return false;
        }
        //Addition data ================================================================================================
        private function get_status_in_group($member_ID, $groups_IDs){
            global $wpdb;
            $result = false;
            if (is_array($groups_IDs) === true){
                $result = array();
                foreach($groups_IDs as $groups_ID){
                    $selector       =   'SELECT * '.
                                            'FROM wp_bp_groups_members '.
                                                'WHERE '.
                                                    'group_id = '.$groups_ID.' AND '.
                                                    'user_id = '.$member_ID;
                    $group_data     = $wpdb->get_results($selector);
                    if (is_array($group_data) === true){
                        if (count($group_data) === 1){
                            $result[] = $group_data[0];
                        }
                    }
                }
            }
            return (is_array($result) === true ? (count($result) === 0 ? false : $result) : false);
        }
        public function get_friends_ids($member_id){
            $result = false;
            if ((int)$member_id > 0){
                global $wpdb;
                $selector   =   'SELECT '.
                                    'friend_user_id AS id '.
                                'FROM '.
                                    'wp_bp_friends '.
                                'WHERE '.
                                    'initiator_user_id = '.(int)$member_id.' '.
                                    'AND is_confirmed = 1 '.
                                'UNION '.
                                'SELECT '.
                                    'initiator_user_id AS id '.
                                'FROM '.
                                    'wp_bp_friends '.
                                'WHERE '.
                                    'friend_user_id = '.(int)$member_id.' '.
                                'AND is_confirmed = 1;';
                $IDs        = $wpdb->get_results($selector);
                if (is_array($IDs) === true){
                    \Pure\Components\Tools\Arrays\Initialization::instance()->attach();
                    $Array  = new \Pure\Components\Tools\Arrays\Arrays();
                    $result = $Array->make_array_by_property_of_array_objects($IDs, 'id', false);
                    $Array  = NULL;
                }
            }
            return (is_array($result) === true ? (count($result) === 0 ? false : $result) : false);
        }
    }
    class Helpers {
        static private $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        public function copyProperties(&$target, $source, $properties){
            foreach($properties as $property){
                if (isset($source->$property) !== false && isset($target->$property) === false ){
                    $target->$property = $source->$property;
                }
            }
            return $target;
        }
    }
}
?>