<?php
namespace Pure\Providers\Comments{
    class last_in_popular implements \Pure\Providers\Provider{
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
            $result             = false;
            $Common             = new Common();
            if ($this->validate($parameters) === true && $Common->validate($parameters) === true){
                \Pure\Components\Tools\SQLConditions\Initialization::instance()->attach(true);
                $SQLConditions  = new \Pure\Components\Tools\SQLConditions\Conditions();
                $where          = $SQLConditions->WHERE('post_date_gmt', $parameters['from_date'], $parameters['days']);
                $selector       =   'SELECT * FROM wp_comments '.
                                        'WHERE comment_approved=1 AND comment_post_ID IN '.
                                            '(SELECT ID FROM wp_posts '.
                                                'WHERE comment_count<>0 AND '.$where.' '.
                                            'ORDER BY comment_count DESC) '.
                                    'ORDER BY comment_date_gmt DESC';
                $_comments      = $wpdb->get_results(   $selector);
                $comments       = $wpdb->get_results(   $selector.
                                                        ' LIMIT '.$parameters['shown'].','.$parameters['maxcount'] );
                $result         = $Common->processing($comments, $parameters, count($_comments));
                $SQLConditions  = NULL;
            }
            $Common         = NULL;
            return $result;
        }
    }
}
?>