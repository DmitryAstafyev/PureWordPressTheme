<?php
namespace Pure\Components\WordPress\Location\Special{
    class Core{
        private $request = false;
        private function validate($key){
            $Register = new Register();
            if (isset($Register->requests[$key]) !== false){
                $this->request = (object)array(
                    'request'       =>$key,
                    'parameters'    =>(object)array()
                );
                foreach($Register->requests[$key]['parameters'] as $parameter){
                    if (isset($_POST[$parameter]) !== true && isset($_GET[$parameter]) !== true){
                        $this->request = false;
                        $Register = NULL;
                        return false;
                    }else{
                        $this->request->parameters->$parameter = esc_sql((isset($_POST[$parameter]) === true ? $_POST[$parameter] : $_GET[$parameter]));
                    }
                }
                $Register = NULL;
                return true;
            }
            $Register = NULL;
            return false;
        }
        private function getRequestKey(){
            $parts = preg_split('/\//', strtoupper(preg_replace('/\?.*/', '', $_SERVER["REQUEST_URI"])));
            $parts = array_values(array_filter( $parts, function($item){ return ($item !== '' ? true : false);} ));
            if (count($parts) === 2){
                $Register = new Register();
                if ($parts[0] === $Register->root && count($parts) === 2){
                    $Register = NULL;
                    return $parts[1];
                }
                $Register = NULL;
            }
            return false;
        }
        public function is(){
            return ($this->request !== false ? true : false);
        }
        public function getRequest(){
            return $this->request;
        }
        function __construct(){
            $this->validate(
                $this->getRequestKey()
            );
        }
    }
}
?>