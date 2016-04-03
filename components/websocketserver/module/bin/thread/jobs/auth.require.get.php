<?php
namespace Pure\Components\webSocketServer\Module\Jobs\Auth{
    class GetRequire extends \Stackable {
        public $parameters;
        public function __construct($parameters){
            $this->parameters = $parameters;
        }
        public function initResources($connection_id, $settings){
            require_once(\Pure\Components\webSocketServer\Paths::instance()->dir(substr(__DIR__, 0, (stripos(__DIR__, 'websocketserver') - 1)).'/websocketserver/module/bin/thread/resources.php'));
            $Resources = new \Pure\Components\webSocketServer\Module\Resources((object)array(
                'uniqid'    =>$connection_id,
                'caller'    =>'CONNECTION-JOB',
                'classes'   =>array(
                    'Logs'              =>true,
                    'Encoding'          =>true,
                    'WordPress'         =>true,
                    //'Token'             =>true,
                ),
                'settings'  =>$settings
            ));
            $Resources->attach();
            $Resources = NULL;
        }
        public function validate(){
            $result = is_object($this->parameters);
            $result = ($result !== false ? isset($this->parameters->user_id ) : false);
            $result = ($result !== false ? isset($this->parameters->token   ) : false);
            return $result;
        }
        public function run(){
            if ($this->worker){
                $this->initResources($this->worker->getConnectionID(), $this->worker->getSettings());
                if ($this->worker->getAvailable() !== false){
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
                            $this->worker->setAvailable(false);
                        }
                    }
                    $Encoding   = new \Pure\Components\webSocketServer\Common\Encoding();
                    $_package   = json_encode($package);
                    $_package   = $Encoding->encode($_package);
                    $Encoding   = NULL;
                    if(socket_write($this->worker->getSocket(), $_package, mb_strlen($_package)) === false) {
                        $this->worker->setAvailable(false);
                    }
                    //Save data
                    if ($package->command === 'accept'){
                        $this->worker->setAccept(true);
                        $this->worker->setUserID((int)$this->parameters->user_id);
                        $this->log(
                            $this->worker->getConnectionID(),
                            $this->worker->getSettings(),
                            '[AUTHORIZATION] User ID'.(int)$this->parameters->user_id.' was accepted',
                            'ACCEPT'
                        );
                    }else{
                        $this->worker->setAccept(false);
                        $this->worker->setUserID(false);
                        $this->log(
                            $this->worker->getConnectionID(),
                            $this->worker->getSettings(),
                            '[AUTHORIZATION] User ID'.(int)$this->parameters->user_id.' was not accepted',
                            'DENY'
                        );
                    }
                }
            }
            //echo "\r\n"."\r\n"."POINT IN STACK"."\r\n"."\r\n";
            $this->worker->notify();
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