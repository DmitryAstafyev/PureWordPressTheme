<?php
namespace Pure\Components\webSocketServer\Module\Jobs\Auth {
    class SendRequire {
        private $instance;
        public function __construct(&$instance){
            $this->instance = &$instance;
        }
        public function run(){
            if ($this->instance->getAvailable() !== false){
                $package    = (object)array(
                    'group'     =>'auth',
                    'command'   =>'require'
                );
                $package    = json_encode($package);
                $Encoding   = new \Pure\Components\webSocketServer\Common\Encoding();
                $package    = $Encoding->encode($package);
                if(socket_write($this->instance->getSocket(), $package, mb_strlen($package)) === false) {
                    $this->instance->setAvailable(false);
                }
                $Encoding   = NULL;
            }
            //echo "\r\n"."\r\n"."POINT IN STACK"."\r\n"."\r\n";
        }
    }
}
?>