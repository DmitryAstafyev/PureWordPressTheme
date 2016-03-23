<?php
namespace Pure\Plugins\OpenMenu {
    class Builder{
        private $parameters;
        function __construct($parameters){
            $this->parameters = $this->validate($parameters);
            if (is_bool($this->parameters) == true){
                throw new \Exception("Pure\Plugins\Inserts\Builder\__construct::: cannot validate settings of widget", E_USER_WARNING);
            }
        }
        private function validate($parameters){
            $parameters['target'	] = (isset($parameters['target'	    ]) == false ? false : (int)$parameters['target']);
            $parameters['title'		] = (isset($parameters['title'		]) == false ? false : (strlen($parameters['title']  ) === 0 ? false : $parameters['title']   ));
            $parameters['template'  ] = (isset($parameters['template'   ]) == false ? false : (strlen($parameters['template']  ) === 0 ? 'A' : $parameters['template']   ));
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
        private function show(){
            $innerHTML = '';
            if ($this->parameters['target'] !== false){
                $template   = \Pure\Templates\Elements\OpenMenu\Initialization::instance()->get($this->parameters['template']);
                if ($template !== false){
                    $innerHTML  = $this->title().$template->innerHTML((object)array('menu_id'=>$this->parameters['target']));
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