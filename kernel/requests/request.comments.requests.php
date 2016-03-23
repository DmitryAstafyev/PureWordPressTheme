<?php
namespace Pure\Requests\Comments\Requests{
    class Create{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'create':
                    $parameters->user_id        = (integer  )($parameters->user_id          );
                    $parameters->post_id        = (integer  )($parameters->post_id          );
                    $parameters->comment_id     = (integer  )($parameters->comment_id       );
                    $parameters->attachment_id  = (integer  )($parameters->attachment_id    );
                    $parameters->comment        = (string   )($parameters->comment          );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'create':
                    $parameters->comment = wp_strip_all_tags($parameters->comment);
                    return true;
            }
        }
        public function create($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user(false, false, true);
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id) {
                    if (strlen($parameters->comment) > 1){
                        \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                        $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->comments->properties;
                        $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                        if (strlen($parameters->comment) < (int)$settings->max_length){
                            $PostProvider       = \Pure\Providers\Posts\Initialization::instance()->getCommon();
                            $post               = $PostProvider->get($parameters->post_id, false);
                            $PostProvider       = NULL;
                            if ($post !== false){
                                if ($post->post->comment_status !== 'closed'){
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
                                    $CommentProvider    = \Pure\Providers\Comments\Initialization::instance()->getCommon();
                                    $result = $CommentProvider->create(
                                        $parameters->user_id,
                                        $parameters->post_id,
                                        $parameters->comment_id,
                                        $parameters->comment.$attachmentStr
                                    );
                                    $CommentProvider    = NULL;
                                    if ($result !== false){
                                        \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach(true);
                                        $BuddyPress = new \Pure\Components\BuddyPress\URLs\Core();
                                        \Pure\Components\WordPress\Settings\Initialization::instance()->attach(true);
                                        $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->comments->properties;
                                        $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                                        if($settings->hot_update === 'on'){
                                            WebSocketServer::add(
                                                (int)$parameters->user_id,
                                                (int)$parameters->post_id,
                                                'post_comment',
                                                (object)array(
                                                    'comment_id'=>$result->id,
                                                    'post_id'   =>$parameters->post_id,
                                                    'created'   =>$result->date
                                                )
                                            );
                                        }
                                        $result->name       = $current->name;
                                        $result->avatar     = $current->avatar;
                                        $result->user_id    = $current->ID;
                                        $result->comment    = stripcslashes($result->comment);
                                        $result->home       = $BuddyPress->member($current->user_login);
                                        $BuddyPress         = NULL;
                                        echo json_encode($result);
                                        return true;
                                    }else{
                                        echo 'error during saving';
                                        return false;
                                    }
                                }else{
                                    echo 'closed';
                                    return false;
                                }
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
            echo 'no access';
            return false;
        }
    }
    class More {
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $parameters->post_id    = (integer  )($parameters->post_id      );
                    $parameters->shown      = (integer  )($parameters->shown        );
                    $parameters->all        = (string   )($parameters->all          );
                    return true;
                    break;
                case 'getFromDateTime':
                    $parameters->user_id    = (integer  )($parameters->user_id      );
                    $parameters->post_id    = (integer  )($parameters->post_id      );
                    $parameters->after_date = (string   )($parameters->after_date   );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'getFromDateTime':
                    $parameters->after_date = esc_sql($parameters->after_date);
                    return true;
            }
        }
        public function get($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($parameters->shown >= 0){
                    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                    $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->comments->properties;
                    $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                    $Comments = \Pure\Providers\Comments\Initialization::instance()->get('last_in_posts');
                    $comments = $Comments->get(
                        array(
                            'from_date'     =>date('Y-m-d'),
                            'days'          =>9999,
                            'shown'         =>$parameters->shown,
                            'maxcount'      =>($parameters->all !== 'yes' ? $settings->show_on_page : 9999),
                            'targets_array' =>array($parameters->post_id),
                            'add_post_data' =>false,
                            'add_user_data' =>true,
                            'add_excerpt'   =>false,
                            'add_DB_fields' =>false,
                            'make_tree'     =>true,
                        )
                    );
                    $Comments = NULL;
                    echo json_encode($comments);
                    return true;
                }
            }
            echo 'no access';
            return false;
        }
        public function getFromDateTime($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id) {
                    if (\DateTime::createFromFormat('Y-m-d H:i:s', (string)$parameters->after_date) !== false){
                        $Comments = \Pure\Providers\Comments\Initialization::instance()->get('last_in_posts');
                        $comments = $Comments->getFromDateTime(
                            array(
                                'from_date'     =>date('Y-m-d'),
                                'days'          =>9999,
                                'shown'         =>0,
                                'maxcount'      =>1000,
                                'targets_array' =>array($parameters->post_id),
                                'after_date'    =>(string)$parameters->after_date,
                                'add_post_data' =>false,
                                'add_user_data' =>true,
                                'add_excerpt'   =>false,
                                'add_DB_fields' =>false,
                            )
                        );
                        $Comments = NULL;
                        echo json_encode($comments);
                        return true;
                    }
                }
            }
            echo 'no access';
            return false;
        }
    }
    class Memes{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $parameters->user_id    = (integer  )($parameters->user_id      );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        public function get($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id) {
                    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                    $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->comments->properties;
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
    }
    class WebSocketServer{
        static function add($sender_id, $post_id, $event, $parameters){
            \Pure\Components\WordPress\Location\Module\Initialization::instance()->attach(true);
            $Recorder   = new \Pure\Components\WordPress\Location\Module\Recorder();
            $recipients = $Recorder->getUsersByObject('post', $post_id);
            if ($recipients !== false){
                \Pure\Components\webSocketServer\Events\Initialization::instance()->attach(true);
                $WebSocketServer = new \Pure\Components\webSocketServer\Events\Events();
                foreach($recipients as $recipient){
                    if ((int)$recipient->id !== (int)$sender_id){
                        $WebSocketServer->add((int)$recipient->id, $event, $parameters);
                    }
                }
                $WebSocketServer = NULL;
            }
            $Recorder   = NULL;
        }
    }

}
?>