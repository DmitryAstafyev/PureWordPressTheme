<?php
namespace Pure\Templates\Galleries{
    class A{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
        }
        public function get($data, $parameters = NULL){
            $this->validate($parameters);
            if (count($data->images) >= 4){
                $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
                $data_images    = array();
                foreach($data->images as $image){
                    array_push($data_images, 'background-image:url('.$image->url.');');
                    if (count($data_images) >= 4){
                        break;
                    }
                    if (count($data_images) >= 4){
                        break;
                    }
                }
                if (count($data_images) < 4){
                    do {
                        $image = 'background:rgb(255,255,255);';
                        array_push($data_images, $image);
                    } while (count($data_images) < 4);
                }
                return      '<!--BEGIN: Galleries Thumbnail A-->'.
                            '<div data-type-element="Gallery.Thumbnail.F.Container"'.$attribute_str.'>'.
                                '<label>'.
                                    '<input data-type-element="Gallery.Thumbnail.F.Switcher" type="checkbox" checked />'.
                                    '<div data-type-element="Gallery.Thumbnail.F.Container.Sub">'.
                                        '<div data-type-element="Gallery.Thumbnail.F.Thumbnail" data-element-order="1" style="'.$data_images[0].'"></div>'.
                                        '<div data-type-element="Gallery.Thumbnail.F.Thumbnail" data-element-order="2" style="'.$data_images[1].'"></div>'.
                                        '<div data-type-element="Gallery.Thumbnail.F.Thumbnail" data-element-order="3"style ="'.$data_images[2].'"></div>'.
                                        '<div data-type-element="Gallery.Thumbnail.F.Thumbnail" data-element-order="4" style="'.$data_images[3].'"></div>'.
                                    '</div>'.
                                '</label>'.
                                '<a data-type-element="Gallery.Thumbnail.F.Author" href="'.$data->author->posts.'">'.$data->author->name.' / '.$data->post->date.'</a>'.
                                '<a data-type-element="Gallery.Thumbnail.F.Info" href="'.$data->post->url.'">'.count($data->images).' images</a>'.
                            '</div>'.
                            '<!--END: Galleries Thumbnail A-->';
            }
            return '';
        }
    }
}
?>