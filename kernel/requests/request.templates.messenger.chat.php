<?php
namespace Pure\Requests\Templates\Messenger\Chat{
    class Chat{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'getMessages':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->maxcount   = (integer  )($parameters->maxcount );
                    return true;
                case 'getMessagesByThread':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->thread_id  = (integer  )($parameters->thread_id);
                    $parameters->shown      = (integer  )($parameters->shown    );
                    $parameters->maxcount   = (integer  )($parameters->maxcount );
                    return true;
                case 'getMessagesByThreadAfterDate':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->thread_id  = (integer  )($parameters->thread_id);
                    $parameters->date       = (string   )($parameters->date     );
                    return true;
                case 'sendMessage':
                    $parameters->user_id            = (integer  )($parameters->user_id          );
                    $parameters->thread_id          = (integer  )($parameters->thread_id        );
                    $parameters->message            = (string   )($parameters->message          );
                    $parameters->recipients         = (string   )($parameters->recipients       );
                    return true;
                case 'getMemes':
                    $parameters->user_id            = (integer  )($parameters->user_id          );
                    return true;
                case 'getUnreadMessagesCount':
                    $parameters->user_id            = (integer  )($parameters->user_id          );
                    return true;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'sendMessage':
                    $parameters->message = sanitize_text_field($parameters->message);
                    return true;
                case 'getMessagesByThreadAfterDate':
                    $parameters->date = esc_sql($parameters->date);
                    return true;
            }
        }
        public function getMessages($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id){
                    \Pure\Components\Messenger\Chat\Initialization::instance()->attach();
                    $Provider = new \Pure\Components\Messenger\Chat\Provider();
                    $messages = $Provider->getMessages((object)array(
                        'user_id'       =>(int)$parameters->user_id,
                        'maxcount'      =>(int)$parameters->maxcount
                    ));
                    $Provider = NULL;
                    echo json_encode($messages);
                    return true;
                }
            }
            echo 'no access';
            return false;
        }
        public function getMessagesByThread($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id){
                    \Pure\Components\Messenger\Chat\Initialization::instance()->attach();
                    $Provider = new \Pure\Components\Messenger\Chat\Provider();
                    $messages = $Provider->getMessagesByThread((object)array(
                        'user_id'   =>(int)$parameters->user_id,
                        'thread_id' =>(int)$parameters->thread_id,
                        'shown'     =>(int)$parameters->shown,
                        'maxcount'  =>(int)$parameters->maxcount
                    ));
                    $Provider = NULL;
                    echo json_encode($messages);
                    return true;
                }
            }
            echo 'no access';
            return false;
        }
        public function getMessagesByThreadAfterDate($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id){
                    \Pure\Components\Messenger\Chat\Initialization::instance()->attach();
                    $Provider = new \Pure\Components\Messenger\Chat\Provider();
                    $messages = $Provider->getMessageByThreadAfterDate((object)array(
                        'user_id'   =>(int)$parameters->user_id,
                        'thread_id' =>(int)$parameters->thread_id,
                        'date'      =>(string)$parameters->date
                    ));
                    $Provider = NULL;
                    echo json_encode($messages);
                    return true;
                }
            }
            echo 'no access';
            return false;
        }
        public function sendMessage($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id){
                    $recipients = array();
                    if ((int)$parameters->thread_id === -1 || (int)$parameters->thread_id === 0){
                        $_recipients = explode(',', $parameters->recipients);
                        if (is_array($_recipients) !== false) {
                            foreach ($_recipients as $recipient) {
                                $recipients[] = (int)$recipient;
                                if ((int)$recipient === 0) {
                                    echo 'no recipients';
                                    return false;
                                }
                            }
                        }else{
                            echo 'no recipients';
                            return false;
                        }
                    }
                    $message = Decoder::decode($parameters->message);
                    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                    $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->messenger->properties;
                    $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                    if (mb_strlen($message) > $settings->chat_message_max_size){
                        echo 'too big message';
                        return false;
                    }
                    if ($message !== '') {
                        if (strpos($message, '[meme:begin]') !== false && strpos($message, '[meme:end]') !== false){
                            $result = false;
                            preg_match('/\[meme:begin\](.*)\[meme:end\]/i', $message, $matches);
                            if (is_array($matches) !== false){
                                if (count($matches) === 2){
                                    $result = filter_var($matches[1], FILTER_VALIDATE_URL);
                                }
                            }
                            if ($result === false){
                                echo 'incorrect meme url';
                                return false;
                            }
                        }
                        \Pure\Components\Messenger\Chat\Initialization::instance()->attach();
                        $Provider   = new \Pure\Components\Messenger\Chat\Provider();
                        $result     = $Provider->create((object)array(
                            'sender_id' =>(int)$parameters->user_id,
                            'thread_id' =>(int)$parameters->thread_id,
                            'message'   =>$message,
                            'recipients'=>$recipients
                        ));
                        $Provider   = NULL;
                        if ($result === false){
                            echo 'error_during_saving';
                            return false;
                        }
                        WebSocketServer::add(
                            (int)$parameters->user_id,
                            (int)$result->thread_id,
                            'chat_message',
                            (object)array(
                                'message_id'=>$result->message_id,
                                'created'   =>$result->created,
                                'thread_id' =>$result->thread_id
                            )
                        );
                        $result = (object)array(
                            'message_id'=>$result->message_id,
                            'created'   =>date("Y-m-d H:i:s"),
                            'thread_id' =>$result->thread_id
                        );
                        echo json_encode($result);
                        return true;
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
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id){
                    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                    $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->messenger->properties;
                    $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                    if ($settings->chat_allow_memes === 'yes'){
                        $filesSystem    = new \Pure\Resources\FileSystem();
                        $Files          = $filesSystem->getFilesList(\Pure\Configuration::instance()->dir(ABSPATH.'/wp-content/uploads/'.$settings->chat_memes_folder));
                        $filesSystem    = NULL;
                        if (is_null($Files) === false) {
                            $memes = array();
                            \Pure\Components\GlobalSettings\MIMETypes\Initialization::instance()->attach(true);
                            foreach ($Files as $File) {
                                if (strpos(\Pure\Components\GlobalSettings\MIMETypes\Types::$images_ext, substr($File, strlen($File) - 3, 3)) !== false ||
                                    strpos(\Pure\Components\GlobalSettings\MIMETypes\Types::$images_ext, substr($File, strlen($File) - 4, 4)) !== false){
                                    $memes[] = get_site_url().'/wp-content/uploads/'.$settings->chat_memes_folder.'/'.$File;
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
        public function getUnreadMessagesCount($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id) {
                    \Pure\Components\WordPress\LastLogin\Initialization::instance()->attach(true);
                    $LastLogin  = new \Pure\Components\WordPress\LastLogin\Provider();
                    $last_login = $LastLogin->get((int)$parameters->user_id);
                    $LastLogin  = NULL;
                    \Pure\Components\Messenger\Chat\Initialization::instance()->attach();
                    $Provider   = new \Pure\Components\Messenger\Chat\Provider();
                    $result     = $Provider->getCountMessagesFromDate(
                        (int)$parameters->user_id,
                        $last_login
                    );
                    $Provider   = NULL;
                    if ($result !== false){
                        echo json_encode($result);
                        return true;
                    }else{
                        echo 'error';
                        return false;
                    }
                }
            }
            echo 'no access';
            return false;
        }
    }
    class Attachments {
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'sendAttachment':
                    $parameters->user_id            = (integer  )($parameters->user_id          );
                    $parameters->thread_id          = (integer  )($parameters->thread_id        );
                    return true;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        static function get_upload_path($path){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            $rootdir    = 'chat';
            $customdir  = '/'.(int)($current !== false ? $current->ID : -1).'/'.date( 'Y/m' );
            $path['path']    = str_replace( 'uploads' . $path['subdir'], $rootdir.$customdir, $path['path'] );
            $path['url']     = str_replace( 'uploads' . $path['subdir'], $rootdir.$customdir, $path['url']  );
            $path['basedir'] = str_replace( 'uploads', $rootdir, $path['basedir']);
            $path['baseurl'] = str_replace( 'uploads', $rootdir, $path['baseurl']);
            $path['subdir']  = $customdir;
            return $path;
        }
        static function get_file_name($dir, $name, $ext){
            do{
                $file_name = uniqid();
            }while(file_exists(\Pure\Configuration::instance()->dir($dir.'/'.$file_name.$ext)) === true);
            return $file_name.$ext;
        }
        private function isAllowedFormat($type){
            \Pure\Components\GlobalSettings\MIMETypes\Initialization::instance()->attach(true);
            return (strpos(\Pure\Components\GlobalSettings\MIMETypes\Types::$images, $type) !== false ? true : false);
        }
        public function sendAttachment($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id) {
                    if ((int)$parameters->thread_id > 0){
                        \Pure\Components\WordPress\Settings\Initialization::instance()->attach(true);
                        $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->messenger->properties;
                        $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                        if (isset($_FILES['attachment']) === true) {
                            if ((int)$_FILES['attachment']['size'] < (int)$settings->chat_attachment_max_size) {
                                if ($this->isAllowedFormat($_FILES['attachment']['type']) !== false){
                                    if ( !function_exists( 'wp_handle_upload' ) ) { require_once( \Pure\Configuration::instance()->dir(ABSPATH . 'wp-admin/includes/file.php') ); }
                                    add_filter      ( 'upload_dir', array('\Pure\Requests\Templates\Messenger\Chat\Attachments', 'get_upload_path') );
                                    $file = wp_handle_upload(
                                        $_FILES['attachment'],
                                        array(
                                            'test_form'                 => false,
                                            'unique_filename_callback'  => array('\Pure\Requests\Templates\Messenger\Chat\Attachments', 'get_file_name')
                                        )
                                    );
                                    remove_filter   ( 'upload_dir', array('\Pure\Requests\Templates\Messenger\Chat\Attachments', 'get_upload_path') );
                                    if (is_array($file) !== false) {
                                        if (array_key_exists('file', $file) !== false && array_key_exists('url', $file) !== false && array_key_exists('type', $file) !== false) {
                                            \Pure\Components\Messenger\Chat\Initialization::instance()->attach(true);
                                            $Attachments    = new \Pure\Components\Messenger\Chat\Provider();
                                            $result         = $Attachments->attachment((object)array(
                                                'sender_id' =>(int)$parameters->user_id,
                                                'thread_id' =>(int)$parameters->thread_id,
                                                'file'      =>$file['file'],
                                                'type'      =>$file['type']
                                            ));
                                            $Attachments    = NULL;
                                            if ($result !== false){
                                                $result = (object)array(
                                                    'message_id'    =>$result->message_id,
                                                    'created'       =>$result->created,
                                                    'thread_id'     =>$result->thread_id,
                                                    'attachment_id' =>$result->attachment_id
                                                );
                                                WebSocketServer::add(
                                                    (int)$parameters->user_id,
                                                    (int)$result->thread_id,
                                                    'chat_message',
                                                    (object)array(
                                                        'message_id'=>$result->message_id,
                                                        'created'   =>date("Y-m-d H:i:s"),
                                                        'thread_id' =>$result->thread_id
                                                    )
                                                );
                                                echo json_encode($result);
                                                return true;
                                            }else{
                                                echo 'fail to save';
                                                return false;
                                            }
                                        }
                                    }
                                    echo 'file not loaded';
                                    return false;
                                }else{
                                    echo 'wrong format';
                                    return false;
                                }
                            }else{
                                echo 'file is too big';
                                return false;
                            }
                        }else{
                            echo 'no file found';
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
    class WebSocketServer{
        static function add($sender_id, $thread_id, $event, $parameters){
            \Pure\Components\Messenger\Chat\Initialization::instance()->attach();
            $Provider   = new \Pure\Components\Messenger\Chat\Provider();
            $recipients = $Provider->getRecipientsOfThread($thread_id, $sender_id);
            $Provider   = NULL;
            if ($recipients !== false){
                \Pure\Components\webSocketServer\Events\Initialization::instance()->attach(true);
                $WebSocketServer = new \Pure\Components\webSocketServer\Events\Events();
                foreach($recipients as $recipient){
                    $WebSocketServer->add($recipient, $event, $parameters);
                }
                $WebSocketServer = NULL;
            }
        }
    }
}
?>