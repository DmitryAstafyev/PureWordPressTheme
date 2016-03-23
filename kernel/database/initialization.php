<?php
namespace Pure\DataBase{
    class TablesQuery{
        static $query = '';
    }
    class TablesNames{
        static private $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        public $admonitions;
        public $profile_config;
        public $quotes;
        public $websockets_state;
        public $security_tokens;
        public $messenger;
        public $mana;
        public $post_visibility;
        public $locations;
        public $user_registration;
        public $logs;
        public $attachments;
        function __construct(){
            global $wpdb;
            $this->admonitions          = $wpdb->prefix.'pure_bp_users_admonitions';
            $this->profile_config       = $wpdb->prefix.'pure_users_profile_config';
            $this->quotes               = $wpdb->prefix.'pure_bp_users_quotes';
            $this->websockets_state     = $wpdb->prefix.'pure_websockets_state';
            $this->websockets_evetns    = $wpdb->prefix.'pure_websockets_events';
            $this->security_tokens      = $wpdb->prefix.'pure_security_tokens';
            $this->messenger            = (object)array(
                'mails'=>(object)array(
                    'messages'  =>$wpdb->prefix.'pure_messenger_mails_messages',
                    'threads'   =>$wpdb->prefix.'pure_messenger_mails_threads',
                    'remove'    =>$wpdb->prefix.'pure_messenger_mails_remove',
                    'read'      =>$wpdb->prefix.'pure_messenger_mails_read',
                    'attaches'  =>$wpdb->prefix.'pure_messenger_mails_attaches',
                ),
                'chat'=>(object)array(
                    'messages'  =>$wpdb->prefix.'pure_messenger_chat_messages',
                    'threads'   =>$wpdb->prefix.'pure_messenger_chat_threads',
                    'read'      =>$wpdb->prefix.'pure_messenger_chat_read',
                    'attaches'  =>$wpdb->prefix.'pure_messenger_chat_attaches',
                )
            );
            $this->mana                 = (object)array(
                'values'    =>$wpdb->prefix.'pure_mana_value',
                'history'   =>$wpdb->prefix.'pure_mana_history',
                //'stars'     =>$wpdb->prefix.'pure_mana_stars', //for the future
            );
            $this->post_visibility      = $wpdb->prefix.'pure_post_visibility';
            $this->locations            = $wpdb->prefix.'pure_user_location';
            $this->user_registration    = $wpdb->prefix.'pure_user_registration';
            $this->logs                 = $wpdb->prefix.'pure_logs';
            $this->attachments          = $wpdb->prefix.'pure_attachments';
        }
    }
    class Initialization{
        private function require_from_folder($folder){
            $filesSystem    = new \Pure\Resources\FileSystem();
            $Files          = $filesSystem->getFilesList($folder);
            $filesSystem    = NULL;
            if (is_null($Files) === false){
                foreach($Files as $File){
                    if (file_exists(\Pure\Configuration::instance()->dir($folder.'/'.$File)) === true){
                        if (stripos($File, '.php') !== false){
                            require_once(\Pure\Configuration::instance()->dir($folder.'/'.$File));
                            try{
                                $class = preg_replace("/(.php)/", '', $File);
                                if (class_exists('Pure\DataBase\Tables\\'.$class) === true){
                                    $className  = 'Pure\DataBase\Tables\\'.$class;
                                    $instance   = new $className();
                                    $instance->create();
                                }
                            }catch (\Exception $e){}
                        }
                    }
                }
                if (TablesQuery::$query !== ''){
                    global $wpdb;
                    mysqli_multi_query($wpdb->dbh, TablesQuery::$query);
                }
            }
        }
        private function tables(){
            $this->require_from_folder(\Pure\Configuration::instance()->dir(__DIR__.'/tables'));
        }
        public function actions(){
            $this->tables();
        }
        public function attach(){
            add_action("after_switch_theme", array($this, 'actions'));
        }
    }
    interface Table{
        public function create();
    }
    //Attach hook
    $Initialization = new Initialization();
    $Initialization->attach();
    $Initialization = NULL;
}
?>