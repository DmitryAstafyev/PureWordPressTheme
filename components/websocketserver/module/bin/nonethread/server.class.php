<?php
namespace Pure\Components\webSocketServer\Module{
    class Server {
        private $uniqid;
        private $settings;
        private $connections;
        function __construct($uniqid = '') {
            ob_implicit_flush();
            $this->initUniqid           ($uniqid);
            $this->initResources        ();
            $this->initSettings         ();
            $this->initConnections      ();
            if ($this->createMasterSocket() !== true) {
                throw new \Exception("Cannot start server. See logs.", E_USER_WARNING);
            }
            $this->log("[SERVER]:: Created", "OK");
        }
        private function initUniqid($uniqid){
            $this->uniqid = ($uniqid !== '' ? $uniqid : uniqid());
        }
        private function initResources(){
            require_once(\Pure\Components\webSocketServer\Paths::instance()->dir(substr(__DIR__, 0, (stripos(__DIR__, 'websocketserver') - 1)).'/websocketserver/module/bin/nonethread/resources.php'));
            $Resources = new \Pure\Components\webSocketServer\Module\Resources((object)array(
                'uniqid'    =>$this->uniqid,
                'caller'    =>'SERVER',
                'classes'   =>array(
                    'Settings'          =>true,
                    'Logs'              =>true,
                    'Pulse'             =>true,
                    'Encoding'          =>true,
                    'Heartbeat'         =>true,
                    'Connection'        =>true,
                    'Connections'       =>true,
                    'Token'             =>true,
                    'WordPress'         =>true,
                    //Jobs
                    'GetRequire'        =>true,
                    'SendRequire'       =>true,
                )
            ));
            $Resources->attach();
            $Resources = NULL;
        }
        private function initSettings(){
            $Settings           = new \Pure\Components\webSocketServer\Common\Settings($this->uniqid);
            $this->settings     = $Settings->get();
            $Settings           = NULL;
        }
        private function initConnections(){
            $this->connections  = new \Pure\Components\webSocketServer\Module\Connections($this->uniqid, $this->settings);
        }
        private function createMasterSocket() {
            $this->log("[CREATING]:: Try create server by: [".$this->settings->address.":".$this->settings->port."]", "");
            $master_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if ($master_socket === false) {
                return $this->log("[socket_create]:: Cannot create socket. (" . socket_strerror(socket_last_error()) . ")", "ERROR");
            } else {
                $this->log("[socket_create]::\t done", "OK");
            }
            if (socket_set_option($master_socket, SOL_SOCKET, SO_REUSEADDR, 1) === false) {
                return $this->log("[socket_set_option]:: Cannot set socket option.", "ERROR");
            } else {
                $this->log("[socket_set_option]::\t done", "OK");
            }
            if (socket_set_nonblock($master_socket) === false) {
                return $this->log("[socket_set_nonblock]:: Cannot set nonblock option.", "ERROR");
            } else {
                $this->log("[socket_set_nonblock]::\t done", "OK");
            }
            if (socket_bind($master_socket, $this->settings->address, $this->settings->port) === false) {
                return $this->log("[socket_bind]:: Cannot bind socket. (" . socket_strerror(socket_last_error()) . ")", "ERROR");
            } else {
                $this->log("[socket_bind]::\t\t done", "OK");
            }
            if (socket_listen($master_socket, $this->settings->backlog) === false) {
                return $this->log("[socket_listen]:: Cannot attach listener. (" . socket_strerror(socket_last_error()) . ")", "ERROR");
            } else {
                $this->log("[socket_listen]::\t done", "OK");
            }
            $this->connections->add((object)array(
                'socket' => $master_socket,
                'master' => true
            ));
            $this->log("[SERVER]:: Initialized on ws://" . $this->settings->address . ":" . $this->settings->port, "OK");
            return true;
        }
        private function connect($socket) {
            $this->log("[SERVER]:: Creating client...");
            $connection = $this->connections->add((object)array(
                "socket" => $socket
            ));
            $this->log("[SERVER]:: Client #" . $connection->uniqid. " is successfully created!", "OK");
        }
        private function disconnect($connection) {
            $this->registerLogout($connection);
            $this->log("[SERVER]:: Disconnecting client #" . $connection->uniqid);
            socket_shutdown($this->connections->getSocket($connection), 2);
            socket_close($this->connections->getSocket($connection));
            $this->connections->remove($connection);
            $this->log("[SERVER]:: Socket of client #" . $connection->uniqid . " closed", "OK");
        }
        private function handshake($connection, $headers) {
            $this->log("[SERVER]:: Doing the handshake...");
            $this->log("[SERVER]:: Getting client WebSocket version...");
            $parameters = (object)array(
                "version"   => false,
                "root"      => false,
                "host"      => false,
                "origin"    => false,
                "key"       => false,
                "acceptKey" => false,
                "headers"   => false
            );
            if (preg_match("/Sec-WebSocket-Version: (.*)\r\n/", $headers, $match)) {
                $parameters->version = $match[1];
                $this->log("[SERVER]:: Client WebSocket version is " . $parameters->version . ", (required: 13)");
                if ($parameters->version == 13) {
                    // Extract header variables
                    $this->log("[SERVER]:: Getting headers...");
                    if (preg_match("/GET (.*) HTTP/", $headers, $match)) {
                        $parameters->root = $match[1];
                    }
                    if (preg_match("/Host: (.*)\r\n/", $headers, $match)) {
                        $parameters->host = $match[1];
                    }
                    if (preg_match("/Origin: (.*)\r\n/", $headers, $match)) {
                        $parameters->origin = $match[1];
                    }
                    if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $headers, $match)) {
                        $parameters->key = $match[1];
                    }
                    $this->log("[SERVER]:: Client headers are:", "OK");
                    $this->log("\t- Root: " . $parameters->root);
                    $this->log("\t- Host: " . $parameters->host);
                    $this->log("\t- Origin: " . $parameters->origin);
                    $this->log("\t- Sec-WebSocket-Key: " . $parameters->key);
                    $this->log("[SERVER]:: Generating Sec-WebSocket-Accept key...");
                    $parameters->acceptKey = $parameters->key . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";
                    $parameters->acceptKey = base64_encode(sha1($parameters->acceptKey, true));
                    $parameters->upgrade = "HTTP/1.1 101 Switching Protocols\r\n" .
                        "Upgrade: websocket\r\n" .
                        "Connection: Upgrade\r\n" .
                        "Sec-WebSocket-Accept: " . $parameters->acceptKey .
                        "\r\n\r\n";
                    $this->log("[SERVER]:: Sending this response to the client #" . $connection->uniqid . ":\r\n" . $parameters->upgrade);
                    socket_write($this->connections->getSocket($connection), $parameters->upgrade);
                    $this->connections->setHandshake($connection);
                    $this->log("[SERVER]:: Handshake is successfully done!", "OK");
                    return true;
                } else {
                    $this->log("[SERVER]:: WebSocket version 13 required (the client supports version " . $parameters->version . ")", "ERROR");
                    return false;
                }
            } else {
                $this->log("[SERVER]:: The client does not support WebSocket", "ERROR");
                return false;
            }
        }
        public function proceed(){
            $Heartbeat = new \Pure\Components\webSocketServer\Module\Heartbeat(
                $this->uniqid,
                $this->settings
            );
            $Heartbeat->init();
            $this->log("[".$this->uniqid."][SERVER]:: Started", "OK");
            $master = $this->connections->getMasterSocket();
            while(true){
                $Heartbeat->proceed();
                if ($Heartbeat->isAlive() !== false){
                    $sockets                = $this->connections->getSockets();
                    $living_sockets_count   = @socket_select($sockets, $write = NULL, $except = NULL, 1);
                    if ($living_sockets_count !== false){
                        if ($living_sockets_count > 0){
                            foreach($sockets as $socket){
                                if ($Heartbeat->isAlive() !== false) {
                                    if ($socket == $master){
                                        $connection = socket_accept($master);
                                        if($connection === false) {
                                            $this->log("[".$this->uniqid."][SERVER]:: Socket error: ".socket_strerror(socket_last_error($master)), "ERROR");
                                        }elseif($connection < 0){
                                            $this->log("[".$this->uniqid."][socket_accept]:: Socket error: ".socket_strerror(socket_last_error($connection)), "ERROR");
                                        }elseif($connection > 0){
                                            $this->connect($connection);
                                        }
                                    }else{
                                        $this->log("[".$this->uniqid."][SERVER]:: Finding the socket. Try find associated client.");
                                        $connection = $this->connections->getBySocket($socket);
                                        if ($connection !== false){
                                            $this->log("[".$this->uniqid."][SERVER]:: Associated client is found. Reading data...");
                                            if ($this->connections->isAvailable($connection) !== false){
                                                $socket_data    = "";
                                                $buffer         = "";
                                                $package        = 0;
                                                $this->log("[".$this->uniqid."][SERVER]:: [Start]:: reading data...");
                                                while(true){
                                                    $bytes_count = @socket_recv($this->connections->getSocket($connection), $buffer, 1024, 0);
                                                    if (is_int($bytes_count) === true){
                                                        if(is_null($buffer) === false){
                                                            $socket_data .= $buffer;
                                                        }else{ break; }
                                                        if ($bytes_count <= 1024) { break; }
                                                    }else{ break; }
                                                    $package ++;
                                                }
                                                $this->log("[".$this->uniqid."][SERVER]:: [Finish]:: reading data...");
                                                $buffer = NULL;
                                                if (mb_strlen($socket_data) > 0){
                                                    $this->log("[".$this->uniqid."][SERVER]:: Received ".mb_strlen($socket_data)." bytes in ".$package." package(s).", "OK");
                                                    if ($this->connections->hasHandshake($connection) === false){
                                                        if ($this->handshake($connection, $socket_data) === true){
                                                            $this->connections->action(
                                                                $connection,
                                                                'auth',
                                                                'send require',
                                                                ''
                                                            );
                                                        }
                                                    }else{
                                                        $this->log("[".$this->uniqid."][SERVER]:: In Action area.");
                                                        $_socket_data = $this->validateSocketData($connection, $socket_data);
                                                        if ($_socket_data !== false && $_socket_data !== true){
                                                            $this->connections->processing($connection, $_socket_data);
                                                        }else if($_socket_data === true){
                                                            $this->log("[".$this->uniqid."][SERVER]:: I guess it is PING from lovely IE (connection #".$connection->uniqid.")", "PING");
                                                        }else if($_socket_data === false){
                                                            $this->log("[".$this->uniqid."][SERVER]:: Disconnect (connection #".$connection->uniqid.")", "ERROR");
                                                            $this->disconnect($connection);
                                                        }
                                                    }
                                                }else{
                                                    $this->log("[".$this->uniqid."][SERVER]:: In Disconnect area.");
                                                    $this->disconnect($connection);
                                                }
                                            }else{
                                                $this->disconnect($connection);
                                            }
                                        }else{
                                            $this->log("[".$this->uniqid."][SERVER]:: No associated client.");
                                        }
                                    }
                                }else{
                                    $this->log("[".$this->uniqid."][SERVER]:: Server will be stopped.");
                                    $Heartbeat = NULL;
                                    return;//Heart stops. New life was born
                                }
                            }
                        }
                    }else{
                        $this->log("[".$this->uniqid."][socket_select]:: Error handling. (".socket_strerror(socket_last_error()).")", "ERROR");
                        $Heartbeat = NULL;
                        return;
                    }
                }else{
                    $this->log("[".$this->uniqid."][SERVER]:: Server will be stopped.");
                    $Heartbeat = NULL;
                    return;//Heart stops. New life was born
                }
            }
            $Heartbeat = NULL;
        }
        private function validateSocketData($connection, $socketData){
            try{
                $Encoding   = new \Pure\Components\webSocketServer\Common\Encoding();
                $data       = $Encoding->unmask($socketData);
                $Encoding   = NULL;
                $_data      = json_decode($data);
                if (isset($_data->group) === true && isset($_data->command) === true){
                    return $_data;
                }else{
                    //$this->log("[".$this->uniqid."][SERVER]:: Get unknown package [".$data."] (connection #".$connection->uniqid.")", "ERROR");
                    return true;
                }
            }catch (\Exception $e){
                $this->log("[".$this->uniqid."][SERVER]:: Error during parsing package (connection #".$connection->uniqid.")", "ERROR");
                return false;
            }
        }
        private function registerLogout($connection){
            $user_id = $this->connections->getUserID($connection);
            if ($user_id !== false){
                $this->log("[SERVER]:: BEGIN:: Registration of logout client #" . $connection->uniqid);
                \Pure\Components\WordPress\LastLogin\Initialization::instance()->attach(true);
                $LastLogin = new \Pure\Components\WordPress\LastLogin\Provider();
                $LastLogin->update();
                $LastLogin = NULL;
                $this->log("[SERVER]:: END:: Registration of logout #" . $connection->uniqid);
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