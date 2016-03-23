<?php
namespace Pure\Templates\Posts\Thumbnails{
    class F{
        private $current = NULL;
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
        }
        private function innerHTML_post_without_miniature($data, $parameters, $attribute_str){
            $innerHTML = Initialization::instance()->html(
                'F/post_with_excerpt',
                array(
                    array('attribute',  $attribute_str                                              ),
                    array('title',      $data->post->title                                          ),
                    array('label_by',   __('by', 'pure')                                     ),
                    array('author',     $data->author->name                                         ),
                    array('created',    date('F j, Y', strtotime($data->post->date))                ),
                    array('comments',   $data->post->comments                                       ),
                    array('views',      $data->post->views                                          ),
                    array('karma',      ($data->post->karma >= 0 ? ($data->post->karma === 0 ? '' : '+').$data->post->karma : '-'.$data->post->karma)          ),
                    array('excerpt',    ($data->post->excerpt !== false ? $data->post->excerpt : '')),
                    array('read_more',  __('read more', 'pure')                              ),
                    array('post_url',   $data->post->url                                            ),
                )
            );
            return $innerHTML;
        }
        private function innerHTML_post_with_miniature($data, $parameters, $attribute_str){
            if ($data->post->excerpt !== false && $data->post->excerpt !== ''){
                $innerHTML = Initialization::instance()->html(
                    'F/post_with_miniature_excerpt',
                    array(
                        array('attribute',  $attribute_str                                              ),
                        array('title',      $data->post->title                                          ),
                        array('label_by',   __('by', 'pure')                                     ),
                        array('author',     $data->author->name                                         ),
                        array('created',    date('F j, Y', strtotime($data->post->date))                ),
                        array('comments',   $data->post->comments                                       ),
                        array('views',      $data->post->views                                          ),
                        array('karma',      ($data->post->karma >= 0 ? ($data->post->karma === 0 ? '' : '+').$data->post->karma : '-'.$data->post->karma)          ),
                        array('excerpt',    $data->post->excerpt                                        ),
                        array('read_more',  __('read more', 'pure')                              ),
                        array('post_url',   $data->post->url                                            ),
                        array('miniature',  $data->post->miniature                                      ),
                    )
                );
            }else{
                $innerHTML = Initialization::instance()->html(
                    'F/post_with_miniature',
                    array(
                        array('attribute',  $attribute_str                                              ),
                        array('title',      $data->post->title                                          ),
                        array('label_by',   __('by', 'pure')                                     ),
                        array('author',     $data->author->name                                         ),
                        array('created',    date('F j, Y', strtotime($data->post->date))                ),
                        array('comments',   $data->post->comments                                       ),
                        array('views',      $data->post->views                                          ),
                        array('karma',      ($data->post->karma >= 0 ? ($data->post->karma === 0 ? '' : '+').$data->post->karma : '-'.$data->post->karma)          ),
                        array('read_more',  __('read more', 'pure')                              ),
                        array('post_url',   $data->post->url                                            ),
                        array('miniature',  $data->post->miniature                                      ),
                    )
                );
            }
            return $innerHTML;
        }
        private function innerHTML_gallery($data, $innerHTMLItems, $parameters, $attribute_str, $gallery_type = ''){
            if ($data->post->excerpt !== false && $data->post->excerpt !== ''){
                $innerHTML = Initialization::instance()->html(
                    'F/post_gallery_with_excerpt',
                    array(
                        array('attribute',      $attribute_str                                              ),
                        array('title',          $data->post->title                                          ),
                        array('label_by',       __('by', 'pure')                                     ),
                        array('author',         $data->author->name                                         ),
                        array('created',        date('F j, Y', strtotime($data->post->date))                ),
                        array('comments',       $data->post->comments                                       ),
                        array('views',          $data->post->views                                          ),
                        array('karma',          ($data->post->karma >= 0 ? ($data->post->karma === 0 ? '' : '+').$data->post->karma : '-'.$data->post->karma)          ),
                        array('excerpt',        $data->post->excerpt                                        ),
                        array('read_more',      __('read more', 'pure')                              ),
                        array('post_url',       $data->post->url                                            ),
                        array('gallery_type',   $gallery_type                                               ),
                        array('items',          $innerHTMLItems                                             ),
                    )
                );
                //
            }else {
                $innerHTML = Initialization::instance()->html(
                    'F/post_gallery',
                    array(
                        array('attribute',      $attribute_str                                              ),
                        array('title',          $data->post->title                                          ),
                        array('label_by',       __('by', 'pure')                                     ),
                        array('author',         $data->author->name                                         ),
                        array('created',        date('F j, Y', strtotime($data->post->date))                ),
                        array('comments',       $data->post->comments                                       ),
                        array('views',          $data->post->views                                          ),
                        array('karma',          ($data->post->karma >= 0 ? ($data->post->karma === 0 ? '' : '+').$data->post->karma : '-'.$data->post->karma)          ),
                        array('read_more',      __('read more', 'pure')                              ),
                        array('post_url',       $data->post->url                                            ),
                        array('gallery_type',   $gallery_type                                               ),
                        array('items',          $innerHTMLItems                                             ),
                    )
                );
            }
            return $innerHTML;
        }
        private function innerHTML_gallery_images($data, $parameters, $attribute_str, $max_items = 10){
            $slider         = \Pure\Templates\Sliders\Initialization::instance()->get('B');
            $_data          = (object)array('items'=>array());
            $count          = 0;
            foreach($data->post->media->gallery as $gallery){
                foreach($gallery->medium as $image){
                    $_data->items[] = '<div data-element-type="Pure.Post.Thumbnail.F.Gallery.Item" style="background-image:url('.$image->src.')"></div>';
                    $count ++;
                    if ($count >= $max_items){ break; }
                }
                if ($count >= $max_items){ break; }
            }
            $innerHTMLItems = $slider->get($_data);
            $slider         = NULL;
            return $this->innerHTML_gallery($data, $innerHTMLItems, $parameters, $attribute_str);
        }
        private function innerHTML_images($data, $parameters, $attribute_str, $max_items = 10){
            $slider             = \Pure\Templates\Sliders\Initialization::instance()->get('B');
            $_data              = (object)array('items'=>array());
            $count              = 0;
            foreach($data->post->images as $image){
                $_data->items[] =   '<div data-element-type="Pure.Post.Thumbnail.F.Gallery.Item" style="background-image:url('.$image->src.')"></div>';
                $count ++;
                if ($count >= $max_items){ break; }
            }
            $innerHTMLItems     = $slider->get($_data);
            $slider             = NULL;
            return $this->innerHTML_gallery($data, $innerHTMLItems, $parameters, $attribute_str);
        }
        private function innerHTML_embed($data, $parameters, $attribute_str, $max_items = 10){
            $slider         = \Pure\Templates\Sliders\Initialization::instance()->get('B');
            $_data          = (object)array('items'=>array());
            $count          = 0;
            foreach($data->post->media->embed as $src){
                $_data->items[] = '<div data-element-type="Pure.Post.Thumbnail.F.Gallery.Item">'.wp_oembed_get($src, array('width'=>400)).'</div>';
                $count ++;
                if ($count >= $max_items){ break; }
            }
            $innerHTMLItems = $slider->get($_data);
            $slider         = NULL;
            return $this->innerHTML_gallery($data, $innerHTMLItems, $parameters, $attribute_str, 'iframe');
        }


