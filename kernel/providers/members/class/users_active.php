<?php
namespace Pure\Providers\Members{
    class users_active implements \Pure\Providers\Provider{
        private function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['from_date'        ]));
                $result = ($result === false ? false : isset($parameters['days'             ]));
                return $result;
            }
            return false;
        }
        public function get($parameters){
            global $wpdb;
            $result             = false;
            $Common             = new Common();
            if ($this->validate($parameters) === true && $Common->validate($parameters) === true){
                \Pure\Components\Tools\SQLConditions\Initialization::instance()->attach(true);
                $SQLConditions  = new \Pure\Components\Tools\SQLConditions\Conditions();
                $where          = $SQLConditions->WHERE('comment_date_gmt', $parameters['from_date'], $parameters['days']);
                $users_data     = $wpdb->get_results( 'SELECT COUNT(comment_ID) as total_comments_count, user_id FROM wp_comments WHERE '.$where.' AND user_id<>0 GROUP BY user_id ORDER BY total_comments_count DESC');
                $result         = $Common->select($users_data, $parameters);
                $SQLConditions  = NULL;
            }
            $Common         = NULL;
            return $result;
        }
    }
}
?>