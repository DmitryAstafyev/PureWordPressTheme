<?php
namespace Pure\Providers\Members{
    class users_of_category implements \Pure\Providers\Provider{
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
                $where          = $SQLConditions->WHERE('wp_posts.post_date_gmt', $parameters['from_date'], $parameters['days']);
                $terms_IDs      = $parameters['targets_array'];
                if (count($terms_IDs) > 0){
                    $taxonomy_selector  = '';
                    $first_enter        = false;
                    foreach($terms_IDs as $term_ID){
                        $taxonomy_selector .= ($first_enter === false ? '' : ' OR ').'term_id='.$term_ID;
                        $first_enter        = true;
                    }
                    $term_taxonomy_IDs  = $wpdb->get_results( 'SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE taxonomy="category" AND ('.$taxonomy_selector.')');
                    $taxonomy_selector  = '';
                    $first_enter        = false;
                    foreach ($term_taxonomy_IDs as $term_taxonomy_ID){
                        $taxonomy_selector .= ($first_enter === false ? '' : ' OR ').'wp_term_relationships.term_taxonomy_id='.$term_taxonomy_ID->term_taxonomy_id;
                        $first_enter        = true;
                    }
                    $users_data = $wpdb->get_results(   'SELECT COUNT(wp_posts.ID) as posts_count, wp_posts.post_author '.
                                                            'FROM wp_posts, wp_term_relationships '.
                                                        'WHERE '.$where.' '.
                                                            'AND wp_posts.post_status="publish" '.
                                                            'AND wp_posts.post_type="post" '.
                                                            'AND ('.$taxonomy_selector.')'.
                                                            'AND wp_posts.ID=wp_term_relationships.object_id '.
                                                        'GROUP BY wp_posts.post_author '.
                                                        'ORDER BY posts_count DESC');
                    $result         = $Common->select($users_data, $parameters);
                }
                $SQLConditions  = NULL;
            }
            $Common         = NULL;
            return $result;
        }
    }
}
?>