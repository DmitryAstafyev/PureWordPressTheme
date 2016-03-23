<?php
namespace Pure\Templates\Posts\Thumbnails{
    class B{
        private $id_number;
        private $id_prefix;
        private function id(){
            $this->id_number ++;
            return $this->id_prefix.'_'.((string)$this->id_number);
        }
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
            $id             = $this->id();
            $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            return  '<!--BEGIN: Thumbnail of post B -->'.
                    '<div data-type-element="Post.Thumbnail.B.Container" '.$attribute_str.' data-width-fix="'.($parameters->fix_width === true ? "true" : "false").'">'.
                        '<input data-type-element="Post.Thumbnail.B.Switcher" type="checkbox" id="Post.Thumbnail.B.'.$id.'" />'.
                        '<div data-type-element="Post.Thumbnail.B.Image" style="background-image:url('.$data->post->miniature.');"></div>'.
                        '<div data-type-element="Post.Thumbnail.B.Title">'.
                            '<div data-type-element="Post.Thumbnail.B.Avatar">'.
                                '<img alt="" data-type-element="Post.Thumbnail.B.Avatar" src="'.$data->author->avatar.'" />'.
                            '</div>'.
                            '<a data-type-element="Post.Thumbnail.B.Title" href="'.$data->post->url.'">'.$data->post->title.'</a>'.
                            '<p data-type-element="Post.Thumbnail.B.Author">by <a href="'.$data->author->posts.'">'.$data->author->name.'</a></p>'.
                            '<p data-type-element="Post.Thumbnail.B.DataCategory">'.$data->post->date.' / <a href="'.$data->category->url.'">'.$data->category->name.'</a> / '.$data->post->comments.'</p>'.
                        '</div>'.
                        '<label for="Post.Thumbnail.B.'.$id.'">'.
                            '<div data-type-element="Post.Thumbnail.B.Excerpt"><p>'.$data->post->excerpt.'</p></div>'.
                        '</label>'.
                        '<div data-type-element="Post.Thumbnail.B.Button">'.
                            '<a href="'.$data->post->url.'">read more</a>'.
                        '</div>'.
                    '</div>'.
                    '<!--END: Thumbnail of post B -->';
        }
        function __construct(){
            $this->id_number    = 0;
            $this->id_prefix    = (string)rand(100000 , 999999);
        }
    }
}
?>