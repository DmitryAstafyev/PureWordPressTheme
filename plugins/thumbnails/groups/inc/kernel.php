<?php
namespace Pure\Plugins\Thumbnails\Groups {
    class Builder{
        private $parameters;
        function __construct($parameters){
            $this->parameters = $this->validate($parameters);
            if (is_bool($this->parameters) == true){
                throw new \Exception("Pure\Plugins\Thumbnails\Comments\Builder\__construct::: cannot validate settings of widget", E_USER_WARNING);
            }
        }
        private function validate($parameters){
            if (isset($parameters['content'])                                                           === false||
                \Pure\Providers\Groups\Initialization::instance()->is_available($parameters['content']) === false){
                return false;
            }
            $parameters['group'	            ] = (isset($parameters['group'	            ]) == false ? NULL 	: (strlen($parameters['group'   ]) === 0 ? NULL : $parameters['group'   ] ));
            $parameters['shown'	            ] = (isset($parameters['shown'	            ]) == false ? NULL 	: $parameters['shown']);
            $parameters['targets'	        ] = (isset($parameters['targets'	        ]) == false ? NULL 	: (strlen($parameters['targets']) === 0 ? NULL : $parameters['targets'] ));
            $parameters['title'		        ] = (isset($parameters['title'		        ]) == false ? NULL 	: (strlen($parameters['title']  ) === 0 ? NULL : $parameters['title']   ));
            $parameters['only_with_avatar'	] = (isset($parameters['only_with_avatar'	]) == false ? NULL 	: (bool)$parameters['only_with_avatar']);
            $parameters['maxcount'	        ] = (isset($parameters['maxcount'	        ]) == false ? NULL 	: $parameters['maxcount']);
            $parameters['top'	            ] = (isset($parameters['top'	            ]) == false ? NULL 	: (bool)$parameters['top']);
            $parameters['displayed'		    ] = (isset($parameters['displayed'		    ]) == false ? NULL 	: (strlen($parameters['displayed']  ) === 0 ? NULL : $parameters['displayed']   ));
            $parameters['show_opened'	    ] = (isset($parameters['show_opened'	    ]) == false ? NULL 	: (bool)$parameters['show_opened']);
            $parameters['more'	            ] = (isset($parameters['more'	            ]) == false ? NULL 	: (bool)$parameters['more']);
            $parameters['init_scripts'      ] = (isset($parameters['init_scripts'       ]) == false ? NULL 	: (bool)$parameters['init_scripts']);
            if (is_null($parameters['maxcount']) == false){
                $parameters['maxcount'] = (integer)$parameters['maxcount'];
                $parameters['maxcount'] = (is_numeric($parameters['maxcount']) == false ? 100 : $parameters['maxcount']);
            }
            if (is_null($parameters['days']) == false){
                $parameters['days'] = (integer)$parameters['days'];
                $parameters['days'] = (is_numeric($parameters['days']) == false ? 30 : $parameters['days']);
            }
            //echo "<p>".var_dump($parameters['targets'])."</p>";
            //echo "<p>==============================================================</p>";
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
            $parameters['more'	            ] = (is_bool    ($parameters['more'             ]) == true ? $parameters['more'	            ] : false           );
            $parameters['top'	            ] = (is_bool    ($parameters['top'              ]) == true ? $parameters['top'	            ] : false           );
            $parameters['init_scripts'	    ] = (is_bool    ($parameters['init_scripts'     ]) == true ? $parameters['init_scripts'	    ] : false           );
            $parameters['only_with_avatar'	] = (is_bool    ($parameters['only_with_avatar' ]) == true ? $parameters['only_with_avatar'	] : false           );
            $parameters['show_content'	    ] = (is_bool    ($parameters['show_content'     ]) == true ? $parameters['show_content'	    ] : false           );
            $parameters['show_admin_part'	] = (is_bool    ($parameters['show_admin_part'  ]) == true ? $parameters['show_admin_part'	] : false           );
            $parameters['show_life'	        ] = (is_bool    ($parameters['show_life'        ]) == true ? $parameters['show_life'	    ] : false           );
            $parameters['show_opened'	    ] = (is_bool    ($parameters['show_opened'      ]) == true ? $parameters['show_opened'	    ] : false           );
            $parameters['displayed'	        ] = (is_string	($parameters['displayed'        ]) == true ? $parameters['displayed'        ] : 'none'          );
            $parameters['from_date'	        ] = (is_string	($parameters['from_date'        ]) == true ? $parameters['from_date'        ] : date('Y-m-d')   );
            $parameters['group'	            ] = (is_string	($parameters['group'            ]) == true ? $parameters['group'            ] : uniqid('Group') );
            $parameters['shown'	            ] = (is_integer	($parameters['shown'            ]) == true ? $parameters['shown'            ] : 0               );
            $parameters['more_settings'     ] = 'pure.settings.plugins.thumbnails.groups.more';
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
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->requests.'/settings/request.plugins.thumbnails.groups.php'));
            $settings = new \Pure\Requests\Plugins\Thumbnails\Groups\Settings\Initialization();
            $settings->init($parameters);
            $settings = NULL;
        }
        private function displayed(){
            switch($this->parameters['displayed']){
                case 'member':
                    if (in_array($this->parameters['content'], array(
                            'users'
                        )) !== false){
                        $this->parameters['targets']        = (string)\Pure\Configuration::instance()->globals->IDs->user_id;
                        $this->parameters['targets_array']  = array((int)\Pure\Configuration::instance()->globals->IDs->user_id);
                    }
                    break;
                case 'group':
                    if (in_array($this->parameters['content'], array(
                            'groups'
                        )) !== false){
                        $this->parameters['targets']        = (string)\Pure\Configuration::instance()->globals->IDs->group_id;
                        $this->parameters['targets_array']  = array((int)\Pure\Configuration::instance()->globals->IDs->group_id);
                    }
                    break;
            }
            if ($this->parameters['displayed'] === true){
            }
        }
        private function show(){
            $this->displayed();
            $innerHTML      = '';
            $provider       = \Pure\Providers\Groups\Initialization::instance()->get($this->parameters['content']);
            if ($provider !== false){
                $groups     = $provider->get($this->parameters);
                $provider   = NULL;
                if (isset($this->parameters['template']) === true && $groups !== false){
                    $template = \Pure\Templates\Groups\Initialization::instance()->get($this->parameters['template']);
                    if (is_null($template) === false){
                        $top_group = ((bool)$this->parameters['top'] === true ? true : false);
                        foreach($groups->groups as $group){
                            $innerHTML .= ( $top_group === true ?
                                $template->top      ($group, (object)array( 'only_with_avatar'  =>(bool)$this->parameters['only_with_avatar'],
                                                                            'show_content'      =>(bool)$this->parameters['show_content'],
                                                                            'show_opened'       =>(bool)$this->parameters['show_opened'],
                                                                            'show_admin_part'   =>(bool)$this->parameters['show_admin_part'],
                                                                            'show_life'         =>(bool)$this->parameters['show_life'],
                                                                            'attribute'         =>(object)array('name'=>'data-type-more-group','value'=>$this->parameters['group']))) :
                                $template->simple   ($group, (object)array( 'only_with_avatar'  =>(bool)$this->parameters['only_with_avatar'],
                                                                            'show_content'      =>(bool)$this->parameters['show_content'],
                                                                            'show_opened'       =>(bool)$this->parameters['show_opened'],
                                                                            'show_admin_part'   =>(bool)$this->parameters['show_admin_part'],
                                                                            'show_life'         =>(bool)$this->parameters['show_life'],
                                                                            'attribute'         =>(object)array('name'=>'data-type-more-group','value'=>$this->parameters['group'])))
                            );
                            if ($top_group === true){
                                $top_group = false;
                            }
                        }
                        if ($this->parameters['more'] === true && method_exists($template, 'more') === true){
                            if (count($groups->groups) > 0){
                                $this->parameters['shown'] = $groups->shown;
                                $this->parameters['total'] = $groups->total;
                                $innerHTML .= $template->more($this->parameters);
                                $this->resources_more($this->parameters);
                            }
                        }
                        $innerHTML = ($innerHTML !== '' ? $this->title().$innerHTML : '');
                        if ($this->parameters['init_scripts'] === true){
                            $innerHTML .= \Pure\Components\More\A\Initialization::instance()->init_scripts(false);
                        }
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