<?php
namespace Pure\Templates\Posts\Thumbnails{
    class QuestionA{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
        }
        public function get($data, $parameters = NULL){
            $this->validate($parameters);
            $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $solution_icon  = ($data->question->has_answer === false ? '' : Initialization::instance()->configuration->urls->images.'/questiona/solved.png' );
            $innerHTML      = Initialization::instance()->html(
                'QuestionA/wrapper',
                array(
                    array('attribute',      $attribute_str                              ),
                    array('solution_icon',  $solution_icon                              ),
                    array('disabled',       ($solution_icon === '' ? 'display:none' : '')),
                    array('title',          $data->post->title                          ),
                    array('by_label',       __('by', 'pure')                     ),
                    array('author',         $data->author->name                         ),
                    array('added',          date('F j, Y', strtotime($data->post->date))),
                    array('read_more',      __('read more', 'pure')              ),
                    array('comments',       $data->post->comments                       ),
                    array('url',            $data->post->url                            ),
                    array('views',          $data->post->views                          ),
                    array('mana',           ($data->post->karma >= 0 ? ($data->post->karma === 0 ? '' : '+').$data->post->karma : '-'.$data->post->karma)),
                    array('category',       $data->category->name                       ),
                    array('category_url',   $data->category->url                        ),
                )
            );
            return  $innerHTML;
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