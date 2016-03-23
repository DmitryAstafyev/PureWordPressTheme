<?php
namespace Pure\Components\webSocketServer\Common{
    class Logs {
        private $parameters;
        function __construct($uniqid = '', $settings = false){
            if (class_exists('\Pure\Components\webSocketServer\Common\Settings'  ) !== false){
                $Settings           = new Settings($uniqid);
                $this->parameters   = $Settings->get();
                $Settings           = NULL;
                $this->PHPDebugMessages();
            }elseif($settings !== false) {
                $this->parameters   = $settings;
            }else{
                throw new \Exception("Attach [common\\settings.php] before use Logs", E_USER_WARNING);
            }
        }
        public function log($message, $status = ""){
            if($this->parameters->logs === 'on'){
                echo    ($this->parameters->logs_as_comment === 'on' ? "<!--|SERLOG|" : "").
                        ($status !== "" ? "[".$status."]" : "").date("[H:i:s] ").$message.
                        ($this->parameters->logs_as_comment === 'on' ? "-->" : "\r\n");
                return false;
            }
        }
        private function PHPDebugMessages(){
            if ($this->parameters->php_debug === 'on'){
                error_reporting(E_ALL);
                ini_set('display_errors',1);
                ini_set('display_startup_errors',1);
                error_reporting(-1);
            }
        }
    }
}
?>