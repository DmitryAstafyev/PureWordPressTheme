<?php
namespace Pure\Components\Messenger\Chat{
    class TablesNames {
        public $messages;
        public $threads;
        public $remove;
        public $read;
        public $attaches;
        static private $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        function __construct(){
            $this->messages = \Pure\DataBase\TablesNames::instance()->messenger->chat->messages;
            $this->threads  = \Pure\DataBase\TablesNames::instance()->messenger->chat->threads;
            $this->attaches = \Pure\DataBase\TablesNames::instance()->messenger->chat->attaches;
        }
    }
    class Provider{
        private function validate(&$parameters, $method){
            $result = false;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'getMessages':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->user_id           ));
                    $result = ($result === false ? false : isset($parameters->maxcount          ));
                    if ($result !== false){
                        $parameters->user_id        = filter_var($parameters->user_id,          FILTER_VALIDATE_INT     );
                        $parameters->maxcount       = filter_var($parameters->maxcount,         FILTER_VALIDATE_INT     );
                        if ($parameters->user_id    === false ||
                            $parameters->maxcount   === false){
                            $result = false;
                        }
                    }
                    break;
                case 'getMessagesByThread':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->user_id           ));
                    $result = ($result === false ? false : isset($parameters->thread_id         ));
                    $result = ($result === false ? false : isset($parameters->shown             ));
                    $result = ($result === false ? false : isset($parameters->maxcount          ));
                    if ($result !== false){
                        $parameters->user_id        = filter_var($parameters->user_id,          FILTER_VALIDATE_INT     );
                        $parameters->thread_id      = filter_var($parameters->thread_id,        FILTER_VALIDATE_INT     );
                        $parameters->shown          = filter_var($parameters->shown,            FILTER_VALIDATE_INT     );
                        $parameters->maxcount       = filter_var($parameters->maxcount,         FILTER_VALIDATE_INT     );
                        if ($parameters->user_id    === false ||
                            $parameters->thread_id  === false ||
                            $parameters->shown      === false ||
                            $parameters->maxcount   === false){
                            $result = false;
                        }
                    }
                    break;
                case 'getMessageByThreadAfterDate':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->user_id           ));
                    $result = ($result === false ? false : isset($parameters->thread_id         ));
                    $result = ($result === false ? false : isset($parameters->date              ));
                    if ($result !== false){
                        $parameters->user_id        = filter_var($parameters->user_id,          FILTER_VALIDATE_INT     );
                        $parameters->thread_id      = filter_var($parameters->thread_id,        FILTER_VALIDATE_INT     );
                        if ($parameters->user_id    === false ||
                            $parameters->thread_id  === false){
                            $result = false;
                        }
                    }
                    break;
                case 'create':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->sender_id     ));
                    $result = ($result === false ? false : isset($parameters->thread_id     ));
                    $result = ($result === false ? false : isset($parameters->message       ));
                    $result = ($result === false ? false : isset($parameters->recipients    ));
                    if ($result !== false){
                        $parameters->sender_id      = filter_var($parameters->sender_id,    FILTER_VALIDATE_INT);
                        $parameters->thread_id      = filter_var($parameters->thread_id,    FILTER_VALIDATE_INT);
                        $parameters->message        = esc_sql((string)$parameters->message );
                        $result                     = ($result !== false ? (gettype($parameters->recipients) === 'array' ? true : false) : false);
                        if ($parameters->sender_id === false ||
                            $parameters->thread_id === false){
                            $result = false;
                        }
                    }
                    break;
                case 'attachment':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->sender_id     ));
                    $result = ($result === false ? false : isset($parameters->thread_id     ));
                    $result = ($result === false ? false : isset($parameters->file          ));
                    $result = ($result === false ? false : isset($parameters->type          ));
                    if ($result !== false){
                        $parameters->sender_id      = filter_var($parameters->sender_id,    FILTER_VALIDATE_INT);
                        $parameters->thread_id      = filter_var($parameters->thread_id,    FILTER_VALIDATE_INT);
                        if ($parameters->sender_id === false ||
                            $parameters->thread_id === false){
                            $result = false;
                        }
                    }
                    break;
            }
            return $result;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'getMessageByThreadAfterDate':
                    $parameters->date = esc_sql($parameters->date);
                    break;
            }
        }
        public function isUserInThread($user_id, $thread_id){
            if ((int)$user_id > 0 && (int)$thread_id > 0){
                global $wpdb;
                $selector =     'SELECT '.
                                    '* '.
                                'FROM '.
                                    TablesNames::instance()->threads.' '.
                                'WHERE '.
                                    'user_id = '.(int)$user_id.' '.
                                'AND thread_id = '.(int)$thread_id;
                $result = $wpdb->query($selector);
                if ($result !== false){
                    return ((int)$result > 0 ? true : false);
                }
            }
            return false;
        }
        private function stripcslashes(&$messages){
            foreach($messages as $key=>$message){
                $messages[$key]->message = stripcslashes($message->message);
            }
            return $messages;
        }
        public function getMessages($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                global $wpdb;
                $selector = 'SELECT '.
                                't_messages.* '.
                            'FROM '.
                                '( '.
                                    'SELECT '.
                                        'thread_id '.
                                    'FROM '.
                                        TablesNames::instance()->threads.' '.
                                    'WHERE '.
                                        'user_id = '.(int)$parameters->user_id.' '.
                                ') AS t_threads, '.
                                TablesNames::instance()->messages.' AS t_messages '.
                            'WHERE '.
                                't_messages.thread_id = t_threads.thread_id '.
                            'AND ( '.
                                't_messages.number_in_thread > 0 '.
                                'AND t_messages.number_in_thread <= '.(int)$parameters->maxcount.' '.
                            ') '.
                            'ORDER BY '.
                                't_messages.thread_id, '.
                                't_messages.created DESC';
                $threads    = $wpdb->get_results($selector);
                if (is_array($threads) !== false){
                    return $this->stripcslashes($threads);
                }
            }
            return false;
        }
        public function getMessagesByThread($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($this->isUserInThread($parameters->user_id, $parameters->thread_id) !== false){
                    global $wpdb;
                    $selector   =   'SELECT '.
                                        '* '.
                                    'FROM '.
                                        TablesNames::instance()->messages.' '.
                                    'WHERE '.
                                        'thread_id = '.$parameters->thread_id.' '.
                                    'ORDER BY '.
                                        'created DESC '.
                                    'LIMIT '.$parameters->shown.', '.$parameters->maxcount;
                    $threads    = $wpdb->get_results($selector);
                    if (is_array($threads) !== false){
                        return $this->stripcslashes($threads);
                    }
                }
            }
            return false;
        }
        public function getMessageByThreadAfterDate($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($this->isUserInThread($parameters->user_id, $parameters->thread_id) !== false &&
                    \DateTime::createFromFormat('Y-m-d H:i:s', (string)$parameters->date) !== false){
                    global $wpdb;
                    $selector   =   'SELECT '.
                                        '* '.
                                    'FROM '.
                                        TablesNames::instance()->messages.' '.
                                    'WHERE '.
                                        'thread_id = '.$parameters->thread_id.' '.
                                    'AND created >= "'.$parameters->date.'" '.
                                    'ORDER BY '.
                                        'created DESC';
                    $threads    = $wpdb->get_results($selector);
                    if (is_array($threads) !== false){
                        return $this->stripcslashes($threads);
                    }
                }
            }
            return false;
        }
        public function getRecipientsOfUser($user_id){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ((int)($current !== false ? $current->ID : -1) === (int)$user_id && (int)$user_id > 0) {
                global $wpdb;
                $selector   =   'SELECT '.
                                    't_threads.user_id AS ID, '.
                                    't_threads.thread_id '.
                                'FROM '.
                                    TablesNames::instance()->threads.' AS t_threads, '.
                                    '( '.
                                        'SELECT '.
                                            'thread_id '.
                                        'FROM '.
                                            TablesNames::instance()->threads.' '.
                                        'WHERE '.
                                            'user_id = '.(int)$user_id.' '.
                                    ') AS t_selected_threads '.
                                'WHERE '.
                                    't_selected_threads.thread_id = t_threads.thread_id '.
                                'AND t_threads.user_id <> '.(int)$user_id;
                $recipients = $wpdb->get_results($selector);
                if (is_array($recipients) !== false){
                    return $recipients;
                }
            }
            return false;
        }
        public function getRecipientsOfThread($thread_id, $ignoreID = false){
            if ((int)$thread_id > 0) {
                global $wpdb;
                $selector   =   'SELECT '.
                                    'user_id AS id '.
                                'FROM '.
                                    TablesNames::instance()->threads.' '.
                                'WHERE '.
                                    'thread_id = '.(int)$thread_id;
                $recipients = $wpdb->get_results($selector);
                if (is_array($recipients) !== false){
                    $_recipients = array();
                    foreach($recipients as $recipient){
                        if ($ignoreID !== false){
                            if ((int)$ignoreID !== (int)$recipient->id){
                                $_recipients[] = $recipient->id;
                            }
                        }else{
                            $_recipients[] = $recipient->id;
                        }
                    }
                    return $_recipients;
                }
            }
            return false;
        }
        private function isThreadExist($thread_id){
            if ((int)$thread_id > 0) {
                global $wpdb;
                $selector   =   'SELECT '.
                                    'user_id AS id '.
                                'FROM '.
                                    TablesNames::instance()->threads.' '.
                                'WHERE '.
                                    'thread_id = '.(int)$thread_id;
                $recipients = $wpdb->query($selector);
                if ($recipients !== false){
                    return ((int)$recipients > 0 ? true : false);
                }
            }
            return false;
        }
        private function getThreadID(){
            global $wpdb;
            $result = $wpdb->get_results(   'SELECT '.
                                                'MAX(thread_id) AS id '.
                                            'FROM '.
                                                TablesNames::instance()->threads
            );
            return (is_array($result) !== false ? ((int)$result[0]->id + 1) : false);
        }
        private function addRecipientToThread($thread_id, $user_id){
            global $wpdb;
            $result = $wpdb->insert(
                TablesNames::instance()->threads,
                array(
                    'thread_id' =>(int)$thread_id,
                    'user_id'   =>(int)$user_id
                ),
                array('%d', '%d')
            );
            return $result;
        }
        private function createThread($_recipients){
            $recipients = array();
            foreach($_recipients as $recipient){
                if (get_userdata($recipient) !== false && in_array($recipient, $recipients) === false){
                    $recipients[] = $recipient;
                }
            }
            if (count($recipients) > 0){
                $thread_id = $this->getThreadID();
                if ($thread_id !== false){
                    foreach($recipients as $recipient){
                        $this->addRecipientToThread($thread_id, $recipient);
                    }
                    return (object)array(
                        'thread_id' =>$thread_id,
                        'recipients'=>$recipients
                    );
                }
            }
            return false;
        }
        public function create($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                global $wpdb;
                $thread_id = (int)$parameters->thread_id;
                if ($thread_id !== -1){
                    $thread_id = ($this->isThreadExist($thread_id) === false ? -1 : $thread_id);
                    if ($thread_id !== -1){
                        if ($this->isUserInThread((int)$parameters->sender_id, (int)$parameters->thread_id) !== false){
                            $recipients = $this->getRecipientsOfThread($thread_id);
                        }else{
                            return false;
                        }
                    }
                }
                if ($thread_id === -1){
                    $recipients                 = $parameters->recipients;
                    $recipients[]               = $parameters->sender_id;
                    $result                     = $this->createThread($recipients);
                    $thread_id                  = ($result !== false ? $result->thread_id   : false);
                    $recipients                 = ($result !== false ? $recipients          : false);
                }
                if ($thread_id !== false && $recipients !== false && $parameters->message !== ''){
                    //insert_id
                    $created    = date("Y-m-d H:i:s");
                    $result     = $wpdb->insert(
                        TablesNames::instance()->messages,
                        array(
                            'message'   =>$parameters->message,
                            'created'   =>$created,
                            'sender_id' =>$parameters->sender_id,
                            'thread_id' =>$thread_id,
                        ),
                        array('%s', '%s', '%d', '%d')
                    );
                    //Update numbers in thread(donuse)
                    $selector   = 'SELECT pure_messenger_chat_update_threads('.(int)$thread_id.')';
                    $_result    = $wpdb->query($selector);
                    if ($result !== false && $_result !== false){
                        $message_id = (int)$wpdb->insert_id;
                        if ($message_id !== 0){
                            return (object)array(
                                'message_id'=>$message_id,
                                'thread_id' =>$thread_id,
                                'created'   =>$created
                            );
                        }
                    }
                }
            }
            return false;
        }
        public function attachment($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                global $wpdb;
                $thread_id = (int)$parameters->thread_id;
                if ($this->isUserInThread((int)$parameters->sender_id, (int)$parameters->thread_id) !== false){
                    $result = $wpdb->insert(
                        TablesNames::instance()->attaches,
                        array(
                            'file'       =>esc_sql((string)$parameters->file),
                            'type'       =>esc_sql((string)$parameters->type)
                        ),
                        array('%s', '%s')
                    );
                    if ($result !== false){
                        $attachment_id  = (int)$wpdb->insert_id;
                        $created        = date("Y-m-d H:i:s");
                        $result         = $wpdb->insert(
                            TablesNames::instance()->messages,
                            array(
                                'message'       =>'',
                                'created'       =>$created,
                                'sender_id'     =>$parameters->sender_id,
                                'thread_id'     =>$thread_id,
                                'attachment_id' =>(int)$attachment_id
                            ),
                            array('%s', '%s', '%d', '%d')
                        );
                        //Update numbers in thread(donuse)
                        $selector   = 'SELECT pure_messenger_chat_update_threads('.(int)$thread_id.')';
                        $_result    = $wpdb->query($selector);
                        if ($result !== false && $_result !== false){
                            $message_id = (int)$wpdb->insert_id;
                            if ($message_id !== 0){
                                return (object)array(
                                    'message_id'    =>$message_id,
                                    'thread_id'     =>$thread_id,
                                    'attachment_id' =>$attachment_id,
                                    'created'       =>$created
                                );
                            }
                        }
                    }
                }
            }
            return false;
        }
        public function getAttachment($_user_id, $attachment_id, $validate = true){
            global $wpdb;
            $user_id = $_user_id;
            if ($validate !== false){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                $user_id    = ($_user_id !== false ? $_user_id : ($current !== false ? $current->ID : -1));
                $allow      = ((int)($current !== false ? $current->ID : -1) === (int)$user_id ? true : false);
            }else{
                $allow      = true;
            }
            if ($allow !== false && $user_id !== -1) {
                if ((int)$attachment_id > 0) {
                    $selector   =   'SELECT '.
                                        '* '.
                                    'FROM '.
                                        TablesNames::instance()->attaches.' '.
                                    'WHERE '.
                                        'id = '.(int)$attachment_id;
                    $attachment = $wpdb->get_results($selector);
                    if (is_array($attachment) !== false) {
                        if (count($attachment) === 1) {
                            $attachment = $attachment[0];
                            $selector   =   'SELECT '.
                                                '* '.
                                            'FROM '.
                                                TablesNames::instance()->messages.' '.
                                            'WHERE '.
                                                'attachment_id = '.(int)$attachment_id;
                            $message    = $wpdb->get_results($selector);
                            if (is_array($message) !== false) {
                                if (count($message) === 1) {
                                    $message = $message[0];
                                    if ($this->isUserInThread((int)$user_id, $message->thread_id) !== false){
                                        return $attachment;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return false;
        }
        public function getCountMessagesFromDate($user_id, $date){
            if ((int)$user_id > 0 && \DateTime::createFromFormat('Y-m-d H:i:s', (string)$date) !== false) {
                global $wpdb;
                $selector   =   'SELECT '.
                                    'COUNT(*) AS count, '.
                                    'thread_id '.
                                'FROM '.
                                    TablesNames::instance()->messages.' '.
                                'WHERE '.
                                    'thread_id IN ( '.
                                        'SELECT '.
                                            'thread_id '.
                                        'FROM '.
                                            TablesNames::instance()->threads.' '.
                                        'WHERE '.
                                            'user_id = '.(int)$user_id.' '.
                                    ') '.
                                    'AND created >= "'.(string)$date.'" '.
                                'GROUP BY '.
                                    'thread_id;';
                $threads = $wpdb->get_results($selector);
                return (is_array($threads) !== false ? $threads : false);
            }
            return false;
        }
    }
}
?>