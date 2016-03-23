<?php
namespace Pure\Providers\Posts{
    class friends_author implements \Pure\Providers\Provider{
        private function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['from_date'        ]));
                $result = ($result === false ? false : isset($parameters['days'             ]));
                $result = ($result === false ? false : isset($parameters['thumbnails'       ]));
                $result = ($result === false ? false : isset($parameters['targets_array'    ]));
                $parameters['selection'] = (isset($parameters['selection'])     !== false ? $parameters['selection'] : false);
                $parameters['selection'] = (is_array($parameters['selection'])  !== false ? $parameters['selection'] : false);
                /* CONTENT:: gallery, playlist, audio, embed */
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
                $SQLConditions          = new \Pure\Components\Tools\SQLConditions\Conditions();
                $where                  = $SQLConditions->WHERE('post_date_gmt', $parameters['from_date'], $parameters['days']);
                $thumbnails_selector    = 'AND ID IN (SELECT post_id FROM wp_postmeta WHERE meta_key="_thumbnail_id") ';
                $authors_selector       =   'post_author IN ( '.
                                                'SELECT '.
                                                    'friend_user_id '.
                                                'FROM '.
                                                    'wp_bp_friends '.
                                                'WHERE '.
                                                    'initiator_user_id IN ('.implode(',', $parameters['targets_array']).') '.
                                                    'AND is_confirmed = 1 '.
                                                'UNION '.
                                                'SELECT '.
                                                    'initiator_user_id '.
                                                'FROM '.
                                                    'wp_bp_friends '.
                                                'WHERE '.
                                                    'friend_user_id IN ('.implode(',', $parameters['targets_array']).') '.
                                                'AND is_confirmed = 1 '.
                                            ')';
                $selector               =   'SELECT * FROM wp_posts '.
                                                'WHERE post_status="'.$parameters['post_status'].'" '.
                                                    'AND '.$Common->get_post_type($parameters['post_type']).' '.
                                                    'AND '.$where.' '.
                                                    'AND '.$authors_selector.' '.
                                                    ($parameters['thumbnails'] === true ? $thumbnails_selector : '').
                                            'ORDER BY post_date_gmt DESC';
                if ($parameters['selection'] !== false){
                    $selector =     'SELECT '.
                                        '* '.
                                    'FROM '.
                                        '('.$selector.') AS t_posts '.
                                    'WHERE '.
                                        $Common->get_selection_selector('t_posts', $parameters['selection']).' '.
                                    'ORDER BY '.
                                        't_posts.post_date_gmt DESC';
                }
                $selector               = $Common->apply_sandbox_setting($parameters, $selector);
                $_posts                 = $wpdb->get_results(   $selector);
                $posts                  = $wpdb->get_results(   $selector.
                                                                ' LIMIT '.$parameters['shown'].','.$parameters['maxcount'] );
                $result                 = $Common->processing($posts, $parameters, count($_posts));
                $SQLConditions          = NULL;
            }
            $Common             = NULL;
            //echo var_dump($result);exit;
            return $result;
        }
    }
}
?>