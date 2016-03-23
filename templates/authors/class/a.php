<?php
namespace Pure\Templates\Authors{
    class A{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->attribute              = (isset($parameters->attribute             ) === true  ? $parameters->attribute            : new \stdClass()   );
            $parameters->attribute->name        = (isset($parameters->attribute->name       ) === true  ? $parameters->attribute->name      : ''                );
            $parameters->attribute->value       = (isset($parameters->attribute->value      ) === true  ? $parameters->attribute->value     : ''                );
        }
        public  function top($data, $parameters = NULL){
            $this->validate($parameters);
            $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $innerHTML =    '<!--BEGIN: Author thumbnail (TOP) -->'.
                            '<div data-type-element="Author.Thumbnail.A.Top.Container" '.$attribute_str.'>'.
                                '<div data-type-element="Author.Thumbnail.A.Top.Avatar">'.
                                    '<img alt="" src="'.$data->author->avatar.'" />'.
                                '</div>'.
                                '<div data-type-element="Author.Thumbnail.A.Top.Info">'.
                                    '<a data-type-element="Author.Thumbnail.A.Top.Name" href="'.$data->author->urls->member.'">'.$data->author->name.'</a>'.
                                    '<p data-type-element="Author.Thumbnail.A.Top">'.__( 'Registered on', 'pure' ).' '.date("F j, Y", strtotime($data->author->date)).'<br/> ('.$data->author->how_long.')'.'</p>'.
                                    '<a data-type-element="Author.Thumbnail.A.Top.Post" href="'.$data->author->urls->posts.'">'.$data->posts->count.' posts / '.$data->comments->count.' comments</a>';
            if (is_null($data->author->friends) === true){
                $innerHTML .=       '<a data-type-element="Author.Thumbnail.A.Top.Friends" href="'.$data->author->urls->profile.'">profile</a>';
            }else{
                $innerHTML .=       '<a data-type-element="Author.Thumbnail.A.Top.Friends" href="'.$data->author->urls->friends.'">'.$data->author->friends.' friends</a>';
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
                            '<div data-type-element="Author.Thumbnail.A.Container" '.$attribute_str.'>'.
                                '<div data-type-element="Author.Thumbnail.A.Avatar">'.
                                    '<img alt="" src="'.$data->author->avatar.'" />'.
                                '</div>'.
                                '<div data-type-element="Author.Thumbnail.A.Info">'.
                                    '<a data-type-element="Author.Thumbnail.A.Name" href="'.$data->author->urls->member.'">'.$data->author->name.'</a>'.
                                    '<a data-type-element="Author.Thumbnail.A.Post" href="'.$data->author->urls->posts.'">'.$data->posts->count.' posts / '.$data->comments->count.' comments</a>';
            if (is_null($data->author->friends) === true){
                $innerHTML .=       '<a data-type-element="Author.Thumbnail.A.Friends" href="'.$data->author->urls->profile.'">profile</a>';
            }else{
                $innerHTML .=       '<a data-type-element="Author.Thumbnail.A.Friends" href="'.$data->author->urls->friends.'">'.$data->author->friends.' friends</a>';
            }
            $innerHTML .=       '</div>'.
                            '</div>'.
                            '<!--END: Author thumbnail (SIMPLE) -->';
            return $innerHTML;
        }
        private function resources_more(){
            \Pure\Resources\Compressor::instance()->CSS(
                \Pure\Templates\Authors\Initialization::instance()->configuration->paths->css.'/'.'A.more.css');
            \Pure\Templates\ProgressBar\Initialization::instance()->get('D');
        }
        public function more($parameters){
            $this->resources_more($parameters);
            $innerHTML =    $this->resources_more($parameters).
                            '<div data-type-element="Author.Thumbnail.A.More" '.
                                'data-type-more-group="'.   $parameters['group'].'" '.
                                'data-type-more-max="'.     $parameters['maxcount'].'" '.
                                'data-type-more-template="'.$parameters['template'].'" '.
                                'data-type-more-settings="'.$parameters['more_settings'].'" '.
                                'data-type-more-progress="D" '.
                                'data-type-use="Pure.Components.More">'.
                                '<p data-type-element="Author.Thumbnail.A.More">'.__('more', 'pure').'</p>'.
                            '</div>'.
                            '<p data-element-type="Author.Thumbnail.A.More.Info">'.
                                '<span data-element-type="Author.Thumbnail.A.More.Info" data-type-use="Pure.Components.More.Shown" data-type-more-group="'.$parameters['group'].'">'.$parameters['shown'].'</span> / '.
                                '<span data-element-type="Author.Thumbnail.A.More.Info">'.$parameters['total'].'</span>'.
                            '</p>'.
                            '<div data-type-element="Pure.Posts.Thumbnail.D.Reset"></div>';
            return $innerHTML;
        }
    }
}
?>