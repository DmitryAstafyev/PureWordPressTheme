<?php
namespace Pure\Templates\Titles{
    class B{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->link           = (isset($parameters->link          ) === true  ? $parameters->link         : new \stdClass()   );
            $parameters->link->title    = (isset($parameters->link->title   ) === true  ? $parameters->link->title  : ''                );
            $parameters->link->href     = (isset($parameters->link->href    ) === true  ? $parameters->link->href   : ''                );
            $parameters->reset_float    = (isset($parameters->reset_float   ) === true  ? $parameters->reset_float  : true              );
        }
        public function get($title, $parameters = NULL){
            $this->validate($parameters);
            return  '<!--BEGIN: Title -->'.
                    ($parameters->reset_float === true ? '<div style="clear: both;"></div>' : '').
                    '<div data-element-type="Pure.Post.Title.A">'.
                        '<div data-element-type="Pure.Post.Title.A.Line"></div>'.
                        '<p data-element-type="Pure.Post.Title.A" data-element-align="Right">'.$title.'</p>'.
                    '</div>'.
                    '<!--END: Title -->';
        }
    }
}
?>