        private function innerHTML_audio($data, $parameters, $attribute_str, $max_items = 10){
            $id                 = uniqid('Pure_Posts_Thumbnail_D_AudioPlayer_');
            $slider             = \Pure\Templates\Sliders\Initialization::instance()->get('B');
            $progress           = \Pure\Components\Slider\A\Initialization::instance();
            $audioplayer        = \Pure\Components\AudioPlayer\A\Initialization::instance();
            $playlist           = $audioplayer->playlist($data->post->media->audio, $id, false);
            $innerHTML          = $playlist->innerHTML;
            $_data              = (object)array('items'=>array());
            $count              = 0;
            foreach($data->post->media->audio as $audio){
                $_data->items[] = Initialization::instance()->html(
                    'F/post_audio_track',
                    array(
                        array('name',           esc_html($audio->post_title     )),
                        array('description',    esc_html($audio->post_content   )),
                    )
                );
                $count ++;
                if ($count >= $max_items){ break; }
            }
            $innerHTMLItems     = $slider->get($_data);
            if ($data->post->excerpt !== false && $data->post->excerpt !== ''){
                $innerHTML .= Initialization::instance()->html(
                    'F/post_audio_with_excerpt',
                    array(
                        array('attribute',      $attribute_str                                              ),
                        array('title',          $data->post->title                                          ),
                        array('label_by',       __('by', 'pure')                                     ),
                        array('author',         $data->author->name                                         ),
                        array('created',        date('F j, Y', strtotime($data->post->date))                ),
                        array('comments',       $data->post->comments                                       ),
                        array('views',          $data->post->views                                          ),
                        array('karma',          ($data->post->karma >= 0 ? ($data->post->karma === 0 ? '' : '+').$data->post->karma : '-'.$data->post->karma)          ),
                        array('excerpt',        $data->post->excerpt                                        ),
                        array('read_more',      __('read more', 'pure')                              ),
                        array('post_url',       $data->post->url                                            ),
                        array('tracks',         $innerHTMLItems                                             ),
                        array('id_container',   $id                                                         ),
                        array('playlist',       $playlist->property                                         ),
                    )
                );
                //
            }else {
                $innerHTML .= Initialization::instance()->html(
                    'F/post_audio',
                    array(
                        array('attribute',      $attribute_str                                              ),
                        array('title',          $data->post->title                                          ),
                        array('label_by',       __('by', 'pure')                                     ),
                        array('author',         $data->author->name                                         ),
                        array('created',        date('F j, Y', strtotime($data->post->date))                ),
                        array('comments',       $data->post->comments                                       ),
                        array('views',          $data->post->views                                          ),
                        array('karma',          ($data->post->karma >= 0 ? ($data->post->karma === 0 ? '' : '+').$data->post->karma : '-'.$data->post->karma)),
                        array('read_more',      __('read more', 'pure')                              ),
                        array('post_url',       $data->post->url                                            ),
                        array('tracks',         $innerHTMLItems                                             ),
                        array('id_container',   $id                                                         ),
                        array('playlist',       $playlist->property                                         ),
                    )
                );
            }
            $innerHTML .= $audioplayer->call_scripts();
            $innerHTML .= $progress->call_scripts();
            $audioplayer->attach();
            $progress->attach();
            $audioplayer= NULL;
            $progress   = NULL;
            $slider     = NULL;
            return $innerHTML;
        }
        public function get($data, $parameters = NULL){
            $this->validate($parameters);
            $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $innerHTML      = '';
            switch ($data->post->post_type){
                case 'post':
                    switch($data->post->type){
                        case 'post_without_miniature':
                            $innerHTML = $this->innerHTML_post_without_miniature($data, $parameters, $attribute_str);
                            break;
                        case 'post_with_miniature':
                            $innerHTML = $this->innerHTML_post_with_miniature($data, $parameters, $attribute_str);
                            break;
                        case 'gallery':
                            $innerHTML = $this->innerHTML_gallery_images($data, $parameters, $attribute_str);
                            break;
                        case 'audio':
                            $innerHTML = $this->innerHTML_audio($data, $parameters, $attribute_str);
                            break;
                        case 'embed':
                            $innerHTML = $this->innerHTML_embed($data, $parameters, $attribute_str);
                            break;
                        case 'video':
                            $innerHTML = '';
                            break;
                        case 'images':
                            $innerHTML = $this->innerHTML_images($data, $parameters, $attribute_str);
                            break;
                        default:
                            $innerHTML = $this->innerHTML_post_without_miniature($data, $parameters, $attribute_str);
                            break;
                    }
                    break;
                case 'event':
                    //do not support
                    break;
            }
            return $innerHTML;
        }
        private function resources_more(){
            \Pure\Resources\Compressor::instance()->CSS(
                \Pure\Templates\Posts\Thumbnails\Initialization::instance()->configuration->paths->css.'/'.'D.more.css'
            );
            $innerHTML =   \Pure\Templates\ProgressBar\Initialization::instance()->get_resources_of('D');
            return $innerHTML;
        }
        public function more($parameters){
            $innerHTML =    $this->resources_more($parameters).
                            '<div data-type-element="Pure.Posts.Thumbnail.D.More" '.
                                'data-type-more-group="'.   $parameters['group'].'" '.
                                'data-type-more-max="'.     $parameters['maxcount'].'" '.
                                'data-type-more-template="'.$parameters['template'].'" '.
                                'data-type-more-post_type="'.$parameters['post_type'].'" '.
                                'data-type-more-settings="'.$parameters['more_settings'].'" '.
                                'data-type-more-progress="D" '.
                                'data-type-use="Pure.Components.More">'.
                                '<p data-type-element="Pure.Posts.Thumbnail.D.More">'.__('more', 'pure').'</p>'.
                            '</div>'.
                            '<p data-element-type="Pure.Posts.Thumbnail.D.More.Info">'.
                                '<span data-element-type="Pure.Posts.Thumbnail.D.More.Info" data-type-use="Pure.Components.More.Shown" data-type-more-group="'.$parameters['group'].'">'.$parameters['shown'].'</span> / '.
                                '<span data-element-type="Pure.Posts.Thumbnail.D.More.Info">'.$parameters['total'].'</span>'.
                            '</p>'.
                            '<div data-type-element="Pure.Posts.Thumbnail.D.Reset"></div>';
            return $innerHTML;
        }
    }
}
?>