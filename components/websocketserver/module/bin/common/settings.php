<?php
namespace Pure\Components\webSocketServer\Common{
    class Settings{
        private $parameters;
        function __construct($uniqid = '', $load_resources = true){
            $this->parameters = false;
            if ($load_resources === true){
                $this->resources($uniqid);
            }
        }
        private function resources($uniqid){
            $Resources = new \Pure\Components\webSocketServer\Module\Resources((object)array(
                'uniqid'    =>$uniqid,
                'caller'    =>'SETTINGS',
                'classes'   =>array(
                    'WordPress'=>true
                )
            ));
            $Resources->attach();
            $Resources = NULL;
        }
        public function get(){
            if ($this->parameters === false){
                \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                $Settings           = new \Pure\Components\WordPress\Settings\Settings();
                $this->parameters   = $Settings->load();
                $this->parameters   = $Settings->less($this->parameters->webSocketServer->properties);
                $Settings           = NULL;
            }
            return $this->parameters;
        }
    }
}
?>