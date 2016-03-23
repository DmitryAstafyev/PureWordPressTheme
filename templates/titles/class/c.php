<?php
namespace Pure\Templates\Titles{
    class C{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
        }
        public function get($title, $parameters = NULL){
            $this->validate($parameters);
            return  '<p data-element-type="Pure.Post.Title.C">'.$title.'</p>';
        }
    }
}
?>