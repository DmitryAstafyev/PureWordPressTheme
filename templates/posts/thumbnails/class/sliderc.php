<?php
namespace Pure\Templates\Posts\Thumbnails{
    class SliderC{
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
            return  '<!--BEGIN: Thumbnail of post Slider.C -->'.
                    '<div data-type-element="Post.Thumbnail.SliderC.Container" '.$attribute_str.' >'.
                        '<div data-type-element="Post.Thumbnail.SliderC.Miniature" style="background-image:url('.$data->post->miniature.');"></div>'.
                        '<div data-type-element="Post.Thumbnail.SliderC.Title">'.
                            '<a data-type-element="Post.Thumbnail.SliderC.Title" href="'.$data->post->url.'">'.$data->post->title.'</a>'.
                            //'<p data-type-element="Post.Thumbnail.SliderC.Excerpt">'.$data->post->excerpt.'</p>'.
                        '</div>'.
                    '</div>'.
                    '<!--END: Thumbnail of post Slider.C -->';
        }
        function __construct(){
        }
    }
}
?>