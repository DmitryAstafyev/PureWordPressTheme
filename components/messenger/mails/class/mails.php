<?php
namespace Pure\Components\Messenger\Mails{
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
            $this->messages = \Pure\DataBase\TablesNames::instance()->messenger->mails->messages;
            $this->threads  = \Pure\DataBase\TablesNames::instance()->messenger->mails->threads;
            $this->remove   = \Pure\DataBase\TablesNames::instance()->messenger->mails->remove;
            $this->read     = \Pure\DataBase\TablesNames::instance()->messenger->mails->read;
            $this->attaches = \Pure\DataBase\TablesNames::instance()->messenger->mails->attaches;
        }
    }
    class Provider{
        private $userDataCache = array();
        private function validate(&$parameters, $method){
            $result = false;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'getInbox':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->user_id           ));
                    $result = ($result === false ? false : isset($parameters->shown             ));
                    $result = ($result === false ? false : isset($parameters->maxcount          ));
                    $result = ($result === false ? false : isset($parameters->as_threads        ));
                    $result = ($result === false ? false : isset($parameters->add_users_data    ));
                    if ($result !== false){
                        $parameters->user_id        = filter_var($parameters->user_id,          FILTER_VALIDATE_INT     );
                        $parameters->shown          = filter_var($parameters->shown,            FILTER_VALIDATE_INT     );
                        $parameters->maxcount       = filter_var($parameters->maxcount,         FILTER_VALIDATE_INT     );
                        $parameters->as_threads     = filter_var($parameters->as_threads,       FILTER_VALIDATE_BOOLEAN );
                        $parameters->add_users_data = filter_var($parameters->add_users_data,   FILTER_VALIDATE_BOOLEAN );
                        if ($parameters->user_id    === false ||
                            $parameters->shown      === false ||
                            $parameters->maxcount   === false){
                            $result = false;
                        }
                    }
                    break;
                case 'getInboxOfThreadAfterDate':
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
                case 'getMessagesOfThread':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->user_id           ));
                    $result = ($result === false ? false : isset($parameters->thread_id         ));
                    if ($result !== false){
                        $parameters->user_id        = filter_var($parameters->user_id,          FILTER_VALIDATE_INT     );
                        $parameters->thread_id      = filter_var($parameters->thread_id,        FILTER_VALIDATE_INT     );
                        if ($parameters->user_id    === false ||
                            $parameters->thread_id  === false){
                            $result = false;
                        }
                    }
                    break;
                case 'getOutbox':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->user_id           ));
                    $result = ($result === false ? false : isset($parameters->shown             ));
                    $result = ($result === false ? false : isset($parameters->maxcount          ));
                    $result = ($result === false ? false : isset($parameters->add_threads       ));
                    $result = ($result === false ? false : isset($parameters->add_users_data    ));
                    if ($result !== false){
                        $parameters->user_id        = filter_var($parameters->user_id,          FILTER_VALIDATE_INT     );
                        $parameters->shown          = filter_var($parameters->shown,            FILTER_VALIDATE_INT     );
                        $parameters->maxcount       = filter_var($parameters->maxcount,         FILTER_VALIDATE_INT     );
                        $parameters->add_threads    = filter_var($parameters->add_threads,      FILTER_VALIDATE_BOOLEAN );
                        $parameters->add_users_data = filter_var($parameters->add_users_data,   FILTER_VALIDATE_BOOLEAN );
                        if ($parameters->user_id    === false ||
                            $parameters->shown      === false ||
                            $parameters->maxcount   === false){
                            $result = false;
                        }
                    }
                    break;
                case 'create':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->sender_id     ));
                    $result = ($result === false ? false : isset($parameters->thread_id     ));
                    $result = ($result === false ? false : isset($parameters->subject       ));
                    $result = ($result === false ? false : isset($parameters->message       ));
                    $result = ($result === false ? false : isset($parameters->recipients    ));
                    if ($result !== false){
                        $parameters->sender_id      = filter_var($parameters->sender_id,    FILTER_VALIDATE_INT);
                        $parameters->thread_id      = filter_var($parameters->thread_id,    FILTER_VALIDATE_INT);
                        $parameters->subject        = esc_sql((string)$parameters->subject  );
                        $parameters->message        = esc_sql((string)$parameters->message );
                        $result                     = ($result !== false ? (gettype($parameters->recipients) === 'array' ? true : false) : false);
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
                case 'getInboxOfThreadAfterDate':
                    $parameters->date = esc_sql($parameters->date);
                    break;
            }
        }
        private function prepareMessage(&$messages){
            if (is_array($messages) !== false){
                foreach($messages as $key=>$message){
                    $messages[$key]->message = stripcslashes($message->message);
                    $messages[$key]->subject = stripcslashes($message->subject);
                }
            }else{
                $messages->message = stripcslashes($messages->message);
                $messages->subject = stripcslashes($messages->subject);
            }
            return $messages;
        }
        private function getAttachments(&$messages, $user_id){
            $Attachments = new Attachments();
            if (is_array($messages) !== false){
                foreach($messages as $key=>$message){
                    $messages[$key]->attachments = $Attachments->getFromMessage($user_id, $message->message_id);
                }
            }else{
                $messages->attachments = $Attachments->getFromMessage($user_id, $messages->message_id);
            }
            $Attachments = NULL;
            return $messages;
        }
        private function getInboxAsList($parameters){
            global $wpdb;
            $selector = 'SELECT '.
                            'temp_messages.*, temp_counts.messages_in_thread '.
                        'FROM '.
                            '( '.
                                'SELECT '.
                                    'thread_id, '.
                                    'COUNT(*) AS messages_in_thread '.
                                'FROM '.
                                    TablesNames::instance()->messages.' '.
                                'GROUP BY '.
                                    'thread_id '.
                            ') AS temp_counts, '.
                            '( '.
                                'SELECT '.
                                    't_messages.*, t_read.is_unread '.
                                'FROM '.
                                    TablesNames::instance()->messages.' t_messages, '.
                                    TablesNames::instance()->read.' t_read, '.
                                    TablesNames::instance()->remove.' t_remove '.
                                'WHERE '.
                                    't_messages.thread_id IN ( '.
                                        'SELECT '.
                                            'thread_id '.
                                        'FROM '.
                                            TablesNames::instance()->threads.' '.
                                        'WHERE '.
                                            'user_id = '.$parameters->user_id.' '.
                                    ') '.
                                'AND t_messages.sender_id <> '.$parameters->user_id.' '.
                                'AND t_messages.id = t_read.message_id '.
                                'AND t_messages.sender_id = t_read.user_id '.
                                'AND t_messages.id = t_remove.message_id '.
                                'AND t_messages.sender_id = t_remove.user_id '.
                                'AND t_remove.is_removed = 0 '.
                                'ORDER BY '.
                                    't_messages.created DESC '.
                            ') AS temp_messages '.
                        'WHERE '.
                            'temp_counts.thread_id = temp_messages.thread_id '.
                        'ORDER BY '.
                            'temp_messages.created DESC';
            $count      = $wpdb->query      (   $selector);
            $messages   = $wpdb->get_results(   $selector.
                                                ' LIMIT '.$parameters->shown.','.$parameters->maxcount  );
            return (object)array(
                'messages'  =>$this->getAttachments($messages, $parameters->user_id),
                'shown'     =>count($messages),
                'total'     =>$count
            );
        }
        private function getInboxAsThreads($parameters){
            global $wpdb;
            $selector = 'SELECT '.
                            '* '.
                        'FROM '.
                            '( '.
                                'SELECT '.
                                    'temp_messages.*, temp_counts.messages_in_thread '.
                                'FROM '.
                                    '( '.
                                        'SELECT '.
                                            'thread_id, '.
                                            'COUNT(*) AS messages_in_thread '.
                                        'FROM '.
                                            TablesNames::instance()->messages.' '.
                                        'GROUP BY '.
                                            'thread_id '.
                                    ') AS temp_counts, '.
                                    '( '.
                                        'SELECT '.
                                            't_messages.*, t_read.is_unread '.
                                        'FROM '.
                                            TablesNames::instance()->messages.' t_messages, '.
                                            TablesNames::instance()->read.' t_read, '.
                                            TablesNames::instance()->remove.' t_remove '.
                                        'WHERE '.
                                            't_messages.thread_id IN ( '.
                                                'SELECT '.
                                                    'thread_id '.
                                                'FROM '.
                                                    TablesNames::instance()->threads.' '.
                                                'WHERE '.
                                                    'user_id = '.$parameters->user_id.' '.
                                            ') '.
                                        'AND t_messages.sender_id <> '.$parameters->user_id.' '.
                                        'AND t_messages.id = t_read.message_id '.
                                        'AND t_read.user_id = '.$parameters->user_id.' '.
                                        'AND t_messages.id = t_remove.message_id '.
                                        'AND t_messages.sender_id = t_remove.user_id '.
                                        'AND t_remove.is_removed = 0 '.
                                        'ORDER BY '.
                                            't_messages.created DESC '.
                                    ') AS temp_messages '.
                                'WHERE '.
                                    'temp_counts.thread_id = temp_messages.thread_id '.
                            ') AS messages '.
                        'GROUP BY '.
                            'messages.thread_id '.
                        'ORDER BY '.
                            'messages.created DESC';
            $count      = $wpdb->query      (   $selector);
            $messages   = $wpdb->get_results(   $selector.
                                                ' LIMIT '.$parameters->shown.','.$parameters->maxcount  );
            if (is_array($messages) === true){
                $messages = $this->fillInboxThreads($messages, $parameters->user_id, true, $parameters->add_users_data);
                return (object)array(
                    'messages'  =>$this->getAttachments($messages, $parameters->user_id),
                    'shown'     =>count($messages),
                    'total'     =>$count
                );
            }
            return false;
        }
        private function getMessagesInThread($thread_id, $user_id, $add_users_data){
            global $wpdb;
            $selector = 'SELECT '.
                            't_messages.id AS message_id, '.
                            't_messages.message, '.
                            't_messages.subject, '.
                            't_messages.created, '.
                            't_messages.sender_id, '.
                            't_messages.thread_id, '.
                            't_read.is_unread '.
                        'FROM '.
                            TablesNames::instance()->messages.' t_messages, '.
                            '( '.
                                'SELECT '.
                                    '* '.
                                'FROM '.
                                    TablesNames::instance()->remove.' '.
                                'WHERE '.
                                    'user_id = '.$user_id.' '.
                            ') t_remove, '.
                            '( '.
                                'SELECT '.
                                    '* '.
                                'FROM '.
                                    TablesNames::instance()->read.' '.
                                'WHERE '.
                                    'user_id = '.$user_id.' '.
                            ') t_read '.
                        'WHERE '.
                            't_messages.thread_id = '.$thread_id.' '.
                            'AND t_messages.id = t_read.message_id '.
                            'AND t_messages.id = t_remove.message_id '.
                            'AND t_remove.is_removed = 0 '.
                        'ORDER BY '.
                            't_messages.created DESC';
            $messages = $wpdb->get_results($selector);
            if ($add_users_data === true){
                foreach($messages as $key=>$message){
                    $messages[$key]->sender = $this->getUserData($message->sender_id);
                }
            }
            return (is_array($messages) === true ? $this->getAttachments($messages, $user_id) : false);
        }
        private function fillInboxThreads($_messages, $user_id, $sort_messages = true, $add_users_data = false){
            $cache      = array();
            $messages   = array();
            foreach($_messages as $_message){
                $message = (object)array(
                    'message_id'=>$_message->id,
                    'message'   =>$_message->message,
                    'subject'   =>$_message->subject,
                    'created'   =>$_message->created,
                    'sender_id' =>$_message->sender_id,
                    'thread_id' =>$_message->thread_id,
                    'is_unread' =>$_message->is_unread,
                    'nested'    =>false
                );
                if ((int)$_message->messages_in_thread > 1){
                    if (isset($cache[$_message->thread_id]) === false){
                        $messages_in_thread             = $this->getMessagesInThread($_message->thread_id, $user_id, $add_users_data);
                        $cache[$_message->thread_id]    = $messages_in_thread;
                    }else{
                        $messages_in_thread             = $cache[$_message->thread_id];
                    }
                    if (count($messages_in_thread) > 0){
                        if ($sort_messages !== false){
                            $index = (count($messages_in_thread) - 1);
                            if ((int)$messages_in_thread[$index]->message_id !== (int)$message->message_id){
                                $message = (object)array(
                                    'message_id'=>$messages_in_thread[$index]->message_id,
                                    'message'   =>$messages_in_thread[$index]->message,
                                    'subject'   =>$messages_in_thread[$index]->subject,
                                    'created'   =>$messages_in_thread[$index]->created,
                                    'sender_id' =>$messages_in_thread[$index]->sender_id,
                                    'thread_id' =>$messages_in_thread[$index]->thread_id,
                                    'is_unread' =>$messages_in_thread[$index]->is_unread,
                                    'nested'    =>false
                                );
                            }
                            array_splice($messages_in_thread, $index, 1);
                        }
                        $message->nested = $this->prepareMessage($messages_in_thread);
                    }
                }
                $messages[] = $this->prepareMessage($message);
            }
            $cache = NULL;
            return $this->getAttachments($messages, $user_id);
        }
        private function getOutboxAsList($parameters){
            global $wpdb;
            $selector   =   'SELECT '.
                                't_messages.id AS message_id, '.
                                't_messages.message, '.
                                't_messages.subject, '.
                                't_messages.created, '.
                                't_messages.sender_id, '.
                                't_messages.thread_id, '.
                                't_read.is_unread '.
                            'FROM '.
                                TablesNames::instance()->messages.' t_messages, '.
                                TablesNames::instance()->read.' t_read, '.
                                TablesNames::instance()->remove.' t_remove '.
                            'WHERE '.
                                'sender_id = '.$parameters->user_id.' '.
                                'AND t_messages.id = t_read.message_id '.
                                'AND t_messages.sender_id = t_read.user_id '.
                                'AND t_messages.id = t_remove.message_id '.
                                'AND t_messages.sender_id = t_remove.user_id '.
                                'AND t_remove.is_removed = 0 '.
                            'ORDER BY t_messages.created DESC';
            $count      = $wpdb->query      (   $selector);
            $messages   = $wpdb->get_results(   $selector.
                                                ' LIMIT '.$parameters->shown.','.$parameters->maxcount  );
            $messages = $this->prepareMessage($messages);
            return (object)array(
                'messages'  =>$this->getAttachments($messages, $parameters->user_id),
                'shown'     =>count($messages),
                'total'     =>$count
            );
        }
        private function getOutboxAsThreads($parameters){
            $messages = $this->getInboxAsList($parameters);
            $messages = $this->fillInboxThreads($messages, $parameters->user_id, false, $parameters->add_users_data);
            return $messages;
        }
        private function getRecipientsFromThread($thread_id, $user_id, $add_users_data = false){
            global $wpdb;
            $selector = 'SELECT '.
                            'user_id AS id '.
                        'FROM '.
                            TablesNames::instance()->threads.' '.
                        'WHERE '.
                            'thread_id = '.$thread_id.' '.
                        'AND user_id <> '.$user_id.' ';
            $recipients = $wpdb->get_results($selector);
            if ($add_users_data === true){
                foreach($recipients as $key=>$recipient){
                    $recipients[$key]       = $this->getUserData($recipient->id);
                    $recipients[$key]->id   = $recipient->id;
                }
            }
            return (is_array($recipients) === true ? $recipients : false);
        }
        private function getUserData($user_id){
            if (isset($this->userDataCache[$user_id]) === false){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $this->userDataCache[$user_id] = (object)array(
                    'id'    =>$user_id,
                    'name'  =>$WordPress->get_name          ((int)$user_id),
                    'avatar'=>$WordPress->user_avatar_url   ($user_id)
                );
                $WordPress  = NULL;
            }
            return $this->userDataCache[$user_id];
        }
        private function isThreadExist($thread_id){
            global $wpdb;
            $result = $wpdb->query( 'SELECT '.
                                        'thread_id '.
                                    'FROM '.
                                        TablesNames::instance()->threads.' '.
                                    'WHERE '.
                                        'thread_id = '.(int)$thread_id.' '
            );
            return ((int)$result > 0 ? true : false);
        }
        private function isUserInThread($thread_id, $user_id){
            if ((int)$thread_id > 0 && (int)$user_id > 0){
                global $wpdb;
                $result = $wpdb->query( 'SELECT '.
                                            '* '.
                                        'FROM '.
                                            TablesNames::instance()->threads.' '.
                                        'WHERE '.
                                            'thread_id = '.(int)$thread_id.' '.
                                            'AND user_id = '.(int)$user_id
                );
                return ((int)$result > 0 ? true : false);
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
        private function addRecipientToThread($thread_id, $recipient){
            global $wpdb;
            $wpdb->query(   'INSERT INTO '.
                                TablesNames::instance()->threads.' '.
                                    'SET '.
                                        'thread_id = '. (int)$thread_id. ','.
                                        'user_id = '.   (int)$recipient
            );
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
        public function getRecipientsByThreadID($thread_id, $ignoreID = false){
            global $wpdb;
            $recipients = $wpdb->get_results(   'SELECT '.
                                                    'user_id '.
                                                'FROM '.
                                                    TablesNames::instance()->threads.' '.
                                                'WHERE '.
                                                    'thread_id = '.(int)$thread_id
            );
            if (is_array($recipients) !== false){
                if (count($recipients) > 0){
                    $_recipients = array();
                    foreach($recipients as $recipient){
                        if ($ignoreID !== false){
                            if ((int)$ignoreID !== (int)$recipient->user_id){
                                $_recipients[] = $recipient->user_id;
                            }
                        }else{
                            $_recipients[] = $recipient->user_id;
                        }
                    }
                    return $_recipients;
                }
            }
            return false;
        }
        public function getThreadIDByMessageID($message_id){
            global $wpdb;
            $result = $wpdb->get_results(   'SELECT '.
                                                'thread_id '.
                                            'FROM '.
                                                TablesNames::instance()->messages.' '.
                                            'WHERE '.
                                                'id = '.(int)$message_id
            );
            return (is_array($result) !== false ? (count($result) === 1 ? (int)$result[0]->thread_id : false) : false);
        }
        public function getInbox($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $messages = ($parameters->as_threads === true ? $this->getInboxAsThreads($parameters) : $this->getInboxAsList($parameters));
                if ($parameters->add_users_data === true){
                    foreach($messages->messages as $key=>$message){
                        $messages->messages[$key]->sender = $this->getUserData($message->sender_id);
                    }
                }
                return $messages;
            }
            return false;
        }
        public function getInboxOfThreadAfterDate($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($this->isUserInThread((int)$parameters->thread_id, (int)$parameters->user_id) !== false &&
                    \DateTime::createFromFormat('Y-m-d H:i:s', (string)$parameters->date) !== false){
                    global $wpdb;
                    $selector = 'SELECT '.
                                    't_messages.*, t_messages.id AS message_id, '.
                                    't_read.is_unread, '.
                                    't_counts.count_in_thread AS count_in_thread '.
                                'FROM '.
                                    '( '.
                                        'SELECT '.
                                            '* '.
                                        'FROM '.
                                            TablesNames::instance()->messages.' '.
                                        'WHERE '.
                                            'created >= "'.(string)$parameters->date.'" '.
                                            'AND thread_id = '.(int)$parameters->thread_id.' '.
                                    ') AS t_messages, '.
                                    '( '.
                                        'SELECT '.
                                            '* '.
                                        'FROM '.
                                            TablesNames::instance()->read.' '.
                                        'WHERE '.
                                            'user_id = '.(int)$parameters->user_id.' '.
                                    ') AS t_read '.
                                'JOIN ( '.
                                    'SELECT '.
                                        'COUNT(*) AS count_in_thread, '.
                                        'thread_id '.
                                    'FROM '.
                                        TablesNames::instance()->messages.' '.
                                    'WHERE '.
                                        'thread_id = '.(int)$parameters->thread_id.' '.
                                ') AS t_counts ON t_counts.thread_id = thread_id '.
                                'WHERE '.
                                    't_messages.thread_id = '.(int)$parameters->thread_id.' '.
                                    'AND t_messages.sender_id <> '.(int)$parameters->user_id.' '.
                                    'AND t_messages.id = t_read.message_id '.
                                'ORDER BY '.
                                    't_messages.created DESC';
                    $messages = $wpdb->get_results($selector);
                    if (is_array($messages) !== false){
                        foreach($messages as $key=>$message){
                            $messages[$key]->sender = $this->getUserData($message->sender_id);
                        }
                        $messages = $this->prepareMessage($messages);
                        return $this->getAttachments($messages, (int)$parameters->user_id);
                    }
                }
            }
            return false;
        }
        public function getMessagesOfThread($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($this->isUserInThread((int)$parameters->thread_id, (int)$parameters->user_id) !== false){
                    return $this->getMessagesInThread(
                        (int)$parameters->thread_id,
                        (int)$parameters->user_id,
                        true
                    );
                }
            }
            return false;
        }

        public function getOutbox($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $messages = ($parameters->add_threads === true ? $this->getOutboxAsThreads($parameters) : $this->getOutboxAsList($parameters));
                foreach($messages->messages as $key=>$message){
                    if ($parameters->add_users_data === true){
                        $messages->messages[$key]->sender = $this->getUserData($message->sender_id);
                    }
                    $messages->messages[$key]->recipients = $this->getRecipientsFromThread($message->thread_id, $message->sender_id, $parameters->add_users_data);
                }
                return $messages;
            }
            return false;
        }
        public function getMessageByID($message_id, $user_id){
            global $wpdb;
            $selector   =   'SELECT '.
                                't_messages.id AS message_id, '.
                                't_messages.message, '.
                                't_messages.subject, '.
                                't_messages.created, '.
                                't_messages.sender_id, '.
                                't_messages.thread_id, '.
                                't_read.is_unread '.
                            'FROM '.
                                TablesNames::instance()->messages.' t_messages, '.
                                TablesNames::instance()->read.' t_read '.
                            'WHERE '.
                                't_messages.id = '.(int)$message_id.' '.
                                'AND t_messages.id = t_read.message_id '.
                                'AND t_read.user_id = '.(int)$user_id.' '.
                            'GROUP BY '.
                                't_messages.id';
            $message    = $wpdb->get_results(   $selector);
            if (is_array($message) !== false){
                if (count($message) === 1){
                    $message                = $message[0];
                    $message                = $this->prepareMessage($message);
                    $message->sender        = $this->getUserData((int)$message->sender_id);
                    if ((int)$message->sender_id === (int)$user_id){
                        $message->recipients = $this->getRecipientsFromThread($message->thread_id, $message->sender_id, true);
                    }
                    return $this->getAttachments($message, $user_id);
                }
            }
            return false;
        }
        public function isMessageAssignedWithUser($message_id, $user_id){
            if ((int)$message_id > 0 && (int)$user_id > 0){
                global $wpdb;
                $selector = 'SELECT '.
                                '* '.
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
                            'AND id = '.(int)$message_id.'';
                $count = $wpdb->query($selector);
                return ((int)$count > 0 ? true : false);
            }
            return false;
        }
        public function getAllRecipientsOfUser($user_id){
            global $wpdb;
            $selector   =   'SELECT '.
                                'user_id AS ID '.
                            'FROM '.
                                TablesNames::instance()->threads.' '.
                            'WHERE '.
                                'thread_id IN ( '.
                                    'SELECT '.
                                        'thread_id '.
                                    'FROM '.
                                        TablesNames::instance()->threads.' '.
                                    'WHERE '.
                                        'user_id = '.(int)$user_id.' '.
                                    'GROUP BY '.
                                        'thread_id '.
                                ') '.
                            'AND user_id <> '.(int)$user_id.' '.
                            'GROUP BY '.
                                'user_id';
            $recipients    = $wpdb->get_results(   $selector);
            if (is_array($recipients) !== false){
                return $recipients;
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
                        $recipients = $this->getRecipientsByThreadID($thread_id);
                    }
                }
                if ($thread_id === -1){
                    $recipients                 = $parameters->recipients;
                    $recipients[]               = $parameters->sender_id;
                    $result                     = $this->createThread($recipients);
                    $thread_id                  = ($result !== false ? $result->thread_id   : false);
                    $recipients                 = ($result !== false ? $recipients          : false);
                }
                if ($thread_id !== false && $recipients !== false && $parameters->message !== '' && $parameters->subject !== ''){
                    //insert_id
                    $created    = date("Y-m-d H:i:s");
                    $result     = $wpdb->insert(
                        TablesNames::instance()->messages,
                        array(
                            'sender_id' =>$parameters->sender_id,
                            'thread_id' =>$thread_id,
                            'created'   =>$created,
                            'subject'   =>$parameters->subject,
                            'message'   =>$parameters->message
                        ),
                        array('%d', '%d', '%s', '%s', '%s')
                    );
                    if ($result !== false){
                        $message_id = (int)$wpdb->insert_id;
                        if ($message_id !== 0){
                            $StatusRead     = new StatusRead    ();
                            $StatusRemove   = new StatusRemove  ();
                            foreach($recipients as $recipient ){
                                $StatusRead     ->add($message_id, $recipient, ((int)$recipient !== (int)$parameters->sender_id ? 1 : 0));
                                $StatusRemove   ->add($message_id, $recipient, 0);
                            }
                            $StatusRead     = NULL;
                            $StatusRemove   = NULL;
                            return (object)array(
                                'message_id'=>$message_id,
                                'created'   =>$created
                            );
                        }
                    }
                }
            }
            return false;
        }
    }
    class Attachments{
        public function clear(){
            global $wpdb;
            $selector       =   'SELECT '.
                                    '* '.
                                'FROM '.
                                    TablesNames::instance()->attaches.' '.
                                'WHERE '.
                                    'message_id = -1 '.
                                'AND DATEDIFF(NOW(), added) > 1';
            $attachments    = $wpdb->get_results($selector);
            if (is_array($attachments) !== false){
                if (count($attachments) > 0){
                    foreach($attachments as $attachment){
                        //Remove record form database
                        $result = $wpdb->delete(
                            TablesNames::instance()->attaches,
                            array(
                                'id' => (int)$attachment->id
                            ),
                            array( '%d' )
                        );
                        if ($result !== false){
                            //Remove file
                            @unlink(stripcslashes(
                                \Pure\Resources\Names::instance()->repairPath($attachment->file)
                            ));
                        }
                    }
                }
            }
            return true;
        }
        public function countWithKey($_key){
            $key = esc_sql((string)$_key);
            if ($key !== '') {
                global $wpdb;
                $count = $wpdb->query(  'SELECT '.
                                            '* '.
                                        'FROM '.
                                            TablesNames::instance()->attaches.' '.
                                        'WHERE '.
                                        '`key`="'.$key.'"'
                );
                return $count;
            }
            return false;
        }
        public function accept($user_id, $_key, $message_id){
            global $wpdb;
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ((int)($current !== false ? $current->ID : -1) === (int)$user_id && (int)$message_id > 0) {
                $key = esc_sql((string)$_key);
                if ($key !== ''){
                    $result = $wpdb->update(
                        TablesNames::instance()->attaches,
                        array( 'key' => '',     'message_id'    => (int)$message_id ),
                        array( 'key' => $key,   'user_id'       => (int)$user_id    ),
                        array( '%s', '%d' ),
                        array( '%s', '%d' )
                    );
                    return $result;
                }
            }
            return false;
        }
        public function add($user_id, $_key, $_path, $_type, $_original_name){
            //run clearing
            $this->clear();
            global $wpdb;
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ((int)($current !== false ? $current->ID : -1) === (int)$user_id) {
                $key            = esc_sql((string)$_key);
                $path           = esc_sql((string)$_path);
                $type           = esc_sql((string)$_type);
                $original_name  = esc_sql((string)$_original_name);
                if ($key !== '' && $path !== '' && $type !== '' && $original_name !== ''){
                    $path   = \Pure\Resources\Names::instance()->clearPath($path);
                    $result = $wpdb->insert(
                        TablesNames::instance()->attaches,
                        array(
                            'message_id'    =>-1,
                            'user_id'       =>(int)$user_id,
                            'file'          =>$path,
                            'type'          =>$type,
                            'original_name' =>$original_name,
                            'added'         =>date("Y-m-d H:i:s"),
                            'key'           =>$key
                        ),
                        array('%d', '%d', '%s', '%s', '%s', '%s', '%s')
                    );
                    if ($result !== false){
                        $attachment_id = (int)$wpdb->insert_id;
                        return ($attachment_id > 0 ? $attachment_id : false);
                    }
                }
            }
            return false;
        }
        public function remove($user_id, $attachment_id){
            global $wpdb;
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ((int)($current !== false ? $current->ID : -1) === (int)$user_id) {
                if ((int)$attachment_id > 0){
                    $selector   =   'SELECT '.
                                        '* '.
                                    'FROM '.
                                        TablesNames::instance()->attaches.' '.
                                    'WHERE '.
                                        'user_id = '.(int)$user_id.' '.
                                        'AND id = '.(int)$attachment_id;
                    $attachment = $wpdb->get_results($selector);
                    if (is_array($attachment) !== false){
                        if (count($attachment) === 1){
                            $attachment = $attachment[0];
                            //Remove file
                            @unlink(stripcslashes(
                                \Pure\Resources\Names::instance()->repairPath($attachment->file)
                            ));
                            //Remove record form database
                            $result = $wpdb->delete(
                                TablesNames::instance()->attaches,
                                array(
                                    'id'        => (int)$attachment_id,
                                    'user_id'   => (int)$user_id
                                ),
                                array( '%d', '%d' )
                            );
                            return ($result !== false ? true : false);
                        }
                    }
                }
            }
            return false;
        }
        public function get($_user_id, $attachment_id, $validate = true){
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
                            $attachment         = $attachment[0];
                            $attachment->file   = \Pure\Resources\Names::instance()->repairPath($attachment->file);
                            $Mails              = new Provider();
                            $isMessageAssigned  = $Mails->isMessageAssignedWithUser((int)$attachment->message_id, (int)$user_id);
                            $Mails              = NULL;
                            return ($isMessageAssigned !== false ? $attachment : false);
                        }
                    }
                }
            }
            return false;
        }
        public function generateURL($attachment_id){
            return get_site_url().'/request/?command=resources_messenger_mails_attachment&mail_attachment_id='.$attachment_id;
        }
        public function getFromMessage($user_id, $message_id, $full = false, $add_url = true){
            if ((int)$message_id > 0 && (int)$user_id > 0) {
                global $wpdb;
                $Mails  = new Provider();
                $access = $Mails->isMessageAssignedWithUser($message_id, $user_id);
                $Mails  = NULL;
                if ($access !== false){
                    if ($full !== false){
                        $selector       =   'SELECT '.
                                                '* '.
                                            'FROM '.
                                                TablesNames::instance()->attaches.' '.
                                            'WHERE '.
                                                'message_id = '.(int)$message_id;
                    }else{
                        $selector       =   'SELECT '.
                                                'id, '.
                                                'message_id, '.
                                                'original_name, '.
                                                'added '.
                                            'FROM '.
                                                TablesNames::instance()->attaches.' '.
                                            'WHERE '.
                                                'message_id = '.(int)$message_id;
                    }
                    $attachments    = $wpdb->get_results($selector);
                    if ($add_url !== false){
                        if (is_array($attachments) !== false){
                            foreach($attachments as $key=>$attachment){
                                $attachments[$key]->url     = $this->generateURL($attachment->id);
                                //$attachments[$key]->file    = \Pure\Resources\Names::instance()->repairPath($attachments[$key]->file);
                            }
                        }
                    }
                    return (is_array($attachments) !== false ? $attachments : false);
                }
            }
            return false;
        }
    }
    class StatusRead{
        public function add($message_id, $user_id, $is_unread = 1){
            global $wpdb;
            $wpdb->query(   'INSERT INTO '.
                                TablesNames::instance()->read.' '.
                                    'SET '.
                                        'message_id = '.(int)$message_id. ','.
                                        'user_id = '.   (int)$user_id. ','.
                                        'is_unread = '. (int)$is_unread
            );
        }
        public function update($message_id, $user_id){
            if ((int)$message_id > 0 && (int)$user_id > 0){
                global $wpdb;
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$user_id) {
                    $result = $wpdb->update(
                        TablesNames::instance()->read,
                        array( 'is_unread' => 0),
                        array( 'message_id' => (int)$message_id, 'user_id' => (int)$user_id),
                        array( '%d' ),
                        array( '%d', '%d' )
                    );
                    return $result;
                }
            }
            return false;
        }
        public function getUnreadCount($user_id){
            if ((int)$user_id > 0){
                global $wpdb;
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$user_id) {
                    $selector   =   'SELECT '.
                                        'COUNT(*) AS count, '.
                                        'thread_id '.
                                    'FROM '.
                                        TablesNames::instance()->messages.' '.
                                    'WHERE '.
                                        'id IN ( '.
                                            'SELECT '.
                                                'message_id '.
                                            'FROM '.
                                                TablesNames::instance()->remove.' '.
                                            'WHERE '.
                                                'user_id = '.(int)$user_id.' '.
                                            'AND is_removed <> 1 '.
                                        ') '.
                                        'AND id IN ( '.
                                            'SELECT '.
                                                'message_id '.
                                            'FROM '.
                                                TablesNames::instance()->read.' '.
                                            'WHERE '.
                                                'user_id = '.(int)$user_id.' '.
                                            'AND is_unread = 1 '.
                                        ') '.
                                    'GROUP BY '.
                                        'thread_id';
                    $threads    = $wpdb->get_results(   $selector);
                    if (is_array($threads) !== false){
                        return $threads;
                    }
                }
            }
            return false;
        }
    }
    class StatusRemove{
        public function add($message_id, $user_id, $is_removed = 0){
            global $wpdb;
            $wpdb->query(   'INSERT INTO '.
                                TablesNames::instance()->remove.' '.
                                'SET '.
                                    'message_id = '.(int)$message_id. ','.
                                    'user_id = '.   (int)$user_id. ','.
                                    'is_removed = '.(int)$is_removed
            );
        }
    }
}
?>