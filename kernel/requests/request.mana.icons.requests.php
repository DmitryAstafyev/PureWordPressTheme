<?php
namespace Pure\Requests\Mana\Icons\Requests{
    class Provider{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $parameters->object     = (string)($parameters->object      );
                    $parameters->user_ids   = (string)($parameters->user_ids    );
                    $parameters->object_ids = (string)($parameters->object_ids  );
                    return true;
                    break;
                case 'set':
                    $parameters->object     = (string)($parameters->object      );
                    $parameters->object_id  = (integer)($parameters->object_id  );
                    $parameters->value      = (integer)($parameters->value      );
                    $parameters->field      = (isset($parameters->field) !== false ? esc_sql($parameters->field): false );//Free field for communication. Can be anything
                    return true;
                    break;
                case 'give':
                    $parameters->source     = (integer)($parameters->source  );
                    $parameters->target     = (integer)($parameters->target  );
                    $parameters->value      = (integer)($parameters->value   );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $parameters->user_ids   = explode(',', $parameters->user_ids    );
                    $parameters->object_ids = explode(',', $parameters->object_ids  );
                    foreach($parameters->user_ids as $key=>$value){
                        $parameters->user_ids[$key] = (int)$value;
                    }
                    foreach($parameters->object_ids as $key=>$value){
                        $parameters->object_ids[$key] = (int)$value;
                    }
                    break;
                case 'set':
                    $parameters->object = esc_sql($parameters->object);
                    break;
            }
        }
        public function get($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if (count($parameters->user_ids) === count($parameters->object_ids)){
                    $data = array();
                    foreach($parameters->user_ids as $key=>$value){
                        $data[] = (object)array(
                            'object_id' =>$parameters->object_ids   [$key],
                            'user_id'   =>$parameters->user_ids     [$key]
                        );
                    }
                    \Pure\Components\Relationships\Mana\Initialization::instance()->attach(true);
                    $Provider   = new \Pure\Components\Relationships\Mana\Provider();
                    $mana_data  = $Provider->fillDataWithObjects(
                        (object)array(
                            'data'  =>$data,
                            'object'=>$parameters->object
                        )
                    );
                    $Provider = NULL;
                    if ($mana_data !== false){
                        echo json_encode($mana_data);
                        return true;
                    }else{
                        echo 'error_during_getting';
                        return false;
                    }
                }
            }
            echo 'access_error';
            return false;
        }
        public function set($parameters){
            //'field' it's free variable. For example for related posts in questions it is QUESTION_ID
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($parameters->object_id > 0){
                    \Pure\Components\Relationships\Mana\Initialization::instance()->attach(true);
                    $Provider = new \Pure\Components\Relationships\Mana\Provider();
                    $result = $Provider->set(
                        (object)array(
                            'object'    =>$parameters->object,
                            'object_id' =>$parameters->object_id,
                            'value'     =>$parameters->value,
                            'field'     =>$parameters->field
                        )
                    );
                    if (is_null($result) !== false){
                        echo 'fail';
                        return false;
                    }
                    if ($result !== false){
                        $updateMana = $Provider->getForObjects(
                            (object)array(
                                'object'=>$parameters->object,
                                'IDs'   =>array($parameters->object_id)
                            )
                        );
                        if ($updateMana !== false){
                            if (count($updateMana) === 1){
                                $updateMana = $updateMana[0];
                                WebSocketServer::add(
                                    (int)$parameters->object_id,
                                    $parameters->object,
                                    $parameters->field,
                                    'mana_update',
                                    $updateMana
                                );
                            }
                        }
                        echo 'success';
                        return true;
                    }
                    if ($result === false){
                        echo 'voted';
                        return true;
                    }
                }
            }
            echo 'error';
            return false;
        }
        public function give($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($parameters->source > 0 && $parameters->target > 0 && $parameters->value > 0){
                    \Pure\Components\Relationships\Mana\Initialization::instance()->attach();
                    $Wallet = new \Pure\Components\Relationships\Mana\Wallet();
                    $result = $Wallet->give($parameters->source, $parameters->target, $parameters->value);
                    if ($result !== false){
                        $wallets = (object)array(
                            'source'=>$Wallet->get($parameters->source),
                            'target'=>$Wallet->get($parameters->target),
                        );
                        echo json_encode($wallets);
                        $Wallet = NULL;
                        return true;
                    }
                    $Wallet = NULL;
                }
            }
            echo 'error';
            return false;
        }
    }
    class WebSocketServer{
        static function add($object_id, $object_type, $field, $event, $parameters){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ($current !== false){
                \Pure\Components\WordPress\Location\Module\Initialization::instance()->attach(true);
                $Recorder   = new \Pure\Components\WordPress\Location\Module\Recorder();
                $recipients = false;
                switch($object_type){
                    case 'comment':
                        $comment    = get_comment($object_id);
                        $post_id    = ($comment !== false ? $comment->comment_post_ID : false);
                        if ($post_id !== false){
                            $recipients = $Recorder->getUsersByObject('post', $post_id);
                        }
                        break;
                    case 'activity':
                        if ($field !== false){
                            if ((int)$field > 0){
                                $current_place =  $Recorder->whereUserIs($current->ID);
                                if ($current_place !== false){
                                    $recipients = $Recorder->getUsersByObject($current_place, (int)$field);
                                }
                            }
                        }
                        break;
                    case 'post':
                        $recipients = $Recorder->getUsersByObject('post', $object_id);
                        break;
                    case 'question_related_post':
                        if ((int)$field > 0){
                            $recipients = $Recorder->getUsersByObject('post', $field);
                        }
                        break;
                    case 'question_related_question':
                        if ((int)$field > 0){
                            $recipients = $Recorder->getUsersByObject('post', $field);
                        }
                        break;
                }
                if ($recipients !== false){
                    \Pure\Components\webSocketServer\Events\Initialization::instance()->attach(true);
                    $WebSocketServer = new \Pure\Components\webSocketServer\Events\Events();
                    foreach($recipients as $recipient){
                        if ((int)$recipient->id !== (int)$current->ID){
                            $WebSocketServer->add((int)$recipient->id, $event, $parameters);
                        }
                    }
                    $WebSocketServer = NULL;
                }
                $Recorder   = NULL;
            }
        }
    }
}
?>