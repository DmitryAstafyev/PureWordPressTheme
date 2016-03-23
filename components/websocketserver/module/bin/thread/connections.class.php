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
            $this->initResources();
        }
        private function initDefaults($uniqid, $settings){
            $this->connections  = array();
            $this->sockets      = array();
            $this->index        = 0;
            $this->master_index = -1;
            $this->uniqid       = $uniqid;
            $this->settings     = $settings;
        }
        private function initResources(){
            require_once(\Pure\Configuration::instance()->dir(substr(__DIR__, 0, (stripos(__DIR__, 'websocketserver') - 1)).'/websocketserver/module/bin/thread/resources.php'));
            $Resources = new \Pure\Components\webSocketServer\Module\Resources((object)array(
                'uniqid'    =>$this->uniqid,
                'caller'    =>'CONNECTIONS-COLLECTOR',
                'classes'   =>array(
                    'Logs'              =>true,
                    'Encoding'          =>true,
                    'ConnectionWorker'  =>true,
                    'WordPress'         =>true,
                    //Jobs
                    'GetRequire'        =>true,
                    'SendRequire'       =>true,
                ),
                'settings'  =>$this->settings
            ));
            $Resources->attach();
            $Resources = NULL;
        }
        private function getIndex(){
            $this->index ++;
            return $this->index;
        }
        public function add($parameters){
            $index                              = $this->getIndex();
            $this->sockets[$index]              = $parameters->socket;
            $this->connections[$index]          = new \stdClass();
            $this->connections[$index]->uniqid  = $index;
            $this->connections[$index]->index   = $index;
            $this->connections[$index]->master  = (isset($parameters->master) === true ? $parameters->master : false);
            $this->connections[$index]->stacks  = array();
            $this->connections[$index]->worker  = new ConnectionWorker(
                $this->uniqid,
                $index,
                $this->settings
            );
            $this->connections[$index]->worker->setSocket($parameters->socket);
            if ((isset($parameters->master) === true ? $parameters->master : false) === true){
                $this->master_index = $index;
            }
            $this->connections[$index]->worker->start(PTHREADS_INHERIT_NONE);
            return $this->connections[$index];
        }
        public function remove($connection){
            $this->connections[$connection->index]->worker->setAvailable(false);
            if ($this->connections[$connection->index]->worker->getStacked() > 0){
                foreach($this->connections[$connection->index]->stacks as $key=>$stack){
                    $this->connections[$connection->index]->worker->unstack($this->connections[$connection->index]->stacks[$key]);
                    $this->connections[$connection->index]->stacks[$key] = NULL;
                    unset($this->connections[$connection->index]->stacks[$key]);
                }
            }
            if ($this->connections[$connection->index]->worker->shutdown() === true){
                $this->log("[CONNECTIONS]:: A connection worker is killed (connection #".$this->connections[$connection->index]->uniqid.")", "OK");
            }else{
                $this->log("[CONNECTIONS]:: A connection worker is not killed (connection #".$this->connections[$connection->index]->uniqid.")", "ERROR");
            }
            $this->connections[$connection->index]->worker = NULL;
            $this->connections[$connection->index]->stacks = NULL;
            unset($this->connections[$connection->index]->worker);
            unset($this->connections[$connection->index]->stacks);
            unset($this->connections[$connection->index]        );
            unset($this->sockets    [$connection->index]        );
        }
        public function getBySocket($socket){
            foreach($this->connections as $connection){
                if ($connection->worker->getSocket() == $socket){
                    return $connection;
                }
            }
            return false;
        }
        public function getByUserID($userID){
            $connections = array();
            foreach($this->connections as $connection){
                if ((int)$connection->worker->getUserID() === (int)$userID){
                    $connections[] = $connection;
                }
            }
            return $connections;
        }
        public function getByIndex($index){
            return (isset($this->connections[$index]) === true ? $this->connections[$index] : false);
        }
        public function hasHandshake($connection){
            return $connection->worker->getHandshake();
        }
        public function setHandshake($connection){
            $connection->worker->setHandshake(true);
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
            return $connection->worker->getAvailable();
        }
        public function getSocket($connection){
            return $connection->worker->getSocket();
        }
        public function getUserID($connection){
            return $connection->worker->getUserID();
        }
        private function addStack($connection, $stack, $stack_uniqid){
            $connection->stacks[$stack_uniqid] = $stack;
            $connection->worker->stack($stack);
            $connection->worker->synchronized(
                function($stack) use ($connection, $stack_uniqid){
                    $stack->wait();
                    $connection->worker->unstack($connection->stacks[$stack_uniqid]);
                    $connection->stacks[$stack_uniqid] = NULL;
                    unset($connection->stacks[$stack_uniqid]);
                    $this->log("[CONNECTIONS]:: Job [".$stack_uniqid."] of connection [#".$connection->uniqid."] is done", "OK");
                },
                $connection->worker
            );
        }
        public function processing($connection, $data){
            $this->log("[CONNECTIONS]:: Get command [".$data->group."][".$data->command."] (connection #".$connection->uniqid.")");
            $this->action($connection, $data->group, $data->command, $data);
        }
        public function action($connection, $group, $action, $parameters = ''){
            $stack_uniqid = uniqid();
            switch($group){
                case 'auth':
                    switch($action){
                        case 'send require':
                            $this->addStack(
                                $connection,
                                new \Pure\Components\webSocketServer\Module\Jobs\Auth\SendRequire($stack_uniqid),
                                $stack_uniqid
                            );
                            $this->log("[CONNECTIONS]:: Sent request for authorization (connection #".$connection->uniqid.")");
                            break;
                        case 'authorization':
                            $this->addStack(
                                $connection,
                                new \Pure\Components\webSocketServer\Module\Jobs\Auth\GetRequire($parameters),
                                $stack_uniqid
                            );
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
            if ((int)$connection->worker->getUserID() > 0){
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
                                    if ($activeConnection->worker->getAvailable() !== false){
                                        $package    = (object)array(
                                            'group'     =>'events',
                                            'command'   =>'event',
                                            'event'     =>$event->event,
                                            'recipient' =>$event->recipient,
                                            'parameters'=>unserialize($event->parameters)
                                        );
                                        $package    = json_encode($package);
                                        $package    = $Encoding->encode($package);
                                        if(socket_write($activeConnection->worker->getSocket(), $package, strlen($package)) === false) {
                                            $activeConnection->worker->setAvailable(false);
                                            $this->log("[CONNECTIONS]:: [WAKEUP]:: Error during [socket_write]  clients of user #".$event->recipient." events (connection #" . $connection->uniqid.")");
                                        }else{
                                            $this->log("[CONNECTIONS]:: [WAKEUP]:: Success clients of user #".$event->recipient." events (connection #" . $connection->uniqid.")");
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