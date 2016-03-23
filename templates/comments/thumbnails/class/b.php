<?php
namespace Pure\Templates\Comments\Thumbnails{
    class B{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
        }
        public function simple($data, $parameters = NULL){
            $this->validate($parameters);
            if ($data->comment->excerpt !== ''){
                return      '<!--BEGIN: Just comment -->'.
                            '<div data-element-type="Pure.Preview.Comments.B.Simple">'.
                                '<div data-element-type="Pure.Preview.Comments.B.Simple.Author">'.
                                    '<img alt="" data-element-type="Pure.Preview.Comments.B.Simple.Avatar" src="'.($data->author->avatar === false ? Initialization::instance()->configuration->urls->images.'/avatar.png' : $data->author->avatar).'"/>'.
                                    '<a data-element-type="Pure.Preview.Comments.B.Simple.Author" href="'.$data->author->posts.'">'.$data->author->name.'</a>'.
                                    '<p data-element-type="Pure.Preview.Comments.B.Simple.Date">'.$data->comment->date.'</p>'.
                                '</div>'.
                                '<a data-element-type="Pure.Preview.Comments.B.Simple.Comment" href="'.$data->post->url.'">'.$data->comment->excerpt.'</a>'.
                            '</div>'.
                            '<!--END: Just comment -->';
            }else{
                return '';
            }
        }
        public function top($data, $parameters = NULL){
            return $this->simple($data, $parameters);
        }
    }
}
?>