<?php
namespace Pure\Providers\Groups{
    class creator implements \Pure\Providers\Provider{
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
                                        'FROM ('.$available_groups_request.') AS groups '.
                                            'WHERE groups.creator_id IN ('.implode(',', $parameters['targets_array']).') '.
                                            'ORDER BY groups.date_created DESC';
                    $_groups    = $wpdb->get_results( $selector);
                    $groups     = $wpdb->get_results( $selector.' LIMIT '.$parameters['shown'].','.$parameters['maxcount']);
                    $result     = $Common->processing($groups, $parameters, count($_groups));
                }
            }
            $Common         = NULL;
            return $result;
        }
    }
}
?>