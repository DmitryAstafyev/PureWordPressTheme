<?php
namespace Pure\Requests\Templates\CreateGroup{
    class CreateGroup{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'create':
                    $parameters->user_id        = (integer  )($parameters->user_id      );
                    $parameters->name           = (string   )($parameters->name         );
                    $parameters->description    = (string   )($parameters->description  );
                    $parameters->visibility     = (string   )($parameters->visibility   );
                    $parameters->invitations    = (string   )($parameters->invitations  );
                    return true;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'create':
                    $parameters->name           = sanitize_text_field($parameters->name         );
                    $parameters->description    = sanitize_text_field($parameters->description  );
                    $parameters->visibility     = sanitize_text_field($parameters->visibility   );
                    $parameters->invitations    = sanitize_text_field($parameters->invitations  );
                    break;
            }
        }
        public function create($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $this->sanitize($parameters, __METHOD__);
                \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                $Groups = new \Pure\Components\BuddyPress\Groups\Core();
                $result = $Groups->create($parameters);
                $Groups = NULL;
                if (is_object($result) === true){
                    echo json_encode($result);
                    return true;
                }else{
                    echo ($result !== false ? $result : 'unknown_error');
                    return false;
                }
            }
            echo 'fail';
            return false;
        }
    }
}
?>