<?php
namespace Pure\Requests\Templates\Messenger\Notifications{
    class Notifications{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->shown      = (integer  )($parameters->shown    );
                    $parameters->maxcount   = (integer  )($parameters->maxcount );
                    return true;
                case 'setAsRead':
                    $parameters->user_id            = (integer  )($parameters->user_id          );
                    $parameters->notification_id    = (integer  )($parameters->notification_id  );
                    return true;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        public function get($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id){
                    \Pure\Components\Messenger\Notifications\Initialization::instance()->attach();
                    $Provider = new \Pure\Components\Messenger\Notifications\Provider();
                    $messages = $Provider->get((object)array(
                        'user_id'       =>(int)$parameters->user_id,
                        'shown'         =>(int)$parameters->shown,
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
        public function setAsRead($parameters) {
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                \Pure\Components\Messenger\Notifications\Initialization::instance()->attach();
                $Provider   = new \Pure\Components\Messenger\Notifications\Provider();
                $result     = $Provider->setAsRead((object)array(
                    'user_id'           =>(int)$parameters->user_id,
                    'notification_id'   =>(int)$parameters->notification_id
                ));
                $Provider   = NULL;
                if ($result !== false){
                    echo 'success';
                    return true;
                }else{
                    echo 'fail';
                    return false;
                }
            }
            echo 'no access';
            return false;
        }
    }
}
?>