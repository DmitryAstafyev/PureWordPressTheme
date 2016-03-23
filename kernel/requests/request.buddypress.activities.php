<?php
namespace Pure\Requests\BuddyPress\Activities{
    class Core{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $parameters->object_type    = (string   )($parameters->object_type  );
                    $parameters->object_id      = (integer  )($parameters->object_id    );
                    $parameters->all            = (string   )($parameters->all          );
                    $parameters->shown          = (integer  )($parameters->shown        );
                    if (in_array($parameters->object_type, array('activity', 'groups')) === false){
                        return false;
                    }
                    return true;
                    break;
                case 'sendComment':
                    $parameters->user_id        = (integer  )($parameters->user_id      );
                    $parameters->activity_id    = (integer  )($parameters->activity_id  );
                    $parameters->root_id        = (integer  )($parameters->root_id      );
                    $parameters->comment        = (string   )($parameters->comment      );
                    $parameters->attachment_id  = (integer  )($parameters->attachment_id);
                    return true;
                    break;
                case 'sendPost':
                    $parameters->user_id        = (integer  )($parameters->user_id      );
                    $parameters->object_id      = (integer  )($parameters->object_id    );
                    $parameters->object_type    = (string   )($parameters->object_type  );
                    $parameters->post           = (string   )($parameters->post         );
                    $parameters->attachment_id  = (integer  )($parameters->attachment_id);
                    if (in_array($parameters->object_type, array('activity', 'groups')) === false){
                        return false;
                    }
                    return true;
                    break;
                case 'getMemes':
                    $parameters->user_id        = (integer  )($parameters->user_id      );
                    return true;
                    break;
                case 'remove':
                    $parameters->user_id        = (integer  )($parameters->user_id      );
                    $parameters->activity_id    = (integer  )($parameters->activity_id  );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'sendComment':
                    $parameters->comment = wp_strip_all_tags($parameters->comment);
                    return true;
            }
        }
        public function get($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($parameters->shown >= 0 && $parameters->object_id > 0) {
                    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                    $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->activities->properties;
                    $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                    $Activities = \Pure\Providers\Activities\Initialization::instance()->get(
                        ($parameters->object_type === 'activity' ? 'of_user' : 'of_group')
                    );
                    $activities = $Activities->get(
                        array(
                            'shown'         =>$parameters->shown,
                            'maxcount'      =>($parameters->all === 'no' ? $settings->show_on_page : 500),
                            'targets_array' =>array($parameters->object_id)
                        )
                    );
                    $Activities = NULL;
                    echo json_encode($activities);
                    return true;
                }
            }
            echo 'fail';
            return false;
        }
        public function sendComment($parameters) {
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user(false, false, true);
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id) {
                    if ($parameters->activity_id > 0 && $parameters->root_id >= 0){
                        if (strlen($parameters->comment) > 1){
                            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                            $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->activities->properties;
                            $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                            if (strlen($parameters->comment) < (int)$settings->max_length){
                                $attachmentStr = '';
                                if ($parameters->attachment_id > 0){
                                    $attachment = get_post($parameters->attachment_id);
                                    if ($attachment !== false && is_null($attachment) === false){
                                        if ((int)$attachment->post_author === (int)$parameters->user_id){
                                            $attachment = wp_get_attachment_image_src( $parameters->attachment_id, 'medium');
                                            if (is_array($attachment) !== false){
                                                $attachmentStr = '[attachment:begin]'.$attachment[0].'[attachment:end]';
                                            }
                                        }
                                    }
                                }
                                $recorded_time  = bp_core_current_time();
                                $activity_id    = bp_activity_add( array(
                                    'content'           =>$parameters->comment.$attachmentStr,
                                    'component'         =>'activity',
                                    'type'              =>'activity_comment',
                                    'user_id'           =>$parameters->user_id,
                                    'item_id'           =>($parameters->root_id === 0 ? $parameters->activity_id : $parameters->root_id),
                                    'secondary_item_id' =>$parameters->activity_id,
                                    'recorded_time'     =>$recorded_time
                                ));
                                if ((int)$activity_id > 0){
                                    \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach(true);
                                    $BuddyPress     = new \Pure\Components\BuddyPress\URLs\Core();
                                    $result         = (object)array(
                                        'id'        =>$activity_id,
                                        'action'    =>'',
                                        'content'   =>$parameters->comment.$attachmentStr,
                                        'parent'    =>$parameters->activity_id,
                                        'root'      =>($parameters->root_id === 0 ? $parameters->activity_id : $parameters->root_id),
                                        'date'      =>$recorded_time,
                                        'user_id'   =>$parameters->user_id,
                                        'name'      =>$current->name,
                                        'avatar'    =>$current->avatar,
                                        'posts'     =>get_author_posts_url((int)$parameters->user_id),
                                        'home'      =>$BuddyPress->member($current->user_login)
                                    );
                                    $BuddyPress     = NULL;
                                    echo json_encode($result);
                                    return true;
                                }else{
                                    echo 'error during saving';
                                    return false;
                                }
                            }else{
                                echo 'big comment';
                                return false;
                            }
                        }else{
                            echo 'short comment';
                            return false;
                        }
                    }
                }
            }
            echo 'no access';
            return false;
        }
        public function sendPost($parameters) {
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user(false, false, true);
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id) {
                    if (($parameters->object_type === 'groups' && $parameters->object_id > 0) || $parameters->object_type !== 'groups'){
                        if (strlen($parameters->post) > 1) {
                            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->activities->properties;
                            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                            $post       = Decoder::decode($parameters->post);
                            if (strlen($post) < (int)$settings->max_length && strlen($post) > 1) {
                                \Pure\Components\Tools\HTMLStrings\Initialization::instance()->attach();
                                $HTMLParser = new \Pure\Components\Tools\HTMLStrings\HTMLParser();
                                $post       = $HTMLParser->remove_tags_from_string(
                                    $post,
                                    array('a', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'strong', 'b', 'i', 'em', 'ul', 'ol', 'li')
                                );
                                if ($post !== false && $post !== ''){
                                    $post = $HTMLParser->remove_attributes_except(
                                        $post,
                                        array('href', 'target', 'style'),
                                        true
                                    );
                                }
                                $HTMLParser = NULL;
                                if ($post !== false && $post !== '') {
                                    $attachmentStr = '';
                                    if ($parameters->attachment_id > 0){
                                        $attachment = get_post($parameters->attachment_id);
                                        if ($attachment !== false && is_null($attachment) === false){
                                            if ((int)$attachment->post_author === (int)$parameters->user_id){
                                                $attachment = wp_get_attachment_image_src( $parameters->attachment_id, 'medium');
                                                if (is_array($attachment) !== false){
                                                    $attachmentStr = '[attachment:begin]'.$attachment[0].'[attachment:end]';
                                                }
                                            }
                                        }
                                    }
                                    $recorded_time  = bp_core_current_time();
                                    add_filter('bp_activity_allowed_tags', array('\Pure\Requests\BuddyPress\Activities\BuddyPressFilters', 'post'));
                                    $activity_id    = bp_activity_add( array(
                                        'content'           =>$post.$attachmentStr,
                                        'component'         =>$parameters->object_type,
                                        'type'              =>'activity_update',
                                        'user_id'           =>$parameters->user_id,
                                        'item_id'           =>($parameters->object_type === 'groups' ? $parameters->object_id : 0),
                                        'secondary_item_id' =>0,
                                        'recorded_time'     =>$recorded_time
                                    ));
                                    remove_filter('bp_activity_allowed_tags', array('\Pure\Requests\BuddyPress\Activities\BuddyPressFilters', 'post'));
                                    if ((int)$activity_id > 0){
                                        \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach(true);
                                        $BuddyPress     = new \Pure\Components\BuddyPress\URLs\Core();
                                        $result         = (object)array(
                                            'id'        =>$activity_id,
                                            'action'    =>'',
                                            'content'   =>$post.$attachmentStr,
                                            'parent'    =>0,
                                            'root'      =>0,
                                            'date'      =>$recorded_time,
                                            'user_id'   =>$parameters->user_id,
                                            'name'      =>$current->name,
                                            'avatar'    =>$current->avatar,
                                            'posts'     =>get_author_posts_url((int)$parameters->user_id),
                                            'home'      =>$BuddyPress->member($current->user_login)
                                        );
                                        $BuddyPress     = NULL;
                                        echo json_encode($result);
                                        return true;
                                    }else{
                                        echo 'error during saving';
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            echo 'no access';
            return false;
        }
        public function getMemes($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id) {
                    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                    $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->activities->properties;
                    $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                    if ($settings->allow_memes === 'on'){
                        $filesSystem    = new \Pure\Resources\FileSystem();
                        $Files          = $filesSystem->getFilesList(\Pure\Configuration::instance()->dir(ABSPATH.'/wp-content/uploads/'.$settings->memes_folder));
                        $filesSystem    = NULL;
                        if (is_null($Files) === false) {
                            $memes = array();
                            \Pure\Components\GlobalSettings\MIMETypes\Initialization::instance()->attach(true);
                            foreach ($Files as $File) {
                                if (strpos(\Pure\Components\GlobalSettings\MIMETypes\Types::$images_ext, substr($File, strlen($File) - 3, 3)) !== false ||
                                    strpos(\Pure\Components\GlobalSettings\MIMETypes\Types::$images_ext, substr($File, strlen($File) - 4, 4)) !== false){
                                    $memes[] = get_site_url().'/wp-content/uploads/'.$settings->memes_folder.'/'.$File;
                                }
                            }
                            echo json_encode($memes);
                            return true;
                        }else{
                            echo 'no memes folder';
                            return false;
                        }
                    }else{
                        echo 'memes are not allowed';
                        return false;
                    }
                }
            }
            echo 'no access';
            return false;
        }
        public function remove($parameters) {
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id) {
                    if ((int)$parameters->activity_id > 0) {
                        $Provider = \Pure\Providers\Activities\Initialization::instance()->getCommon();
                        $activity = $Provider->getItemByID((int)$parameters->activity_id);
                        if ($activity !== false){
                            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                            $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->activities->properties;
                            $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                            if ($activity->type === 'activity_update'){
                                //Activity
                                if ((int)$current->ID === (int)$activity->user_id && $settings->allow_remove_activities === 'yes'){
                                    if (bp_activity_delete(array("id"=>(int)$activity->id)) !== false){
                                        echo 'success';
                                        return true;
                                    }else{
                                        echo 'fail';
                                        return false;
                                    }
                                }else{
                                    echo 'no permit to remove';
                                    return false;
                                }
                            }else if($activity->type === 'activity_comment'){
                                //Comment
                                $root_parent = $Provider->getItemByID((int)$activity->item_id);
                                if ($root_parent !== false){
                                    if ((int)$current->ID === (int)$root_parent->user_id && $settings->allow_remove_comments === 'yes'){
                                        if ( bp_activity_delete_comment((int)$root_parent->id, (int)$activity->id) !== false){
                                            echo 'success';
                                            return true;
                                        }else{
                                            echo 'fail';
                                            return false;
                                        }
                                    }else{
                                        echo 'no permit to remove';
                                        return false;
                                    }
                                }else{
                                    echo 'cannot find root';
                                    return false;
                                }
                            }
                            echo 'cannot remove';
                            return false;
                        }
                    }
                }
            }
            echo 'no access';
            return false;
        }
    }
    class Decoder{
        static function decode($text){
            $result = preg_replace('/\s/', '+', stripcslashes($text));
            $result = base64_decode($result);
            $result = preg_replace('/\r\n/',   '', $result);
            $result = preg_replace('/\n/',     '', $result);
            $result = preg_replace('/\t/',     '', $result);
            return $result;
        }
    }
    class BuddyPressFilters{
        static function post($activity_allowedtags){
            $activity_allowedtags['p'] = array();
            $activity_allowedtags['p']['style'] = array();
            $activity_allowedtags['h1'] = array();
            $activity_allowedtags['h1']['style'] = array();
            $activity_allowedtags['h2'] = array();
            $activity_allowedtags['h2']['style'] = array();
            $activity_allowedtags['h3'] = array();
            $activity_allowedtags['h3']['style'] = array();
            $activity_allowedtags['h4'] = array();
            $activity_allowedtags['h4']['style'] = array();
            $activity_allowedtags['h5'] = array();
            $activity_allowedtags['h5']['style'] = array();
            $activity_allowedtags['strong'] = array();
            $activity_allowedtags['b'] = array();
            $activity_allowedtags['i'] = array();
            $activity_allowedtags['em'] = array();
            $activity_allowedtags['a'] = array();
            $activity_allowedtags['a']['style'] = array();
            $activity_allowedtags['a']['title'] = array();
            $activity_allowedtags['a']['href'] = array();
            $activity_allowedtags['ol'] = array();
            $activity_allowedtags['ol']['style'] = array();
            $activity_allowedtags['ul'] = array();
            $activity_allowedtags['ul']['style'] = array();
            $activity_allowedtags['li'] = array();
            $activity_allowedtags['li']['style'] = array();
            return $activity_allowedtags;
        }
    }
}
?>