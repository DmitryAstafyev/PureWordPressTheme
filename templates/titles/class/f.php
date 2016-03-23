<?php
namespace Pure\Templates\Titles{
    class F{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->echo           = (isset($parameters->echo          ) === true  ? $parameters->echo         : false             );
        }
        public function get($title, $parameters = NULL){
            $this->validate($parameters);
            $innerHTML =        '<!--BEGIN: Post.Title.F -->'.
                                '<p data-element-type="Pure.Title.F">'.$title.'</p>'.
                                '<!--END: Post.Title.F -->';
            if ($parameters->echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
    }
}
?>