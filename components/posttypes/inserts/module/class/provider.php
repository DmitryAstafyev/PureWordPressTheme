<?php
namespace Pure\Components\PostTypes\Inserts\Module{
    class Provider{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'create':
                    \Pure\Components\WordPress\Post\Visibility\Initialization::instance()->attach(true);
                    $parameters->post_title                         = (string   )($parameters->post_title               );
                    $parameters->post_content                       = (string   )($parameters->post_content             );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'create':
                    $parameters->post_title         = wp_strip_all_tags($parameters->post_title         );
                    return true;
            }
        }
        public function create($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                \Pure\Components\PostTypes\Post\Module\Initialization::instance()->attach();
                $PostProvider   = new \Pure\Components\PostTypes\Post\Module\Core();
                $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                $current        = $WordPress->get_current_user();
                $WordPress      = NULL;
                if ($current !== false){
                    $post_id        = $PostProvider->unsafeAddEmptyDraft($current->ID, 'insert');
                    if ($post_id !== false){
                        $arguments  = array(
                            'comment_status'    => 'open',
                            'post_content'      => $parameters->post_content,
                            'post_excerpt'      => '',
                            'post_title'        => $parameters->post_title,
                            'post_type'         => 'insert',
                            'post_status'       => 'publish',
                            'ID'                => $post_id
                        );
                        //Save post
                        $post_id = wp_update_post($arguments);
                        if ((int)$post_id > 0){
                            return (int)$post_id;
                        }
                    }
                }
                $PostProvider = NULL;
            }
            return false;
        }
    }
}