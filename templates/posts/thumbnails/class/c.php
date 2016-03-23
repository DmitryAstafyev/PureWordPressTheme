<?php
namespace Pure\Templates\Posts\Thumbnails{
    class C{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
            $parameters->fix_width          = (isset($parameters->fix_width         ) === true  ? $parameters->fix_width        : false             );
        }
        public function get($data, $parameters = NULL){
            $this->validate($parameters);
            $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $miniature      = ($data->post->miniature !== '' ? '<span data-type-element="Post.Thumbnail.C.Excerpt.Thumbnail" style="background-image:url('.$data->post->miniature.');"></span>' : '');
            return  '<!--BEGIN: Thumbnail of post C -->'.
                    '<div data-type-element="Post.Thumbnail.C.Container"'.$attribute_str.' data-width-fix="'.($parameters->fix_width === true ? "true" : "false").'">'.
                        '<p data-type-element="Post.Thumbnail.C.Date">'.$data->post->date.' / <a href="'.$data->author->posts.'">'.$data->author->name.'</a></p>'.
                        '<a data-type-element="Post.Thumbnail.C.Title">'.$data->post->title.'</a>'.
                        '<p data-type-element="Post.Thumbnail.C.Excerpt">'.$miniature.' '.$data->post->excerpt.' <a href="'.$data->post->url.'">more</a></p>'.
                    '</div>'.
                    '<!--END: Thumbnail of post C -->';
        }
    }
}
?>