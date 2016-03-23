<?php
namespace Pure\Components\webSocketServer\Module{
    class ConnectionWorker extends \Worker{
        private $server_id;
        private $connection_id;
        private $settings;
        private $available;
        private $handshake;
        private $socket;
        private $stacks;
        private $accepted;
        private $user_id;
        function __construct($server_id, $connection_id, $settings){
            $this->initDefaults($server_id, $connection_id, $settings);
        }
        public function initDefaults($server_id, $connection_id, $settings){
            $this->server_id        = $server_id;
            $this->connection_id    = $connection_id;
            $this->settings         = $settings;
            $this->available        = true;
            $this->handshake        = false;
            $this->socket           = false;
            $this->stacks           = array();
            $this->accepted         = false;
            $this->user_id          = false;
        }
        public function setHandshake($value){
            $this->handshake = $value;
        }
        public function getHandshake(){
            return $this->handshake;
        }
        public function setAvailable($value){
            $this->available = $value;
        }
        public function getAvailable(){
            return $this->available;
        }
        public function setSocket($socket){
            $this->socket = $socket;
        }
        public function getSocket(){
            return $this->socket;
        }
        public function setAccept($accepted){
            $this->accepted = $accepted;
        }
        public function getAccept(){
            return $this->accepted;
        }
        public function setUserID($user_id){
            $this->user_id = $user_id;
        }
        public function getUserID(){
            return $this->user_id;
        }
        public function getConnectionID(){
            return $this->connection_id;
        }
        public function getSettings(){
            return $this->settings;
        }
        public function run(){
        }
    }
}
?>