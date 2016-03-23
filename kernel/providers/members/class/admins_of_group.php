<?php
namespace Pure\Providers\Members{
    class admins_of_group implements \Pure\Providers\Provider{
        private function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['from_date'        ]));
                $result = ($result === false ? false : isset($parameters['days'             ]));
                $result = ($result === false ? false : isset($parameters['targets_array'    ]));
                return $result;
            }
            return false;
        }
        public function get($parameters){
            global $wpdb;
            $result             = false;
            $Common             = new Common();
            if ($this->validate($parameters) === true && $Common->validate($parameters) === true){
                $GroupsCommon               = \Pure\Providers\Groups\Initialization::instance()->getCommon();
                $available_groups_request   = $GroupsCommon->available_groups_request($parameters['from_date'], $parameters['days']);
                if (count($parameters['targets_array']) > 0){
                    $selector       =   'SELECT user_id AS ID '.
                                            'FROM wp_bp_groups_members '.
                                                'WHERE '.
                                                    'is_confirmed = 1 '.                                            'AND '.
                                                    'is_admin = 1 '.                                                'AND '.
                                                    'is_banned = 0 '.                                               'AND '.
                                                    'group_id IN ('.implode(',', $parameters['targets_array']).') '.'AND '.
                                                    'group_id IN (SELECT groups.id FROM ('.$available_groups_request.') AS groups)';
                    $users_data     = $wpdb->get_results( $selector);
                    $result         = $Common->select($users_data, $parameters);
                }
            }
            $Common         = NULL;
            return $result;
        }
    }
}
?>