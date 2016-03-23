<?php
namespace Pure\Requests\Templates\Messenger{
    class Mails{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'getInboxMessages':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->shown      = (integer  )($parameters->shown    );
                    $parameters->maxcount   = (integer  )($parameters->maxcount );
                    return true;
                case 'getInboxByThreadAfterDate':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->thread_id  = (integer  )($parameters->thread_id);
                    $parameters->date       = (string   )($parameters->date     );
                    return true;
                case 'getMessagesOfThread':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->thread_id  = (integer  )($parameters->thread_id);
                    return true;
                case 'getOutboxMessages':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->shown      = (integer  )($parameters->shown    );
                    $parameters->maxcount   = (integer  )($parameters->maxcount );
                    return true;
                case 'getEditor':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    return true;
                case 'sendMessage':
                    $parameters->user_id            = (integer  )($parameters->user_id          );
                    $parameters->message_id         = (integer  )($parameters->message_id       );
                    $parameters->message            = (string   )($parameters->message          );
                    $parameters->subject            = (string   )($parameters->subject          );
                    $parameters->recipients         = (string   )($parameters->recipients       );
                    $parameters->attachments_key    = (string   )($parameters->attachments_key  );
                    return true;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'getInboxByThreadAfterDate':
                    $parameters->date = esc_sql($parameters->date);
                    return true;
            }
        }
        public function getInboxMessages($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id){
                    \Pure\Components\Messenger\Mails\Initialization::instance()->attach();
                    $Provider = new \Pure\Components\Messenger\Mails\Provider();
                    $messages = $Provider->getInbox((object)array(
                        'user_id'       =>(int)$parameters->user_id,
                        'shown'         =>(int)$parameters->shown,
                        'maxcount'      =>(int)$parameters->maxcount,
                        'as_threads'    =>true,
                        'add_users_data'=>true
                    ));
                    $Provider = NULL;
                    echo json_encode($messages);
                    return true;
                }
            }
            echo 'no access';
            return false;
        }
        public function getInboxByThreadAfterDate($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id){
                    \Pure\Components\Messenger\Mails\Initialization::instance()->attach();
                    $Provider = new \Pure\Components\Messenger\Mails\Provider();
                    $messages = $Provider->getInboxOfThreadAfterDate((object)array(
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
        public function getMessagesOfThread($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id){
                    \Pure\Components\Messenger\Mails\Initialization::instance()->attach();
                    $Provider = new \Pure\Components\Messenger\Mails\Provider();
                    $messages = $Provider->getMessagesOfThread((object)array(
                        'user_id'   =>(int)$parameters->user_id,
                        'thread_id' =>(int)$parameters->thread_id
                    ));
                    $Provider = NULL;
                    echo json_encode($messages);
                    return true;
                }
            }
            echo 'no access';
            return false;
        }
        public function getOutboxMessages($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id){
                    \Pure\Components\Messenger\Mails\Initialization::instance()->attach();
                    $Provider = new \Pure\Components\Messenger\Mails\Provider();
                    $messages = $Provider->getOutbox((object)array(
                        'user_id'       =>(int)$parameters->user_id,
                        'shown'         =>(int)$parameters->shown,
                        'maxcount'      =>(int)$parameters->maxcount,
                        'add_threads'   =>false,
                        'add_users_data'=>true
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
                    $recipients     = array();
                    $_recipients    = explode(',', $parameters->recipients);
                    if (is_array($_recipients) !== false){
                        foreach($_recipients as $recipient){
                            $recipients[] = (int)$recipient;
                            if ((int)$recipient === 0){
                                echo 'no access';
                                return false;
                            }
                        }
                        \Pure\Components\Tools\HTMLStrings\Initialization::instance()->attach();
                        $message = Decoder::decode($parameters->message);
                        $subject = Decoder::decode($parameters->subject);
                        \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                        $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->messenger->properties;
                        $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                        if (mb_strlen($message) > $settings->mail_max_size){
                            echo 'too big message';
                            return false;
                        }
                        if (mb_strlen($subject) > $settings->mail_subject_max_size){
                            echo 'too big subject';
                            return false;
                        }
                        if ($message !== '' && $subject !== ''){
                            $HTMLParser = new \Pure\Components\Tools\HTMLStrings\HTMLParser();
                            $message    = $HTMLParser->remove_tags_from_string(
                                $message,
                                array('a', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'strong', 'b', 'i', 'em', 'ul', 'ol', 'li')
                            );
                            $subject    = $HTMLParser->remove_tags_from_string($subject);
                            if ($message !== false && $message !== ''){
                                $message = $HTMLParser->remove_attributes_except(
                                    $message,
                                    array('href', 'target'),
                                    true
                                );
                            }
                            $HTMLParser = NULL;
                            if ($message !== false && $message !== '' && $subject !== false && $subject !== '' && $parameters->message_id !== 0){
                                $result = false;
                                \Pure\Components\Messenger\Mails\Initialization::instance()->attach();
                                $Provider = new \Pure\Components\Messenger\Mails\Provider();
                                if ($parameters->message_id !== -1){
                                    //Message in thread(donuse)
                                    $thread_id  = $Provider->getThreadIDByMessageID((int)$parameters->message_id);
                                    if ($thread_id !== false){
                                        $result = $Provider->create((object)array(
                                            'sender_id'  => (int)$parameters->user_id,
                                            'thread_id'  => $thread_id,
                                            'recipients' => $recipients,
                                            'subject'    => $subject,
                                            'message'    => $message
                                        ));
                                    }else{
                                        echo 'cannot find thread_id';
                                        return false;
                                    }
                                }else{
                                    //New message
                                    $result = $Provider->create((object)array(
                                        'sender_id'  => (int)$parameters->user_id,
                                        'thread_id'  => -1,
                                        'recipients' => $recipients,
                                        'subject'    => $subject,
                                        'message'    => $message
                                    ));
                                }
                                if ($result !== false && (int)$result->message_id > 0){
                                    if ($parameters->attachments_key !== ''){
                                        //Accept attachments if it is
                                        $Attachments = new \Pure\Components\Messenger\Mails\Attachments();
                                        $Attachments->accept(
                                            (int)$parameters->user_id,
                                            (string)$parameters->attachments_key,
                                            (int)$result->message_id
                                        );
                                        $Attachments = NULL;
                                    }
                                    $message    = $Provider->getMessageByID((int)$result->message_id, (int)$parameters->user_id);
                                    $Provider   = NULL;
                                    WebSocketServer::add(
                                        (int)$parameters->user_id,
                                        (int)$message->thread_id,
                                        'mail_message',
                                        (object)array(
                                            'message_id'=>$message->message_id,
                                            'created'   =>$result->created,
                                            'thread_id' =>$message->thread_id
                                        )
                                    );
                                    echo json_encode($message);
                                    return true;
                                }else{
                                    echo 'fail during sending';
                                    $Provider = NULL;
                                    return false;
                                }
                                $Provider = NULL;
                            }else{
                                echo 'bad message or subject';
                                return false;
                            }
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
    class Attachments {
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'preload':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->key        = (string   )($parameters->key      );
                    return true;
                case 'remove':
                    $parameters->user_id        = (integer)($parameters->user_id        );
                    $parameters->attachment_id  = (integer)($parameters->attachment_id  );
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
            $rootdir    = 'messenger';
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
        public function preload($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id) {
                    \Pure\Components\WordPress\Settings\Initialization::instance()->attach(true);
                    $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->messenger->properties;
                    $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                    if ($settings->allow_attachment_in_mail === 'on'){
                        \Pure\Components\Messenger\Mails\Initialization::instance()->attach(true);
                        $Attaches   = new \Pure\Components\Messenger\Mails\Attachments();
                        $count      = $Attaches->countWithKey($parameters->key);
                        if ((int)$count < (int)$settings->attachment_max_count){
                            if (isset($_FILES['attachment']) === true) {
                                if ((int)$_FILES['attachment']['size'] < (int)$settings->attachment_max_size) {
                                    if ( !function_exists( 'wp_handle_upload' ) ) { require_once( \Pure\Configuration::instance()->dir(ABSPATH . 'wp-admin/includes/file.php') ); }
                                    add_filter      ( 'upload_dir', array('\Pure\Requests\Templates\Messenger\Attachments', 'get_upload_path') );
                                    $file = wp_handle_upload(
                                        $_FILES['attachment'],
                                        array(
                                            'test_form'                 => false,
                                            'unique_filename_callback'  => array('\Pure\Requests\Templates\Messenger\Attachments', 'get_file_name')
                                        )
                                    );
                                    remove_filter   ( 'upload_dir', array('\Pure\Requests\Templates\Messenger\Attachments', 'get_upload_path') );
                                    if (is_array($file) !== false){
                                        if (array_key_exists('file', $file) !== false && array_key_exists('url', $file) !== false && array_key_exists('type', $file) !== false){
                                            $result     = $Attaches->add(
                                                $parameters->user_id,
                                                $parameters->key,
                                                $file['file'],
                                                $file['type'],
                                                $_FILES['attachment']['name']
                                            );
                                            if ($result !== false){
                                                echo $result;
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
                                    echo 'too big file';
                                    return false;
                                }
                            }else{
                                echo 'file not found';
                                return false;
                            }
                        }else{
                            echo 'too many attachments';
                            return false;
                        }
                    }else{
                        echo 'attachments are not allowed';
                        return false;
                    }
                }
            }
            echo 'no access';
            return false;
        }
        public function accept($user_id, $_key, $message_id){
            \Pure\Components\Messenger\Mails\Initialization::instance()->attach(true);
            $Attaches   = new \Pure\Components\Messenger\Mails\Attachments();
            $result     = $Attaches->accept((int)$user_id, (string)$_key, (int)$message_id);
            $Attaches   = NULL;
            return $result;
        }
        public function remove($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id) {
                    \Pure\Components\Messenger\Mails\Initialization::instance()->attach(true);
                    $Attaches   = new \Pure\Components\Messenger\Mails\Attachments();
                    $result     = $Attaches->remove(
                        (int)$parameters->user_id,
                        (int)$parameters->attachment_id
                    );
                    $Attaches   = NULL;
                    if ($result !== false){
                        echo 'success';
                        return true;
                    }else{
                        echo 'fail';
                        return false;
                    }
                }
            }
            echo 'no access';
            return false;
        }
    }
    class StatusRead{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'update':
                    $parameters->user_id            = (integer  )($parameters->user_id          );
                    $parameters->message_id         = (integer  )($parameters->message_id       );
                    return true;
                case 'getCount':
                    $parameters->user_id            = (integer  )($parameters->user_id          );
                    return true;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        public function update($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id){
                    \Pure\Components\Messenger\Mails\Initialization::instance()->attach();
                    $Provider   = new \Pure\Components\Messenger\Mails\StatusRead();
                    $result     = $Provider->update($parameters->message_id, $parameters->user_id);
                    $Provider   = NULL;
                    echo ($result !== false ? 'success' : 'fail');
                    return true;
                }
            }
            echo 'no access';
            return false;
        }
        public function getCount($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id){
                    \Pure\Components\Messenger\Mails\Initialization::instance()->attach();
                    $Provider   = new \Pure\Components\Messenger\Mails\StatusRead();
                    $result     = $Provider->getUnreadCount($parameters->user_id);
                    $Provider   = NULL;
                    echo ($result !== false ? json_encode($result) : 'fail');
                    return true;
                }
            }
            echo 'no access';
            return false;
        }
    }
    class WebSocketServer{
        static function add($sender_id, $thread_id, $event, $parameters){
            \Pure\Components\Messenger\Chat\Initialization::instance()->attach();
            $Provider   = new \Pure\Components\Messenger\Mails\Provider();
            $recipients = $Provider->getRecipientsByThreadID($thread_id, $sender_id);
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