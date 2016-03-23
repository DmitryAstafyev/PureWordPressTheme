<?php
namespace Pure\Components\webSocketServer\Module {
    class Heartbeat{
        private $uniqid;
        private $settings;
        function __construct($uniqid, $settings){
            $this->uniqid   = $uniqid;
            $this->settings = $settings;
            $this->resources();
        }
        private function resources(){
            require_once(\Pure\Configuration::instance()->dir(substr(__DIR__, 0, (stripos(__DIR__, 'websocketserver') - 1)).'/websocketserver/module/bin/thread/resources.php'));
            $Resources = new \Pure\Components\webSocketServer\Module\Resources((object)array(
                'uniqid'    =>$this->uniqid,
                'caller'    =>'HEARTBEAT',
                'classes'   =>array(
                    'Logs'      =>true,
                    'Pulse'     =>true,
                ),
                'settings'  =>$this->settings
            ));
            $Resources->attach();
            $Resources = NULL;
        }
        public function proceed(&$stack){
            $this->log("[".$this->uniqid."][HEARTBEAT]:: Heartbeat was started.");
            $Pulse = new \Pure\Components\webSocketServer\Common\Pulse($this->uniqid);
            $Pulse->clear();
            $Pulse->register($this->uniqid);
            sleep(round($this->settings->heartbeat_timeout));
            $iteration = 0;
            while(true){
                $this->memory($iteration);
                $state = $Pulse->refresh($this->uniqid);
                if ($state !== false){
                    sleep($this->settings->heartbeat_timeout);
                }else{
                    $this->log("[".$this->uniqid."][HEARTBEAT]:: will be stopped");
                    $stack->set(false);
                    break;
                }
                $iteration ++;
                if ($this->settings->heartbeat_interations < $iteration){
                    $this->log("[".$this->uniqid."][HEARTBEAT]:: Heartbeat made ".($iteration - 1)." iterations and will be stopped");
                    $stack->set(false);
                    break;
                }
            }
            $Pulse = NULL;
            $this->log("[".$this->uniqid."][HEARTBEAT]:: Heartbeat was stopped.");
        }
        private function log($message, $status = ""){
            if (class_exists('\Pure\Components\webSocketServer\Common\Logs') !== false){
                if (isset($this->logs_instance) === false){
                    $this->logs_instance = new \Pure\Components\webSocketServer\Common\Logs($this->uniqid, $this->settings);
                }
                $this->logs_instance->log($message, $status);
            }
        }
        private function memory($iteration){
            if ($this->settings->show_memoryusage_with_heartbeat === 'on'){
                $this->last = (isset($this->last) === true ? $this->last : 0);
                $total      = round(memory_get_usage(true)/1024);
                $changes    = round(($total - $this->last)/1024);
                $this->last = $total;
                $this->log( "[".$this->uniqid."][HEARTBEAT]:: iteration #".
                            $iteration. "\t Memory total:: ".
                            $total." kB \t changes:: ".
                            $changes." kB", 'MEMORY'
                );
            }
            gc_collect_cycles();
        }
    }
}
?>