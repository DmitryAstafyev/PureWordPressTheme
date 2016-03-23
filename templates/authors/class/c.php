<?php
namespace Pure\Templates\Authors{
    class C{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
        }
        private function getExcerpt($post){
            if ($post->post->excerpt !== false && $post->post->excerpt !== ''){
                return $post->post->excerpt;
            }else{
                if ($post->post->images !== false){
                    return '"'.$post->post->title.'" '.__('consists images from', 'pure').' '.$post->author->name;
                }
                if ($post->post->media !== false){
                    return '"'.$post->post->title.'" '.__('consists media content from', 'pure').' '.$post->author->name;
                }
            }
            return false;
        }
        private function innerHTMLPosts($user_id){
            $innerHTML  = '';
            $Posts      = \Pure\Providers\Posts\Initialization::instance()->get('author');
            $posts      = $Posts->get(array(
                'from_date'     =>date("Y-m-d H:i:s"),
                'days'          =>365,
                'thumbnails'    =>false,
                'targets_array' =>array($user_id),
                'selection'     =>false,
                'post_type'     =>array('post'),
                'shown'         =>0,
                'post_status'   =>'publish',
                'maxcount'      =>5,
                'profile'       =>'',
            ));
            $Posts      = NULL;
            if ($posts !== false){
                if (isset($posts->posts) !== false){
                    if (isset($posts->posts->$user_id) !== false){
                        $count = 0;
                        foreach($posts->posts->$user_id as $post){
                            $excerpt = $this->getExcerpt($post);
                            if ($excerpt !== false){
                                $innerHTML .= Initialization::instance()->html(
                                    'C/post',
                                    array(
                                        array('post_title',     $post->post->title                              ),
                                        array('post_date',      date('F j, Y', strtotime($post->post->date))    ),
                                        array('post_excerpt',   $excerpt                                        ),
                                        array('post_url',       $post->post->url                                ),
                                        array('read_more',      __('read more', 'pure')                  ),
                                    )
                                );
                                $count ++;
                                if ($count === 2){
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            return $innerHTML;
        }
        public  function top($data, $parameters = NULL){
            return $this->simple($data, $parameters);
        }
        public function simple($data, $parameters = NULL){
            $this->validate($parameters);
            $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $innerHTML      = Initialization::instance()->html(
                'C/wrapper',
                array(
                    array('attribute',  $attribute_str                          ),
                    array('avatar',     $data->author->avatar                   ),
                    array('url',        $data->author->urls->member             ),
                    array('name',       $data->author->name                     ),
                    array('how_long',   $data->author->how_long                 ),
                    array('posts',      $this->innerHTMLPosts($data->author->id)),
                )
            );
            return $innerHTML;
        }
        private function resources_more(){
            \Pure\Resources\Compressor::instance()->CSS(
                \Pure\Templates\Authors\Initialization::instance()->configuration->paths->css.'/'.'C.more.css');
            \Pure\Templates\ProgressBar\Initialization::instance()->get('D');
        }
        public function more($parameters){
            $this->resources_more($parameters);
            $innerHTML =    '<div data-type-element="Author.Thumbnail.C.More" '.
                                    'data-type-more-group="'.   $parameters['group'].'" '.
                                    'data-type-more-max="'.     $parameters['maxcount'].'" '.
                                    'data-type-more-template="'.$parameters['template'].'" '.
                                    'data-type-more-progress="D" '.
                                    'data-type-more-settings="'.$parameters['more_settings'].'" '.
                                    'data-type-use="Pure.Components.More">'.
                                '<p data-type-element="Author.Thumbnail.C.More">more</p>'.
                            '</div>'.
                            '<p data-element-type="Author.Thumbnail.C.More.Info">'.
                                '<span data-element-type="Author.Thumbnail.C.More.Info" data-type-use="Pure.Components.More.Shown" data-type-more-group="'.$parameters['group'].'">'.$parameters['shown'].'</span> / '.
                                '<span data-element-type="Author.Thumbnail.C.More.Info">'.$parameters['total'].'</span>'.
                            '</p>'.
                            '<div data-type-element="Author.Thumbnail.C.Reset"></div>';
            return $innerHTML;
        }
    }
}
?>