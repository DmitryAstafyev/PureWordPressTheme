<?php
namespace Pure\Providers\Comments{
    class last_of_user implements \Pure\Providers\Provider{
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
                    $where          = $SQLConditions->WHERE('comment_date_gmt', $parameters['from_date'], $parameters['days']);
                    $selector       =   'SELECT * FROM wp_comments '.
                                            'WHERE comment_approved=1 AND '.$where.' AND user_id IN '.
                                            '('.implode(',', $parameters['targets_array']).') '.
                                        'ORDER BY comment_date_gmt DESC';
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