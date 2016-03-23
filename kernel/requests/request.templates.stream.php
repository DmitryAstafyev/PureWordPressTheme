<?php
namespace Pure\Requests\Templates\Stream{
    class Stream{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'remove':
                    $parameters->owner_id    = (integer)($parameters->owner_id    );
                    $parameters->target_id   = (integer)($parameters->target_id   );
                    return true;
                case 'add':
                    $parameters->owner_id    = (integer)($parameters->owner_id    );
                    $parameters->target_id   = (integer)($parameters->target_id   );
                    return true;
                case 'toggle':
                    $parameters->owner_id    = (integer)($parameters->owner_id    );
                    $parameters->target_id   = (integer)($parameters->target_id   );
                    return true;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        private function action($parameters, $action){
            $WordPress          = new \Pure\Components\WordPress\UserData\Data();
            $current            = $WordPress->get_current_user();
            $WordPress          = NULL;
            if ($current !== false) {
                if ((int)$current->ID === (int)$parameters->owner_id) {
                    \Pure\Components\Stream\Module\Initialization::instance()->attach();
                    $Stream = new \Pure\Components\Stream\Module\Provider();
                    switch($action){
                        case 'add':
                            $result = $Stream->add((int)$parameters->owner_id, (int)$parameters->target_id);
                            break;
                        case 'remove':
                            $result = $Stream->remove((int)$parameters->owner_id, (int)$parameters->target_id);
                            break;
                        case 'toggle':
                            $result = $Stream->toggle((int)$parameters->owner_id, (int)$parameters->target_id);
                            break;
                    }
                    //echo var_dump($result);
                    $Stream = NULL;
                    if ($result === true){
                        echo 'done';
                        return true;
                    }else{
                        echo 'error_during_removing';
                        return false;
                    }
                }
            }
            echo 'incorrect_user_data';
            return false;
        }
        public function remove($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                $this->sanitize($parameters, __METHOD__);
                return $this->action($parameters, 'remove');
            }
            echo 'fail';
            return false;
        }
        public function add($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                $this->sanitize($parameters, __METHOD__);
                return $this->action($parameters, 'add');
            }
            echo 'fail';
            return false;
        }
        public function toggle($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                $this->sanitize($parameters, __METHOD__);
                return $this->action($parameters, 'toggle');
            }
            echo 'fail';
            return false;
        }
    }
}
?>