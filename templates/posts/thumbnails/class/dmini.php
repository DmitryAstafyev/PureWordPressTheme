<?php
namespace Pure\Templates\Posts\Thumbnails{
    class DMini{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
        }
        private function innerHTML_post_without_miniature($data, $parameters, $attribute_str){
            $innerHTML =    '<!--BEGIN: Thumbnail.D : POST WITHOUT MINIATURE -->'.
                            '<article data-custom-element="Pure.Posts.Thumbnail.DMini.Container" data-post-type="PostNoMiniature" '.$attribute_str.'>'.
                                '<p data-element-type="Pure.Posts.Thumbnail.DMini.Post.Name" data-post-mark="Standart">'.$data->post->title.'</p>'.
                                '<p data-element-type="Pure.Posts.Thumbnail.DMini.Statistic"><span data-element-type="Date">'.date('F j, Y', strtotime($data->post->date)).'</span>, views: <span data-element-type="Views">'.$data->post->views.'</span></p>'.
                                '<p data-element-type="Pure.Posts.Thumbnail.DMini.Excerpt">'.($data->post->excerpt !== false ? $data->post->excerpt : '').'</p>'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Controls">'.
                                    '<a data-element-type="Pure.Posts.Thumbnail.DMini.Button" data-button-type="More" href="'.$data->post->url.'">read more</a>'.
                                    '<div data-element-type="Pure.Posts.Thumbnail.DMini.ResetFloat"></div>'.
                                '</div>'.
                            '</article>'.
                            '<!--END: Thumbnail.D : POST WITHOUT MINIATURE -->';
            return $innerHTML;
        }
        private function innerHTML_post_with_miniature($data, $parameters, $attribute_str){
            $innerHTML =    '<!--BEGIN: Thumbnail.D : POST WITH MINIATURE -->'.
                            '<article data-custom-element="Pure.Posts.Thumbnail.DMini.Container" data-post-type="PostMiniature" '.$attribute_str.'>'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Miniature" style="background-image:url('.$data->post->miniature.')">'.
                                '</div>'.
                                '<p data-element-type="Pure.Posts.Thumbnail.DMini.Gallery.Name" data-post-mark="Standart">'.$data->post->title.'</p>'.
                                '<p data-element-type="Pure.Posts.Thumbnail.DMini.Statistic"><span data-element-type="Date">'.date('F j, Y', strtotime($data->post->date)).'</span>, views: <span data-element-type="Views">'.$data->post->views.'</span></p>'.
                                '<p data-element-type="Pure.Posts.Thumbnail.DMini.Excerpt">'.$data->post->excerpt.'</p>'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Controls">'.
                                    '<a data-element-type="Pure.Posts.Thumbnail.DMini.Button" data-button-type="More" href="'.$data->post->url.'">read more</a>'.
                                    '<div data-element-type="Pure.Posts.Thumbnail.DMini.ResetFloat"></div>'.
                                '</div>'.
                            '</article>'.
                            '<!--END: Thumbnail.D : POST WITH MINIATURE -->';
            return $innerHTML;
        }
        private function innerHTML_gallery($data, $parameters, $attribute_str, $max_items = 10){
            $slider     = \Pure\Templates\Sliders\Initialization::instance()->get('B');
            $innerHTML  =   '<!--BEGIN: Thumbnail.D : POST WITH IMAGES -->'.
                            '<article data-custom-element="Pure.Posts.Thumbnail.DMini.Container" data-post-type="PostGallery" '.$attribute_str.' data-engine-element="parent">'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Gallery">';
            $_data      = (object)array('items'=>array());
            $count      = 0;
            foreach($data->post->media->gallery as $gallery){
                foreach($gallery->medium as $image){
                    $_data->items[] = '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Gallery.Item" style="background-image:url('.$image->src.')"></div>';
                    $count ++;
                    if ($count >= $max_items){ break; }
                }
                if ($count >= $max_items){ break; }
            }
            $innerHTML .=           $slider->get($_data);
            $innerHTML .=       '</div>'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Gallery.Controlls">'.
                                    '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Gallery.Controlls.Left" data-engine-type="Slider.B.Button.Left">'.
                                    '</div>'.
                                    '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Gallery.Controlls.Right" data-engine-type="Slider.B.Button.Right">'.
                                    '</div>'.
                                '</div>'.
                                '<p data-element-type="Pure.Posts.Thumbnail.DMini.Gallery.Name" data-post-mark="Standart">'.$data->post->title.'</p>'.
                                '<p data-element-type="Pure.Posts.Thumbnail.DMini.Statistic"><span data-element-type="Date">'.date('F j, Y', strtotime($data->post->date)).'</span>, views: <span data-element-type="Views">'.$data->post->views.'</span></p>'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Controls">'.
                                    '<a data-element-type="Pure.Posts.Thumbnail.DMini.Button" data-button-type="More" href="'.$data->post->url.'">read more</a>'.
                                    '<div data-element-type="Pure.Posts.Thumbnail.DMini.ResetFloat"></div>'.
                                '</div>'.
                            '</article>'.
                            '<!--END: Thumbnail.D : POST WITH IMAGES -->';
            $slider = NULL;
            return $innerHTML;
        }
        private function innerHTML_audio($data, $parameters, $attribute_str, $max_items = 10){
            $id         = uniqid('Pure_Posts_Thumbnail_D_AudioPlayer_');
            $slider     = \Pure\Templates\Sliders\Initialization::instance()->get('B');
            $progress   = \Pure\Components\Slider\A\Initialization::instance();
            $audioplayer= \Pure\Components\AudioPlayer\A\Initialization::instance();
            $playlist   = $audioplayer->playlist($data->post->media->audio, $id, false);
            $innerHTML  = $playlist->innerHTML;
            $innerHTML .=   '<!--BEGIN: Thumbnail.D : POST WITH AUDIO -->'.
                            '<article data-custom-element="Pure.Posts.Thumbnail.DMini.Container" data-post-type="PostVideo" '.$attribute_str.' data-engine-element="parent" data-engine-element-ID="'.$id.'">'.
                                '<p data-element-type="Pure.Posts.Thumbnail.DMini.Audio.Name" data-post-mark="Standart">'.$data->post->title.'</p>'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Audios">';
            $_data      = (object)array('items'=>array());
            $count      = 0;
            foreach($data->post->media->audio as $audio){
                $_data->items[] =   '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Audios.Track">'.
                                        '<p data-element-type="Pure.Posts.Thumbnail.DMini.Audio.Track.Name">'.esc_html($audio->post_title).'</p>'.
                                        '<p data-element-type="Pure.Posts.Thumbnail.DMini.Audio.Track.Info">'.esc_html($audio->post_content).'</p>'.
                                    '</div>';
                $count ++;
                if ($count >= $max_items){ break; }
            }
            $innerHTML  .= $slider->get($_data);
            $innerHTML  .=      '</div>'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Audio.Controlls" '.
                                     'data-type-use="Pure.Components.AudioPlayer.A" '.
                                     'data-AudioPlayer-handle-volume="pure.components.slider.A.Handles.set" '.
                                     'data-AudioPlayer-handle-volume-param="'.$id.'_volume" '.
                                     'data-AudioPlayer-handle-progress="pure.components.slider.A.Handles.set" '.
                                     'data-AudioPlayer-handle-progress-param="'.$id.'_progress" '.
                                     'data-AudioPlayer-handle-ghost="pure.components.slider.A.Handles.ghost" '.
                                     'data-AudioPlayer-handle-ghost-param="'.$id.'" '.
                                     'data-type-AudioPlayer-playlist="'.$playlist->property.'" '.
                                     'data-type-AudioPlayer-ID="'.$id.'">'.
                                    '<input data-custom-element="Pure.Posts.Thumbnail.DMini.Audio.Controlls.More" type="checkbox" id="'.$id.'"/>'.
                                    '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Audio.Controlls.Basic">'.
                                        '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Audio.Controlls.Left" data-engine-type="Slider.B.Button.Left" data-type-AudioPlayer="previous">'.
                                        '</div>'.
                                        '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Audio.Controlls.Play" data-type-AudioPlayer-state="play" data-type-AudioPlayer="playpause">'.
                                        '</div>'.
                                        '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Audio.Controlls.Right" data-engine-type="Slider.B.Button.Right" data-type-AudioPlayer="next">'.
                                        '</div>'.
                                    '</div>'.
                                '</div>'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Controls">'.
                                    '<p data-element-type="Pure.Posts.Thumbnail.DMini.Statistic"><span data-element-type="Date">'.date('F j, Y', strtotime($data->post->date)).'</span>, views: <span data-element-type="Views">'.$data->post->views.'</span></p>'.
                                    '<a data-element-type="Pure.Posts.Thumbnail.DMini.Button" data-button-type="More" href="'.$data->post->url.'">read more</a>'.
                                    '<div data-element-type="Pure.Posts.Thumbnail.DMini.ResetFloat"></div>'.
                                '</div>'.
                            '</article>'.
                            '<!--END: Thumbnail.D : POST WITH AUDIO -->';
            $innerHTML .= $audioplayer->call_scripts();
            $innerHTML .= $progress->call_scripts();
            $audioplayer->attach();
            $progress->attach();
            $audioplayer= NULL;
            $progress   = NULL;
            $slider     = NULL;
            return $innerHTML;
        }
        private function innerHTML_embed($data, $parameters, $attribute_str, $max_items = 10){
            $slider     = \Pure\Templates\Sliders\Initialization::instance()->get('B');
            $innerHTML  =   '<!--BEGIN: Thumbnail.D : POST WITH VIDEO -->'.
                            '<article data-custom-element="Pure.Posts.Thumbnail.DMini.Container" data-post-type="PostVideo" '.$attribute_str.' data-engine-element="parent">'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Video">';
            $_data      = (object)array('items'=>array());
            $count      = 0;
            foreach($data->post->media->embed as $src){
                $_data->items[] = '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Video.Item">'.wp_oembed_get($src, array('width'=>400)).'</div>';
                $count ++;
                if ($count >= $max_items){ break; }
            }
            $innerHTML .=           $slider->get($_data);
            $innerHTML .=       '</div>'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Video.Controlls.Left" data-engine-type="Slider.B.Button.Left">'.
                                '</div>'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Video.Controlls.Right" data-engine-type="Slider.B.Button.Right">'.
                                '</div>'.
                                '<p data-element-type="Pure.Posts.Thumbnail.DMini.Video.Name" data-post-mark="Standart">'.$data->post->title.'</p>'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Controls">'.
                                    '<p data-element-type="Pure.Posts.Thumbnail.DMini.Statistic"><span data-element-type="Date">'.date('F j, Y', strtotime($data->post->date)).'</span>, views: <span data-element-type="Views">'.$data->post->views.'</span></p>'.
                                    '<a data-element-type="Pure.Posts.Thumbnail.DMini.Button" data-button-type="More" href="'.$data->post->url.'">read more</a>'.
                                    '<div data-element-type="Pure.Posts.Thumbnail.DMini.ResetFloat"></div>'.
                                '</div>'.
                            '</article>'.
                            '<!--END: Thumbnail.D : POST WITH VIDEO -->';
            $slider = NULL;
            return $innerHTML;
        }
        private function innerHTML_event_with_miniature($data, $parameters, $attribute_str, $no_miniature = false){
            $innerHTML = '';
            if ($data->event !== false){
                $timestamp  = strtotime($data->event->start);
                $day        = date('d', $timestamp);
                $time       = date('F Y G:i', $timestamp);
                $place      = ($data->event->place !== '' ? $data->event->place : $data->event->on_map);
                $innerHTML =    '<!--BEGIN: Thumbnail.D : EVENT -->'.
                                '<article data-custom-element="Pure.Posts.Thumbnail.DMini.Container" data-post-type="Event" '.$attribute_str.'>'.
                                    '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Event.Image" style="background-image:url('.
                                        ($no_miniature === false ? $data->post->miniature : \Pure\Templates\Posts\Thumbnails\Initialization::instance()->configuration->urls->images.'/A.event.jpg').')">'.
                                    '</div>'.
                                    '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Event.Info">'.
                                        '<p data-element-type="Pure.Posts.Thumbnail.DMini.Event.Date"><span data-element-type="Event.Day">'.$day.'</span><sup data-element-type="Event.Date">'.$time.'</sup></p>'.
                                    '</div>'.
                                    '<p data-element-type="Pure.Posts.Thumbnail.DMini.Video.Name" data-post-mark="Standart">'.$data->post->title.'</p>'.
                                    '<p data-element-type="Pure.Posts.Thumbnail.DMini.Statistic">views: <span data-element-type="Views">'.$data->post->views.'</span>, members: <span data-element-type="Comments">'.$data->event->count.'</span></p>'.
                                    '<p data-element-type="Pure.Posts.Thumbnail.DMini.Statistic" style="white-space:normal">'.$place.'</p>'.
                                    '<p data-element-type="Pure.Posts.Thumbnail.DMini.Excerpt">'.$data->post->excerpt.'</p>'.
                                    '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Controls">'.
                                        '<a data-element-type="Pure.Posts.Thumbnail.DMini.Button" data-button-type="More" href="'.$data->post->url.'">read more</a>'.
                                        '<div data-element-type="Pure.Posts.Thumbnail.DMini.ResetFloat"></div>'.
                                    '</div>'.
                                '</article>'.
                                '<!--END: Thumbnail.D : EVENT -->';
            }
            return $innerHTML;
        }
        private function innerHTML_event_without_miniature($data, $parameters, $attribute_str){
            return $this->innerHTML_event_with_miniature($data, $parameters, $attribute_str, true);;
        }
        private function innerHTML_video($data, $parameters, $attribute_str){
            $innerHTML = '';
            return $innerHTML;
        }
        private function innerHTML_images($data, $parameters, $attribute_str, $max_items = 10){
            $slider     = \Pure\Templates\Sliders\Initialization::instance()->get('B');
            $innerHTML  =   '<!--BEGIN: Thumbnail.D : POST WITH IMAGES -->'.
                            '<article data-custom-element="Pure.Posts.Thumbnail.DMini.Container" data-post-type="PostGallery" '.$attribute_str.' data-engine-element="parent">'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Gallery">';
            $_data      = (object)array('items'=>array());
            $count      = 0;
            foreach($data->post->images as $image){
                $_data->items[] =   '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Gallery.Item" style="background-image:url('.$image->src.')"></div>';
                $count ++;
                if ($count >= $max_items){ break; }
            }
            $innerHTML .=           $slider->get($_data);
            $innerHTML .=       '</div>'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Gallery.Controlls">'.
                                    '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Gallery.Controlls.Left" data-engine-type="Slider.B.Button.Left">'.
                                    '</div>'.
                                    '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Gallery.Controlls.Right" data-engine-type="Slider.B.Button.Right">'.
                                    '</div>'.
                                '</div>'.
                                '<p data-element-type="Pure.Posts.Thumbnail.DMini.Gallery.Name" data-post-mark="Standart">'.$data->post->title.'</p>'.
                                '<div data-custom-element="Pure.Posts.Thumbnail.DMini.Controls">'.
                                    '<p data-element-type="Pure.Posts.Thumbnail.DMini.Statistic"><span data-element-type="Date">'.date('F j, Y', strtotime($data->post->date)).'</span>, views: <span data-element-type="Views">'.$data->post->views.'</span></p>'.
                                    '<a data-element-type="Pure.Posts.Thumbnail.DMini.Button" data-button-type="More" href="'.$data->post->url.'">read more</a>'.
                                    '<div data-element-type="Pure.Posts.Thumbnail.DMini.ResetFloat"></div>'.
                                '</div>'.
                            '</article>'.
                            '<!--END: Thumbnail.D : POST WITH IMAGES -->';
            $slider = NULL;
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
                            $innerHTML = $this->innerHTML_gallery($data, $parameters, $attribute_str);
                            break;
                        case 'audio':
                            $innerHTML = $this->innerHTML_audio($data, $parameters, $attribute_str);
                            break;
                        case 'embed':
                            $innerHTML = $this->innerHTML_embed($data, $parameters, $attribute_str);
                            break;
                        case 'video':
                            $innerHTML = $this->innerHTML_video($data, $parameters, $attribute_str);
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
                    switch($data->post->type){
                        case 'post_without_miniature':
                            $innerHTML = $this->innerHTML_event_without_miniature($data, $parameters, $attribute_str);
                            break;
                        case 'post_with_miniature':
                            $innerHTML = $this->innerHTML_event_with_miniature($data, $parameters, $attribute_str);
                            break;
                    }
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