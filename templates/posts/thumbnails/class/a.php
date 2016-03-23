<?php
namespace Pure\Templates\Posts\Thumbnails{
    class A{
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
            return      '<!--BEGIN: Thumbnail of post A-->'.
                        '<div data-custom-element="Pure.Post.Thumbnail.A.Container"'.$attribute_str.'>'.
                            '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Container">'.
                                '<a data-custom-element="Pure.Post.Thumbnail.A.Preview.Image" href="'.$data->post->url.'">'.
                                    '<img alt="" src="'.$data->post->miniature.'"/>'.
                                    '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Read">'.
                                        '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Read.Icon">'.
                                        '</div>'.
                                    '</div>'.
                                '</a>'.
                                '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription">'.
                                    '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Date">'.
                                        '<p>'.$data->post->date.'</p>'.
                                    '</div>'.
                                    '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Category">'.
                                        '<a href="'.$data->category->url.'">'.$data->category->name.'</a>'.
                                    '</div>'.
                                    '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Container">'.
                                        '<label>'.
                                            '<input data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Title" type="checkbox"/>'.
                                            '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Title">'.
                                                '<p>'.$data->post->title.'</p>'.
                                            '</div>'.
                                            '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Excerpt">'.
                                                '<p>'.$data->post->excerpt.'</p>'.
                                            '</div>'.
                                        '</label>'.
                                        '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Fader">'.
                                        '</div>'.
                                        '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Container">'.
                                            '<label>'.
                                                '<input data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Switcher" type="checkbox" checked="checked" />'.
                                                '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.MovementContainer">'.
                                                    '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Page.One">'.
                                                        '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Icon">'.
                                                            '<img alt="" src="'.$data->author->avatar.'"/>'.
                                                        '</div>'.
                                                        '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Name">'.
                                                            '<p>'.$data->author->name.'</p>'.
                                                        '</div>'.
                                                        '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Statistic">'.
                                                            '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Statistic.Segment" data-element-define="Views">'.
                                                                '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Statistic.Segment.Icon" data-element-define="Views">'.
                                                                '</div>'.
                                                                '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Statistic.Segment.Value">'.
                                                                    '<p>'.$data->post->views.'</p>'.
                                                                '</div>'.
                                                           '</div>'.
                                                            '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Statistic.Segment" data-element-define="Comments">'.
                                                                '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Statistic.Segment.Icon" data-element-define="Comments">'.
                                                                '</div>'.
                                                                '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Statistic.Segment.Value">'.
                                                                    '<p>'.$data->post->comments.'</p>'.
                                                                '</div>'.
                                                            '</div>'.
                                                        '</div>'.
                                                    '</div>'.
                                                    '<div data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Page.Two">'.
                                                        '<a data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Actions.Button" href="'.$data->author->profile.'">'.
                                                            '<p>'.__('Author page', 'pure').'</p>'.
                                                        '</a>'.
                                                        '<a data-custom-element="Pure.Post.Thumbnail.A.Preview.Discription.Author.Actions.Button" href="'.$data->author->posts.'">'.
                                                            '<p>'.__('Author posts', 'pure').'</p>'.
                                                        '</a>'.
                                                    '</div>'.
                                                '</div>'.
                                            '</label>'.
                                        '</div>'.
                                    '</div>'.
                                '</div>'.
                           ' </div>'.
                        '</div>'.
                        '<!--END: Thumbnail of post A -->';
        }
    }
}
?>