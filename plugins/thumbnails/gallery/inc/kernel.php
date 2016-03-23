<?php
namespace Pure\Plugins\Thumbnails\Gallery {
    class Builder{
        private $parameters;
        private $id = 0;
        function __construct($parameters){
            $this->parameters = $this->validate($parameters);
            if (is_bool($this->parameters) == true){
                throw new \Exception("Pure\Plugins\Thumbnails\Gallery\Builder\__construct::: cannot validate settings of widget", E_USER_WARNING);
            }
        }
        private function validate($parameters){
            if (isset($parameters['content']) == false){
                return false;
            }
            if ($parameters['content'] != 'last' 	    &&
                $parameters['content'] != 'popular'     &&
                $parameters['content'] != 'by_authors'  &&
                $parameters['content'] != 'by_categories'){
                return false;
            }
            $parameters['tragets'	        ] = (isset($parameters['tragets'	        ]) == false ? NULL 	: (strlen($parameters['tragets']) === 0 ? NULL : $parameters['tragets'] ));
            $parameters['title'		        ] = (isset($parameters['title'		        ]) == false ? NULL 	: (strlen($parameters['title']  ) === 0 ? NULL : $parameters['title']   ));
            $parameters['maxcount'	        ] = (isset($parameters['maxcount'	        ]) == false ? NULL 	: $parameters['maxcount']);
            $parameters['profile'	        ] = (isset($parameters['profile'	        ]) == false ? NULL 	: $parameters['profile']);
            if (is_null($parameters['maxcount']) == false){
                $parameters['maxcount'] = (integer)$parameters['maxcount'];
                $parameters['maxcount'] = (is_numeric($parameters['maxcount']) == false ? 100 : $parameters['maxcount']);
            }
            if (is_null($parameters['days']) == false){
                $parameters['days'] = (integer)$parameters['days'];
                $parameters['days'] = (is_numeric($parameters['days']) == false ? 30 : $parameters['days']);
            }
            //echo "<p>".var_dump($parameters['tragets'])."</p>";
            //echo "<p>==============================================================</p>";
            if (is_null($parameters['tragets']) == false){
                $targets	    = preg_split('/,/', $parameters['tragets']);
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
                $parameters['tragets']          = $strTargets;
                $parameters['tragets_array']    = $targets_array;
            }
            $parameters['profile'	        ] = (is_string	($parameters['profile'          ]) == true ? $parameters['profile'	        ] : '#'		        );
            $parameters['from_date'	        ] = (is_string	($parameters['from_date'        ]) == true ? $parameters['from_date'        ] : date('Y-m-d')   );
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
        private function getGallariesData($post){
            if (strpos($post->post_content, '[gallery ids=') !== false){
                \Pure\Components\Tools\HTMLStrings\Initialization::instance()->attach();
                $strings                = new \Pure\Components\Tools\HTMLStrings\HTMLParser();
                $gallaries              = new \stdClass();
                $gallaries->gallaries   = array();
                $images_count           = 0;
                preg_match('/\[gallery.*?\]/i', $post->post_content, $IDs);
                foreach($IDs as $ID){
                    $ID_string      = preg_replace("/[^0-9\,]/", '', $ID);
                    $attacments	    = preg_split('/,/', $ID_string);
                    $gallery        = array();
                    foreach($attacments as $attacment){
                        $image      = new \stdClass();
                        $tag        = wp_get_attachment_link($attacment, 'thumbnail', true);
                        $url        = $strings->get_attribute($tag, 'src');
                        $image->id  = $attacment;
                        $image->url = $url;
                        array_push($gallery, $image);
                        $images_count ++;
                    }
                    array_push($gallaries->gallaries, $gallery);
                }
                $gallaries->images      = $images_count;
                $strings = NULL;
                return $gallaries;
            }
            return NULL;
        }
        private function fillGalleryProperty($galleries){
            $_gallery = array();
            foreach($galleries as $gallery){
                $target = false;
                $target = ($target === false ? (count($gallery->thumbnails   ) > 0 ? 'thumbnails'    : false) : $target);
                $target = ($target === false ? (count($gallery->medium       ) > 0 ? 'medium'        : false) : $target);
                if ($target !== false){
                    foreach($gallery->$target as $image){
                        $_gallery[] = (object)array(
                            'url'=>$image->src
                        );
                    }
                }
            }
            return (count($_gallery) === 0 ? false : $_gallery);
        }
        public function render(){
            $innerHTML  = '';
            $provider   = \Pure\Providers\Posts\Initialization::instance()->get($this->parameters['content']);
            if ($provider !== false) {
                $this->parameters['shown']          = 0;
                $this->parameters['selection']      = array('gallery');
                $this->parameters['post_type']      = 'post';
                $this->parameters['post_status']    = 'publish';
                $this->parameters['thumbnails']     = false;
                $this->parameters['targets_array']  = array();
                $posts      = $provider->get($this->parameters);
                $provider   = NULL;
                $innerHTML  = $this->title();
                if ($posts !== false){
                    \Pure\Components\WordPress\Post\Parser\Initialization::instance()->attach();
                    $PostParser = new \Pure\Components\WordPress\Post\Parser\Parser();
                    $Template   = \Pure\Templates\Galleries\Initialization::instance()->get($this->parameters['template']);
                    foreach($posts->posts as $post){
                        $galleries    = $PostParser->gallery($post->post->id);
                        if ($galleries !== false){
                            $galleries = $this->fillGalleryProperty($galleries);
                            if ($galleries !== false){
                                $post->images       = $galleries;
                                $innerHTML          .= $Template->get($post);
                            }
                        }

                    }
                    $Template   = NULL;
                    $PostParser = NULL;
                }
            }
            return $innerHTML;
        }
    }
}
?>