<?php
namespace Pure\Templates\Authors{
    class B{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
        }
        public  function top($data, $parameters = NULL){
            $this->validate($parameters);
            $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $innerHTML =    '<!--BEGIN: Author thumbnail (TOP) -->'.
                            '<div data-type-element="Author.Thumbnail.B.Top.Container" '.$attribute_str.'>'.
                                '<div data-type-element="Author.Thumbnail.B.Top.Avatar">'.
                                    '<img alt="" src="'.$data->author->avatar.'" />'.
                                '</div>'.
                                '<div data-type-element="Author.Thumbnail.B.Top.Info">'.
                                    '<a data-type-element="Author.Thumbnail.B.Top.Name" href="'.$data->author->urls->member.'">'.$data->author->name.'</a>'.
                                    '<a data-type-element="Author.Thumbnail.B.Top.Post" href="'.$data->author->urls->posts.'">'.$data->posts->count.' posts / '.$data->comments->count.' comments</a>';
            if (is_null($data->author->friends) === true){
                $innerHTML .=       '<a data-type-element="Author.Thumbnail.B.Top.Friends" href="'.$data->author->urls->profile.'">profile</a>';
            }else{
                $innerHTML .=       '<a data-type-element="Author.Thumbnail.B.Top.Friends" href="'.$data->author->urls->friends.'">'.$data->author->friends.' friends</a>';
            }
            $innerHTML .=       '</div>'.
                            '</div>'.
                            '<!--END: Author thumbnail (TOP) -->';
            return $innerHTML;
        }
        public function simple($data, $parameters = NULL){
            $this->validate($parameters);
            $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $innerHTML =    '<!--BEGIN: Author thumbnail (SIMPLE) -->'.
                            '<div data-type-element="Author.Thumbnail.B.Container" '.$attribute_str.'>'.
                                '<div data-type-element="Author.Thumbnail.B.Avatar">'.
                                    '<img alt="" src="'.$data->author->avatar.'" />'.
                                '</div>'.
                                '<div data-type-element="Author.Thumbnail.B.Info">'.
                                    '<a data-type-element="Author.Thumbnail.B.Name" href="'.$data->author->urls->member.'">'.$data->author->name.'</a>'.
                                    '<a data-type-element="Author.Thumbnail.B.Post" href="'.$data->author->urls->posts.'">'.$data->posts->count.' posts / '.$data->comments->count.' comments</a>';
            if (is_null($data->author->friends) === true){
                $innerHTML .=       '<a data-type-element="Author.Thumbnail.B.Friends" href="'.$data->author->urls->profile.'">profile</a>';
            }else{
                $innerHTML .=       '<a data-type-element="Author.Thumbnail.B.Friends" href="'.$data->author->urls->friends.'">'.$data->author->friends.' friends</a>';
            }
            $innerHTML .=       '</div>'.
                            '</div>'.
                            '<!--END: Author thumbnail (SIMPLE) -->';
            return $innerHTML;
        }
        private function resources_more(){
            \Pure\Resources\Compressor::instance()->CSS(
                \Pure\Templates\Authors\Initialization::instance()->configuration->paths->css.'/'.'B.more.css');
            \Pure\Templates\ProgressBar\Initialization::instance()->get('D');
        }
        public function more($parameters){
            $this->resources_more($parameters);
            $innerHTML =    '<div data-type-element="Author.Thumbnail.B.More" '.
                                    'data-type-more-group="'.   $parameters['group'].'" '.
                                    'data-type-more-max="'.     $parameters['maxcount'].'" '.
                                    'data-type-more-template="'.$parameters['template'].'" '.
                                    'data-type-more-progress="D" '.
                                    'data-type-more-settings="'.$parameters['more_settings'].'" '.
                                    'data-type-use="Pure.Components.More">'.
                                '<p data-type-element="Author.Thumbnail.B.More">more</p>'.
                            '</div>'.
                            '<p data-element-type="Author.Thumbnail.B.More.Info">'.
                                '<span data-element-type="Author.Thumbnail.B.More.Info" data-type-use="Pure.Components.More.Shown" data-type-more-group="'.$parameters['group'].'">'.$parameters['shown'].'</span> / '.
                                '<span data-element-type="Author.Thumbnail.B.More.Info">'.$parameters['total'].'</span>'.
                            '</p>'.
                            '<div data-type-element="Author.Thumbnail.B.Reset"></div>';
            return $innerHTML;
        }
    }
}
?>