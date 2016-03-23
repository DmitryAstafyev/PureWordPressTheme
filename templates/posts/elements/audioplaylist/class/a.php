<?php
namespace Pure\Templates\Posts\Elements\AudioPlaylist{
    class A{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->audios ) !== false ? true : false));
                return $result;
            }
            return false;
        }
        /*
         * $parameters->audios - list of audios (post's objects)
         *
         */
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $id         = uniqid('playlist');
                $slider     = \Pure\Templates\Sliders\Initialization::instance()->get('B');
                $progress   = \Pure\Components\Slider\A\Initialization::instance();
                $audioplayer= \Pure\Components\AudioPlayer\A\Initialization::instance();
                $playlist   = $audioplayer->playlist($parameters->audios, $id, false);
                $innerHTML  = $playlist->innerHTML;
                $innerHTML .=   '<!--BEGIN: AudioPlaylist.A -->'.
                                '<article data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Container" data-post-type="PostVideo" data-engine-element="parent" data-engine-element-ID="'.$id.'">'.
                                    '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Audios">';
                $_data      = (object)array('items'=>array());
                foreach($parameters->audios as $audio){
                    $_data->items[] =   '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Track">'.
                                            '<p data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Track.Name">'.esc_html($audio->post_title).'</p>'.
                                            '<p data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Track.Info">'.esc_html($audio->post_content).'</p>'.
                                        '</div>';
                }
                $innerHTML  .= $slider->get($_data);
                $innerHTML  .=      '</div>'.
                                    '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls" '.
                                         'data-type-use="Pure.Components.AudioPlayer.A" '.
                                         'data-AudioPlayer-handle-volume="pure.components.slider.A.Handles.set" '.
                                         'data-AudioPlayer-handle-volume-param="'.$id.'_volume" '.
                                         'data-AudioPlayer-handle-progress="pure.components.slider.A.Handles.set" '.
                                         'data-AudioPlayer-handle-progress-param="'.$id.'_progress" '.
                                         'data-AudioPlayer-handle-ghost="pure.components.slider.A.Handles.ghost" '.
                                         'data-AudioPlayer-handle-ghost-param="'.$id.'" '.
                                         'data-type-AudioPlayer-playlist="'.$playlist->property.'" '.
                                         'data-type-AudioPlayer-ID="'.$id.'">'.
                                        '<input data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.More" type="checkbox" id="'.$id.'"/>'.
                                        '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Basic">'.
                                            '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Time">'.
                                                '<p data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Track.Time.Position" data-type-AudioPlayer="current">.. : ..</p>'.
                                                '<p data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Track.Time.Total" data-type-AudioPlayer="total">.. : ..</p>'.
                                            '</div>'.
                                            '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Left" data-engine-type="Slider.B.Button.Left" data-type-AudioPlayer="previous">'.
                                            '</div>'.
                                            '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Play.Container">'.
                                                '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Play" data-type-AudioPlayer-state="play" data-type-AudioPlayer="playpause">'.
                                                '</div>'.
                                                '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Play.Light" data-addition-type-direction="forward">'.
                                                '</div>'.
                                                '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Play.Light" data-addition-type-direction="back">'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Right" data-engine-type="Slider.B.Button.Right" data-type-AudioPlayer="next">'.
                                            '</div>'.
                                            '<label for="'.$id.'">'.
                                                '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.More">'.
                                                '</div>'.
                                            '</label>'.
                                        '</div>'.
                                        '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Addition">'.
                                            '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Volume.Container" '.
                                                 'data-type-use="Pure.Components.Slider.A" '.
                                                 'data-type-slider-ID="'.$id.'_volume"'.
                                                 'data-slider-handle-onchange="pure.components.audioplayer.A.Handles.volume" '.
                                                 'data-slider-handle-onchange-param="'.$id.'" '.
                                                 'data-slider-handle-onfinish="pure.components.audioplayer.A.Handles.volume" '.
                                                 'data-slider-handle-onfinish-param="'.$id.'">'.
                                                '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Volume.Line" data-type-slider="line">'.
                                                    '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Volume.Peak" data-type-slider-ghost="peak">'.
                                                    '</div>'.
                                                    '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Volume.Progress" data-type-slider="progress">'.
                                                    '</div>'.
                                                    '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Volume.Pointer" data-type-slider="pointer">'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Progress.Container" '.
                                                 'data-type-use="Pure.Components.Slider.A" '.
                                                 'data-type-slider-ID="'.$id.'_progress" '.
                                                 'data-slider-handle-onchange="pure.components.audioplayer.A.Handles.position" '.
                                                 'data-slider-handle-onchange-param="'.$id.'" '.
                                                 'data-slider-handle-onfinish="pure.components.audioplayer.A.Handles.position" '.
                                                 'data-slider-handle-onfinish-param="'.$id.'">'.
                                                '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Progress.Line" data-type-slider="line">'.
                                                    '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Progress.Cache" data-type-slider-ghost="buffered">'.
                                                    '</div>'.
                                                    '<div data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Controlls.Progress.Current" data-type-slider="progress">'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                '</article>'.
                                '<!--END: AudioPlaylist.A -->';
                $innerHTML .= $audioplayer->call_scripts();
                $innerHTML .= $progress->call_scripts();
                $audioplayer->attach();
                $progress->attach();
                $audioplayer= NULL;
                $progress   = NULL;
                $slider     = NULL;
                return $innerHTML;







                $progress   = \Pure\Components\Slider\A\Initialization::instance();
                $audioplayer= \Pure\Components\AudioPlayer\A\Initialization::instance();
                $playlist   = $audioplayer->playlist(array($parameters->audio), $id, true);
                $innerHTML  = '';
                $innerHTML .=   '<!--BEGIN:Audio.A -->'.
                                '<article data-post-element-type="Pure.Posts.Elements.AudioPlaylist.A.Container" data-engine-element="parent" data-engine-element-ID="'.$id.'">'.
                                    '<div data-post-element-type="Pure.Posts.Elements.Audio.A">';
                $innerHTML .=           '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Track">'.
                                            '<p data-post-element-type="Pure.Posts.Elements.Audio.A.Track.Name">'.esc_html($parameters->audio->post_title).'</p>'.
                                            '<p data-post-element-type="Pure.Posts.Elements.Audio.A.Track.Info">'.esc_html($parameters->audio->post_content).'</p>'.
                                        '</div>';
                $innerHTML  .=      '</div>'.
                                    '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls" '.
                                         'data-type-use="Pure.Components.AudioPlayer.A" '.
                                         'data-AudioPlayer-handle-volume="pure.components.slider.A.Handles.set" '.
                                         'data-AudioPlayer-handle-volume-param="'.$id.'_volume" '.
                                         'data-AudioPlayer-handle-progress="pure.components.slider.A.Handles.set" '.
                                         'data-AudioPlayer-handle-progress-param="'.$id.'_progress" '.
                                         'data-AudioPlayer-handle-ghost="pure.components.slider.A.Handles.ghost" '.
                                         'data-AudioPlayer-handle-ghost-param="'.$id.'" '.
                                         'data-type-AudioPlayer-playlist="'.$playlist->property.'" '.
                                         'data-type-AudioPlayer-ID="'.$id.'">'.
                                        '<input data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.More" type="checkbox" id="'.$id.'"/>'.
                                        '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Basic">'.
                                            '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Time">'.
                                                '<p data-post-element-type="Pure.Posts.Elements.Audio.A.Track.Time">'.
                                                    '<span data-type-AudioPlayer="current">00:00</span> / <span data-type-AudioPlayer="total">00:00</span>'.
                                                '</p>'.
                                            '</div>'.
                                            '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Play.Container">'.
                                                '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Play" data-type-AudioPlayer-state="play" data-type-AudioPlayer="playpause">'.
                                                '</div>'.
                                                '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Play.Light" data-addition-type-direction="forward">'.
                                                '</div>'.
                                                '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Play.Light" data-addition-type-direction="back">'.
                                                '</div>'.
                                            '</div>'.
                                            '<label for="'.$id.'">'.
                                                '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.More">'.
                                                '</div>'.
                                            '</label>'.
                                        '</div>'.
                                        '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Addition">'.
                                            '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Volume.Container" '.
                                                 'data-type-use="Pure.Components.Slider.A" '.
                                                 'data-type-slider-ID="'.$id.'_volume"'.
                                                 'data-slider-handle-onchange="pure.components.audioplayer.A.Handles.volume" '.
                                                 'data-slider-handle-onchange-param="'.$id.'" '.
                                                 'data-slider-handle-onfinish="pure.components.audioplayer.A.Handles.volume" '.
                                                 'data-slider-handle-onfinish-param="'.$id.'">'.
                                                '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Volume.Line" data-type-slider="line">'.
                                                    '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Volume.Peak" data-type-slider-ghost="peak">'.
                                                    '</div>'.
                                                    '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Volume.Progress" data-type-slider="progress">'.
                                                    '</div>'.
                                                    '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Volume.Pointer" data-type-slider="pointer">'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Progress.Container" '.
                                                 'data-type-use="Pure.Components.Slider.A" '.
                                                 'data-type-slider-ID="'.$id.'_progress" '.
                                                 'data-slider-handle-onchange="pure.components.audioplayer.A.Handles.position" '.
                                                 'data-slider-handle-onchange-param="'.$id.'" '.
                                                 'data-slider-handle-onfinish="pure.components.audioplayer.A.Handles.position" '.
                                                 'data-slider-handle-onfinish-param="'.$id.'">'.
                                                '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Progress.Line" data-type-slider="line">'.
                                                    '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Progress.Cache" data-type-slider-ghost="buffered">'.
                                                    '</div>'.
                                                    '<div data-post-element-type="Pure.Posts.Elements.Audio.A.Controlls.Progress.Current" data-type-slider="progress">'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                '</article>'.
                                '<!--END: Audio.A -->';
                $innerHTML .= $audioplayer->call_scripts();
                $innerHTML .= $progress->call_scripts();
                $audioplayer->attach();
                $progress->attach();
                $audioplayer= NULL;
                $progress   = NULL;
            }
            return $innerHTML;
        }
    }
}
?>