<?php
namespace Pure\Plugins\Footage {
    class Builder{
        private $parameters;
        function __construct($parameters){
            $this->parameters = $this->validate($parameters);
            if (is_bool($this->parameters) == true){
                throw new \Exception("Pure\Plugins\Footage\Builder\__construct::: cannot validate settings of widget", E_USER_WARNING);
            }
        }
        private function validate($parameters){
            $parameters['title'		        ] = (isset($parameters['title'		    ]) == false ? false : (strlen($parameters['title'           ]) === 0 ? ''   : $parameters['title'           ]   ));
            $parameters['description'       ] = (isset($parameters['description'    ]) == false ? false : (strlen($parameters['description'     ]) === 0 ? ''   : $parameters['description'     ]   ));
            $parameters['link'              ] = (isset($parameters['link'           ]) == false ? false : (strlen($parameters['link'            ]) === 0 ? ''   : $parameters['link'            ]   ));
            $parameters['link_label'        ] = (isset($parameters['link_label'     ]) == false ? false : (strlen($parameters['link_label'      ]) === 0 ? ''   : $parameters['link_label'      ]   ));
            $parameters['alt_background'    ] = (isset($parameters['alt_background' ]) == false ? false : (strlen($parameters['alt_background'  ]) === 0 ? ''   : $parameters['alt_background'  ]   ));
            $parameters['template'          ] = (isset($parameters['template'       ]) == false ? false : (strlen($parameters['template'        ]) === 0 ? 'A'  : $parameters['template'        ]   ));
            $parameters['types'             ] = (isset($parameters['types'          ]) == false ? false : (is_array($parameters['types'         ]) !== false ? $parameters['types'              ] : array()   ));
            $parameters['srcs'              ] = (isset($parameters['srcs'           ]) == false ? false : (is_array($parameters['srcs'          ]) !== false ? $parameters['srcs'               ] : array()   ));
            return $parameters;
        }
        private function show(){
            $innerHTML = '';
            if (count($this->parameters['types']) > 0){
                $template   = \Pure\Templates\Footage\Initialization::instance()->get($this->parameters['template']);
                if ($template !== false){
                    $sources    = array();
                    for($index = 0; $index < count($this->parameters['types']); $index ++){
                        $sources[]  = (object)array(
                            'type'  =>$this->parameters['types' ][$index],
                            'src'   =>$this->parameters['srcs'  ][$index],
                        );
                    }
                    if (count($sources) > 0){
                        if ($this->parameters['alt_background'] !== false){
                            $alt_background = wp_get_attachment_image_src($this->parameters['alt_background'], 'full');
                            $alt_background = (is_array($alt_background) !== false ? $alt_background[0] : '');
                        }else{
                            $alt_background = '';
                        }
                        $innerHTML  = $template->get(
                            (object)array(
                                'sources'       =>$sources,
                                'title'         =>$this->parameters['title'             ],
                                'description'   =>$this->parameters['description'       ],
                                'link'          =>$this->parameters['link'              ],
                                'link_label'    =>$this->parameters['link_label'        ],
                                'alt_background'=>$alt_background,
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