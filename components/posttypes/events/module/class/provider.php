<?php
namespace Pure\Components\PostTypes\Events\Module{
    class Provider{
        private $fields = array(
            'start'                 => 'pure_theme_field_event_start',
            'finish'                => 'pure_theme_field_event_finish',
            'registration_start'    => 'pure_theme_field_event_registration_start',
            'registration_finish'   => 'pure_theme_field_event_registration_finish',
            'place'                 => 'pure_theme_field_event_place',
            'on_map'                => 'pure_theme_field_event_map',
            'members'               => 'pure_theme_field_event_members',
            'limit'                 => 'pure_theme_field_event_members_limit',
        );
        public function get($post_id){
            $result = false;
            if ((int)$post_id > 0){
                $result = new \stdClass();
                foreach($this->fields as $key=>$field){
                    $result->$key = get_post_meta((int)$post_id, $field, true);
                }
                if (isset($result->members) !== false){
                    $result->members    = @unserialize($result->members);
                    if (is_array($result->members) !== false){
                        $result->count  = count($result->members);
                    }else{
                        $result->members    = array();
                        $result->count      = 0;
                    }
                }else{
                    $result->members    = array();
                    $result->count      = 0;
                }
            }
            //serialize
            return $result;
        }
        public function getMembers($post_id){
            $event = $this->get($post_id);
            if ($event !== false){
                $Provider   = \Pure\Providers\Members\Initialization::instance()->getCommon();
                $members    = array();
                foreach($event->members as $member){
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
        public function addMember($post_id, $user_id){
            $event = $this->get($post_id);
            if ($event !== false){
                if (in_array((int)$user_id, $event->members) === false){
                    $members    = $event->members;
                    $members[]  = (int)$user_id;
                    return $this->set(
                        $post_id,
                        (object)array(
                            'members'=>serialize($members)
                        )
                    );
                }
            }
            return false;
        }
        public function removeMember($post_id, $user_id){
            $event = $this->get($post_id);
            if ($event !== false){
                $index = array_search((int)$user_id, $event->members);
                if ($index !== false){
                    $members    = $event->members;
                    array_splice($members, $index, 1);
                    return $this->set(
                        $post_id,
                        (object)array(
                            'members'=>serialize($members)
                        )
                    );
                }
            }
            return false;
        }
        public function set($post_id, $fields){
            $result = false;
            if ((int)$post_id > 0){
                foreach($this->fields as $key=>$field){
                    if (isset($fields->$key) !== false){
                        update_post_meta((int)$post_id, $field, $fields->$key);
                        $result = true;
                    }
                }
            }
            return $result;
        }
        public function isRegistrationAvailable($event_id){
            $event          = $this->get($event_id);
            $times          = (object)array(
                'current'   =>time(),
                'start'     =>strtotime($event->registration_start),
                'finish'    =>strtotime($event->registration_finish)
            );
            if ($times->current < $times->start || $times->current > $times->finish){
                return false;
            }
            return true;
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
                    $parameters->event_start_day                    = (integer  )($parameters->event_start_day                  );
                    $parameters->event_start_month                  = (integer  )($parameters->event_start_month                );
                    $parameters->event_start_year                   = (integer  )($parameters->event_start_year                 );
                    $parameters->event_start_hour                   = (integer  )($parameters->event_start_hour                 );
                    $parameters->event_start_minute                 = (integer  )($parameters->event_start_minute               );
                    $parameters->event_finish_day                   = (integer  )($parameters->event_finish_day                 );
                    $parameters->event_finish_month                 = (integer  )($parameters->event_finish_month               );
                    $parameters->event_finish_year                  = (integer  )($parameters->event_finish_year                );
                    $parameters->event_finish_hour                  = (integer  )($parameters->event_finish_hour                );
                    $parameters->event_finish_minute                = (integer  )($parameters->event_finish_minute              );
                    $parameters->event_registration_start_day       = (integer  )($parameters->event_registration_start_day     );
                    $parameters->event_registration_start_month     = (integer  )($parameters->event_registration_start_month   );
                    $parameters->event_registration_start_year      = (integer  )($parameters->event_registration_start_year    );
                    $parameters->event_registration_start_hour      = (integer  )($parameters->event_registration_start_hour    );
                    $parameters->event_registration_start_minute    = (integer  )($parameters->event_registration_start_minute  );
                    $parameters->event_registration_finish_day      = (integer  )($parameters->event_registration_finish_day    );
                    $parameters->event_registration_finish_month    = (integer  )($parameters->event_registration_finish_month  );
                    $parameters->event_registration_finish_year     = (integer  )($parameters->event_registration_finish_year   );
                    $parameters->event_registration_finish_hour     = (integer  )($parameters->event_registration_finish_hour   );
                    $parameters->event_registration_finish_minute   = (integer  )($parameters->event_registration_finish_minute );
                    $parameters->event_members_limit                = (integer  )($parameters->event_members_limit              );
                    $parameters->event_on_map                       = (string  )($parameters->event_on_map                      );
                    $parameters->event_place_name                   = ($parameters->event_place_name        !== false ? (string)    ($parameters->event_place_name          ) : ''          );
                    $parameters->post_miniature                     = ($parameters->post_miniature          !== false ? (string)    ($parameters->post_miniature            ) : 'miniature' );
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
                    $parameters->event_on_map       = wp_strip_all_tags($parameters->event_on_map       );
                    $parameters->event_place_name   = wp_strip_all_tags($parameters->event_place_name   );
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
        private function parseDates($parameters){
            //Y-m-d H:i
            $dates = (object)array(
                'start'                 =>$parameters->event_start_year.              '-'.($parameters->event_start_month               < 10 ? '0' : '').$parameters->event_start_month.                 '-'.($parameters->event_start_day              < 10 ? '0' : '').$parameters->event_start_day.               ' '.($parameters->event_start_hour                 < 10 ? '0' : '').$parameters->event_start_hour.              ':'.($parameters->event_start_minute               < 10 ? '0' : '').$parameters->event_start_minute,
                'finish'                =>$parameters->event_finish_year.             '-'.($parameters->event_finish_month              < 10 ? '0' : '').$parameters->event_finish_month.                '-'.($parameters->event_finish_day             < 10 ? '0' : '').$parameters->event_finish_day.              ' '.($parameters->event_finish_hour                < 10 ? '0' : '').$parameters->event_finish_hour.             ':'.($parameters->event_finish_minute              < 10 ? '0' : '').$parameters->event_finish_minute,
                'registration_start'    =>$parameters->event_registration_start_year. '-'.($parameters->event_registration_start_month  < 10 ? '0' : '').$parameters->event_registration_start_month.    '-'.($parameters->event_registration_start_day < 10 ? '0' : '').$parameters->event_registration_start_day.  ' '.($parameters->event_registration_start_hour    < 10 ? '0' : '').$parameters->event_registration_start_hour. ':'.($parameters->event_registration_start_minute  < 10 ? '0' : '').$parameters->event_registration_start_minute,
                'registration_finish'   =>$parameters->event_registration_finish_year.'-'.($parameters->event_registration_finish_month < 10 ? '0' : '').$parameters->event_registration_finish_month.   '-'.($parameters->event_registration_finish_day< 10 ? '0' : '').$parameters->event_registration_finish_day. ' '.($parameters->event_registration_finish_hour   < 10 ? '0' : '').$parameters->event_registration_finish_hour.':'.($parameters->event_registration_finish_minute < 10 ? '0' : '').$parameters->event_registration_finish_minute
            );
            foreach($dates as $date){
                if (\DateTime::createFromFormat('Y-m-d H:i', $date) === false){
                    return false;
                }
            }
            return $dates;
        }
        public function create_from_POST($parameters, $update = false){
            $result_object = (object)array(
                'message'   =>false,
                'id'        =>false
            );
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($parameters->post_id !== -1) {
                    $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                    $current    = $WordPress->get_current_user();
                    $WordPress  = NULL;
                    if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->author_id) {
                        if (strlen($parameters->post_title) > 3){
                            if (strlen($parameters->post_content) > 0){
                                $dates = $this->parseDates($parameters);
                                if ($dates !== false){
                                    $arguments  = array(
                                        'comment_status'    => $parameters->post_allow_comments,
                                        'post_author'       => $parameters->author_id,
                                        'post_category'     => ($parameters->post_sandbox === 'yes' ? array($parameters->post_category, $this->getSandboxCategoryID()) : array($parameters->post_category)),
                                        'post_content'      => $parameters->post_content,
                                        'post_excerpt'      => $parameters->post_excerpt,
                                        'post_title'        => $parameters->post_title,
                                        'post_type'         => 'event',
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
                                        //Events
                                        \Pure\Components\PostTypes\Events\Module\Initialization::instance()->attach(true);
                                        $EventsProvider = new \Pure\Components\PostTypes\Events\Module\Provider();
                                        $EventsProvider->set(
                                            (int)$post_id,
                                            (object)array(
                                                'start'                 => $dates->start,
                                                'finish'                => $dates->finish,
                                                'registration_start'    => $dates->registration_start,
                                                'registration_finish'   => $dates->registration_finish,
                                                'place'                 => $parameters->event_place_name,
                                                'on_map'                => $parameters->event_on_map,
                                                'limit'                 => $parameters->event_members_limit
                                            )
                                        );
                                        $EventsProvider = NULL;
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
                                        //Error: during saving
                                        $result_object->message = 'error during saving';
                                        return $result_object;
                                    }
                                }else{
                                    //Error: during saving
                                    $result_object->message = 'bad date';
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
                    $parameters->post_id    = $PostProvider->unsafeAddEmptyDraft($current->ID, 'event');
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
                    $result_object->message = 'no event';
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