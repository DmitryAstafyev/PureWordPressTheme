<?php
namespace Pure\Components\webSocketServer\Module {
    class Heartbeat{
        private $uniqid;
        private $settings;
        private $alive;
        private $iteration;
        private $last_pulse;
        private $Pulse;
        function __construct($uniqid, $settings){
            $this->uniqid       = $uniqid;
            $this->settings     = $settings;
            $this->alive        = true;
            $this->iteration    = 0;
            $this->last_pulse   = 0;
        }
        private function stop(){
            $this->alive = false;
            $this->Pulse = NULL;
        }
        public function isAlive(){
            return $this->alive;
        }
        public function init(){
            $this->log("[".$this->uniqid."][HEARTBEAT]:: Heartbeat was started.");
            $this->Pulse = new \Pure\Components\webSocketServer\Common\Pulse($this->uniqid);
            $this->Pulse->clear();
            $this->Pulse->register($this->uniqid);
            $this->last_pulse = time();
        }
        public function proceed(){
            if ($this->isAlive() !== false){
                $time = time();
                if (($time - $this->last_pulse) > (int)$this->settings->heartbeat_timeout){
                    $this->last_pulse   = $time;
                    $state              = $this->Pulse->refresh($this->uniqid);
                    //echo var_dump($this->uniqid);
                    $this->memory();
                    if ($state === false){
                        $this->log("[".$this->uniqid."][HEARTBEAT]:: will be stopped");
                        $this->stop();
                        $this->log("[".$this->uniqid."][HEARTBEAT]:: Heartbeat was stopped.");
                    }
                    $this->iteration ++;
                    if ($this->settings->heartbeat_interations < $this->iteration){
                        $this->log("[".$this->uniqid."][HEARTBEAT]:: Heartbeat made ".($this->iteration - 1)." iterations and will be stopped");
                        $this->stop();
                        $this->log("[".$this->uniqid."][HEARTBEAT]:: Heartbeat was stopped.");
                    }
                }
            }
        }
        private function log($message, $status = ""){
            if (class_exists('\Pure\Components\webSocketServer\Common\Logs') !== false){
                if (isset($this->logs_instance) === false){
                    $this->logs_instance = new \Pure\Components\webSocketServer\Common\Logs($this->uniqid, $this->settings);
                }
                $this->logs_instance->log($message, $status);
            }
        }
        private function memory(){
            if ($this->settings->show_memoryusage_with_heartbeat === 'on'){
                $this->last = (isset($this->last) === true ? $this->last : 0);
                $total      = round(memory_get_usage(true)/1024);
                $changes    = round(($total - $this->last)/1024);
                $this->last = $total;
                $this->log( "[".$this->uniqid."][HEARTBEAT]:: iteration #".
                            $this->iteration. "\t Memory total:: ".
                            $total." kB \t changes:: ".
                            $changes." kB", 'MEMORY'
                );
            }
            gc_collect_cycles();
        }
    }
}
?>