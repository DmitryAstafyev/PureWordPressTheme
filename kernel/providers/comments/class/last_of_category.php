<?php
namespace Pure\Providers\Comments{
    class last_of_category implements \Pure\Providers\Provider{
        private function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['from_date'        ]));
                $result = ($result === false ? false : isset($parameters['days'             ]));
                $result = ($result === false ? false : isset($parameters['shown'            ]));
                $result = ($result === false ? false : isset($parameters['maxcount'         ]));
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
                if (count($parameters['targets_array']) > 0){
                    $where_posts        = $SQLConditions->WHERE('post_date_gmt', $parameters['from_date'], $parameters['days']);
                    $where_comments     = $SQLConditions->WHERE('comment_date_gmt', $parameters['from_date'], $parameters['days']);
                    $selector           =   'SELECT * FROM wp_comments WHERE '.$where_comments.' AND comment_post_ID IN '.
                                                '(SELECT ID FROM wp_posts WHERE '.$where_posts.' AND ID IN '.
                                                    '(SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id IN '.
                                                        '(SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE term_id IN '.
                                                            '('.implode(',', $parameters['targets_array']).'))))';
                    $_comments      = $wpdb->get_results(   $selector);
                    $comments       = $wpdb->get_results(   $selector.
                                                            ' LIMIT '.$parameters['shown'].','.$parameters['maxcount'] );
                    $result         = $Common->processing($comments, $parameters, count($_comments));
                }
                $SQLConditions  = NULL;
            }
            $Common         = NULL;
            return $result;
        }
    }
}
?>