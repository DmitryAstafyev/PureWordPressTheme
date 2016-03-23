<?php
namespace Pure\Components\Relationships\Mana{
    class Provider{
        private $table;
        private function validate(&$parameters, $method){
            $result = false;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'getForObjects':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->object    ));
                    $result = ($result === false ? false : isset($parameters->IDs       ));
                    $result = ($result === false ? false : is_array($parameters->IDs    ));
                    if ($result !== false){
                        $parameters->object     = (string)$parameters->object;
                        foreach($parameters->IDs as $key=>$id){
                            $parameters->IDs[$key] = (int)$id;
                        }
                    }
                    break;
                case 'getForObjectsWithDefault':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->object    ));
                    $result = ($result === false ? false : isset($parameters->IDs       ));
                    $result = ($result === false ? false : is_array($parameters->IDs    ));
                    if ($result !== false){
                        $parameters->object     = (string)$parameters->object;
                        foreach($parameters->IDs as $key=>$id){
                            $parameters->IDs[$key] = (int)$id;
                        }
                    }
                    break;
                case 'fillDataWithObjects':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->object    ));
                    $result = ($result === false ? false : isset($parameters->data      ));
                    $result = ($result === false ? false : is_array($parameters->data   ));
                    if ($result !== false){
                        $parameters->object = (string)$parameters->object;
                        $data               = [];
                        $IDs                = [];
                        foreach($parameters->data as $key=>$record){
                            if (isset($record->object_id) && isset($record->user_id)){
                                $record->user_id    = (int)$record->user_id;
                                $record->object_id  = (int)$record->object_id;
                                if ($record->user_id > 0 && $record->object_id > 0){
                                    $data[$record->object_id]   = (object)array(
                                        'user_id'   =>$record->user_id,
                                        'object_id' =>$record->object_id
                                    );
                                    $IDs[]                      = $record->object_id;
                                }
                            }
                        }
                        $parameters->data   = $data;
                        $parameters->IDs    = $IDs;
                    }
                    break;
                case 'set':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->object        ));
                    $result = ($result === false ? false : isset($parameters->object_id     ));
                    $result = ($result === false ? false : isset($parameters->value         ));
                    if ($result !== false){
                        $parameters->object     = (string)$parameters->object;
                        $parameters->object_id  = (integer)$parameters->object_id;
                        $parameters->value      = (integer)$parameters->value;
                        $parameters->field      = (isset($parameters->field) !== false ? $parameters->field : false);
                    }
                    break;
            }
            return $result;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'getForObjects':
                    $parameters->object = esc_sql($parameters->object);
                    break;
                case 'fillDataWithObjects':
                    $parameters->object = esc_sql($parameters->object);
                    break;
                case 'set':
                    $parameters->object = esc_sql($parameters->object);
                    break;
            }
        }
        public function getDefault($user_id = 0, $object_type = '', $object_id = 0){
            return (object)array(
                'id'            =>0,
                'user_id'       =>$user_id,
                'object_type'   =>$object_type,
                'object_id'     =>$object_id,
                'minus'         =>0,
                'plus'          =>0,
                'used'          =>0,
            );
        }
        public function fillDataWithObjects($parameters){
            $getRecord  = function($records, $id){
                foreach($records as $record){
                    if ((int)$record->object_id === $id){
                        return $record;
                    }
                }
                return false;
            };
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $records = $this->getForObjects(
                    (object)array(
                        'object'=>$parameters->object,
                        'IDs'   =>$parameters->IDs
                    )
                );
                if($records !== false){
                    $result = array();
                    foreach($parameters->IDs as $id){
                        $record         = $getRecord($records, $id);
                        $result[$id]    = ($record !== false ? $record : $this->getDefault(
                            $parameters->data[$id]->user_id,
                            $parameters->object,
                            $parameters->data[$id]->object_id
                        ));
                    }
                    return $result;
                }
            }
            return false;
        }
        public function getForObjects($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                global $wpdb;
                $selector   =   'SELECT '.
                                    '* '.
                                'FROM '.
                                    $this->table.' '.
                                'WHERE '.
                                    'object_type = "'.$parameters->object.'" '.
                                    'AND object_id IN ('.implode(',', $parameters->IDs).')';
                $records    = $wpdb->get_results($selector);
                if (is_array($records) !== false){
                    return $records;
                }
            }
            return false;
        }
        public function getForObjectsWithDefault($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                //echo var_dump($parameters);
                if (count($parameters->IDs) > 0){
                    global $wpdb;
                    $selector   =   'SELECT '.
                                        '* '.
                                    'FROM '.
                                        $this->table.' '.
                                    'WHERE '.
                                        'object_type = "'.$parameters->object.'" '.
                                        'AND object_id IN ('.implode(',', $parameters->IDs).')';
                    $records    = $wpdb->get_results($selector);
                    if (is_array($records) !== false){
                        $getRecord  = function($records, $id){
                            foreach($records as $record){
                                if ((int)$record->object_id === $id){
                                    return $record;
                                }
                            }
                            return false;
                        };
                        foreach($parameters->IDs as $id){
                            $record         = $getRecord($records, $id);
                            $result[$id]    = ($record !== false ? $record : 0);
                        }
                        return $result;
                    }
                }
            }
            return false;
        }
        private function getUserIDForObject($object, $object_id, $field = false){
            $user_id = false;
            if ((int)$object_id > 0){
                global $wpdb;
                $result = false;
                switch($object){
                    case 'comment':
                        $selector   = 'SELECT user_id AS id FROM wp_comments WHERE	comment_ID = '.(int)$object_id;
                        $result     = $wpdb->get_results($selector);
                        break;
                    case 'post':
                        $selector   = 'SELECT post_author AS id FROM wp_posts WHERE ID ='.(int)$object_id;
                        $result     = $wpdb->get_results($selector);
                        break;
                    case 'question_related_post':
                        \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
                        $Questions  = new \Pure\Components\PostTypes\Questions\Module\Provider();
                        $user_id    = $Questions->whoAddRelatedPost($field, $object_id);
                        $Questions  = NULL;
                        return ($user_id !== false ? $user_id : false);
                        break;
                    case 'question_related_question':
                        \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
                        $Questions  = new \Pure\Components\PostTypes\Questions\Module\Provider();
                        $user_id    = $Questions->whoAddRelatedQuestion($field, $object_id);
                        $Questions  = NULL;
                        return ($user_id !== false ? $user_id : false);
                        break;
                    case 'image':
                        $selector   = 'SELECT post_author AS id FROM wp_posts WHERE ID ='.(int)$object_id;
                        $result     = $wpdb->get_results($selector);
                        break;
                    case 'activity':
                        $selector   = 'SELECT user_id AS id FROM wp_bp_activity WHERE id='.(int)$object_id;
                        $result     = $wpdb->get_results($selector);
                        break;
                }
                if (is_array($result) !== false){
                    if (count($result) === 1){
                        $user_id = $result[0]->id;
                    }
                }
            }
            return $user_id;
        }
        private function getRecord($user_id, $object, $object_id){
            global $wpdb;
            $selector   =   'SELECT '.
                                '* '.
                            'FROM '.
                                $this->table.' '.
                            'WHERE '.
                                'user_id = '.(int)$user_id.' '.
                                'AND object_type = "'.$object.'" '.
                                'AND object_id = '.(int)$object_id.'';
            $result     = $wpdb->get_results($selector);
            if (is_array($result) !== false){
                if (count($result) === 1){
                    return $result[0];
                }
            }
            return false;
        }
        /*RETURN
         * true     - mana was updated
         * false    - mana wasn't updated because current user had rate it before
         * NULL     - some error is
         * */
        public function set($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false){
                    if (($parameters->value === -1 || $parameters->value === 1) && $parameters->object_id > 0){
                        $user_id = $this->getUserIDForObject($parameters->object, $parameters->object_id, $parameters->field);
                        if ($user_id !== false){
                            //Check sandbox
                            $SandboxSwitcher    = new SandboxSwitcher();
                            $SandboxSwitcher->check($user_id, ($parameters->value === -1 ? -1 : 1), true);
                            $SandboxSwitcher    = NULL;
                            //Wallet
                            $Wallet             = new Wallet();
                            $Wallet->update($user_id, ($parameters->value === -1 ? -1 : 1));
                            $Wallet             = NULL;
                            //Make changes
                            global $wpdb;
                            $History    = new History();
                            $record     = $this->getRecord($user_id, $parameters->object, $parameters->object_id);
                            if ($record !== false){
                                //Modify
                                $history    = $History->get($record->id, $current->ID);
                                if ($history === false){
                                    $result     = $wpdb->update(
                                        $this->table,
                                        ($parameters->value === -1 ? array('minus'  =>((int)$record->minus + 1)) : array('plus'  =>((int)$record->plus + 1))),
                                        array( 'user_id' => (int)$user_id, 'object_type'=> (string)$parameters->object, 'object_id'=> (int)$parameters->object_id),
                                        array( '%d' ),
                                        array( '%d', '%s', '%d')
                                    );
                                    $History->set($record->id, $current->ID);
                                }else{
                                    $History = NULL;
                                    return false;
                                }
                                $History = NULL;
                                return ($result !== false ? true : NULL);
                            }else{
                                //Create new
                                $result = $wpdb->insert(
                                    $this->table,
                                    array(
                                        'user_id'       =>(int)$user_id,
                                        'object_type'   =>(string)$parameters->object,
                                        'object_id'     =>(int)$parameters->object_id,
                                        'minus'         =>($parameters->value === -1 ? 1 : 0),
                                        'plus'          =>($parameters->value === 1 ? 1 : 0),
                                        'used'          =>0,
                                    ),
                                    array('%d', '%s', '%d', '%d', '%d', '%d')
                                );
                                if ($result !== false){
                                    $History->set((int)$wpdb->insert_id, $current->ID);
                                    $History = NULL;
                                    return true;
                                }
                                $History = NULL;
                                return NULL;
                            }
                            $History = NULL;
                        }
                    }
                }
            }
            return NULL;
        }
        public function getForUser($user_id){
            if ((int)$user_id > 0) {
                $real   = $this->getRealManaForUser($user_id);
                $Wallet = new Wallet();
                $wallet = $Wallet->get($user_id);
                $Wallet = NULL;
                if ($real !== false && $wallet !== false){
                    return (object)array(
                        'plus'  =>$real->plus,
                        'minus' =>$real->minus,
                        'value' =>$wallet
                    );
                }
            }
            return false;
        }
        public function getRealManaForUser($user_id){
            if ((int)$user_id > 0) {
                global $wpdb;
                $selector   =   'SELECT '.
                                    'SUM(minus) AS minus, '.
                                    'SUM(plus) AS plus '.
                                'FROM '.
                                    $this->table.' '.
                                'WHERE '.
                                    'user_id = '.(int)$user_id.' '.
                                    'AND object_type <> "wallet"';
                $mana       = $wpdb->get_results($selector);
                if (is_array($mana) !== false){
                    if (count($mana) === 1){
                        return (object)array(
                            'plus'  =>(int)$mana[0]->plus,
                            'minus' =>(int)$mana[0]->minus,
                            'value' =>(int)$mana[0]->plus - (int)$mana[0]->minus
                        );
                    }
                }
                return (object)array(
                    'plus'  =>0,
                    'minus' =>0,
                    'value' =>0
                );
            }
            return false;
        }
        public function getUserPermissions($user_id){
            $cache = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, func_get_args());
            if (! $permissions = $cache->value){
                \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                $parameters     = \Pure\Components\WordPress\Settings\Instance::instance()->settings->mana->properties;
                $parameters     = \Pure\Components\WordPress\Settings\Instance::instance()->less($parameters);
                $permissions    = (object)array(
                    'allow_create'  => (object)array(
                        'post'      =>true,
                        'event'     =>true,
                        'report'    =>true,
                        'question'  =>true,
                        'comment'   =>true,
                        'activity'  =>true,
                    ),
                    'allow_remove'  => (object)array(
                        'comment'   =>true,
                        'activity'  =>true,
                    ),
                    'allow_manage'  => (object)array(
                        'categories'=>true,
                        'comment'   =>true,
                        'vote'      =>true,
                    ),
                    'allow_vote'    => (object)array(
                        'comment'   =>true,
                        'post'      =>true,
                    ),
                    'threshold'     =>(object)array(
                        'allow_create'  => (object)array(
                            'post'      =>(int)$parameters->mana_threshold_create_post,
                            'event'     =>(int)$parameters->mana_threshold_create_event,
                            'report'    =>(int)$parameters->mana_threshold_create_report,
                            'question'  =>(int)$parameters->mana_threshold_create_question,
                            'comment'   =>(int)$parameters->mana_threshold_create_comment,
                            'activity'  =>(int)$parameters->mana_threshold_create_activity,
                        ),
                        'allow_remove'  => (object)array(
                            'comment'   =>(int)$parameters->mana_threshold_do_comment_remove,
                            'activity'  =>(int)$parameters->mana_threshold_do_activity_remove,
                        ),
                        'allow_manage'  => (object)array(
                            'categories'=>(int)$parameters->mana_threshold_manage_categories,
                            'comment'   =>(int)$parameters->mana_threshold_manage_comments,
                            'rate'      =>(int)$parameters->mana_threshold_manage_vote,
                        ),
                        'allow_vote'    => (object)array(
                            'comment'   =>(int)$parameters->mana_threshold_vote_comment,
                            'post'      =>(int)$parameters->mana_threshold_vote_post,
                        )
                    )
                );
                if ($parameters->mana_using === 'on'){
                    $mana = $this->getForUser($user_id);
                    if ($mana !== false){
                        $permissions->allow_create->post        = ((int)$parameters->mana_threshold_create_post         <= $mana->value ? true : false);
                        $permissions->allow_create->event       = ((int)$parameters->mana_threshold_create_event        <= $mana->value ? true : false);
                        $permissions->allow_create->report      = ((int)$parameters->mana_threshold_create_report       <= $mana->value ? true : false);
                        $permissions->allow_create->question    = ((int)$parameters->mana_threshold_create_question     <= $mana->value ? true : false);
                        $permissions->allow_create->comment     = ((int)$parameters->mana_threshold_create_comment      <= $mana->value ? true : false);
                        $permissions->allow_create->activity    = ((int)$parameters->mana_threshold_create_activity     <= $mana->value ? true : false);
                        $permissions->allow_remove->comment     = ((int)$parameters->mana_threshold_do_comment_remove   <= $mana->value ? true : false);
                        $permissions->allow_remove->activity    = ((int)$parameters->mana_threshold_do_activity_remove  <= $mana->value ? true : false);
                        $permissions->allow_manage->categories  = ((int)$parameters->mana_threshold_manage_categories   <= $mana->value ? true : false);
                        $permissions->allow_manage->comment     = ((int)$parameters->mana_threshold_manage_comments     <= $mana->value ? true : false);
                        $permissions->allow_manage->vote        = ((int)$parameters->mana_threshold_manage_vote         <= $mana->value ? true : false);
                        $permissions->allow_vote->comment       = ((int)$parameters->mana_threshold_vote_comment        <= $mana->value ? true : false);
                        $permissions->allow_vote->post          = ((int)$parameters->mana_threshold_vote_post           <= $mana->value ? true : false);
                    }
                }
                $permissions->value = $mana->value;
                \Pure\Components\Tools\Cache\Cache::set($cache->key, $permissions);
            }
            return $permissions;
        }
        /*
         * Standard checking:
         * - user isn't authorised              - don't allow
         * - user is authorised, mana is OFF    - allow
         * - user is authorised, mana is ON     - permit according mana count
         *
         * ACTION: [allow_create, allow_manage, allow_vote]
         * OBJECT: for example for [allow_vote]:: [comment, post]
         *
         * DEFAULT USER IS CURRENT
         * */
        public function hasPermit($action, $object, $user_id = false){
            $cache = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, func_get_args());
            if (! $allows = $cache->value){
                $allows = false;
                if ($user_id === false){
                    $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                    $current    = $WordPress->get_current_user();
                    $WordPress  = NULL;
                    $userID     = ($current !== false ? $current->ID : false);
                }else{
                    $userID = ((int)$user_id > 0 ? $user_id : false);
                }
                if ($userID !== false){
                    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                    $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->mana->properties;
                    $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                    if ($settings->mana_using === 'on'){
                        $allows = $this->getUserPermissions($userID);
                        $allows = ($allows !== false ? (isset($allows->$action) !== false ? $allows->$action : false) : false);
                        $allows = ($allows !== false ? (isset($allows->$object) !== false ? $allows->$object : false) : false);
                    }else{
                        $allows = true;
                    }
                }
                \Pure\Components\Tools\Cache\Cache::set($cache->key, $allows);
            }
            return $allows;
        }
        function __construct(){
            $this->table = \Pure\DataBase\TablesNames::instance()->mana->values;
        }
    }
    class History{
        private $table = false;
        public function get($mana_id, $user_id){
            if ((int)$user_id > 0 && (int)$mana_id > 0) {
                global $wpdb;
                $selector   =   'SELECT '.
                                    '* '.
                                'FROM '.
                                    $this->table.' '.
                                'WHERE '.
                                    'user_id = '.(int)$user_id.' '.
                                    'AND mana_id = '.(int)$mana_id;
                $history    = $wpdb->get_results($selector);
                if (is_array($history) !== false) {
                    if (count($history) === 1) {
                        return $history[0];
                    }
                }
            }
            return false;
        }
        public function set($mana_id, $user_id){
            if ((int)$user_id > 0 && (int)$mana_id > 0) {
                global $wpdb;
                $result = $wpdb->insert(
                    $this->table,
                    array(
                        'mana_id'   =>(int)$mana_id,
                        'user_id'   =>(int)$user_id,
                        'happened'  =>date("Y-m-d H:i:s")
                    ),
                    array('%d', '%d', '%s')
                );
                return ($result !== false ? true : false);
            }
            return false;
        }
        function __construct(){
            $this->table = \Pure\DataBase\TablesNames::instance()->mana->history;
        }
    }
    class SandboxSwitcher{
        public function check($user_id, $value, $is_offset = false){
            if ((int)$user_id > 0 && is_int($value) !== false){
                $Provider   = new Provider();
                $mana       = $Provider->getForUser($user_id);
                $Provider   = NULL;
                if ($mana !== false){
                    $mana       = $mana->value;
                    $value      = ($is_offset === false ? $value : $mana + $value);
                    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                    $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->mana->properties;
                    $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                    $Posts      = \Pure\Providers\Posts\Initialization::instance()->getCommon();
                    if ($mana   < $settings->mana_threshold_manage_categories &&
                        $value  >= $settings->mana_threshold_manage_categories){
                        //Go out from sandbox
                        $Posts->apply_sandbox_to_author($user_id, false);
                    }
                    if ($mana   >= $settings->mana_threshold_manage_categories &&
                        $value  < $settings->mana_threshold_manage_categories){
                        //Go into sandbox
                        $Posts->apply_sandbox_to_author($user_id, true);
                    }
                    $Posts      = NULL;
                }
            }
        }
    }
    class Wallet{
        private $table;
        private function create($user_id){
            if ((int)$user_id > 0) {
                global $wpdb;
                $result = $wpdb->insert(
                    $this->table,
                    array(
                        'user_id'       =>(int)$user_id,
                        'object_type'   =>'wallet',
                        'object_id'     =>0,
                        'minus'         =>0,
                        'plus'          =>0,
                        'used'          =>0,
                    ),
                    array('%d', '%s', '%d', '%d', '%d', '%d')
                );
                return ($result !== false ? true : false);
            }
            return false;
        }
        public function get($user_id){
            if ((int)$user_id > 0) {
                global $wpdb;
                $selector   =   'SELECT '.
                                    '* '.
                                'FROM '.
                                    $this->table.' '.
                                'WHERE '.
                                    'user_id = '.(int)$user_id.' '.
                                    'AND object_type = "wallet"';
                $wallet     = $wpdb->get_results($selector);
                if (is_array($wallet) !== false){
                    if (count($wallet) === 1){
                        return (int)$wallet[0]->plus;
                    }else{
                        $this->create($user_id);
                    }
                }else{
                    $this->create($user_id);
                }
                return 0;
            }
            return false;
        }
        public function update($user_id, $value){
            if ((int)$user_id > 0) {
                global $wpdb;
                $wallet     = $this->get($user_id);
                $result     = $wpdb->update(
                    $this->table,
                    array(  'plus'       => ((int)$wallet + (int)$value)),
                    array(  'user_id'    => (int)$user_id,
                            'object_type'=> 'wallet'),
                    array( '%d' ),
                    array( '%d', '%s')
                );
                return ($result !== false ? true : false);
            }
            return false;
        }
        public function give($source_user, $target_user, $value){
            if ((int)$source_user > 0 && (int)$target_user > 0 && (int)$value > 0){
                \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->mana->properties;
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                if ((int)$settings->mana_maximum_gift >= $value){
                    $source_user_wallet = $this->get($source_user);
                    if (((int)$source_user_wallet - (int)$settings->mana_threshold_manage_categories) >= (int)$value){
                        //Check sandbox
                        $SandboxSwitcher    = new SandboxSwitcher();
                        $SandboxSwitcher->check($source_user, -$value, true);
                        $SandboxSwitcher->check($target_user, $value, true);
                        $SandboxSwitcher    = NULL;
                        //Change mana
                        $this->update($source_user, -$value);
                        $this->update($target_user, $value);
                        return true;
                    }
                }
            }
            return false;
        }
        function __construct(){
            $this->table = \Pure\DataBase\TablesNames::instance()->mana->values;
        }
    }
}
?>