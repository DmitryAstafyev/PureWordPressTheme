<?php
namespace Pure\Providers\Posts{
    class Common{
        public function validate(&$parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['shown'            ]));
                $result = ($result === false ? false : isset($parameters['maxcount'         ]));
                $result = ($result === false ? false : isset($parameters['profile'          ]));
                $result = ($result === false ? false : isset($parameters['post_type'        ]));
                $result = ($result === false ? false : isset($parameters['post_status'      ]));
                $SandboxSelector = new SandboxSelector();
                $SandboxSelector->validate($parameters);
                $SandboxSelector = NULL;
                return $result;
            }
            return false;
        }
        public function apply_sandbox_setting($parameters, $posts_selector){
            $SandboxSelector = new SandboxSelector();
            $result_selector = $SandboxSelector->apply_sandbox_setting($parameters, $posts_selector);
            $SandboxSelector = NULL;
            return $result_selector;
        }
        public function get_post_type($post_type){
            $SQLRequestPart = false;
            if (is_array($post_type) === true){
                $SQLRequestPart = 'post_type IN (';
                $index          = 0;
                foreach($post_type as $type){
                    $SQLRequestPart .= '"'.$type.'"';
                    $index ++;
                    if ($index < count($post_type)){
                        $SQLRequestPart .= ',';
                    }
                }
                $SQLRequestPart .= ')';
            }else{
                if (is_string($post_type) === true){
                    $SQLRequestPart = 'post_type="'.$post_type.'"';
                }
            }
            return $SQLRequestPart;
        }
        public function get_selection_selector($table, $content){
            $selector   = '';
            $index      = 0;
            foreach($content as $element){
                switch($element){
                    case 'gallery':
                        $selector .= ($index > 0 ? ' OR ' : '').$table.'.post_content LIKE "%[gallery%ids=\"%\"]%"';
                        break;
                    case 'playlist':
                        $selector .= ($index > 0 ? ' OR ' : '').$table.'.post_content LIKE "%[playlist%ids=\"%\"]%"';
                        break;
                    case 'audio':
                        $selector .= ($index > 0 ? ' OR ' : '').$table.'.post_content LIKE "%[audio %]%[/audio]%"';
                        break;
                    case 'embed':
                        $selector .= ($index > 0 ? ' OR ' : '').$table.'.post_content LIKE "%[embed]%[/embed]%"';
                        break;
                    default:
                        $index --;
                        break;
                }
                $index ++;
            }
            return $selector;
        }
        private function get_type($data, $post_original){
            $type = false;
            if (($data->post->excerpt !== false || $data->post->excerpt !== '') && $post_original->post_excerpt === ''){
                //Originally post does not have excerpt, but excerpt was generated
                if ($type === false && $data->post->media->gallery  !== false) { $type = 'gallery'; }
                if ($type === false && $data->post->media->audio    !== false) { $type = 'audio';   }
                if ($type === false && $data->post->media->embed    !== false) { $type = 'embed';   }
                if ($type === false && $data->post->media->video    !== false) { $type = 'video';   }
                if ($type === false && $data->post->images          !== false) { $type = 'images';  }
            }
            if ($data->post->excerpt === false || $data->post->excerpt === ''){
                //Post has not excerpt
                if ($type === false && $data->post->media->gallery  !== false) { $type = 'gallery'; }
                if ($type === false && $data->post->media->audio    !== false) { $type = 'audio';   }
                if ($type === false && $data->post->media->embed    !== false) { $type = 'embed';   }
                if ($type === false && $data->post->media->video    !== false) { $type = 'video';   }
                if ($type === false && $data->post->images          !== false) { $type = 'images';  }
            }
            if ($type === false){
                $type = ($data->post->miniature === '' ? 'post_without_miniature' : 'post_with_miniature');
            }
            return $type;
        }
        private function add_data($post, $parameters){
            $cache = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, array($post->ID, $parameters));
            if (! $result = $cache->value){
                \Pure\Components\BuddyPress\URLs\               Initialization::instance()->attach(true);
                \Pure\Components\WordPress\Post\ViewsCounter\   Initialization::instance()->attach(true);
                \Pure\Components\WordPress\Post\Parser\         Initialization::instance()->attach(true);
                \Pure\Components\WordPress\Post\Visibility\     Initialization::instance()->attach(true);
                $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                $PostParser     = new \Pure\Components\WordPress\Post\Parser\Parser();
                $ViewsCounter   = new \Pure\Components\WordPress\Post\ViewsCounter\Counter();
                $Visibility     = new \Pure\Components\WordPress\Post\Visibility\Provider();
                $BuddyPressURLs = new \Pure\Components\BuddyPress\URLs\Core();
                $user_data      = get_userdata($post->post_author);
                $categories     = get_the_category($post->ID);
                $category       = (count($categories) > 0 ? $categories[0] : false);
                $_categories    = array();
                foreach($categories as $_category){
                    $_categories[] = (object)array(
                        'id'    =>$_category->cat_ID,
                        'name'  =>$_category->cat_name,
                        'url'   =>get_category_link($_category->cat_ID ),
                    );
                }
                $categories     = $_categories;
                $_post          = (object)array(
                    'post'      =>(object)array(
                        'id'            =>$post->ID,
                        'date'          =>(new \DateTime($post->post_date))->format('Y-m-d H:i'),
                        'url'           =>get_permalink($post->ID),
                        'excerpt'       =>$PostParser->get_excerpt($post->ID, 'post', 500),
                        'images'        =>$PostParser->get_images($post->ID, 10),
                        'media'         =>$PostParser->get_media($post->ID),
                        'title'         =>$post->post_title,
                        'comments'      =>$post->comment_count,
                        'views'         =>$ViewsCounter->get($post->ID),
                        'miniature'     =>(has_post_thumbnail($post->ID) === true ? wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large')[0] : ''),
                        'type'          =>false,
                        'post_type'     =>$post->post_type,
                        'post_status'   =>$post->post_status,
                    ),
                    'author'    =>(object)array(
                        'id'        =>$post->post_author,
                        'avatar'    =>$WordPress->user_avatar_url($post->post_author),
                        'posts'     =>get_author_posts_url($post->post_author),
                        'user_login'=>$user_data->user_login,
                        'profile'   =>$BuddyPressURLs->member($user_data->user_login, (isset($parameters['profile']) !== false ? $parameters['profile'] : '')),
                        'name'      =>$WordPress->get_name($user_data),
                    ),
                    'category'  => false,
                    'visibility'=>$Visibility->get($post->ID),
                    'event'     =>(object)array(
                        'start'     =>false,
                        'place'     =>false,
                        'members'   =>false,
                    ),
                );
                if ($category !== false){
                    $_post->category = (object)array(
                        'id'        =>$category->cat_ID,
                        'url'       =>get_category_link( $category->cat_ID ),
                        'name'      =>$category->cat_name,
                        'all'       =>$categories
                    );
                }
                if ($_post->post->post_type === 'event'){
                    $EventProvider  = new \Pure\Components\PostTypes\Events\Module\Provider();
                    $_post->event   = $EventProvider->get($_post->post->id);
                    $EventProvider  = NULL;
                }
                if ($_post->post->post_type === 'question'){
                    \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
                    $Questions          = new \Pure\Components\PostTypes\Questions\Module\Provider();
                    $_post->question    = $Questions->get($_post->post->id);
                    $Questions          = NULL;
                }
                $_post->post->type = $this->get_type($_post, $post);
                if (isset($parameters['add_original']) !== false){
                    if ($parameters['add_original'] !== false){
                        foreach($post as $key=>$value){
                            $_post->post->$key = (isset($_post->post->$key) === false ? $value : $_post->post->$key);
                        }
                        $_post->post->post_content = $post->post_content;
                    }
                }
                $WordPress      = NULL;
                $PostParser     = NULL;
                $ViewsCounter   = NULL;
                $BuddyPressURLs = NULL;
                $user_data      = NULL;
                $category       = NULL;
                $result         = $_post;
                \Pure\Components\Tools\Cache\Cache::set($cache->key, $result);
            }
            return $result;
        }
        private function fill_mana(&$posts, $posts_IDs){
            \Pure\Components\Relationships\Mana\Initialization::instance()->attach();
            $Mana   = new \Pure\Components\Relationships\Mana\Provider();
            $values = $Mana->getForObjectsWithDefault(
                (object)array(
                    'object'=>'post',
                    'IDs'   =>$posts_IDs
                )
            );
            $Mana   = NULL;
            if ($values !== false){
                foreach($posts as $key=>$post){
                    if (is_object($values[$post->post->id]) !== false){
                        $value = (int)$values[$post->post->id]->plus - (int)$values[$post->post->id]->minus;
                    }else{
                        $value = (int)$values[$post->post->id];
                    }
                    $posts[$key]->post->karma = $value;
                }
            }
        }
        public function processing($posts, $parameters, $count){
            $_posts     = array();
            $posts_IDs  = array();
            foreach ($posts as $post){
                $_posts[]       = $this->add_data($post, $parameters);
                $posts_IDs[]    = $post->ID;
            }
            $this->fill_mana($_posts, $posts_IDs);
            return (object)array(
                'posts'     =>$_posts,
                'shown'     =>count($_posts),
                'total'     =>$count
            );
        }
        public function get($post_id, $short = false, $profile = ''){
            if ((int)$post_id > 0){
                $post = get_post((int)$post_id);
                if ($post !== false && is_null($post) === false){
                    if ($short !== false){
                        return $post;
                    }else{
                        return $this->add_data(
                            $post,
                            array(
                                'profile'       =>$profile,
                                'add_original'  =>true
                            )
                        );
                    }
                }
            }
            return false;
        }
        public function get_attachment_post_by_url($_url){
            $url = filter_var($_url, FILTER_VALIDATE_URL);
            if ($url !== false){
                global $wpdb;
                $selector   =   'SELECT '.
                                    '* '.
                                'FROM '.
                                    'wp_posts '.
                                'WHERE '.
                                    'post_type = "attachment" '.
                                    'AND guid = "'.$url.'"';
                $post       = $wpdb->get_results(   $selector);
                if (is_array($post) !== false){
                    if (count($post) === 1){
                        return $post[0];
                    }
                }
            }
            return false;
        }
        //$object_id = -1 => for all post and events
        public function get_posts_count_of_type($object_id, $type, $is_term = false){
            if ((int)$object_id > 0 || (int)$object_id === -1){
                switch($type){
                    case 'all':
                        $selection = array();
                        break;
                    case 'galleries':
                        $selection = array('gallery');
                        break;
                    case 'audio':
                        $selection = array('playlist', 'audio');
                        break;
                    case 'media':
                        $selection = array('embed');
                        break;
                }
                global $wpdb;
                $selection  = $this->get_selection_selector('wp_posts', $selection);
                $selection  = ($selection !== '' ? ' AND ('.$selection.')' : '');
                if ((int)$object_id === -1){
                    $selector   =   'SELECT '.
                                        '* '.
                                    'FROM '.
                                        'wp_posts '.
                                    'WHERE '.
                                        '(post_type = "post" OR post_type = "event") AND '.
                                        'post_status = "publish"'.
                                        $selection;
                }else{
                    if ($is_term === false){
                        //Search like USER
                        $selector   =   'SELECT '.
                                            '* '.
                                        'FROM '.
                                            'wp_posts '.
                                        'WHERE '.
                                            'post_author = '.$object_id.' AND '.
                                            '(post_type = "post" OR post_type = "event") AND '.
                                            'post_status = "publish"'.
                                            $selection;
                    }else{
                        //Search like term (tag or category)
                        $term_selector =    'SELECT '.
                                                'object_id '.
                                            'FROM '.
                                                'wp_term_relationships '.
                                            'WHERE '.
                                                'term_taxonomy_id IN ( '.
                                                    'SELECT '.
                                                        'term_taxonomy_id '.
                                                    'FROM '.
                                                        'wp_term_taxonomy '.
                                                    'WHERE '.
                                                        'term_id = '.$object_id.')';
                        $selector   =   'SELECT '.
                                            '* '.
                                        'FROM '.
                                            'wp_posts '.
                                        'WHERE '.
                                            'ID IN ('.$term_selector.') AND '.
                                            '(post_type = "post" OR post_type = "event") AND '.
                                            'post_status = "publish"'.
                                            $selection;
                    }
                }
                $posts      = $wpdb->query($selector);
                return ($posts !== false ? (int)$posts : false);
            }
            return false;
        }
        public function get_posts_count_of_post_type($type){
            global $wpdb;
            $posts      = $wpdb->query( 'SELECT '.
                                            '* '.
                                        'FROM '.
                                            'wp_posts '.
                                        'WHERE '.
                                            'post_type = "'.$type.'" AND '.
                                            'post_status = "publish"');
            return ($posts !== false ? (int)$posts : false);
        }
        public function get_posts_counts_by_types($IDs = false, $category_id = false, $tag_id = false){
            if ($IDs !== false || $category_id !== false || $tag_id !== false){
                $tag = false;
                if ($tag_id !== false){
                    $tag = get_terms( 'post_tag', 'include=' . $tag_id );
                    if ($tag !== false){
                        $tag = $tag[0]->name;
                    }
                }
                $posts = get_posts( array(
                    'numberposts'   => 9999,
                    'offset'        => 0,
                    'tag'           => ($tag !== false ? $tag : ''),
                    'category'      => ($category_id !== false ? $category_id : ''),
                    'include'       => ($IDs !== false ? implode(',', $IDs) : ''),
                    'exclude'       => '',
                    'post_type'     =>'any',
                    'post_status'   => 'publish'
                ) );
                if ($posts !== false){
                    $result = (object)array(
                        'post'      =>(object)array(
                            'count' =>0,
                            'IDs'   =>array()
                        ),
                        'event'     =>(object)array(
                            'count' =>0,
                            'IDs'   =>array()
                        ),
                        'report'    =>(object)array(
                            'count' =>0,
                            'IDs'   =>array()
                        ),
                        'question'  =>(object)array(
                            'count' =>0,
                            'IDs'   =>array()
                        )
                    );
                    foreach($posts as $post){
                        $post_type                      = $post->post_type;
                        if (isset($result->$post_type) !== false){
                            $result->$post_type->IDs[]  = $post->ID;
                            $result->$post_type->count ++;
                        }
                    }
                    return $result;
                }
            }
            return false;
        }
        public function get_members_posts_counts_by_types($user_IDs){
            if (is_array($user_IDs) !== false){
                foreach($user_IDs as $key=>$value){
                    if ((int)$value > 0){
                        $user_IDs[$key] = (int)$value;
                    }else{
                        return false;
                    }
                }
                global $wpdb;
                $posts      = $wpdb->get_results(   'SELECT '.
                                                        '* '.
                                                    'FROM '.
                                                        'wp_posts '.
                                                    'WHERE '.
                                                        'post_author IN ('.implode(',', $user_IDs).') AND '.
                                                        'post_status = "publish"');
                if (is_array($posts) !== false){
                    $result = (object)array(
                        'post'      =>(object)array(
                            'count' =>0,
                            'IDs'   =>array()
                        ),
                        'event'     =>(object)array(
                            'count' =>0,
                            'IDs'   =>array()
                        ),
                        'report'    =>(object)array(
                            'count' =>0,
                            'IDs'   =>array()
                        ),
                        'question'  =>(object)array(
                            'count' =>0,
                            'IDs'   =>array()
                        )
                    );
                    foreach($posts as $post){
                        $post_type                      = $post->post_type;
                        if (isset($result->$post_type) !== false){
                            $result->$post_type->IDs[]  = $post->ID;
                            $result->$post_type->count ++;
                        }
                    }
                    return $result;
                }
                return false;
            }
            return false;
        }
        public function get_groups_posts_counts_by_types($groups_IDs){
            if (is_array($groups_IDs) !== false){
                foreach($groups_IDs as $key=>$value){
                    if ((int)$value > 0){
                        $groups_IDs[$key] = (int)$value;
                    }else{
                        return false;
                    }
                }
                $Provider   = \Pure\Providers\Posts\Initialization::instance()->get('group');
                $posts      = $Provider->get(array(
                    'from_date'     =>date("Y-m-d H:i:s"),
                    'days'          =>9999,
                    'thumbnails'    =>false,
                    'targets_array' =>$groups_IDs,
                    'selection'     =>false,
                    'shown'         =>0,
                    'maxcount'      =>1000,
                    'profile'       =>'',
                    'post_type'     =>array('post', 'event', 'report', 'question'),
                    'post_status'   =>'publish',
                ), false);
                if (is_array($posts) !== false){
                    $result = (object)array(
                        'post'      =>(object)array(
                            'count' =>0,
                            'IDs'   =>array()
                        ),
                        'event'     =>(object)array(
                            'count' =>0,
                            'IDs'   =>array()
                        ),
                        'report'    =>(object)array(
                            'count' =>0,
                            'IDs'   =>array()
                        ),
                        'question'  =>(object)array(
                            'count' =>0,
                            'IDs'   =>array()
                        )
                    );
                    foreach($posts as $post){
                        $post_type                      = $post->post_type;
                        if (isset($result->$post_type) !== false){
                            $result->$post_type->IDs[]  = $post->ID;
                            $result->$post_type->count ++;
                        }
                    }
                    return $result;
                }
            }
            return false;
        }
        public function get_posts_IDs_by_category_tag($categories_ids, $tags_ids){
            $IDs        = array();
            if (count($categories_ids) > 0 && count($tags_ids) > 0){
                global $wpdb;
                $selector   =   'SELECT '.
                                    'ID '.
                                'FROM '.
                                    'wp_posts '.
                                'WHERE '.
                                    'ID IN ( '.
                                        'SELECT '.
                                            'object_id '.
                                        'FROM '.
                                            'wp_term_relationships '.
                                        'WHERE '.
                                            'term_taxonomy_id IN ('.implode(',',$categories_ids).') '.
                                    ') '.
                                    'AND '.
                                    'ID IN ( '.
                                        'SELECT '.
                                            'object_id '.
                                        'FROM '.
                                            'wp_term_relationships '.
                                        'WHERE '.
                                            'term_taxonomy_id IN ('.implode(',',$tags_ids).') '.
                                    ') '.
                                'AND post_status = "publish" '.
                                'AND post_type IN ( '.
                                    '"post", '.
                                    '"event", '.
                                    '"report", '.
                                    '"question" '.
                                ')';
                $_IDs       = $wpdb->get_results($selector);
                if (is_array($_IDs) !== false){
                    \Pure\Components\Tools\Arrays\Initialization::instance()->attach();
                    $Tools  = new \Pure\Components\Tools\Arrays\Arrays();
                    $IDs    = $Tools->make_array_by_property_of_array_objects($_IDs, 'ID');
                    $Tools  = NULL;
                }
            }
            return $IDs;
        }
        public function apply_sandbox_to_author($user_id, $into_sandbox = false){
            if ((int)$user_id > 0){
                $SandboxSwitcher = new SandboxSwitcher();
                $SandboxSwitcher->switcher($user_id, $into_sandbox);
                $SandboxSwitcher = NULL;
            }
        }
    }
    class SandboxSelector{
        public function validate(&$parameters){
            if (is_array($parameters) === true){
                $parameters['sandbox'] = (isset($parameters['sandbox']) !== false ? $parameters['sandbox'] : 'exclude_sandbox');
                $parameters['sandbox'] = (in_array($parameters['sandbox'], array('exclude_sandbox', 'all', 'only_sandbox')) !== false ? $parameters['sandbox'] : 'exclude_sandbox');
            }
        }
        public function apply_sandbox_setting($parameters, $posts_selector){
            $result_selector = $posts_selector;
            switch($parameters['sandbox']){
                case 'exclude_sandbox':
                    $result_selector = $this->selector_sandbox($result_selector, true);
                    break;
                case 'all':
                    //Do nothing
                    break;
                case 'only_sandbox':
                    $result_selector = $this->selector_sandbox($result_selector, false);
                    break;
            }
            return $result_selector;
        }
        private function selector_sandbox($posts_selector, $exclude = true){
            $result_selector    = $posts_selector;
            $cache              = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, array('sandbox_id'));
            if (! $sandbox_id = $cache->value){
                \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->settings->mana->properties;
                $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->less($parameters);
                $sandbox_id = (int)$parameters->mana_threshold_manage_categories_sandbox;
                \Pure\Components\Tools\Cache\Cache::set($cache->key, $sandbox_id);
            }
            $category = get_category($sandbox_id);
            if (!is_wp_error( $category )){
                $result_selector =  'SELECT '.
                                        'posts_result.* '.
                                    'FROM '.
                                        '('.$result_selector.') AS posts_result '.
                                    'WHERE '.
                                        'posts_result.ID'.($exclude === false ? ' ' : ' NOT ').'IN '.
                                            '( '.
                                                'SELECT '.
                                                    'object_id '.
                                                'FROM '.
                                                    'wp_term_relationships '.
                                                'WHERE '.
                                                    'term_taxonomy_id = '.(int)$sandbox_id.' '.
                                            ')';
            }
            return $result_selector;
        }

    }
    class SandboxSwitcher{
        public function switcher($user_id, $into_sandbox = false){
            if ((int)$user_id > 0){
                \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->mana->properties;
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                $sandbox_id = $settings->mana_threshold_manage_categories_sandbox;
                if ((int)$sandbox_id > 0){
                    global $wpdb;
                    $selector   =   'SELECT '.
                                        'ID '.
                                    'FROM '.
                                        'wp_posts '.
                                    'WHERE '.
                                        'post_type IN ( '.
                                            '"post", '.
                                            '"event", '.
                                            '"report", '.
                                            '"question" '.
                                        ') '.
                                    'AND post_author = '.(int)$user_id.' '.
                                    'AND ID '.($into_sandbox === false ? '' : 'NOT ').'IN ( '.
                                        'SELECT '.
                                            'object_id '.
                                        'FROM '.
                                            'wp_term_relationships '.
                                        'WHERE '.
                                            'term_taxonomy_id = '.(int)$sandbox_id.' '.
                                    ')';
                    $IDs        = $wpdb->get_results($selector);
                    if (is_array($IDs) !== false){
                        foreach($IDs as $ID){
                            if ($into_sandbox === false){
                                //Detach
                                wp_remove_object_terms(
                                    $ID->ID,
                                    (int)$sandbox_id,
                                    'category'
                                );
                            }else{
                                //Attach
                                wp_set_object_terms(
                                    $ID->ID,
                                    (int)$sandbox_id,
                                    'category',
                                    true
                                );
                            }
                        }
                    }
                }
            }
        }
    }
}
?>