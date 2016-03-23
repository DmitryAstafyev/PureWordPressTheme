<?php
namespace Pure\Providers\Groups{
    class Common{
        public function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['only_with_avatar' ]));
                return $result;
            }
            return false;
        }
        private function get_members_data($group_ID, $load_all_members = false){
            if (function_exists('groups_get_group_members')){
                $members_data = groups_get_group_members(array(
                    'group_id'  => $group_ID,
                    'per_page'  => ($load_all_members === false ? 10 : 1000),
                    'max'       => ($load_all_members === false ? 10 : 1000),
                    'type'      => 'last_joined',
                ));
                return $members_data;
            }
            return NULL;
        }
        public function available_groups_request($from_date = false, $days = false){
            $WordPress      = new \Pure\Components\WordPress\UserData\Data();
            \Pure\Components\Tools\SQLConditions\Initialization::instance()->attach(true);
            $SQLConditions  = new \Pure\Components\Tools\SQLConditions\Conditions();
            if ($from_date === false || $days === false){
                $from_date              = false;
                $days                   = false;
                $where                  = '';
                $where_wp_bp_groups     = '';
            }else{
                $where                  = $SQLConditions->WHERE('date_created',                 $from_date, $days).' AND ';
                $where_wp_bp_groups     = $SQLConditions->WHERE('wp_bp_groups.date_created',    $from_date, $days).' AND ';
            }
            $current_user           = $WordPress->get_current_user();
            if ($current_user === false){
                $SQLRequest =   'SELECT * '.
                                    'FROM wp_bp_groups '.
                                        'WHERE '.
                                            $where.
                                            'status <> "hidden"';
            }else{
                $SQLRequest =   'SELECT * '.
                                    'FROM wp_bp_groups '.
			                            'WHERE '.
                                            $where.
                                            'status <> "hidden" '.
	                            'UNION '.
                                'SELECT wp_bp_groups.* '.
		                            'FROM wp_bp_groups, wp_bp_groups_members '.
                                        'WHERE '.
                                            $where_wp_bp_groups.
                                            'wp_bp_groups.id = wp_bp_groups_members.group_id '. 'AND '.
                                            'wp_bp_groups.status = "hidden" '.                  'AND '.
                                            'wp_bp_groups_members.is_banned = 0 '.              'AND '.
                                            'wp_bp_groups_members.is_confirmed = 1 '.           'AND '.
                                            'wp_bp_groups_members.user_id = '.(int)$current_user->ID.' '.
			                            'GROUP BY '.
                                            'wp_bp_groups.id';
            }
            $WordPress      = NULL;
            return $SQLRequest;
        }
        public function get_group_membership_requests_for_user($group_id, $user_id){
            global $wpdb;
            $selector = 'SELECT user_id, id AS request_id '.
                            'FROM wp_bp_groups_members '.
                                'WHERE '.
                                    'group_id='.(int)$group_id. ' AND '.
                                    'user_id='.(int)$user_id.   ' AND '.
                                    'inviter_id=0'.             ' AND '.
                                    'is_confirmed=0'.           ' AND '.
                                    'invite_sent=0';
            return $wpdb->get_results($selector);
        }
        public function get_group_membership_requests($group_id){
            global $wpdb;
            $selector = 'SELECT user_id AS id, date_modified AS date, id AS request_id '.
                            'FROM wp_bp_groups_members '.
                                'WHERE '.
                                    'group_id='.(int)$group_id. ' AND '.
                                    'inviter_id=0'.             ' AND '.
                                    'is_confirmed=0'.           ' AND '.
                                    'invite_sent=0';
            $requests  = $wpdb->get_results($selector);
            if (is_array($requests) === true){
                if (count($requests) > 0){
                    \Pure\Components\Tools\Arrays\Initialization::instance()->attach(true);
                    $Helpers    = new \Pure\Components\Tools\Arrays\Arrays();
                    $users_IDs  = $Helpers->make_array_by_property_of_array_objects($requests, 'id', 'integer');
                    $Helpers    = NULL;
                    $provider   = \Pure\Providers\Members\Initialization::instance()->get('users');
                    $users      = $provider->get(array(
                        'shown'             =>0,
                        'only_with_avatar'  =>false,
                        'maxcount'          =>1000,
                        'profile'           =>'',
                        'from_date'         =>date('Y-m-d'),
                        'days'              =>9999,
                        'targets_array'     =>$users_IDs)
                    );
                    if ($users !== false){
                        $_users = array();
                        foreach($users->members as $key=>$user){
                            $index = array_search($user->author->id, $users_IDs);
                            if ($index !== false){
                                $user->membership_request_date  = $requests[$index]->date;
                                $user->membership_request_id    = $requests[$index]->request_id;
                                $_users[]                       = $user;
                            }
                        }
                        return $_users;
                    }
                }
            }
            return false;
        }
        public function processing($groups, $parameters, $total){
            $result         = new \stdClass();
            $result->groups = array();
            $result->shown  = -1;
            $result->total  = -1;
            if (is_null($groups) === false){
                $_parameters = (object)array(
                    'load_all_members'          => (array_key_exists('load_all_members',            $parameters) === true ? $parameters['load_all_members']         : false),
                    'load_membership_requests'  => (array_key_exists('load_membership_requests',    $parameters) === true ? $parameters['load_membership_requests'] : false)
                );
                $WordPress              = new \Pure\Components\WordPress\UserData\Data();
                \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                $BuddyPressGroups       = new \Pure\Components\BuddyPress\Groups\Core();
                \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach();
                $BuddyPressURL          = new \Pure\Components\BuddyPress\URLs\Core();
                foreach($groups as $key => $group){
                    $members                = $this->get_members_data($group->id, $_parameters->load_all_members);
                    $groups[$key]->count    = intval($members['count']);
                    $groups[$key]->members  = $members['members'];
                    foreach ($groups[$key]->members as $member_key => $member){
                        $groups[$key]->members[$member_key]->name       = $WordPress->get_name($member);
                        $groups[$key]->members[$member_key]->avatar     = $WordPress->user_avatar_url($member->ID);
                        $groups[$key]->members[$member_key]->posts_url  = get_author_posts_url($member->ID);
                    }

                    $avatar                 = $BuddyPressGroups->getAvatar((object)array('id'=>(int)$group->id));
                    $groups[$key]->avatar   = (is_string($avatar) === true ? ($avatar !== '' ? $avatar : false) : false);
                    $groups[$key]->url      = $BuddyPressURL->group($group->slug);
                    if ($_parameters->load_membership_requests === true){
                        $groups[$key]->membership_requests = $this->get_group_membership_requests($group->id);
                    }
                    $result->groups[]       = $groups[$key];
                }
                $result->shown  = count($groups);
                $result->total  = $total;
            }
            $BuddyPressURL      = NULL;
            $BuddyPressGroups   = NULL;
            $WordPress          = NULL;
            return $result;
        }
        public function get_groups_IDs_where_user_is($member_id){
            $result = false;
            if ((int)$member_id > 0){
                global $wpdb;
                $selector   =   'SELECT '.
                                    'group_id '.
                                'FROM '.
                                    'wp_bp_groups_members '.
                                'WHERE '.
                                    'user_id = '.(int)$member_id.' '.
                                    'AND is_confirmed = 1 '.
                                    'AND is_banned = 0;';
                $IDs        = $wpdb->get_results($selector);
                if (is_array($IDs) === true){
                    \Pure\Components\Tools\Arrays\Initialization::instance()->attach();
                    $Array  = new \Pure\Components\Tools\Arrays\Arrays();
                    $result = $Array->make_array_by_property_of_array_objects($IDs, 'group_id', false);
                    $Array  = NULL;
                }
            }
            return (is_array($result) === true ? (count($result) === 0 ? false : $result) : false);
        }
    }
}
?>