<?php
namespace Pure\Templates\Posts\Thumbnails{
    class E{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
        }
        public function get($data, $parameters = NULL){
            $this->validate($parameters);
            return  '<div data-type-element="Post.Thumbnail.E.Container">'.
                        '<a data-type-element="Post.Thumbnail.E.Title" href="'.$data->post->url.'">'.$data->post->title.'</a>'.
                        '<p data-type-element="Post.Thumbnail.E.Date">'.$data->post->date.' / <a href="'.$data->author->posts.'">'.$data->author->name.'</a></p>'.
                    '</div>';
        }
    }
}
?>