<?php
namespace Pure\Templates\ProgressBar{
    class A{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
        }
        public function attach($parameters = NULL){
            $this->validate($parameters);
            //Do nothing, because css and js will be attached automatically
        }
    }
}
?>