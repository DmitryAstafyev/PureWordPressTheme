<?php
namespace Pure\Plugins\Quotes {
    class Builder{
        private $parameters;
        function __construct($parameters){
            $this->parameters = $this->validate($parameters);
            if (is_bool($this->parameters) == true){
                throw new \Exception("Pure\Plugins\Quotes\Builder\__construct::: cannot validate settings of widget", E_USER_WARNING);
            }
        }
        private function validate($parameters){
            $parameters['target'	] = (isset($parameters['target'	    ]) == false ? false : ((int)$parameters['target'] > 0 ? (int)$parameters['target'] : false ));
            $parameters['title'		] = (isset($parameters['title'		]) == false ? false : (strlen($parameters['title']  ) === 0 ? false : $parameters['title']   ));
            $parameters['template'  ] = (isset($parameters['template'   ]) == false ? false : (strlen($parameters['template']  ) === 0 ? 'A' : $parameters['template']   ));
            $parameters['random'    ] = (isset($parameters['random'     ]) == false ? NULL 	: (bool)$parameters['random']);
            $parameters['displayed' ] = (isset($parameters['displayed'  ]) == false ? NULL 	: (bool)$parameters['displayed']);
            $parameters['random'    ] = (is_bool($parameters['random'   ]) == true ? $parameters['random'   ] : false);
            $parameters['displayed' ] = (is_bool($parameters['displayed']) == true ? $parameters['displayed'] : false);
            return $parameters;
        }
        private function title(){
            $template   = \Pure\Templates\Titles\Initialization::instance()->get($this->parameters['title_type']);
            $innerHTML  = '';
            if ($this->parameters['title'] !== false ){
                if (is_null($template) === false){
                    $innerHTML  = $template->get($this->parameters['title']);
                }
            }
            $template   = NULL;
            return $innerHTML;
        }
        private function get_target_id(){
            if ($this->parameters['random'] !== false){
                \Pure\Components\BuddyPress\Quotes\Initialization::instance()->attach();
                $Quotes     = new \Pure\Components\BuddyPress\Quotes\Core();
                $user_id    = $Quotes->getRandomUser(1);
                $Quotes     = NULL;
            }else if ((int)$this->parameters['target'] > 0){
                $user_id = (int)$this->parameters['target'];
            }else if ($this->parameters['displayed'] !== false){
                $user_id = (int)(int)\Pure\Configuration::instance()->globals->IDs->user_id;
            }
            return ((int)$user_id > 0 ? (int)$user_id : false);
        }
        private function show(){
            $innerHTML = '';
            $user_id = $this->get_target_id();
            if ($user_id !== false){
                $template   = \Pure\Templates\BuddyPress\QuotesRender\Initialization::instance()->get($this->parameters['template']);
                if ($template !== false ){
                    $innerHTML  = $this->title().$template->get((object)array('user_id'=>$user_id));
                }
                $template   = NULL;
            }
            return $innerHTML;
        }
        public function render(){
            return $this->show();
        }
    }
}
?>