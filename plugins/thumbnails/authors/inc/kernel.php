<?php
namespace Pure\Plugins\Thumbnails\Authors {
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
                \Pure\Providers\Members\Initialization::instance()->is_available($parameters['content'])    === false){
                return false;
            }
            $parameters['group'	            ] = (isset($parameters['group'	            ]) == false ? NULL 	: (strlen($parameters['group'   ]) === 0 ? NULL : $parameters['group'   ] ));
            $parameters['targets'	        ] = (isset($parameters['targets'	        ]) == false ? NULL 	: (strlen($parameters['targets' ]) === 0 ? NULL : $parameters['targets' ] ));
            $parameters['title'		        ] = (isset($parameters['title'		        ]) == false ? NULL 	: (strlen($parameters['title'   ]) === 0 ? NULL : $parameters['title'   ] ));
            $parameters['shown'	            ] = (isset($parameters['shown'	            ]) == false ? NULL 	: $parameters['shown']);
            $parameters['maxcount'	        ] = (isset($parameters['maxcount'	        ]) == false ? NULL 	: $parameters['maxcount']);
            $parameters['only_with_avatar'	] = (isset($parameters['only_with_avatar'	]) == false ? NULL 	: (bool)$parameters['only_with_avatar']);
            $parameters['top'	            ] = (isset($parameters['top'	            ]) == false ? NULL 	: (bool)$parameters['top']);
            $parameters['displayed'	        ] = (isset($parameters['displayed'	        ]) == false ? NULL 	: (bool)$parameters['displayed']);
            $parameters['more'	            ] = (isset($parameters['more'	            ]) == false ? NULL 	: (bool)$parameters['more']);
            $parameters['profile'	        ] = (isset($parameters['profile'	        ]) == false ? NULL 	: $parameters['profile']);
            $parameters['wrapper'	        ] = (isset($parameters['wrapper'	        ]) == false ? NULL 	: $parameters['wrapper']);
            $parameters['min_width'	        ] = (isset($parameters['min_width'	        ]) == false ? NULL 	: $parameters['min_width']);
            $parameters['templates_settings'] = (isset($parameters['templates_settings' ]) == false ? false : $parameters['templates_settings']);
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
            }else{
                $parameters['targets']          = '';
                $parameters['targets_array']    = array();
            }
            $parameters['top'	            ] = (is_bool    ($parameters['top'              ]) == true ? $parameters['top'	            ] : false           );
            $parameters['displayed'	        ] = (is_bool    ($parameters['displayed'        ]) == true ? $parameters['displayed'	    ] : false           );
            $parameters['more'	            ] = (is_bool    ($parameters['more'             ]) == true ? $parameters['more'	            ] : false           );
            $parameters['only_with_avatar'	] = (is_bool    ($parameters['only_with_avatar' ]) == true ? $parameters['only_with_avatar'	] : false           );
            $parameters['wrapper'	        ] = (is_bool    ($parameters['wrapper'          ]) == true ? $parameters['wrapper'	        ] : false           );
            $parameters['profile'	        ] = (is_string	($parameters['profile'          ]) == true ? $parameters['profile'	        ] : '#'		        );
            $parameters['from_date'	        ] = (is_string	($parameters['from_date'        ]) == true ? $parameters['from_date'        ] : date('Y-m-d')   );
            $parameters['group'	            ] = (is_string	($parameters['group'            ]) == true ? $parameters['group'            ] : uniqid('group_'));
            $parameters['shown'	            ] = (is_integer	($parameters['shown'            ]) == true ? $parameters['shown'            ] : 0               );
            $parameters['min_width'	        ] = (is_integer	($parameters['min_width'        ]) == true ? $parameters['min_width'        ] : 300             );
            $parameters['more_settings'     ] = 'pure.settings.plugins.thumbnails.authors.more';
            return $parameters;
        }
        private function title(){
            $template   = \Pure\Templates\Titles\Initialization::instance()->get($this->parameters['title_type']);
            $innerHTML  = '';
            if (is_null($this->parameters['title']) == false){
                if (is_null($template) === false){
                    $innerHTML  = $template->get($this->parameters['title']);
                }
            }
            $template   = NULL;
            return $innerHTML;
        }
        private function resources_more($parameters){
            \Pure\Components\More\A\Initialization::instance()->attach();
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->requests.'/settings/request.plugins.thumbnails.authors.php'));
            $settings = new \Pure\Requests\Plugins\Thumbnails\Authors\Settings\Initialization();
            $settings->init($parameters);
            $settings = NULL;
        }
        private function displayed(){
            if ($this->parameters['displayed'] === true){
                if (in_array($this->parameters['content'], array(
                        'users', 'friends_of_user', 'recipients_of_user', 'talks_of_user', 'in_stream_of_users'
                    )) !== false){
                    $this->parameters['targets']        = (string)\Pure\Configuration::instance()->globals->IDs->user_id;
                    $this->parameters['targets_array']  = array((int)\Pure\Configuration::instance()->globals->IDs->user_id);
                }else if(in_array($this->parameters['content'], array(
                        'users_of_group'
                    )) !== false){
                    $this->parameters['targets']        = (string)\Pure\Configuration::instance()->globals->IDs->group_id;
                    $this->parameters['targets_array']  = array((int)\Pure\Configuration::instance()->globals->IDs->group_id);
                }
            }
        }
        private function show(){
            $this->displayed();
            $innerHTML      = '';
            $provider       = \Pure\Providers\Members\Initialization::instance()->get($this->parameters['content']);
            if ($provider !== false){
                $authors    = $provider->get($this->parameters);
                $provider   = NULL;
                if (isset($this->parameters['template']) === true && $authors !== false){
                    $template = \Pure\Templates\Authors\Initialization::instance()->get($this->parameters['template']);
                    if (is_null($template) === false){
                        $templates_settings = (isset($this->parameters['templates_settings'][$this->parameters['template']]) === true ? $this->parameters['templates_settings'][$this->parameters['template']] : false);
                        $top_author         = ((bool)$this->parameters['top'] === true ? true : false);
                        $items              = array();
                        foreach($authors->members as $member){
                            $innerHTMLItem = ($top_author === true ?
                                $template->top      ($member, (object)array('attribute'         =>(object)array('name'=>'data-type-more-group','value'=>$this->parameters['group']),
                                                                            'templates_settings'=>$templates_settings)) :
                                $template->simple   ($member, (object)array('attribute'         =>(object)array('name'=>'data-type-more-group','value'=>$this->parameters['group']),
                                                                            'templates_settings'=>$templates_settings)));
                            if ($top_author === true){
                                $top_author = false;
                            }
                            if ($this->parameters['wrapper'] === false){
                                $innerHTML .= $innerHTMLItem;
                            }else{
                                $items[] = $innerHTMLItem;
                            }
                        }
                        if ($this->parameters['wrapper'] !== false){
                            $Wrapper    = \Pure\Templates\Positioning\Initialization::instance()->get('A');
                            $innerHTML  = $Wrapper->get(
                                $items,
                                (object)array(
                                    'min_width'=>$this->parameters['min_width']
                                )
                            );
                            $Wrapper    = NULL;
                        }
                        if ($this->parameters['more'] === true && method_exists($template, 'more') === true){
                            if (count($authors->members) > 0){
                                $this->parameters['shown'] = $authors->shown;
                                $this->parameters['total'] = $authors->total;
                                $innerHTML .= $template->more($this->parameters);
                                $this->resources_more($this->parameters);
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