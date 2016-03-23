<?php
namespace Pure\Plugins\Tags {
    class Builder{
        private $parameters;
        function __construct($parameters){
            $this->parameters = $this->validate($parameters);
            if (is_bool($this->parameters) == true){
                throw new \Exception("Pure\Plugins\Tags\Builder\__construct::: cannot validate settings of widget", E_USER_WARNING);
            }
        }
        private function validate($parameters){
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
        private function getItems(){
            $items      = array();
            $tags       = get_tags();
            if (is_array($tags) !== false){
                foreach($tags as $tag){
                    $items[] = (object)array(
                        'name'  =>$tag->name,
                        'count' =>$tag->count,
                        'url'   =>get_tag_link($tag->term_id)
                    );
                }
            }
            return $items;
        }
        private function show(){
            $innerHTML  = '';
            $template   = \Pure\Templates\Elements\Categories\Initialization::instance()->get($this->parameters['template']);
            if ($template !== false){
                $innerHTML  = $this->title().$template->innerHTML($this->getItems());
            }
            $template   = NULL;
            return $innerHTML;
        }
        public function render(){
            return $this->show();
        }
    }
}
?>