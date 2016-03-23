<?php
namespace Pure\Templates\Inserts{
    class A{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
        }
        public function get($data, $parameters = NULL){
            $this->validate($parameters);
            $content = (preg_match('/[<|>]/',$data->post_content) === 1 ? $data->post_content : '<p data-element-type="Pure.Insert.A.Content">'.$data->post_content.'</p>');
            return      '<!--BEGIN: Top comment -->'.
                        '<div data-element-type="Pure.Insert.A">'.
                            '<p data-element-type="Pure.Insert.A.Title">'.$data->post_title.'</p>'.
                            $content.
                        '</div>'.
                        '<!--END: Top comment -->';
        }
    }
}
?>