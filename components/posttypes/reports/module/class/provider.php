<?php
namespace Pure\Components\PostTypes\Reports\Module{
    class Provider{
        private $meta_field         = 'pure_theme_field_reports_meta';
        private $meta_field_on_map  = 'pure_theme_field_reports_meta_on_map';
        private $meta_field_place   = 'pure_theme_field_reports_meta_place';
        private $cache_key          = 'pure_theme_field_reports_cache_key';
        private $fields             = array(
            'indexes'       => 'indexes',
            'votes'         => 'votes',
            'history'       => 'history',
            'min'           => 'min',
            'max'           => 'max',
            'author_votes'  => 'author_votes',
            'members'       => 'members',
        );
        private function getDefaults(){
            return (object)array(
                'indexes'       =>array(),
                'votes'         =>array(),
                'history'       =>array(),
                'min'           =>array(),
                'max'           =>array(),
                'author_votes'  =>array(),
                'members'       =>array(),
                'members_count' =>0
            );
        }
        public function getAvailableCollections(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $properties     = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->reports->properties;
            $collections    = @unserialize(base64_decode($properties->collections->value));
            return (is_array($collections) !== false ? $collections : false);
        }
        public function updateCollection($data){
            if (isset($data->names      ) !== false && //Array with collection names                        : names[collection]
                isset($data->indexes    ) !== false && //Array with names of indexes of each collection     : indexes[collection][index]
                isset($data->maximums   ) !== false){  //Array with maximums of indexes of each collection  : maximums[collection][index]
                //Check structure
                foreach($data->names as $collection_key=>$collection_name){
                    if (isset($data->indexes[$collection_key]) === false || isset($data->maximums[$collection_key]) === false){
                        return false;
                    }
                    if (count($data->indexes[$collection_key]) !== count($data->maximums[$collection_key])){
                        return false;
                    }
                }
                //Update
                $collections = array();
                foreach($data->names as $collection_key=>$collection_name){
                    $indexes = array();
                    foreach($data->indexes[$collection_key] as $index_key=>$index_name){
                        $indexes[] = (object)array(
                            'name'  =>$index_name,
                            'max'   =>(int)$data->maximums[$collection_key][$index_key]
                        );
                        $indexes[count($indexes) - 1]->max = ($indexes[count($indexes) - 1]->max > 10   ? 10    : $indexes[count($indexes) - 1]->max);
                        $indexes[count($indexes) - 1]->max = ($indexes[count($indexes) - 1]->max < 2    ? 2     : $indexes[count($indexes) - 1]->max);
                    }
                    $collections[] = (object)array(
                        'name'      =>$collection_name,
                        'indexes'   =>$indexes
                    );
                }
                //Save
                $collections                    = @serialize($collections);
                $collections                    = base64_encode($collections);
                $Settings                       = new \Pure\Components\WordPress\Settings\Settings();
                $Settings->try_save_by_name('reports', 'collections', $collections);
                $Settings                       = NULL;
                \Pure\Components\WordPress\Settings\Instance::instance()->reload();
                return true;
            }
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
                        //Update count of votes
                        $result->members_count = count($result->members);
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
        public function setup($post_id, $collection){
            if ((int)$post_id > 0 && (int)$collection >= 0){
                $collections = $this->getAvailableCollections();
                if (isset($collections[$collection]) !== false){
                    $fields = $this->getDefaults();
                    foreach($collections[$collection]->indexes as $key=>$index){
                        $fields->indexes[]  = $index->name;
                        $fields->max[]      = $index->max;
                        $fields->min[]      = 1;
                        $fields->votes[]    = 0;
                    }
                    update_post_meta((int)$post_id, $this->meta_field, $fields);
                    return true;
                }
            }
            return false;
        }
        public function getPlace($post_id){
            $on_map = get_post_meta((int)$post_id, $this->meta_field_on_map,    true);
            $place  = get_post_meta((int)$post_id, $this->meta_field_place,     true);
            if ($on_map !== false){
                return (object)array(
                    'on_map'=>$on_map,
                    'place' =>($place !== false ? $place : ''),
                );
            }
            return (object)array(
                'on_map'=>'',
                'place' =>''
            );
        }
        public function setPlace($post_id, $on_map, $place){
            update_post_meta((int)$post_id, $this->meta_field_on_map,   $on_map );
            update_post_meta((int)$post_id, $this->meta_field_place,    $place  );
        }
        public function getMembers($post_id){
            $report = $this->get($post_id);
            if ($report !== false){
                $Provider   = \Pure\Providers\Members\Initialization::instance()->getCommon();
                $members    = array();
                foreach($report->members as $member){
                    $user   = $Provider->get($member, 'name_avatar_id');
                    if ($user !== null){
                        $members[] = $user;
                    }
                }
                $Provider   = NULL;
                return $members;
            }
            return false;
        }
        public function addVote($post_id, $index, $value, $user_id){
            $report = $this->get($post_id);
            if ($report !== false){
                //Check index
                if (isset($report->indexes[(int)$index]) !== false){
                    //Check value
                    if ((int)$value <= (int)$report->max[(int)$index] && (int)$value >= (int)$report->min[(int)$index]){
                        //Fields to update
                        $fields = new \stdClass();
                        //Add member if necessary
                        if (in_array((int)$user_id, $report->members) === false){
                            $members            = $report->members;
                            $members[]          = (int)$user_id;
                            $fields->members    = $members;
                        }
                        //Check vote of user
                        if (isset($report->history[(int)$index]) === false){
                            $report->history[(int)$index] = array();
                        }
                        if (isset($report->history[(int)$index][(int)$user_id]) === false){
                            $report->history[(int)$index][(int)$user_id] = $value;
                            //Calculate new value
                            $report->votes[(int)$index] = array_sum($report->history[(int)$index]) / count($report->history[(int)$index]);
                            //Update data
                            $fields->history            = $report->history;
                            $fields->votes              = $report->votes;
                            $post_author                = get_post_field( 'post_author', $post_id );
                            if ((int)$post_author === (int)$user_id){
                                $report->author_votes[(int)$index] = $value;
                                $fields->author_votes              = $report->author_votes;
                            }
                            $this->set($post_id, $fields);
                            return $report->votes[(int)$index];
                        }else{
                            //User voted before
                            return false;
                        }
                    }
                }
            }
            return false;
        }
        public function isUserVoted($post_id, $index, $user_id){
            $report = $this->get($post_id);
            if ($report !== false){
                //Check index
                if (isset($report->indexes[(int)$index]) !== false){
                    //Check user vote
                    if (in_array((int)$user_id, $report->members) !== false){
                        if (isset($report->history[(int)$index]) !== false){
                            if (isset($report->history[(int)$index][(int)$user_id]) !== false){
                                return true;
                            }
                        }
                    }
                }
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
                    $parameters->post_excerpt                       = (string   )($parameters->post_excerpt             );
                    $parameters->post_visibility                    = (integer  )($parameters->post_visibility          );
                    $parameters->post_association                   = (string   )($parameters->post_association         );
                    $parameters->post_category                      = (integer  )($parameters->post_category            );
                    $parameters->post_allow_comments                = (string   )($parameters->post_allow_comments      );
                    $parameters->report_collection                  = (integer  )($parameters->report_collection        );
                    $parameters->post_miniature                     = ($parameters->post_miniature          !== false ? (string)    ($parameters->post_miniature            ) : 'miniature' );
                    $parameters->report_on_map                      = ($parameters->report_on_map           !== false ? (string)    ($parameters->report_on_map             ) : ''          );
                    $parameters->report_place_name                  = ($parameters->report_place_name       !== false ? (string)    ($parameters->report_place_name         ) : ''          );
                    $parameters->post_sandbox                       = ($parameters->post_sandbox            !== false ? (string)    ($parameters->post_sandbox              ) : 'no'        );
                    $parameters->post_association_object            = ($parameters->post_association_object !== false ? (integer)   ($parameters->post_association_object   ) : 0           );
                    //Possible values
                    if (in_array($parameters->action,               array('publish', 'draft', 'preview', 'update'   )               ) === false){ return false; }
                    if (array_search($parameters->post_visibility,  \Pure\Components\WordPress\Post\Visibility\Data::$visibility    ) === false){ return false; }
                    if (array_search($parameters->post_association, \Pure\Components\WordPress\Post\Visibility\Data::$association   ) === false){ return false; }
                    if (in_array($parameters->post_sandbox,         array('yes', 'no'                               )               ) === false){ return false; }
                    if (in_array($parameters->post_allow_comments,  array('open', 'closed'                          )               ) === false){ return false; }
                    if (isset($parameters->post_tags) !== false){
                        if (is_array($parameters->post_tags) !== false){
                            foreach($parameters->post_tags as $key=>$tag){
                                $parameters->post_tags[$key] = mb_strtolower((string)$tag);
                            }
                        }else{
                            $parameters->post_tags = array();
                        }
                    }else{
                        $parameters->post_tags = array();
                    }
                    if (isset($parameters->post_warnings) !== false){
                        if (is_array($parameters->post_warnings) !== false){
                            foreach($parameters->post_warnings as $key=>$tag){
                                $parameters->post_warnings[$key] = mb_strtolower((string)$tag);
                            }
                        }else{
                            $parameters->post_warnings = array();
                        }
                    }else{
                        $parameters->post_warnings = array();
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
                    $parameters->post_excerpt       = wp_strip_all_tags($parameters->post_excerpt       );
                    $parameters->report_on_map      = wp_strip_all_tags($parameters->report_on_map      );
                    $parameters->report_place_name  = wp_strip_all_tags($parameters->report_place_name  );
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
                                    'post_excerpt'      => $parameters->post_excerpt,
                                    'post_title'        => $parameters->post_title,
                                    'post_type'         => 'report',
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
                                    $report_collection_status = false;
                                    if ($parameters->report_collection >= 0){
                                        //Define collection
                                        \Pure\Components\PostTypes\Reports\Module\Initialization::instance()->attach();
                                        $Reports                    = new \Pure\Components\PostTypes\Reports\Module\Provider();
                                        $report_collection_status   = $Reports->setup($post_id, $parameters->report_collection);
                                        $Reports                    = NULL;
                                    }else{
                                        //Do not define collection, just check
                                        $report_collection_status   = true;
                                    }
                                    if ($report_collection_status !== false){
                                        //Tags
                                        \Pure\Components\WordPress\Terms\Module\Initialization::instance()->attach();
                                        $Terms = new \Pure\Components\WordPress\Terms\Module\Provider();
                                        $Terms->update($parameters->post_tags, 'post_tag');
                                        $Terms->attach(
                                            (int)$post_id,
                                            $parameters->post_tags,
                                            'post_tag',
                                            true
                                        );
                                        $Terms->attach(
                                            (int)$post_id,
                                            $parameters->post_warnings,
                                            'warning_mark',
                                            true
                                        );
                                        $Terms = NULL;
                                        //Map
                                        \Pure\Components\PostTypes\Reports\Module\Initialization::instance()->attach();
                                        $Reports = new \Pure\Components\PostTypes\Reports\Module\Provider();
                                        if ($parameters->report_on_map !== ''){
                                            $Reports->setPlace(
                                                $post_id,
                                                $parameters->report_on_map,
                                                $parameters->report_place_name
                                            );
                                        }else{
                                            $Reports->setPlace(
                                                $post_id,
                                                '',
                                                ''
                                            );
                                        }
                                        $Reports = NULL;
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
                                        //Miniature
                                        if ($parameters->post_miniature === 'no miniature'){
                                            delete_post_thumbnail($post_id);
                                        }else if($parameters->post_miniature === 'miniature'){
                                            if (isset($_FILES['post_miniature']) !== false){
                                                require_once( \Pure\Configuration::instance()->dir(ABSPATH.'wp-admin/includes/image.php')   );
                                                require_once( \Pure\Configuration::instance()->dir(ABSPATH.'wp-admin/includes/file.php')    );
                                                require_once( \Pure\Configuration::instance()->dir(ABSPATH.'wp-admin/includes/media.php')   );
                                                $attachment_id = media_handle_upload( 'post_miniature', $post_id );
                                                if (is_wp_error( $attachment_id ) === false) {
                                                    if (set_post_thumbnail($post_id, $attachment_id) === false){
                                                        //Error: with setting attachment
                                                        $result_object->message = 'thumbnail error';
                                                        return $result_object;
                                                    }
                                                } else {
                                                    //Error: with loading attachment
                                                    $result_object->message = 'thumbnail error';
                                                    return $result_object;
                                                }
                                            }
                                        }else if((int)$parameters->post_miniature > 0){
                                            if (set_post_thumbnail($post_id, (int)$parameters->post_miniature) === false){
                                                //Error: with setting attachment
                                                $result_object->message = 'thumbnail error';
                                                return $result_object;
                                            }
                                        }
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
                                        //Error: with indexes
                                        $result_object->message = 'bad indexes';
                                        return $result_object;
                                    }
                                }else{
                                    //Error: no access
                                    $result_object->message = 'bad data';
                                    return $result_object;
                                }
                            }else{
                                //Error: no content
                                $result_object->message = 'no content';
                                return $result_object;
                            }
                        }else{
                            //Error: no title
                            $result_object->message = ' no title';
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
                    $parameters->post_id    = $PostProvider->unsafeAddEmptyDraft($current->ID, 'report');
                    $PostProvider           = NULL;
                    if ((int)$parameters->post_id > 0){
                        if ($parameters->post_miniature !== 'no miniature'){
                            if (file_exists(\Pure\Configuration::instance()->dir($parameters->post_miniature)) !== false){
                                \Pure\Components\PostTypes\Post\Module\Initialization::instance()->attach();
                                $PostProvider   = new \Pure\Components\PostTypes\Post\Module\Core();
                                $attachment_id  = $PostProvider->unsafeAddAttachment(
                                    0,
                                    (object)array(
                                        'name'=>pathinfo($parameters->post_miniature)['basename'],
                                        'full'=>$parameters->post_miniature
                                    )
                                );
                                $PostProvider = NULL;
                                if ((int)$attachment_id > 0){
                                    $parameters->post_miniature = $attachment_id;
                                }else{
                                    $parameters->post_miniature = 'no miniature';
                                }
                            }else{
                                $parameters->post_miniature = 'no miniature';
                            }
                        }
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
                    //Error: post was not found
                    $result_object->message = 'no report';
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