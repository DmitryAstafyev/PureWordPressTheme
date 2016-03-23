<?php
namespace Pure\Templates\Comments\Thumbnails{
    class A{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
        }
        public function simple($data, $parameters = NULL){
            $this->validate($parameters);
            if ($data->comment->excerpt !== ''){
                return      '<!--BEGIN: Just comment -->'.
                            '<div data-element-type="Pure.Preview.Comments.Simple">'.
                                '<div data-element-type="Pure.Preview.Comments.Simple.Author">'.
                                    '<img alt="" data-element-type="Pure.Preview.Comments.Simple.Avatar" src="'.($data->author->avatar === false ? Initialization::instance()->configuration->urls->images.'/avatar.png' : $data->author->avatar).'"/>'.
                                    '<a data-element-type="Pure.Preview.Comments.Simple.Author" href="'.$data->author->posts.'">'.$data->author->name.'</a>'.
                                    '<p data-element-type="Pure.Preview.Comments.Simple.Date">'.$data->comment->date.'</p>'.
                                '</div>'.
                                '<a data-element-type="Pure.Preview.Comments.Simple.Comment" href="'.$data->post->url.'">'.$data->comment->excerpt.'</a>'.
                            '</div>'.
                            '<!--END: Just comment -->';
            }else{
                return '';
            }
        }
        public function top($data, $parameters = NULL){
            $this->validate($parameters);
            return      '<!--BEGIN: Top comment -->'.
                        '<div data-element-type="Pure.Preview.Comments.Top">'.
                            '<div data-element-type="Pure.Preview.Comments.Top.Comment">'.
                                '<p>'.$data->comment->excerpt.'</p>'.
                            '</div>'.
                            '<div data-element-type="Pure.Preview.Comments.Top.Author">'.
                                '<img alt="" data-element-type="Pure.Preview.Comments.Top.Avatar" src="'.($data->author->avatar === false ? Initialization::instance()->configuration->urls->images.'/avatar.png' : $data->author->avatar).'"/>'.
                                '<div data-element-type="Pure.Preview.Comments.Top.Author.Info">'.
                                    '<a data-element-type="Pure.Preview.Comments.Top.Author" href="'.$data->author->posts.'">'.$data->author->name.'</a>'.
                                    '<p data-element-type="Pure.Preview.Comments.Top.About">about</p>'.
                                    '<a data-element-type="Pure.Preview.Comments.Top.Post" href="'.$data->post->url.'">'.$data->post->title.'</a>'.
                                '</div>'.
                            '</div>'.
                        '</div>'.
                        '<!--END: Top comment -->';
        }
    }
}
?>