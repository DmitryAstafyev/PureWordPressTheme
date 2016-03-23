<?php
namespace Pure\Components\webSocketServer\Module {
    class HeartbeatStack extends \Stackable {//Threaded
        public function __construct(){
            $this->state = true;
        }
        public function set($state){
            $this->state = $state;
        }
        public function get(){
            return $this->state;
        }
        public function run(){
            /* this particular object won't run */
        }
    }
}
?>