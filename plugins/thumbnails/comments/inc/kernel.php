<?php
namespace Pure\Plugins\Thumbnails\Comments {
    class Builder{
        private $parameters;
        function __construct($parameters){
            $this->parameters = $this->validate($parameters);
            if (is_bool($this->parameters) == true){
                throw new \Exception("Pure\Plugins\Thumbnails\Comments\Builder\__construct::: cannot validate settings of widget", E_USER_WARNING);
            }
        }
        private function validate($parameters){
            if (isset($parameters['content'])                                                               === false ||
                \Pure\Providers\Comments\Initialization::instance()->is_available($parameters['content'])   === false){
                return false;
            }
            $parameters['group'     ] = (isset($parameters['group'      ]) == false ? NULL 	: (strlen($parameters['group'   ]) === 0 ? NULL : $parameters['group'   ] ));
            $parameters['shown'     ] = (isset($parameters['shown'      ]) == false ? NULL 	: $parameters['shown']);
            $parameters['targets'	] = (isset($parameters['targets'	]) == false ? NULL 	: (strlen($parameters['targets']) === 0 ? NULL : $parameters['targets'] ));
            $parameters['title'		] = (isset($parameters['title'		]) == false ? NULL 	: (strlen($parameters['title']  ) === 0 ? NULL : $parameters['title']   ));
            $parameters['maxcount'	] = (isset($parameters['maxcount'	]) == false ? NULL 	: $parameters['maxcount'	]);
            $parameters['members'	] = (isset($parameters['members'	]) == false ? NULL 	: (bool)$parameters['members'	    ]);
            $parameters['top'	    ] = (isset($parameters['top'	    ]) == false ? NULL 	: (bool)$parameters['top'	        ]);
            $parameters['displayed' ] = (isset($parameters['displayed'  ]) == false ? NULL 	: (strlen($parameters['displayed']  ) === 0 ? NULL : $parameters['displayed']   ));
            $parameters['profile'	] = (isset($parameters['profile'	]) == false ? NULL 	: $parameters['profile'		]);
            if (is_null($parameters['maxcount']) == false){
                $parameters['maxcount'] = (integer)$parameters['maxcount'];
                $parameters['maxcount'] = (is_numeric($parameters['maxcount']) == false ? 100 : $parameters['maxcount']);
            }
            if (is_null($parameters['days']) == false){
                $parameters['days'] = (integer)$parameters['days'];
                $parameters['days'] = (is_numeric($parameters['days']) == false ? 30 : $parameters['days']);
            }
            if (is_null($parameters['targets']) == false){
                $targets	    = preg_split('/,/', $parameters['targets']);
                $targets_array  = array();
                $strTargets     = '';
                for ($index = count($targets) - 1; $index >= 0; $index --){
                    $targets[$index] = (integer)$targets[$index];
                    $targets[$index] = (is_numeric($targets[$index]) == false ? NULL : $targets[$index]);
                    if (is_null($targets[$index]) == false){
                        if (strlen($strTargets) > 0){
                            $strTargets = $strTargets.','.((string)$targets[$index]);
                        }else{
                            $strTargets = (string)$targets[$index];
                        }
                        array_push($targets_array, (integer)$targets[$index]);
                    }
                }
                $parameters['targets']          = $strTargets;
                $parameters['targets_array']    = $targets_array;
            }
            $parameters['top'	    ] = (is_bool    ($parameters['top'          ]) == true ? $parameters['top'	        ] : false           );
            $parameters['members'	] = (is_bool    ($parameters['members'      ]) == true ? $parameters['members'	    ] : false           );
            $parameters['profile'	] = (is_string	($parameters['profile'      ]) == true ? $parameters['profile'	    ] : '#'		        );
            $parameters['from_date'	] = (is_string	($parameters['from_date'    ]) == true ? $parameters['from_date'    ] : date('Y-m-d')   );
            $parameters['group'	    ] = (is_string	($parameters['group'        ]) == true ? $parameters['group'        ] : 'group_'.rand(100000, 999999).'_'.rand(100000, 999999));
            $parameters['shown'	    ] = (is_integer	($parameters['shown'        ]) == true ? $parameters['shown'        ] : 0               );
            $parameters['displayed' ] = (is_string	($parameters['displayed'    ]) == true ? $parameters['displayed'    ] : 'none'          );
            return $parameters;
        }
        private function title(){
            $template   = \Pure\Templates\Titles\Initialization::instance()->get($this->parameters['title_type']);
            $innerHTML  = '';
            if (is_null($this->parameters['title']) == false ){
                if (is_null($template) === false){
                    $innerHTML  = $template->get($this->parameters['title']);
                }
            }
            $template   = NULL;
            return $innerHTML;
        }
        private function displayed(){
            switch($this->parameters['displayed']){
                case 'member':
                    if (in_array($this->parameters['content'], array(
                            'last_of_user', 'where_post_author'
                        )) !== false){
                        $this->parameters['targets']        = (string)\Pure\Configuration::instance()->globals->IDs->user_id;
                        $this->parameters['targets_array']  = array((int)\Pure\Configuration::instance()->globals->IDs->user_id);
                    }
                    break;
                case 'post':
                    if (in_array($this->parameters['content'], array(
                            'last_in_posts'
                        )) !== false){
                        $this->parameters['targets']        = (string)\Pure\Configuration::instance()->globals->IDs->post_id;
                        $this->parameters['targets_array']  = array((int)\Pure\Configuration::instance()->globals->IDs->post_id);
                    }
                    break;
            }
            if ($this->parameters['displayed'] === true){
            }
        }
        private function show(){
            $this->displayed();
            $innerHTML      = '';
            $provider       = \Pure\Providers\Comments\Initialization::instance()->get($this->parameters['content']);
            if ($provider !== false){
                $comments   = $provider->get($this->parameters);
                $provider   = NULL;
                if (isset($this->parameters['template']) === true && $comments !== false){
                    $template = \Pure\Templates\Comments\Thumbnails\Initialization::instance()->get($this->parameters['template']);
                    if (is_null($template) === false){
                        $top_author = ((bool)$this->parameters['top'] === true ? true : false);
                        foreach($comments->comments as $comment){
                            $innerHTML .= ($top_author === true ?
                                $template->top      ($comment) :
                                $template->simple   ($comment));
                            if ($top_author === true){
                                $top_author = false;
                            }
                        }
                    }
                    $template     = NULL;
                }
            }
            return ($innerHTML !== '' ? $this->title().$innerHTML : '');
        }
        public function render(){
            return $this->show();
        }
    }
}
?>