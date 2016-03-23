<?php
namespace Pure\Requests\Events\Actions{
    class Core{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'action':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->event_id   = (integer  )($parameters->event_id );
                    $parameters->action     = (string   )($parameters->action   );
                    if ($parameters->action !== 'join' && $parameters->action !== 'refuse'){
                        return false;
                    }
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        public function action($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->user_id) {
                    \Pure\Components\PostTypes\Events\Module\Initialization::instance()->attach(true);
                    $EventProvider = new \Pure\Components\PostTypes\Events\Module\Provider();
                    if ($EventProvider->isRegistrationAvailable($parameters->event_id) !== false){
                        switch($parameters->action){
                            case 'join':
                                if ($EventProvider->addMember($parameters->event_id, (int)$parameters->user_id) !== false){
                                    echo 'success'; return true;
                                }else{
                                    echo 'fail'; return false;
                                }
                                break;
                            case 'refuse':
                                if ($EventProvider->removeMember($parameters->event_id, (int)$parameters->user_id) !== false){
                                    echo 'success'; return true;
                                }else{
                                    echo 'fail'; return false;
                                }
                                break;
                        }
                    }else{
                        echo 'registration is closed';
                        return false;
                    }
                }
            }
            //Error: no access
            echo 'no access';
            return false;
        }
    }
}
?>