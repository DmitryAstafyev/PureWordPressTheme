<?php
namespace Pure\Providers\Groups{
    class popular implements \Pure\Providers\Provider{
        private function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['from_date'        ]));
                $result = ($result === false ? false : isset($parameters['days'             ]));
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
                $selector       =   'SELECT COUNT(wp_bp_groups_members.group_id) as members_count, groups.* '.
                                        'FROM ('.$available_groups_request.') AS groups, wp_bp_groups_members '.
                                            'WHERE '.
                                                'groups.id=wp_bp_groups_members.group_id '.
                                            'GROUP BY wp_bp_groups_members.group_id '.
                                            'ORDER BY members_count DESC';
                $_groups        = $wpdb->get_results(   $selector);
                $groups         = $wpdb->get_results(   $selector.' '.
                                                        'LIMIT '.$parameters['shown'].','.$parameters['maxcount']);
                $result         = $Common->processing($groups, $parameters, count($_groups));
                $SQLConditions  = NULL;
            }
            $Common         = NULL;
            return $result;
        }
    }
}
?>