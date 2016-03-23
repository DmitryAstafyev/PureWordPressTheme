<?php
namespace Pure\Plugins\HighLights {
    class Builder{
        private $parameters;
        function __construct($parameters){
            $this->parameters = $this->validate($parameters);
            if (is_bool($this->parameters) == true){
                throw new \Exception("Pure\Plugins\HighLights\Builder\__construct::: cannot validate settings of widget", E_USER_WARNING);
            }
        }
        private function validate($parameters){
            $parameters['title'		    ] = (isset($parameters['title'		    ]) == false ? false : (strlen($parameters['title'           ]) === 0 ? false    : $parameters['title'       ]   ));
            $parameters['title_type'    ] = (isset($parameters['title_type'		]) == false ? false : (strlen($parameters['title_type'      ]) === 0 ? false    : $parameters['title_type'  ]   ));
            $parameters['template'      ] = (isset($parameters['template'       ]) == false ? false : (strlen($parameters['template'        ]) === 0 ? 'A'      : $parameters['template'    ]   ));
            $parameters['icons'         ] = (isset($parameters['icons'          ]) == false ? false : (is_array($parameters['icons'         ]) !== false ? $parameters['icons'          ] : array()   ));
            $parameters['titles'        ] = (isset($parameters['titles'         ]) == false ? false : (is_array($parameters['titles'        ]) !== false ? $parameters['titles'         ] : array()   ));
            $parameters['descriptions'  ] = (isset($parameters['descriptions'   ]) == false ? false : (is_array($parameters['descriptions'  ]) !== false ? $parameters['descriptions'   ] : array()   ));
            $parameters['post_ids'      ] = (isset($parameters['post_ids'       ]) == false ? false : (is_array($parameters['post_ids'      ]) !== false ? $parameters['post_ids'       ] : array()   ));
            $parameters['page_ids'      ] = (isset($parameters['page_ids'       ]) == false ? false : (is_array($parameters['page_ids'      ]) !== false ? $parameters['page_ids'       ] : array()   ));
            $parameters['urls'          ] = (isset($parameters['urls'           ]) == false ? false : (is_array($parameters['urls'          ]) !== false ? $parameters['urls'           ] : array()   ));
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
        private function getURL($index){
            $url = '';
            if ((int)$this->parameters['post_ids'][$index] > 0 && $url === ''){
                $url = get_permalink((int)$this->parameters['post_ids'][$index]);
                $url = ($url !== false ? $url : '');
            }
            if ((int)$this->parameters['page_ids'][$index] > 0 && $url === ''){
                $url = get_permalink((int)$this->parameters['page_ids'][$index]);
                $url = ($url !== false ? $url : '');
            }
            if ($this->parameters['urls'][$index] !== '' && $url === ''){
                $url = $this->parameters['urls'][$index];
            }
            return $url;
        }
        private function show(){
            $innerHTML = '';
            if (count($this->parameters['icons']) > 0){
                $template   = \Pure\Templates\HighLights\Initialization::instance()->get($this->parameters['template']);
                if ($template !== false){
                    $items      = array();
                    for($index = 0; $index < count($this->parameters['icons']); $index ++){
                        $icon       = wp_get_attachment_image_src($this->parameters['icons'][$index], 'full');
                        $icon       = (is_array($icon) !== false ? $icon[0] : '');
                        $items[]    = (object)array(
                            'title'         =>$this->parameters['titles'        ][$index],
                            'description'   =>$this->parameters['descriptions'  ][$index],
                            'icon'          =>$icon,
                            'url'           =>$this->getURL($index)
                        );
                    }
                    if (count($items) > 0){
                        $innerHTML  = $this->title().$template->get($items);
                    }
                }
            }
            return $innerHTML;
        }
        public function render(){
            return $this->show();
        }
    }
}
?>