<?php
namespace Pure\Providers\Posts{
    class category implements \Pure\Providers\Provider{
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
        private function categories($parameters){
            global $wpdb;
            $result         = array();
            $selector       =   'SELECT wp_term_taxonomy.term_id AS id, wp_terms.name AS name '.
                                'FROM wp_term_taxonomy, wp_terms '.
                                    'WHERE taxonomy="category" '.
                                        (count($parameters['targets_array']) > 0 ? 'AND wp_term_taxonomy.term_id IN ('.implode(',', $parameters['targets_array']).') ' : '').
                                        'AND wp_terms.term_id=wp_term_taxonomy.term_id';
            $terms          = $wpdb->get_results($selector);
            foreach($terms as $term){
                $result[] = (object)array(
                    'id'    =>$term->id,
                    'name'  =>$term->name,
                    'url'   =>get_category_link( $term->id ),
                );
            }
            return $result;
        }
        private function apply_sandbox($category_id, $parameters, $selector){
            $cache = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, array('sandbox_id'));
            if (! $sandbox_id = $cache->value){
                \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->mana->properties;
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                $sandbox_id = (int)$settings->mana_threshold_manage_categories_sandbox;
                \Pure\Components\Tools\Cache\Cache::set($cache->key, $sandbox_id);
            }
            if ((int)$sandbox_id !== (int)$category_id){
                $Common     = new Common();
                $selector   = $Common->apply_sandbox_setting($parameters, $selector);
                $Common     = NULL;
            }
            return $selector;
        }
        private function select_category($category_id, $parameters){
            global $wpdb;
            $Common                 = new Common();
            \Pure\Components\Tools\SQLConditions\Initialization::instance()->attach(true);
            $SQLConditions          = new \Pure\Components\Tools\SQLConditions\Conditions();
            $where                  = $SQLConditions->WHERE('post_date_gmt', $parameters['from_date'], $parameters['days']);
            $thumbnails_selector    = 'AND ID IN (SELECT post_id FROM wp_postmeta WHERE meta_key="_thumbnail_id") ';
            $selector               =   'SELECT * FROM wp_posts '.
                                            'WHERE post_status="'.$parameters['post_status'].'" '.
                                                'AND '.$Common->get_post_type($parameters['post_type']).' '.
                                                'AND '.$where.' '.
                                                'AND ID IN '.
                                                    '(SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id IN '.
                                                        '(SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE term_id='.$category_id.')) '.
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

            $selector               = $this->apply_sandbox($category_id, $parameters, $selector);
            $_posts                 = $wpdb->get_results(   $selector);
            $posts                  = $wpdb->get_results(   $selector.
                                                            ' LIMIT '.$parameters['shown'].','.$parameters['maxcount'] );
            $_result                = $Common->processing($posts, $parameters, count($_posts));
            $SQLConditions          = NULL;
            $Common                 = NULL;
            return $_result;
        }
        public function get($parameters){
            global $wpdb;
            $result             = false;
            $Common             = new Common();
            if ($this->validate($parameters) === true && $Common->validate($parameters) === true){
                $result = (object)array(
                    'posts'     =>new \stdClass(),
                    'categories'=>new \stdClass(),
                    'shown'     =>0,
                    'total'     =>0
                );
                $categories = $this->categories($parameters);
                foreach($categories as $category){
                    $_result = $this->select_category($category->id, $parameters);
                    if ($_result !== false){
                        $result->shown += $_result->shown;
                        $result->total += $_result->total;
                        $key                        = $category->id;
                        $result->posts->$key        = $_result->posts;
                        $result->categories->$key   = $category;
                    }
                }
            }
            $Common             = NULL;
            return $result;
        }
    }
}
?>