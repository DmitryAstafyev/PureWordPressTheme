<?php
namespace Pure\Requests\Templates\Messenger{
    class Messenger{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'getBody':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->template   = (string   )($parameters->template );
                    return true;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'getBody':
                    $parameters->template = sanitize_text_field($parameters->template );
                    break;
            }
        }
        public function getBody($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->user_id){
                    $Manager    = \Pure\Templates\Messenger\Manager\Initialization::instance()->get($parameters->template, 'immediately');
                    $innerHTML  = $Manager->get();
                    $Manager    = NULL;
                    echo $innerHTML;
                    return true;
                }
            }
            echo 'no access';
            return false;
        }
    }
}
?>