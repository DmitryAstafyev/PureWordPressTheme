<?php
namespace Pure\Components\webSocketServer\Module\Jobs\Auth{
    class GetRequire {
        public $parameters;
        private $instance;
        public function __construct($parameters, &$instance){
            $this->parameters   = $parameters;
            $this->instance     = &$instance;
        }
        public function validate(){
            $result = is_object($this->parameters);
            $result = ($result !== false ? isset($this->parameters->user_id     ) : false);
            $result = ($result !== false ? isset($this->parameters->token       ) : false);
            return $result;
        }
        public function run(){
            if ($this->instance->getAvailable() !== false){
                $package    = (object)array(
                    'group'     =>'auth',
                    'command'   =>'deny'
                );
                if ($this->validate() !== false){
                    if (class_exists('\Pure\Components\Token\Module\Initialization') === true){
                        \Pure\Components\Token\Module\Initialization::instance()->attach(true);
                        $Token = new \Pure\Components\Token\Module\Token();
                        if ($Token->isValid((int)$this->parameters->user_id, $this->parameters->token) !== false){
                            $package    = (object)array(
                                'group'     =>'auth',
                                'command'   =>'accept'
                            );
                        }
                        $Token = NULL;
                    }else{
                        $this->instance->setAvailable(false);
                    }
                }
                $Encoding   = new \Pure\Components\webSocketServer\Common\Encoding();
                $_package   = json_encode($package);
                $_package   = $Encoding->encode($_package);
                $Encoding   = NULL;
                if(socket_write($this->instance->getSocket(), $_package, mb_strlen($_package)) === false) {
                    $this->instance->setAvailable(false);
                }
                //Save data
                if ($package->command === 'accept'){
                    $this->instance->setAccept(true);
                    $this->instance->setUserID((int)$this->parameters->user_id);
                    $this->log(
                        $this->instance->getConnectionID(),
                        $this->instance->getSettings(),
                        '[AUTHORIZATION] User ID'.(int)$this->parameters->user_id.' was accepted',
                        'ACCEPT'
                    );
                }else{
                    $this->instance->setAccept(false);
                    $this->instance->setUserID(false);
                    $this->log(
                        $this->instance->getConnectionID(),
                        $this->instance->getSettings(),
                        '[AUTHORIZATION] User ID'.(int)$this->parameters->user_id.' was NOT accepted',
                        'DENY'
                    );
                }
            }
            //echo "\r\n"."\r\n"."POINT IN STACK"."\r\n"."\r\n";
        }
        private function log($connection_id, $setting, $message, $status = ""){
            if (class_exists('\Pure\Components\webSocketServer\Common\Logs') !== false){
                if (isset($this->logs_instance) === false){
                    $this->logs_instance = new \Pure\Components\webSocketServer\Common\Logs($connection_id, $setting);
                }
                $this->logs_instance->log($message, $status);
            }
        }
    }
}
?>