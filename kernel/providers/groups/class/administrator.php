<?php
namespace Pure\Providers\Groups{
    class administrator implements \Pure\Providers\Provider{
        private function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['from_date'        ]));
                $result = ($result === false ? false : isset($parameters['days'             ]));
                $result = ($result === false ? false : isset($parameters['targets_array'    ]));
                $result = ($result === false ? false : isset($parameters['shown'            ]));
                $result = ($result === false ? false : isset($parameters['maxcount'         ]));
                return $result;
            }
            return false;
        }
        public function get($parameters){
            global $wpdb;
            $result                     = false;
            $Common                     = new Common();
            $available_groups_request   = $Common->available_groups_request($parameters['from_date'], $parameters['days']);
            if ($this->validate($parameters) === true && $Common->validate($parameters) === true){
                if (count($parameters['targets_array']) > 0){
                    $selector   =   'SELECT groups.* '.
                                        'FROM ('.$available_groups_request.') AS groups, wp_bp_groups_members '.
                                            'WHERE '.
                                                'groups.id=wp_bp_groups_members.group_id '. 'AND '.
                                                'wp_bp_groups_members.is_admin=1 '.         'AND '.
                                                'wp_bp_groups_members.is_banned = 0 '.      'AND '.
                                                'wp_bp_groups_members.is_confirmed = 1 '.   'AND '.
                                                'wp_bp_groups_members.user_id IN ('.implode(',', $parameters['targets_array']).') '.
                                            'GROUP BY wp_bp_groups_members.group_id '.
                                            'ORDER BY groups.date_created DESC';
                    $_groups    = $wpdb->get_results(   $selector);
                    $groups     = $wpdb->get_results(   $selector.' '.
                                                        'LIMIT '.$parameters['shown'].','.$parameters['maxcount']);
                    $result     = $Common->processing($groups, $parameters, count($_groups));
                }
            }
            $Common         = NULL;
            return $result;
        }
    }
}
?>