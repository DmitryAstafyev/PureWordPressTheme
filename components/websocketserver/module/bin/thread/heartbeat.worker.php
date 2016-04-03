<?php
namespace Pure\Components\webSocketServer\Module {
    class HeartbeatWorker extends \Worker{
        private     $uniqid;
        private     $settings;
        protected   $stack;
        function __construct($uniqid, $stack, $settings){
            $this->uniqid   = $uniqid;
            $this->settings = $settings;
            $this->stack    = $stack;
        }
        private function resources(){
            require_once(\Pure\Components\webSocketServer\Paths::instance()->dir(substr(__DIR__, 0, (stripos(__DIR__, 'websocketserver') - 1)).'/websocketserver/module/bin/thread/resources.php'));
            $Resources = new \Pure\Components\webSocketServer\Module\Resources((object)array(
                'uniqid'    =>$this->uniqid,
                'caller'    =>'HEARTBEATWORKER',
                'classes'   =>array(
                    'Heartbeat'         =>true,
                    'HeartbeatStack'    =>true,
                )
            ));
            $Resources->attach();
            $Resources = NULL;
        }
        public function run(){
            $this->resources();
            $Heartbeat = new \Pure\Components\webSocketServer\Module\Heartbeat($this->uniqid, $this->settings);
            $Heartbeat->proceed($this->stack);
        }
        public function isAlive(){
            return $this->stack->get();
        }
    }
}
?>