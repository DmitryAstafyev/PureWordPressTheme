<?php
namespace Pure\Components\webSocketServer\Module{
    class Connections{
        private $connections;
        private $sockets;
        private $index;
        private $master_index;
        private $settings;
        private $uniqid;
        function __construct($uniqid, $settings){
            $this->initDefaults ($uniqid, $settings);
        }
        private function initDefaults($uniqid, $settings){
            $this->connections  = array();
            $this->sockets      = array();
            $this->index        = 0;
            $this->master_index = -1;
            $this->uniqid       = $uniqid;
            $this->settings     = $settings;
        }
        private function getIndex(){
            $this->index ++;
            return $this->index;
        }
        public function add($parameters){
            $index                                  = $this->getIndex();
            $this->sockets[$index]                  = $parameters->socket;
            $this->connections[$index]              = (object)array(
                'uniqid'    =>$index,
                'index'     =>$index,
                'master'    =>(isset($parameters->master) === true ? $parameters->master : false),
                'instance'  =>new \Pure\Components\webSocketServer\Module\Connection(
                    $this->uniqid,
                    $index,
                    $this->settings
                ),
            );
            $this->connections[$index]->instance->setSocket($parameters->socket);
            if ((isset($parameters->master) === true ? $parameters->master : false) === true){
                $this->master_index = $index;
            }
            return $this->connections[$index];
        }
        public function remove($connection){
            $this->connections[$connection->index]->instance->setAvailable(false);
            $this->log("[CONNECTIONS]:: A connection instance is killed (connection #".$this->connections[$connection->index]->uniqid.")", "OK");
            $this->connections[$connection->index]->instance = NULL;
            unset($this->connections[$connection->index]->instance  );
            unset($this->connections[$connection->index]            );
            unset($this->sockets    [$connection->index]            );
        }
        public function getBySocket($socket){
            foreach($this->connections as $connection){
                if ($connection->instance->getSocket() == $socket){
                    return $connection;
                }
            }
            return false;
        }
        public function getByUserID($userID){
            $connections = array();
            foreach($this->connections as $connection){
                if ((int)$connection->instance->getUserID() === (int)$userID){
                    $connections[] = $connection;
                }
            }
            return $connections;
        }
        public function getByIndex($index){
            return (isset($this->connections[$index]) === true ? $this->connections[$index] : false);
        }
        public function hasHandshake($connection){
            return $connection->instance->getHandshake();
        }
        public function setHandshake($connection){
            $connection->instance->setHandshake(true);
        }
        public function getSockets(){
            return $this->sockets;
        }
        public function getMasterSocket(){
            if ($this->master_index !== -1){
                return $this->sockets[$this->master_index];
            }
        }
        public function isAvailable($connection){
            return $connection->instance->getAvailable();
        }
        public function getSocket($connection){
            return $connection->instance->getSocket();
        }
        public function getUserID($connection){
            return $connection->instance->getUserID();
        }
        public function processing($connection, $data){
            $this->log("[CONNECTIONS]:: Get command [".$data->group."][".$data->command."] (connection #".$connection->uniqid.")");
            $this->action($connection, $data->group, $data->command, $data);
        }
        public function action($connection, $group, $action, $parameters = ''){
            switch($group){
                case 'auth':
                    switch($action){
                        case 'send require':
                            $SendRequire = new \Pure\Components\webSocketServer\Module\Jobs\Auth\SendRequire($connection->instance);
                            $SendRequire->run();
                            $SendRequire = NULL;
                            $this->log("[CONNECTIONS]:: Sent request for authorization (connection #".$connection->uniqid.")");
                            break;
                        case 'authorization':
                            $GetRequire = new \Pure\Components\webSocketServer\Module\Jobs\Auth\GetRequire($parameters, $connection->instance);
                            $GetRequire->run();
                            $GetRequire = NULL;
                            $this->log("[CONNECTIONS]:: Request for authorization was processed (connection #".$connection->uniqid.")");
                            break;
                    }
                    break;
                case 'actions':
                    switch($action) {
                        case 'wakeup':
                            $this->wakeupHandle($connection);
                            break;
                    }
                    break;
            }
        }
        private function wakeupHandle($connection){
            $this->log("[CONNECTIONS]:: Get wakeup command from connection #" . $connection->uniqid);
            if ((int)$connection->instance->getUserID() > 0){
                $this->log("[CONNECTIONS]:: Start wakeup from connection #" . $connection->uniqid);
                \Pure\Components\webSocketServer\Events\Initialization::instance()->attach(true);
                $WebSocketEvents    = new \Pure\Components\webSocketServer\Events\Events();
                $Encoding           = new \Pure\Components\webSocketServer\Common\Encoding();
                $events             = $WebSocketEvents->get();
                if ($events !== false){
                    if (count($events) > 0){
                        $eventsIDs = array();
                        foreach($events as $event){
                            $eventsIDs[] = (int)$event->id;
                        }
                        $WebSocketEvents->remove($eventsIDs);
                        $index      = 0;
                        $total      = count($eventsIDs);
                        $eventsIDs  = NULL;
                        foreach($events as $event){
                            $activeConnections = $this->getByUserID($event->recipient);
                            if (count($activeConnections) > 0){
                                $this->log("[CONNECTIONS]:: [WAKEUP]:: Found ".count($activeConnections)." clients of user #".$event->recipient." events (connection #" . $connection->uniqid.")");
                                foreach($activeConnections as $activeConnection){
                                    if ($activeConnection->instance->getAvailable() !== false){
                                        $package    = (object)array(
                                            'group'     =>'events',
                                            'command'   =>'event',
                                            'event'     =>$event->event,
                                            'recipient' =>$event->recipient,
                                            'parameters'=>unserialize($event->parameters)
                                        );
                                        $package    = json_encode($package);
                                        $package    = $Encoding->encode($package);
                                        if(socket_write($activeConnection->instance->getSocket(), $package, strlen($package)) === false) {
                                            $activeConnection->instance->setAvailable(false);
                                        }
                                        $index ++;
                                    }
                                }
                                $activeConnections = NULL;
                            }
                        }
                        $this->log("[CONNECTIONS]:: [WAKEUP]:: Sent ".$index." notifications from ".$total." events (connection #" . $connection->uniqid.")");
                    }
                }
                $WebSocketEvents    = NULL;
                $Encoding           = NULL;
                $this->log("[CONNECTIONS]:: Finish wakeup from connection #" . $connection->uniqid);
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
    }
}
?>