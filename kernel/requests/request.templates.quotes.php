<?php
namespace Pure\Requests\Templates\Quotes{
    class Quotes{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'remove':
                    $parameters->user_id    = (integer)($parameters->user_id    );
                    $parameters->quote_id   = (integer)($parameters->quote_id   );
                    return true;
                case 'state':
                    $parameters->user_id    = (integer)($parameters->user_id    );
                    $parameters->quote_id   = (integer)($parameters->quote_id   );
                    return true;
                case 'add':
                    $parameters->user_id    = (integer)($parameters->user_id    );
                    $parameters->quote      = (string)($parameters->quote       );
                    return true;
                case 'import':
                    $parameters->user_id    = (integer)($parameters->user_id    );
                    $parameters->quote_id   = (integer)($parameters->quote_id   );
                    return true;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'add':
                    $parameters->quote = sanitize_text_field($parameters->quote);
                    break;
            }
        }
        public function remove($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $current            = $WordPress->get_current_user();
                $WordPress          = NULL;
                if ($current !== false) {
                    if ((int)$current->ID === (int)$parameters->user_id) {
                        \Pure\Components\BuddyPress\Quotes\Initialization::instance()->attach();
                        $Quotes = new \Pure\Components\BuddyPress\Quotes\Core();
                        $result = $Quotes->remove((object)array(
                            'user'      =>(int)$parameters->user_id,
                            'quote_id'  =>(int)$parameters->quote_id,
                        ));
                        $Quotes = NULL;
                        if ($result === true){
                            echo 'removed';
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
            echo 'fail';
            return false;
        }
        public function state($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $current            = $WordPress->get_current_user();
                $WordPress          = NULL;
                if ($current !== false) {
                    if ((int)$current->ID === (int)$parameters->user_id) {
                        \Pure\Components\BuddyPress\Quotes\Initialization::instance()->attach();
                        $Quotes = new \Pure\Components\BuddyPress\Quotes\Core();
                        $quote  = $Quotes->getByID((object)array( 'id'  =>(int)$parameters->quote_id ));
                        if ($quote !== false){
                            $result = $Quotes->state((object)array(
                                'user'      =>(int)$parameters->user_id,
                                'quote_id'  =>(int)$parameters->quote_id,
                                'active'    =>((boolean)$quote->active === true ? false : true)
                            ));
                            if ($result !== false){
                                echo ((boolean)$quote->active === true ? 'deactivated' : 'activated');
                                return true;
                            }
                        }
                        echo 'error_during_updating';
                        return false;
                    }
                }
                echo 'incorrect_user_data';
                return false;
            }
            echo 'fail';
            return false;
        }
        public function add($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $this->sanitize($parameters, __METHOD__);
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $current            = $WordPress->get_current_user();
                $WordPress          = NULL;
                if ($current !== false) {
                    if ((int)$current->ID === (int)$parameters->user_id) {
                        if (mb_strlen(preg_replace("/\s/", '', $parameters->quote)) < 5){
                            echo 'less_5';
                            return false;
                        }
                        if (mb_strlen(preg_replace("/\s/", '', $parameters->quote)) > 500){
                            echo 'more_500';
                            return false;
                        }
                        \Pure\Components\BuddyPress\Quotes\Initialization::instance()->attach();
                        $Quotes = new \Pure\Components\BuddyPress\Quotes\Core();
                        $result = $Quotes->add((object)array(
                            'user'      =>(int)$parameters->user_id,
                            'quote'     =>$parameters->quote,
                            'active'    =>true
                        ));
                        if ($result !== false){
                            $result = $Quotes->getByID((object)array(
                                'id' =>(int)$result
                            ));
                            $Quotes = NULL;
                            if ($result !== false){
                                echo json_encode((object)array(
                                    'status'        =>'added',
                                    'quote_id'      =>$result->id,
                                    'quote'         =>$result->quote,
                                    'date_created'  =>$result->date_created,
                                    'user_name'     =>$result->user_name,
                                    'active'        =>$result->active
                                ));
                                return true;
                            }
                        }
                        $Quotes = NULL;
                        echo 'error_during_creation';
                        return false;
                    }
                }
                echo 'incorrect_user_data';
                return false;
            }
            echo 'fail';
            return false;
        }
        public function import($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $this->sanitize($parameters, __METHOD__);
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $current            = $WordPress->get_current_user();
                $WordPress          = NULL;
                if ($current !== false) {
                    if ((int)$current->ID === (int)$parameters->user_id) {
                        \Pure\Components\BuddyPress\Quotes\Initialization::instance()->attach();
                        $Quotes = new \Pure\Components\BuddyPress\Quotes\Core();
                        $result = $Quotes->import((object)array(
                            'user_id'   =>(int)$parameters->user_id,
                            'quote_id'  =>(int)$parameters->quote_id,
                            'active'    =>true
                        ));
                        if ($result !== false){
                            $result = $Quotes->getByID((object)array(
                                'id' =>(int)$result
                            ));
                            $Quotes = NULL;
                            if ($result !== false){
                                echo json_encode((object)array(
                                    'status'        =>'imported',
                                    'quote_id'      =>$result->id,
                                    'quote'         =>$result->quote,
                                    'date_created'  =>$result->date_created,
                                    'user_name'     =>$result->user_name,
                                    'active'        =>$result->active
                                ));
                                return true;
                            }
                        }
                        $Quotes = NULL;
                        echo 'error_during_creation';
                        return false;
                    }
                }
                echo 'incorrect_user_data';
                return false;
            }
            echo 'fail';
            return false;
        }
    }
}
?>