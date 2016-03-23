<?php
namespace Pure\Plugins\Counters {
    class Builder{
        private $parameters;
        function __construct($parameters){
            $this->parameters = $this->validate($parameters);
            if (is_bool($this->parameters) == true){
                throw new \Exception("Pure\Plugins\Counters\Builder\__construct::: cannot validate settings of widget", E_USER_WARNING);
            }
        }
        private function validate($parameters){
            $parameters['offset'	    ] = (isset($parameters['offset'		    ]) == false ? false : (strlen($parameters['offset'          ]) === 0 ? 50       : $parameters['offset'      ]   ));
            $parameters['background'    ] = (isset($parameters['background'		]) == false ? false : (strlen($parameters['background'      ]) === 0 ? false    : $parameters['background'  ]   ));
            $parameters['template'      ] = (isset($parameters['template'       ]) == false ? false : (strlen($parameters['template'        ]) === 0 ? 'A'      : $parameters['template'    ]   ));
            $parameters['icons'         ] = (isset($parameters['icons'          ]) == false ? false : (is_array($parameters['icons'         ]) !== false ? $parameters['icons'          ] : array()   ));
            $parameters['titles'        ] = (isset($parameters['titles'         ]) == false ? false : (is_array($parameters['titles'        ]) !== false ? $parameters['titles'         ] : array()   ));
            $parameters['counts'        ] = (isset($parameters['counts'         ]) == false ? false : (is_array($parameters['counts'        ]) !== false ? $parameters['counts'         ] : array()   ));
            $parameters['urls'          ] = (isset($parameters['urls'           ]) == false ? false : (is_array($parameters['urls'          ]) !== false ? $parameters['urls'           ] : array()   ));
            return $parameters;
        }
        private function show(){
            $innerHTML = '';
            if (count($this->parameters['icons']) > 0){
                $template = \Pure\Templates\Elements\CounterWrapper\Initialization::instance()->get($this->parameters['template']);
                if ($template !== false){
                    $items      = array();
                    for($index = 0; $index < count($this->parameters['icons']); $index ++){
                        $icon       = wp_get_attachment_image_src($this->parameters['icons'][$index], 'full');
                        $icon       = (is_array($icon) !== false ? $icon[0] : '');
                        $items[]    = (object)array(
                            'title'         =>$this->parameters['titles'][$index],
                            'count'         =>$this->parameters['counts'][$index],
                            'icon'          =>$icon,
                            'url'           =>$this->parameters['urls'  ][$index]
                        );
                    }
                    if (count($items) > 0){
                        $innerHTML  = $template->innerHTML(
                            (object)array(
                                'background'=>$this->parameters['background'],
                                'offset'    =>$this->parameters['offset'    ],
                                'items'     =>$items
                            )
                        );
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