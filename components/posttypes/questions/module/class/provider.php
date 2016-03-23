<?php
namespace Pure\Components\PostTypes\Questions\Module{
    class Provider{
        private $meta_field = 'pure_theme_field_questions_meta';
        private $cache_key  = 'pure_theme_field_questions_cache_key';
        private $fields     = array(
            'posts'         => 'posts',
            'questions'     => 'questions',
            'answers'       => 'answers',
            'has_answer'    => 'has_answer'
        );
        private function getDefaults(){
            return (object)array(
                'posts'         => array(),//[(object)array('post_id'=>int, 'added_by'=>int)]
                'questions'     => array(),//[(object)array('post_id'=>int, 'added_by'=>int)]
                'answers'       => array(),
                'has_answer'    => false
            );
        }
        public function get($post_id){
            $result = false;
            if ((int)$post_id > 0){
                $cache  = \Pure\Components\Tools\Cache\Cache::get($this->cache_key, array($post_id));
                if (! $result = $cache->value){
                    $result = get_post_meta((int)$post_id, $this->meta_field, true);
                    if ($result !== false && is_object($result) !== false){
                        //Check data integrity
                        foreach($this->fields as $key=>$field){
                            if (isset($result->$key) === false){
                                $result = $this->getDefaults();
                                break;
                            }
                        }
                    }else{
                        $result = $this->getDefaults();
                    }
                    \Pure\Components\Tools\Cache\Cache::set($cache->key, $result);
                }
            }
            return $result;
        }
        public function set($post_id, $fields){
            if ((int)$post_id > 0){
                $cache  = \Pure\Components\Tools\Cache\Cache::get($this->cache_key, array($post_id));
                if (! $report_meta = $cache->value) {
                    $report_meta = $this->get($post_id);
                }
                if ($report_meta !== false){
                    $updated = false;
                    foreach($report_meta as $key=>$field){
                        if (isset($fields->$key) !== false){
                            $report_meta->$key  = $fields->$key;
                            $updated            = true;
                        }
                    }
                    if ($updated !== false){
                        update_post_meta((int)$post_id, $this->meta_field, $report_meta);
                        \Pure\Components\Tools\Cache\Cache::set($cache->key, $report_meta);
                        return true;
                    }
                }
            }
            return false;
        }
        public function setup($post_id){
            if ((int)$post_id > 0){
                $fields = $this->getDefaults();
                update_post_meta((int)$post_id, $this->meta_field, $fields);
                return true;
            }
            return false;
        }
        private function addPost($question_id, $post_id, $user_id, $target){
            if ((int)$question_id > 0 && (int)$post_id > 0 && (int)$user_id > 0){
                $data = $this->get($question_id);
                if ($data !== false){
                    $isExist    = false;
                    $link       = &$data->$target;
                    foreach($link as $post){
                        if ((int)$post->post_id === (int)$post_id){
                            $isExist = true;
                            break;
                        }
                    }
                    if ($isExist === false){
                        $link[] = (object)array(
                            'post_id'   =>(int)$post_id,
                            'added_by'  =>(int)$user_id,
                        );
                        $this->set($question_id, $data);
                        return true;
                    }
                }
            }
            return false;
        }
        private function removePost($question_id, $post_id, $user_id, $target){
            if ((int)$question_id > 0 && (int)$post_id > 0 && (int)$user_id > 0){
                $data = $this->get($question_id);
                if ($data !== false){
                    $related_post   = false;
                    $link           = &$data->$target;
                    foreach($link as $related_key_post=>$post){
                        if ((int)$post->post_id === (int)$post_id){
                            $related_post = $post;
                            break;
                        }
                    }
                    if ($related_post !== false){
                        $author_question = get_post_field( 'post_author', $question_id);
                        if ((int)$author_question > 0){
                            if ((int)$author_question           === (int)$user_id ||
                                (int)$related_post->added_by    === (int)$user_id){
                                if (isset($related_post->wait_for) === false){
                                    if ((int)$author_question === (int)$related_post->added_by){
                                        unset($link[$related_key_post]);
                                        $this->set($question_id, $data);
                                        return 'removed';
                                    }else{
                                        if ((int)$user_id === (int)$author_question){
                                            $link[$related_key_post]->wait_for = (int)$related_post->added_by;
                                        }else{
                                            $link[$related_key_post]->wait_for = (int)$author_question;
                                        }
                                        $this->set($question_id, $data);
                                        return 'wait';
                                    }
                                }else{
                                    if ((int)$related_post->wait_for === (int)$user_id){
                                        unset($link[$related_key_post]);
                                        $this->set($question_id, $data);
                                        return 'removed';
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return false;
        }
        public function whoAddPost($question_id, $post_id, $target){
            if ((int)$question_id > 0 && (int)$post_id > 0){
                $data = $this->get($question_id);
                if ($data !== false) {
                    $link = &$data->$target;
                    foreach ($link as $related_key_post => $post) {
                        if ((int)$post->post_id === (int)$post_id) {
                            return (int)$post->added_by;
                        }
                    }
                }
            }
            return false;
        }
        public function addRelatedPost($question_id, $post_id, $user_id){
            return $this->addPost($question_id, $post_id, $user_id, 'posts');
        }
        public function removeRelatedPost($question_id, $post_id, $user_id){
            return $this->removePost($question_id, $post_id, $user_id, 'posts');
        }
        public function whoAddRelatedPost($question_id, $post_id){
            return $this->whoAddPost($question_id, $post_id, 'posts');
        }
        public function addRelatedQuestion($question_id, $post_id, $user_id){
            return $this->addPost($question_id, $post_id, $user_id, 'questions');
        }
        public function removeRelatedQuestion($question_id, $post_id, $user_id){
            return $this->removePost($question_id, $post_id, $user_id, 'questions');
        }
        public function whoAddRelatedQuestion($question_id, $post_id){
            return $this->whoAddPost($question_id, $post_id, 'questions');
        }
    }
    class Solutions{
        public function hasAnswer($question_id, $question = false){
            $checkAnswer = function($records){
                foreach($records as $record){
                    if (isset($record->is_active) !== false){
                        if ($record->is_active === true){
                            return true;
                        }
                    }
                }
                return false;
            };
            $has_answer = false;
            if ((int)$question_id > 0) {
                if ($question === false){
                    $Questions  = new Provider();
                    $question   = $Questions->get($question_id);
                    $Questions  = NULL;
                }
                if ($question !== false) {
                    $has_answer = ($has_answer === false ? $checkAnswer($question->posts    )               : $has_answer);
                    $has_answer = ($has_answer === false ? $checkAnswer($question->questions)               : $has_answer);
                    $has_answer = ($has_answer === false ? (count($question->answers) > 0 ? true : false)   : $has_answer);
                }
            }
            return $has_answer;
        }
        public function set($question_id, $object_type, $object_id){
            if ((int)$question_id > 0 && (int)$object_id > 0){
                $Questions  = new Provider();
                $question   = $Questions->get($question_id);
                if ($question !== false){
                    switch($object_type){
                        case 'related_post':
                            foreach($question->posts as $key=>$post){
                                if ((int)$object_id === (int)$post->post_id){
                                    $status = (object)array(
                                        'is_item_active'        =>false,
                                        'has_question_answer'   =>false
                                    );
                                    if (isset($post->is_active) === false){
                                        $question->posts[$key]->is_active   = true;
                                        $question->has_answer               = true;
                                        $status->is_item_active             = true;
                                        $status->has_question_answer        = true;
                                    }else{
                                        unset($question->posts[$key]->is_active);
                                        $status->is_item_active             = false;
                                        if ($this->hasAnswer($question_id, $question) === false){
                                            $question->has_answer           = false;
                                            $status->has_question_answer    = false;
                                        }else{
                                            $status->has_question_answer    = true;
                                        }
                                    }
                                    $result     = $Questions->set($question_id, $question);
                                    $Questions  = NULL;
                                    if ($result !== false){
                                        return $status;
                                    }
                                }
                            }
                            break;
                        case 'related_question':
                            break;
                        case 'answer':
                            $Questions  = new Provider();
                            $question   = $Questions->get($question_id);
                            if ($question !== false){
                                if (is_array($question->answers) !== false){
                                    $status = (object)array(
                                        'is_item_active'        =>false,
                                        'has_question_answer'   =>false
                                    );
                                    if (in_array((int)$object_id, $question->answers) !== false){
                                        unset($question->answers[array_search((int)$object_id, $question->answers)]);
                                        $status->is_item_active             = false;
                                        $status->has_question_answer        = $this->hasAnswer($question_id, $question);
                                    }else{
                                        $question->answers[] = (int)$object_id;
                                        $question->has_answer               = true;
                                        $status->is_item_active             = true;
                                        $status->has_question_answer        = true;
                                    }
                                    $result     = $Questions->set($question_id, $question);
                                    $Questions  = NULL;
                                    if ($result !== false){
                                        return $status;
                                    }
                                }
                            }
                            $Questions  = NULL;
                            break;
                    }
                }
                $Questions  = NULL;
            }
            return false;
        }
        public function isAnswerSolution($question_id, $answer_id){
            if ((int)$question_id > 0 && (int)$answer_id > 0){
                $Question = new Provider();
                $question = $Question->get($question_id);
                $Question = NULL;
                if ($question !== false){
                    if (is_array($question->answers) !== false){
                        return (in_array((int)$answer_id, $question->answers) !== false ? true : false);
                    }
                }
            }
            return false;
        }
    }
    class Additions{
        public function get($post_id){
            if ((int)$post_id > 0){
                $additions = get_posts( array(
                    'numberposts'     => -1,
                    'offset'          => 0,
                    'category'        => '',
                    'orderby'         => 'post_date',
                    'order'           => 'ASC',
                    'include'         => '',
                    'exclude'         => '',
                    'meta_key'        => '',
                    'meta_value'      => '',
                    'post_type'       => 'question_addition',
                    'post_mime_type'  => '',
                    'post_parent'     => $post_id,
                    'post_status'     => 'publish'
                ) );
                return (is_array($additions) === true ? $additions : false);
            }
            return false;
        }
    }
    class Create{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'create_from_POST':
                case 'create_not_from_POST':
                    \Pure\Components\WordPress\Post\Visibility\Initialization::instance()->attach(true);
                    $parameters->post_id                            = (integer  )(isset($parameters->post_id) === false ? -1 : $parameters->post_id);
                    $parameters->author_id                          = (integer  )($parameters->author_id                );
                    $parameters->action                             = (string   )($parameters->action                   );
                    $parameters->post_title                         = (string   )($parameters->post_title               );
                    $parameters->post_content                       = (string   )($parameters->post_content             );
                    $parameters->post_visibility                    = (integer  )($parameters->post_visibility          );
                    $parameters->post_association                   = (string   )($parameters->post_association         );
                    $parameters->post_category                      = (integer  )($parameters->post_category            );
                    $parameters->post_allow_comments                = (string   )($parameters->post_allow_comments      );
                    $parameters->post_sandbox                       = ($parameters->post_sandbox            !== false ? (string)    ($parameters->post_sandbox              ) : 'no'        );
                    $parameters->post_association_object            = ($parameters->post_association_object !== false ? (integer)   ($parameters->post_association_object   ) : 0           );
                    //Possible values
                    if (in_array($parameters->action,               array('publish', 'draft', 'preview', 'update'   )               ) === false){ return false; }
                    if (array_search($parameters->post_visibility,  \Pure\Components\WordPress\Post\Visibility\Data::$visibility    ) === false){ return false; }
                    if (array_search($parameters->post_association, \Pure\Components\WordPress\Post\Visibility\Data::$association   ) === false){ return false; }
                    if (in_array($parameters->post_sandbox,         array('yes', 'no'                               )               ) === false){ return false; }
                    if (in_array($parameters->post_allow_comments,  array('open', 'closed'                          )               ) === false){ return false; }
                    foreach($parameters->post_keywords as $key=>$keyword){
                        $parameters->post_keywords[$key] = mb_strtolower((string)$keyword);
                    }
                    return true;
                    break;
                case 'update':
                    $parameters->post_id                    = (integer  )($parameters->post_id                  );
                    $parameters->action                     = (string   )($parameters->action                   );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'create_from_POST':
                    $parameters->post_title         = wp_strip_all_tags($parameters->post_title         );
                    return true;
            }
        }
        private function getSandboxCategoryID(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->settings->mana->properties;
            $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->less($parameters);
            $categoryID = (int)$parameters->mana_threshold_manage_categories_sandbox;
            $parameters = NULL;
            return (int)$categoryID;
        }
        public function create_from_POST($parameters, $update = false){
            $result_object = (object)array(
                'message'   =>false,
                'id'        =>false
            );
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($parameters->post_id !== -1){
                    $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                    $current    = $WordPress->get_current_user();
                    $WordPress  = NULL;
                    if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->author_id) {
                        if (strlen($parameters->post_title) > 3){
                            if (strlen($parameters->post_content) > 0){
                                $arguments  = array(
                                    'comment_status'    => $parameters->post_allow_comments,
                                    'post_author'       => $parameters->author_id,
                                    'post_category'     => ($parameters->post_sandbox === 'yes' ? array($parameters->post_category, $this->getSandboxCategoryID()) : array($parameters->post_category)),
                                    'post_content'      => $parameters->post_content,
                                    'post_excerpt'      => '',
                                    'post_title'        => $parameters->post_title,
                                    'post_type'         => 'question',
                                    'ID'                => $parameters->post_id
                                );
                                if ($update !== false){
                                    if ($parameters->action !== 'update'){
                                        $arguments['post_status'] = ($parameters->action === 'publish' ? 'publish' : 'draft');
                                    }
                                    $post_id                    = wp_update_post($arguments);
                                }else{
                                    $arguments['post_status']   = ($parameters->action === 'publish' ? 'publish' : 'draft');
                                    $post_id                    = wp_update_post($arguments);
                                }
                                if ((int)$post_id > 0){
                                    \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
                                    $Questions = new \Pure\Components\PostTypes\Questions\Module\Provider();
                                    if ($Questions->setup((int)$post_id) !== false){
                                        $Questions = NULL;
                                        //Keywords
                                        \Pure\Components\WordPress\Terms\Module\Initialization::instance()->attach();
                                        $Terms = new \Pure\Components\WordPress\Terms\Module\Provider();
                                        $Terms->update($parameters->post_keywords, 'keyword');
                                        $Terms->attach(
                                            (int)$post_id,
                                            $parameters->post_keywords,
                                            'keyword',
                                            true
                                        );
                                        $Terms = NULL;
                                        //Visibility
                                        $Visibility = new \Pure\Components\WordPress\Post\Visibility\Provider();
                                        $Visibility->set(
                                            (int)$post_id,
                                            (object)array(
                                                'visibility'    =>$parameters->post_visibility,
                                                'association'   =>$parameters->post_association,
                                            ),
                                            (int)$parameters->post_association_object
                                        );
                                        if ($parameters->action === 'publish' || $parameters->action === 'update'){
                                            $result_object->message = 'publish';
                                            $result_object->id      = $post_id;
                                            return $result_object;
                                        }else{
                                            $result_object->message = 'drafted';
                                            $result_object->id      = $current->ID;
                                            return $result_object;
                                        }
                                    }else{
                                        //Error: during saving
                                        $Questions = NULL;
                                        $result_object->message = 'error saving';
                                        return $result_object;
                                    }
                                }else{
                                    //Error: during saving
                                    $result_object->message = 'error saving';
                                    return $result_object;
                                }
                            }else{
                                //Error: no content
                                $result_object->message = 'no content';
                                return $result_object;
                            }
                        }else{
                            //Error: no title
                            $result_object->message = 'no title';
                            return $result_object;
                        }
                    }
                }
            }
            //Error: no access
            $result_object->message = 'no access';
            return $result_object;
        }
        public function create_not_from_POST($parameters, $update = false){
            $result_object = (object)array(
                'message'   =>false,
                'id'        =>false
            );
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false) {
                    \Pure\Components\PostTypes\Post\Module\Initialization::instance()->attach();
                    $PostProvider           = new \Pure\Components\PostTypes\Post\Module\Core();
                    $parameters->post_id    = $PostProvider->unsafeAddEmptyDraft($current->ID, 'question');
                    $PostProvider           = NULL;
                    if ((int)$parameters->post_id > 0){
                        return $this->create_from_POST($parameters, $update);
                    }
                }
            }
            //Error: no access
            $result_object->message = 'no access';
            return $result_object;
        }
        public function update($parameters){
            $result_object = (object)array(
                'message'   =>false,
                'id'        =>false
            );
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $Posts = \Pure\Providers\Posts\Initialization::instance()->getCommon();
                if ($Posts->get($parameters->post_id, true) !== false){
                    if ($parameters->action === 'remove'){
                        $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                        $current    = $WordPress->get_current_user();
                        $WordPress  = NULL;
                        $post       = get_post($parameters->post_id);
                        if ($post !== false && is_null($post) === false){
                            if ((int)($current !== false ? $current->ID : -1) === (int)$post->post_author) {
                                wp_delete_post($parameters->post_id, true);
                                $result_object->message = 'removed';
                                $result_object->id      = $current->ID;
                                return $result_object;
                            }
                        }
                    }else{
                        return $this->create_from_POST($parameters, true);
                    }
                }else{
                    //Error: question was not found
                    $result_object->message = 'no question';
                    return $result_object;
                }
                $Posts = NULL;
            }
            //Error: no access
            $result_object->message = 'no access';
            return $result_object;
        }
    }

}