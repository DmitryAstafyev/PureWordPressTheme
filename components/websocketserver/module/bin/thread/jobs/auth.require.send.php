<?php
namespace Pure\Components\webSocketServer\Module\Jobs\Auth {
    class SendRequire extends \Stackable {
        public $stack_uniqid;
        public function __construct($stack_uniqid){
            $this->stack_uniqid = $stack_uniqid;
        }
        public function initResources($connection_id, $settings){
            require_once(\Pure\Components\webSocketServer\Paths::instance()->dir(substr(__DIR__, 0, (stripos(__DIR__, 'websocketserver') - 1)).'/websocketserver/module/bin/thread/resources.php'));
            $Resources = new \Pure\Components\webSocketServer\Module\Resources((object)array(
                'uniqid'    =>$connection_id,
                'caller'    =>'CONNECTION-JOB',
                'classes'   =>array(
                    'Encoding'          =>true,
                ),
                'settings'  =>$settings
            ));
            $Resources->attach();
            $Resources = NULL;
        }
        public function run(){
            if ($this->worker){
                $this->initResources($this->worker->getConnectionID(), $this->worker->getSettings());
                if ($this->worker->getAvailable() !== false){
                    $package    = (object)array(
                        'group'     =>'auth',
                        'command'   =>'require'
                    );
                    $package    = json_encode($package);
                    $Encoding   = new \Pure\Components\webSocketServer\Common\Encoding();
                    $package    = $Encoding->encode($package);
                    if(socket_write($this->worker->getSocket(), $package, mb_strlen($package)) === false) {
                        $this->worker->setAvailable(false);
                    }
                    $Encoding   = NULL;
                }
            }
            //echo "\r\n"."\r\n"."POINT IN STACK"."\r\n"."\r\n";
            $this->worker->notify();
        }
    }
}
?>