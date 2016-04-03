<?php
namespace Pure\Components\webSocketServer {
    class Paths {
        public $LPS     = '/'; //Local Path Separator. Mostly works well with unix standard [/]. But if you have a problems on win - change it on [\]
        public $root    = NULL;
        static private $self;
        static function instance() {
            if (!self::$self) {
                self::$self = new self();
            }
            return self::$self;
        }
        public function dir($path) {
            return preg_replace('/\\\{1,}|\/{1,}/', $this->LPS, $this->root . strtolower(str_replace($this->root, '', $path)));
        }
        public function url($url) {
            return strtolower($url);
        }
        function __construct() {
            $this->root = substr(__DIR__, 0, (stripos(__DIR__, 'wp-content') - 1));
        }
    }
}
namespace Pure\Components\webSocketServer\Module{
    class Launcher{
        private $uniqid;
        private $settings;
        function __construct(){
            $this->uniqid = uniqid();
        }
        private function resources(){
            require_once(\Pure\Configuration::instance()->dir(substr(__DIR__, 0, (stripos(__DIR__, 'websocketserver') - 1)).'/websocketserver/module/bin/nonethread/resources.php'));
            $Resources = new \Pure\Components\webSocketServer\Module\Resources((object)array(
                'uniqid'    =>$this->uniqid,
                'caller'    =>'LAUNCHER',
                'classes'   =>array(
                    'WordPress' =>true,
                    'Pulse'     =>true,
                    'Settings'  =>true,
                    'Logs'      =>true,
                )
            ));
            $Resources->attach();
            $Resources = NULL;
        }
        private function initSETTINGS(){
            $Settings       = new \Pure\Components\webSocketServer\Common\Settings($this->uniqid);
            $this->settings = $Settings->get();
            $Settings       = NULL;
        }
        private function log($message, $status = ""){
            if (class_exists('\Pure\Components\webSocketServer\Common\Logs') !== false){
                if (isset($this->logs_instance) === false){
                    $this->logs_instance = new \Pure\Components\webSocketServer\Common\Logs($this->uniqid, $this->settings);
                }
                $this->logs_instance->log($message, $status);
            }
        }
        private function isWorking($instances){
            $result     = false;
            if (count($instances) > 0){
                $checkpoint = strtotime("now");
                foreach($instances as $instance){
                    $pulse  = strtotime($instance->pulse);
                    $result = ($result === true ? true : ($checkpoint - $pulse > (((int)$this->settings->heartbeat_timeout) * 10) ? false : true));
                }
            }
            return $result;
        }
        private function launch($iteration){
            $this->log("[LAUNCHER]:: Start server with iteration #".$iteration);
            if (substr(php_uname(), 0, 7) == "Windows"){
                $path       = addslashes(Initialization::instance()->instance()->configuration->paths->bin.'\nonethread\initialization.php');
                $WshShell   = new \COM("WScript.Shell");
                $WshShell->Run('cmd /C php -q '.$path, 0, false);
                //Key [/C] - close CMD after command will be finished
            }else {
                $cmd = 'php -q '.Initialization::instance()->instance()->configuration->paths->bin.'/nonethread/initialization.php';
                exec($cmd." > /dev/null &");
            }
        }
        private function processing(){
            $Pulse = new \Pure\Components\webSocketServer\Common\Pulse($this->uniqid);
            $Pulse->clear();
            $iteration = 0;
            while(true){
                $instances = $Pulse->get();
                if ($instances === false){
                    $this->launch($iteration);
                    $iteration ++;
                }else{
                    if ($this->isWorking($instances) === false){
                        $this->launch($iteration);
                        $iteration ++;
                    }
                }
                sleep($this->settings->heartbeat_timeout);
            }
        }
        public function start(){
            $this->resources();
            $this->initSETTINGS();
            $this->processing();
        }
    }
    $Launcher = new Launcher();
    $Launcher->start();
}
?>