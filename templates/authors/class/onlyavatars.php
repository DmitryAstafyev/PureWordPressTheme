<?php
namespace Pure\Templates\Authors{
    class OnlyAvatars{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
        }
        public  function top($data, $parameters = NULL){
            return $this->simple($data, $parameters);
        }
        public function simple($data, $parameters = NULL){
            $this->validate($parameters);
            $innerHTML =    '<div data-type-element="Author.Thumbnail.OnlyAvatars.Avatar">'.
                                '<a data-type-element="Author.Thumbnail.OnlyAvatars.Avatar" href="'.$data->author->urls->member.'">'.
                                    '<img data-type-element="Author.Thumbnail.OnlyAvatars.Avatar" alt="" src="'.$data->author->avatar.'" />'.
                                '</a>'.
                            '</div>';
            return $innerHTML;
        }
    }
}
?>