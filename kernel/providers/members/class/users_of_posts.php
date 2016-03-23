<?php
namespace Pure\Providers\Members{
    class users_of_posts implements \Pure\Providers\Provider{
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
                \Pure\Components\Tools\SQLConditions\Initialization::instance()->attach(true);
                $SQLConditions  = new \Pure\Components\Tools\SQLConditions\Conditions();
                $where          = $SQLConditions->WHERE('post_date_gmt', $parameters['from_date'], $parameters['days']);
                $posts_IDs      = $parameters['targets_array'];
                if (count($posts_IDs) > 0){
                    $posts_selector     = '';
                    $first_enter        = false;
                    foreach($posts_IDs as $post_ID){
                        $posts_selector .= ($first_enter === false ? '' : ' OR ').'ID='.$post_ID;
                        $first_enter     = true;
                    }
                    $users_data = $wpdb->get_results( 'SELECT post_author FROM wp_posts WHERE '.$where.' AND post_status="publish" AND post_type="post" AND ('.$posts_selector.') ORDER BY post_date_gmt DESC');
                }
                $result         = $Common->select($users_data, $parameters);
                $SQLConditions  = NULL;
            }
            $Common         = NULL;
            return $result;
        }
    }
}
?>