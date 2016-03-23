<?php
namespace Pure\Providers\Comments{
    class last_in_posts implements \Pure\Providers\Provider{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    if (is_array($parameters) === true){
                        $result = true;
                        $result = ($result === false ? false : isset($parameters['from_date'        ]));
                        $result = ($result === false ? false : isset($parameters['days'             ]));
                        $result = ($result === false ? false : isset($parameters['shown'            ]));
                        $result = ($result === false ? false : isset($parameters['maxcount'         ]));
                        $result = ($result === false ? false : isset($parameters['targets_array'    ]));
                        if ($result !== false){
                            foreach ($parameters['targets_array'] as $key=>$value){
                                $parameters['targets_array'][$key] = (int)$value;
                            }
                        }
                        return $result;
                    }
                    break;
                case 'getFromDateTime':
                    if (is_array($parameters) === true){
                        $result = true;
                        $result = ($result === false ? false : isset($parameters['after_date'       ]));
                        $result = ($result === false ? false : isset($parameters['shown'            ]));
                        $result = ($result === false ? false : isset($parameters['maxcount'         ]));
                        $result = ($result === false ? false : isset($parameters['targets_array'    ]));
                        if ($result !== false){
                            foreach ($parameters['targets_array'] as $key=>$value){
                                $parameters['targets_array'][$key] = (int)$value;
                            }
                        }
                        return $result;
                    }
                    break;
            }
            return false;
        }
        public function get($parameters){
            global $wpdb;
            $result             = false;
            $Common             = new Common();
            if ($this->validate($parameters, __METHOD__) !== false && $Common->validate($parameters) !== false){
                \Pure\Components\Tools\SQLConditions\Initialization::instance()->attach(true);
                $SQLConditions  = new \Pure\Components\Tools\SQLConditions\Conditions();
                if (count($parameters['targets_array']) > 0){
                    $where          = $SQLConditions->WHERE('comment_date_gmt', $parameters['from_date'], $parameters['days']);
                    $selector       =   '( '.
                                            'SELECT '.
                                                '* '.
                                            'FROM '.
                                                'wp_comments '.
                                            'WHERE '.
                                                'comment_post_ID IN ('.implode(',', $parameters['targets_array']).') '.
                                                'AND comment_approved = 1 '.
                                                'AND comment_parent = 0 '.
                                                'AND '.$where.' '.
                                            'ORDER BY '.
                                                'comment_date_gmt DESC '.
                                            'LIMIT '.$parameters['shown'].', '.$parameters['maxcount'].' '.
                                        ') '.
                                        'UNION '.
                                            '( '.
                                                'SELECT '.
                                                    'wp_comments.* '.
                                                'FROM '.
                                                    'wp_comments, '.
                                                    'wp_commentmeta, '.
                                                    '( '.
                                                        'SELECT '.
                                                            'comment_ID '.
                                                        'FROM '.
                                                            'wp_comments '.
                                                        'WHERE '.
                                                            'comment_post_ID IN ('.implode(',', $parameters['targets_array']).') '.
                                                            'AND comment_approved = 1 '.
                                                            'AND comment_parent = 0 '.
                                                            'AND '.$where.' '.
                                                        'ORDER BY '.
                                                            'comment_date_gmt DESC '.
                                                        'LIMIT '.$parameters['shown'].', '.$parameters['maxcount'].' '.
                                                    ') AS IDs '.
                                                'WHERE '.
                                                    'wp_commentmeta.meta_key = "comment_root" '.
                                                    'AND wp_commentmeta.meta_value = IDs.comment_ID '.
                                                    'AND wp_comments.comment_ID = wp_commentmeta.comment_id '.
                                            ')';
                    $selector_total =   'SELECT '.
                                            '* '.
                                        'FROM '.
                                            'wp_comments '.
                                        'WHERE '.
                                            'comment_post_ID IN ('.implode(',', $parameters['targets_array']).') '.
                                            'AND comment_approved = 1 '.
                                            'AND comment_parent = 0 '.
                                            'AND '.$where;
                    $total          = $wpdb->query      ($selector_total);
                    $comments       = $wpdb->get_results($selector);
                    if ($total !== false && is_array($comments) !== false){
                        $result     = $Common->processing($comments, $parameters, (int)$total);
                        if ($parameters['make_tree'] !== false){
                            $result = $Common->tree($result);
                        }
                    }
                }
                $SQLConditions  = NULL;
            }
            $Common         = NULL;
            return $result;
        }
        public function getFromDateTime($parameters){
            global $wpdb;
            $result             = false;
            $Common             = new Common();
            if ($this->validate($parameters, __METHOD__) !== false && $Common->validate($parameters) !== false){
                if (count($parameters['targets_array']) > 0){
                    $selector       =   'SELECT '.
                                            '* '.
                                        'FROM '.
                                            'wp_comments '.
                                        'WHERE '.
                                            'comment_post_ID IN ('.implode(',', $parameters['targets_array']).') '.
                                        'AND comment_approved = 1 '.
                                        'AND comment_date >= "'.$parameters['after_date'].'" '.
                                        'ORDER BY '.
                                            'comment_date DESC';
                    $comments       = $wpdb->get_results($selector);
                    if (is_array($comments) !== false){
                        $result     = $Common->processing($comments, $parameters, count($comments));
                    }
                }
                $SQLConditions  = NULL;
            }
            $Common         = NULL;
            return $result;
        }

    }
}
?>