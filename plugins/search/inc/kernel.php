<?php
namespace Pure\Plugins\Search {
    class Builder{
        private $parameters;
        function __construct($parameters){
            $this->parameters = $this->validate($parameters);
            if (is_bool($this->parameters) == true){
                throw new \Exception("Pure\Plugins\Search\Builder\__construct::: cannot validate settings of widget", E_USER_WARNING);
            }
        }
        private function validate($parameters){
            $parameters['title'		    ] = (isset($parameters['title'		    ]) == false ? false : (strlen($parameters['title'       ]  ) === 0 ? false  : $parameters['title'       ]   ));
            $parameters['template'      ] = (isset($parameters['template'       ]) == false ? false : (strlen($parameters['template'    ]  ) === 0 ? 'A'    : $parameters['template'    ]   ));
            $parameters['background'    ] = (isset($parameters['background'     ]) == false ? false : (strlen($parameters['background'  ]  ) === 0 ? ''     : $parameters['background'  ]   ));
            return $parameters;
        }
        private function show(){
            $innerHTML  = '';
            $template   = \Pure\Templates\Elements\Search\Initialization::instance()->get($this->parameters['template']);
            if ($template !== false){
                $innerHTML  = $template->innerHTML(
                    (object)array(
                        'title'     =>$this->parameters['title'		    ],
                        'background'=>$this->parameters['background'    ],
                    )
                );
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