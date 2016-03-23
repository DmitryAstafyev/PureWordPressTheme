<?php
namespace Pure\Providers\Posts{
    class author implements \Pure\Providers\Provider{
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
                $selector               =   'SELECT * FROM wp_posts '.
                                                'WHERE post_status="'.$parameters['post_status'].'" '.
                                                    'AND '.$Common->get_post_type($parameters['post_type']).' '.
                                                    'AND '.$where.' '.
                                                    'AND post_author IN ('.implode(',', $parameters['targets_array']).') '.
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
                $_result                = $Common->processing($posts, $parameters, count($_posts));
                $result                 = new \stdClass();
                $result->posts          = new \stdClass();
                $result->shown          = $_result->shown;
                $result->total          = $_result->total;
                foreach($_result->posts as $post){
                    $key = $post->author->id;
                    if (isset($result->posts->$key) === false){
                        $result->posts->$key = array();
                    }
                    array_push($result->posts->$key, $post);
                }
                $SQLConditions          = NULL;
            }
            $Common             = NULL;
            return $result;
        }
    }
}
?>