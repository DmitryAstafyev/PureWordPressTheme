<?php
namespace Pure\Components\webSocketServer{
    class Paths {
        public $LPS     = '/'; //Local Path Separator. Mostly works well with unix standard [/]. But if you have a problems on win - change it on [\]
        public $root    = NULL;
        static private $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        public function dir($path){
            return preg_replace('/\\\{1,}|\/{1,}/', $this->LPS, $this->root.strtolower(str_replace($this->root, '', $path)));
        }
        public function url($url){
            return strtolower($url);
        }
        function __construct(){
            $this->root = substr(__DIR__, 0, (stripos(__DIR__, 'wp-content') - 1));
        }
    }
    class Initialization{
        private $parameters;
        private function validate(&$parameters = NULL) {
            $parameters         = (is_object($parameters) === true ? $parameters : new \stdClass());
            $parameters->uniqid = (isset($parameters->uniqid) === true ? (gettype($parameters->uniqid) === "string" ? $parameters->uniqid : uniqid()) : uniqid());
            return $parameters;
        }
        public function __construct($parameters = NULL){
            if (function_exists("socket_create") === true){
                $this->parameters = $this->validate($parameters);
            }else{
                throw new \Exception("PHP does not support sockets. Check php.ini and reference to php_sockets.dll", E_USER_WARNING);
            }
        }
        private function resources(){
            require_once(\Pure\Components\webSocketServer\Paths::instance()->dir(substr(__DIR__, 0, (stripos(__DIR__, 'websocketserver') - 1)).'/websocketserver/module/bin/thread/resources.php'));
            $Resources = new \Pure\Components\webSocketServer\Module\Resources((object)array(
                'uniqid'    =>$this->parameters->uniqid,
                'caller'    =>'INITIALIZATION',
                'classes'   =>array(
                    'Logs'      =>true,
                    'Settings'  =>true,
                    'Server'    =>true,
                )
            ));
            $Resources->attach();
            $Resources = NULL;
        }
        private function start(){
            $Server = new \Pure\Components\webSocketServer\Module\Server($this->parameters->uniqid);
            $Server->proceed();
        }
        public function proceed(){
            $this->resources();
            $this->start();
        }
    }
    $Initialization = new Initialization();
    $Initialization->proceed();
    $Initialization = NULL;
}
?>