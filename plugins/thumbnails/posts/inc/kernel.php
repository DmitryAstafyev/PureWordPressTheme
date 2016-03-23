<?php
namespace Pure\Plugins\Thumbnails\Posts {
    class Builder{
        private $parameters;
        private $id = 0;
        function __construct($parameters){
            $this->parameters = $this->validate($parameters);
            if (is_bool($this->parameters) == true){
                throw new \Exception("Pure\Plugins\Thumbnails\Posts\Builder\__construct::: cannot validate settings of widget", E_USER_WARNING);
            }
        }
        private function validate($parameters){
            if (isset($parameters['content'])                                                           === false ||
                \Pure\Providers\Posts\Initialization::instance()->is_available($parameters['content'])  === false){
                return false;
            }
            if ($parameters['tabs_columns'] != '1'&&
                $parameters['tabs_columns'] != '2'){
                $parameters['tabs_columns'] = 1;
            }
            $parameters['tabs_columns'] = (integer)$parameters['tabs_columns'];
            if ($parameters['presentation'] != 'clear'&&
                $parameters['presentation'] != 'slider'&&
                $parameters['presentation'] != 'wrapper'&&
                $parameters['presentation'] != 'tabs'){
                $parameters['presentation'] = 'clear';
            }
            $parameters['post_type'	] = (isset($parameters['post_type'  ]) == false ? NULL 	: $parameters['post_type']);
            $parameters['wrapper_width'	] = (isset($parameters['wrapper_width'  ]) == false ? NULL 	: (int)$parameters['wrapper_width']);
            $parameters['wrapper_space'	] = (isset($parameters['wrapper_space'  ]) == false ? NULL 	: (int)$parameters['wrapper_space']);
            $parameters['group'	    ] = (isset($parameters['group'	    ]) == false ? NULL 	: (strlen($parameters['group'   ]) === 0 ? NULL : $parameters['group'   ] ));
            $parameters['shown'	    ] = (isset($parameters['shown'	    ]) == false ? NULL 	: $parameters['shown']);
            $parameters['targets'	] = (isset($parameters['targets'	]) == false ? NULL 	: (strlen($parameters['targets']) === 0 ? NULL : $parameters['targets'] ));
            $parameters['hidetitle'	] = (isset($parameters['hidetitle'	]) == false ? NULL 	: $parameters['hidetitle'	]);
            $parameters['thumbnails'] = (isset($parameters['thumbnails'	]) == false ? NULL 	: $parameters['thumbnails'	]);
            $parameters['title'		] = (isset($parameters['title'		]) == false ? NULL 	: (strlen($parameters['title']  ) === 0 ? NULL : $parameters['title']   ));
            $parameters['maxcount'	] = (isset($parameters['maxcount'	]) == false ? NULL 	: $parameters['maxcount'	]);
            $parameters['profile'	] = (isset($parameters['profile'	]) == false ? NULL 	: $parameters['profile'		]);
            $parameters['days'	    ] = (isset($parameters['days'	    ]) == false ? NULL 	: $parameters['days'		]);
            $parameters['from_date'	] = (isset($parameters['from_date'	]) == false ? NULL 	: $parameters['from_date'   ]);
            $parameters['displayed' ] = (isset($parameters['displayed'  ]) == false ? NULL 	: (strlen($parameters['displayed']  ) === 0 ? NULL : $parameters['displayed']   ));
            if (is_null($parameters['maxcount']) == false){
                $parameters['maxcount'] = (integer)$parameters['maxcount'];
                $parameters['maxcount'] = (is_numeric($parameters['maxcount']) == false ? 100 : $parameters['maxcount']);
            }
            if (is_null($parameters['days']) == false){
                $parameters['days'] = (integer)$parameters['days'];
                $parameters['days'] = (is_numeric($parameters['days']) == false ? 30 : $parameters['days']);
            }
            //echo "<p>".var_dump($parameters['maxcount'])."</p>";
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
            }else{
                $parameters['targets_array'] = array();
            }
            $parameters['hidetitle'	    ] = ($parameters['hidetitle'	] == 'on' ? true : false );
            $parameters['thumbnails'	] = ($parameters['thumbnails'	] == 'on' ? true : false );
            $parameters['profile'	    ] = (is_string	($parameters['profile']		) == true ? $parameters['profile'	] : '#'		        );
            $parameters['from_date'	    ] = (is_string	($parameters['from_date']	) == true ? $parameters['from_date'	] : date('Y-m-d')   );
            $parameters['group'	        ] = (is_string	($parameters['group'            ]) == true ? $parameters['group'            ] : uniqid());
            $parameters['shown'	        ] = (is_integer	($parameters['shown'            ]) == true ? $parameters['shown'            ] : 0       );
            $parameters['post_type'	    ] = (is_string	($parameters['post_type'        ]) == true ? $parameters['post_type'        ] : 'all'   );
            $parameters['wrapper_width' ] = (is_integer	($parameters['wrapper_width'    ]) == true ? $parameters['wrapper_width'    ] : 23      );
            $parameters['wrapper_space' ] = (is_integer	($parameters['wrapper_space'    ]) == true ? $parameters['wrapper_space'    ] : 1       );
            $parameters['more_settings' ] = 'pure.settings.plugins.thumbnails.posts.more';
            $parameters['selection'     ] = (isset($parameters['selection'])    !== false   ? $parameters['selection']   : false);
            $parameters['selection'     ] = (is_array($parameters['selection']) !== false   ? $parameters['selection']   : false);
            $parameters['post_status'   ] = (isset($parameters['post_status'])  !== false   ? $parameters['post_status'] : 'publish');
            $parameters['post_status'   ] = ($parameters['post_status']         !== ''      ? $parameters['post_status'] : 'publish');
            $parameters['post_status'   ] = ($parameters['post_status']         !== false   ? $parameters['post_status'] : 'publish');
            $parameters['displayed'     ] = (is_string	($parameters['displayed'    ]) == true ? $parameters['displayed'    ] : 'none'          );
            $parameters['sandbox'       ] = (isset($parameters['sandbox']) !== false ? $parameters['sandbox'] : 'exclude_sandbox');
            $parameters['sandbox'       ] = (in_array($parameters['sandbox'], array('exclude_sandbox', 'all', 'only_sandbox')) !== false ? $parameters['sandbox'] : 'exclude_sandbox');
            return $parameters;
        }
        private function getTitleURL(){
            \Pure\Components\WordPress\Location\Special\Initialization::instance()->attach();
            $URLs = new \Pure\Components\WordPress\Location\Special\Register();
            switch($this->parameters['content']){
                case 'last':
                    return $URLs->getURL('TOP', array('type'=>'post'));
                    break;
                case 'popular':
                    return $URLs->getURL('TOP', array('type'=>'post'));
                    break;
                case 'group':
                    return '#';
                    break;
                default:
                    return '#';
                    break;
            }
        }
        private function title($title = false, $link_title = '', $link_href = ''){
            $template   = \Pure\Templates\Titles\Initialization::instance()->get($this->parameters['title_type']);
            $innerHTML  = '';
            if (is_null($this->parameters['title']) == false || $title !== false){
                if (is_null($template) === false){
                    $link_title = ($link_title  === '' ? __('see more', 'pure')  : $link_title   );
                    $link_href  = ($link_href   === '' ? $this->getTitleURL()           : $link_href    );
                    $innerHTML  = $template->get(
                        ($title !== false ? $title :$this->parameters['title']),
                        (object)array('link'=>(object)array('title'=>$link_title, 'href'=>$link_href))
                    );
                }
            }
            $template   = NULL;
            return $innerHTML;
        }
        private function simple($posts, $PostTemplate){
            switch($this->parameters['presentation']){
                case 'clear':
                    $innerHTML = '';
                    foreach($posts->posts as $post){
                        $innerHTML .= $PostTemplate->get(   $post,
                                                            (object)array( 'attribute' =>(object)array('name'=>'data-type-more-group','value'=>$this->parameters['group'])));
                    }
                    $innerHTML .= $this->more($PostTemplate, $posts);
                    return ($innerHTML !== '' ? $this->title().$innerHTML : '');
                    break;
                case 'slider':
                    $data           = new \stdClass();
                    $data->title    = (is_null($this->parameters['title']) === false ? $this->parameters['title'] : '');
                    $data->items    = array();
                    foreach($posts->posts as $post){
                        array_push($data->items, $PostTemplate->get($post,(object)array('fix_width'=>true)));
                    }
                    $slider         = \Pure\Templates\Sliders\Initialization::instance()->get($this->parameters['slider_template']);
                    $innerHTML      = $slider->get($data);
                    $slider         = NULL;
                    return $innerHTML;
                    break;
                case 'tabs':
                    $data       = new \stdClass();
                    $data->tabs = array();
                    $tab        = new \stdClass();
                    $tab->title = (is_null($this->parameters['title']) === false ? $this->parameters['title'] : '');
                    $tab->items = array();
                    foreach($posts->posts as $post){
                        array_push($tab->items, $PostTemplate->get($post));
                    }
                    array_push($data->tabs, $tab);
                    $tabs           = \Pure\Templates\Tabs\Initialization::instance()->get($this->parameters['tab_template']);
                    $innerHTML      = $tabs->get($data, (object)array('columns'=>$this->parameters['tabs_columns']));
                    $tabs           = NULL;
                    return $innerHTML;
                    break;
                case 'wrapper':
                    $wrapper    = \Pure\Templates\Positioning\Initialization::instance()->get('B');
                    $innerHTML  = '';
                    foreach($posts->posts as $post){
                        $innerHTML .= $PostTemplate->get(   $post,
                            (object)array( 'attribute' =>(object)array('name'=>'data-type-more-group','value'=>$this->parameters['group'])));
                    }
                    $innerHTML .= $this->more($PostTemplate, $posts);
                    $innerHTML  = ($innerHTML !== '' ? $wrapper->get(
                        $innerHTML,
                        (object)array(
                            'id'            =>uniqid(),
                            'column_width'  =>$this->parameters['wrapper_width'].'rem',
                            'node_type'     =>'article',
                            'space'         =>$this->parameters['wrapper_space'].'rem'
                        )
                    ) : '');
                    $wrapper    = NULL;
                    return ($innerHTML !== '' ? $this->title().$innerHTML : '');
                    break;
            }
            return '';
        }
        private function terms($posts, $PostTemplate, $property){
            switch($this->parameters['presentation']){
                case 'clear':
                    $innerHTML = '';
                    foreach($posts->$property as $key=>$term){
                        if ($this->parameters['hidetitle'] == false){
                            $innerHTML  .= $this->title($term->name, __('see more', 'pure'), $term->url);
                        }
                        foreach($posts->posts->$key as $post){
                            $innerHTML .= $PostTemplate->get(   $post,
                                                                (object)array( 'attribute' =>(object)array('name'=>'data-type-more-group','value'=>$this->parameters['group'])));
                        }
                    }
                    $innerHTML .= $this->more($PostTemplate, $posts);
                    return $innerHTML;
                    break;
                case 'slider':
                    $data           = new \stdClass();
                    $data->title    = (is_null($this->parameters['title']) === false ? $this->parameters['title'] : '');
                    $data->items    = array();
                    foreach($posts->$property as $key=>$term){
                        foreach($posts->posts->$key as $post){
                            array_push($data->items, $PostTemplate->get($post, (object)array('fix_width'=>true)));
                        }
                    }
                    $slider         = \Pure\Templates\Sliders\Initialization::instance()->get($this->parameters['slider_template']);
                    $innerHTML      = $slider->get($data);
                    $slider         = NULL;
                    return $innerHTML;
                    break;
                case 'tabs':
                    $data           = new \stdClass();
                    $data->tabs     = array();
                    foreach($posts->$property as $key=>$term){
                        $tab        = new \stdClass();
                        $tab->title = $term->name;
                        $tab->items = array();
                        foreach($posts->posts->$key as $post){
                            array_push($tab->items, $PostTemplate->get($post));
                        }
                        array_push($data->tabs, $tab);
                    }
                    $tabs           = \Pure\Templates\Tabs\Initialization::instance()->get($this->parameters['tab_template']);
                    $innerHTML      = $tabs->get($data, (object)array('columns'=>$this->parameters['tabs_columns']));
                    $tabs           = NULL;
                    return $innerHTML;
                    break;
            }
            return '';
        }
        private function categories($posts, $PostTemplate){
            return $this->terms($posts, $PostTemplate, 'categories');
        }
        private function tags($posts, $PostTemplate){
            return $this->terms($posts, $PostTemplate, 'tags');
        }
        private function authors($posts, $PostTemplate){
            switch($this->parameters['presentation']){
                case 'clear':
                    $innerHTML  = "";
                    foreach ($posts->posts as $author) {
                        if ($this->parameters['hidetitle'] == false){
                            $innerHTML  .= $this->title($author[0]->author->name, __('see more', 'pure'), get_author_posts_url($author[0]->author->id));
                        }
                        foreach($author as $post){
                            $innerHTML .= $PostTemplate->get(   $post,
                                                                (object)array( 'attribute' =>(object)array('name'=>'data-type-more-group','value'=>$this->parameters['group'])));
                        }
                    }
                    $innerHTML .= $this->more($PostTemplate, $posts);
                    return $innerHTML;
                    break;
                case 'slider':
                    $data           = new \stdClass();
                    $data->title    = (is_null($this->parameters['title']) === false ? $this->parameters['title'] : '');
                    $data->items    = array();
                    foreach ($posts->posts as $author) {
                        foreach($author as $post){
                            array_push($data->items, $PostTemplate->get($post, (object)array('fix_width'=>true)));
                        }
                    }
                    $slider         = \Pure\Templates\Sliders\Initialization::instance()->get($this->parameters['slider_template']);
                    $innerHTML      = $slider->get($data);
                    $slider         = NULL;
                    return $innerHTML;
                    break;
                case 'tabs':
                    $data           = new \stdClass();
                    $data->tabs     = array();
                    foreach($posts->posts as $author){
                        $tab        = new \stdClass();
                        $tab->title = $author[0]->category->name;
                        $tab->items = array();
                        foreach($author as $post){
                            array_push($tab->items, $PostTemplate->get($post));
                        }
                        array_push($data->tabs, $tab);
                    }
                    $tabs           = \Pure\Templates\Tabs\Initialization::instance()->get($this->parameters['tab_template']);
                    $innerHTML      = $tabs->get($data, (object)array('columns'=>$this->parameters['tabs_columns']));
                    $tabs           = NULL;
                    return $innerHTML;
                    break;
            }
            return '';
        }
        private function displayed(){
            switch($this->parameters['displayed']){
                case 'member':
                    if (in_array($this->parameters['content'], array(
                            'author', 'friends_author'
                        )) !== false){
                        $this->parameters['targets']        = (string)\Pure\Configuration::instance()->globals->IDs->user_id;
                        $this->parameters['targets_array']  = array((int)\Pure\Configuration::instance()->globals->IDs->user_id);
                    }
                    break;
                case 'post':
                    if (in_array($this->parameters['content'], array(
                            'defined'
                        )) !== false){
                        $this->parameters['targets']        = (string)\Pure\Configuration::instance()->globals->IDs->post_id;
                        $this->parameters['targets_array']  = array((int)\Pure\Configuration::instance()->globals->IDs->post_id);
                    }
                    if (in_array($this->parameters['content'], array(
                            'category'
                        )) !== false){
                        $categories                             = wp_get_post_categories(\Pure\Configuration::instance()->globals->IDs->post_id);
                        if (is_array($categories) !== false){
                            $this->parameters['targets']        = (string)implode(',', $categories);
                            $this->parameters['targets_array']  = $categories;
                        }
                    }
                    break;
                case 'group':
                    if (in_array($this->parameters['content'], array(
                            'group'
                        )) !== false){
                        $this->parameters['targets']        = (string)\Pure\Configuration::instance()->globals->IDs->group_id;
                        $this->parameters['targets_array']  = array((int)\Pure\Configuration::instance()->globals->IDs->group_id);
                    }
                    break;
            }
            if ($this->parameters['displayed'] === true){
            }
        }
        public function show(){
            $this->displayed();
            $innerHTML  = '';
            if ($this->parameters['post_type'] === 'all'){
                $this->parameters['post_type'] = array('post', 'event', 'report', 'question');
            }else{
                $this->parameters['post_type'] = array($this->parameters['post_type']);
            }
            $provider   = \Pure\Providers\Posts\Initialization::instance()->get($this->parameters['content']);
            if ($provider !== false) {
                $posts      = $provider->get($this->parameters);
                $provider   = NULL;
                if (isset($this->parameters['template']) === true && $posts !== false) {
                    $PostTemplate = \Pure\Templates\Posts\Thumbnails\Initialization::instance()->get($this->parameters['template']);
                    if (is_null($PostTemplate) === false) {
                        switch($this->parameters['content']){
                            case 'last':
                                $innerHTML = $this->simple      ($posts, $PostTemplate);
                                break;
                            case 'popular':
                                $innerHTML = $this->simple      ($posts, $PostTemplate);
                                break;
                            case 'category':
                                $innerHTML = $this->categories  ($posts, $PostTemplate);
                                break;
                            case 'tag':
                                $innerHTML = $this->tags        ($posts, $PostTemplate);
                                break;
                            case 'author':
                                $innerHTML = $this->authors     ($posts, $PostTemplate);
                                break;
                            case 'group':
                                $innerHTML = $this->simple      ($posts, $PostTemplate);
                                break;
                            case 'defined':
                                $innerHTML = $this->simple      ($posts, $PostTemplate);
                                break;
                            case 'friends_author':
                                $innerHTML = $this->simple      ($posts, $PostTemplate);
                                break;
                            case 'questions_solved':
                                $innerHTML = $this->simple      ($posts, $PostTemplate);
                                break;
                            case 'questions_unsolved':
                                $innerHTML = $this->simple      ($posts, $PostTemplate);
                                break;
                        }
                    }
                    $PostTemplate = NULL;
                }
            }
            return $innerHTML;
        }
        private function resources_more($parameters){
            \Pure\Components\More\A\Initialization::instance()->attach(false, 'after');
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->requests.'/settings/request.plugins.thumbnails.posts.php'));
            $settings = new \Pure\Requests\Plugins\Thumbnails\Posts\Settings\Initialization();
            $settings->init($parameters);
            $settings = NULL;
        }
        private function more($PostTemplate, $posts){
            $innerHTML = '';
            if (($this->parameters['more'] === true || $this->parameters['more'] === 'on') && method_exists($PostTemplate, 'more') === true){
                if ($posts->shown < $posts->total){
                    if (is_array($this->parameters['post_type']) !== false){
                        if (count($this->parameters['post_type']) > 1){
                            $this->parameters['post_type'] = 'all';
                        }else{
                            $this->parameters['post_type'] = $this->parameters['post_type'][0];
                        }
                    }
                    $this->parameters['shown'] = $posts->shown;
                    $this->parameters['total'] = $posts->total;
                    $innerHTML = $PostTemplate->more($this->parameters);
                    $this->resources_more($this->parameters);
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