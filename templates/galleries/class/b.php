<?php
namespace Pure\Templates\Galleries{
    class B{
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
            $data_images = 'background-image:url('.$data->images[0]->url.');';
            return  '<!--BEGIN: Galleries -->'.
                    '<div data-type-element="Gallery.Thumbnail.G.Container"'.$attribute_str.'>'.
                        '<div data-type-element="Gallery.Thumbnail.G.Thumbnail" style="'.$data_images.'"></div>'.
                        '<a data-type-element="Gallery.Thumbnail.G.Author" href="'.$data->author->posts.'">'.$data->author->name.' / '.$data->post->date.'</a>'.
                        '<a data-type-element="Gallery.Thumbnail.G.Info" href="'.$data->post->url.'">'.count($data->images).' images</a>'.
                    '</div>'.
                    '<!--END: Galleries -->';
        }
    }
}
?>