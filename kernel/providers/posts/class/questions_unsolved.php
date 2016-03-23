<?php
namespace Pure\Providers\Posts{
    class questions_unsolved implements \Pure\Providers\Provider{
        private function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['from_date'        ]));
                $result = ($result === false ? false : isset($parameters['days'             ]));
                $result = ($result === false ? false : isset($parameters['thumbnails'       ]));
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
                $selector_ids           =   'SELECT '.
                                                'post_id '.
                                            'FROM '.
                                                '( '.
                                                    'SELECT '.
                                                        '* '.
                                                    'FROM '.
                                                        'wp_postmeta '.
                                                    'WHERE '.
                                                        'post_id IN ( '.
                                                            'SELECT '.
                                                                'ID '.
                                                            'FROM '.
                                                                'wp_posts '.
                                                            'WHERE '.
                                                                'post_type = "question" '.
                                                        ') '.
                                                    'AND meta_key = "pure_theme_field_questions_meta" '.
                                                ') AS questions WHERE questions.meta_value LIKE \'%"has_answer";b:0;}%\'';
                $selector               =   'SELECT * FROM wp_posts '.
                                                'WHERE post_status="'.$parameters['post_status'].'" '.
                                                    'AND '.$Common->get_post_type(array('question')).' '.
                                                    'AND '.$where.' '.
                                                    ($parameters['thumbnails'] === true ? $thumbnails_selector : '').
                                                    'AND ID IN ('.$selector_ids.') '.
                                            'ORDER BY post_date_gmt DESC';
                if ($parameters['selection'] !== false){
                    $selector       =   'SELECT '.
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
            return $result;
        }
    }
}
?>