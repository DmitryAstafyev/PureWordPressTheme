<?php
namespace Pure\Templates\Sliders{
    class B{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
            $parameters->windowresize       = (isset($parameters->windowresize      ) === true  ? $parameters->windowresize     : false             );
        }
        public function scripts(){
            return  '<!--INIT:[pure.sliders.B]-->'.
                    '<!--JS:['.\Pure\Templates\Sliders\Initialization::instance()->configuration->urls->js.'/B.js'.']-->'.
                    '<!--CSS:['.\Pure\Templates\Sliders\Initialization::instance()->configuration->urls->css.'/B.css'.']-->';
        }
        public function get($data, $parameters = NULL){
            $this->validate($parameters);
            $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $innerHTML =    '<!--BEGIN: Slider.B -->'.
                            '<div data-element-type="Pure.Slider.B" data-engine-element="Slider.B" '.$attribute_str.' '.($parameters->windowresize === true ? 'data-engine-windowresize="true"' : '').'>'.
                                '<!--BEGIN: Slider.B.Content -->'.
                                '<div data-element-type="Pure.Slider.B.Content" data-engine-type="Slider.B.Content.Container">'.
                                    '<div data-element-type="Pure.Slider.B.Content.Container" data-engine-type="Slider.B.Content">';
            if (isset($data->items) === true){
                foreach($data->items as $item){
                    $innerHTML .=       '<!--BEGIN: Slider.B.Content.Item -->'.
                                        '<div data-element-type="Pure.Slider.B.Content.Item" data-engine-type="Slider.B.Item">';
                    $innerHTML .=           $item;
                    $innerHTML .=       '</div>'.
                                        '<!--END: Slider.B.Content.Item -->';
                }
            }
            $innerHTML .=               '<div data-element-type="Pure.Slider.B.Content.Item.ResetFloat"></div>'.
                                    '</div>'.
                                '</div>'.
                            '</div>'.
                            '<!--END: Slider.B -->';
            $innerHTML .= $this->scripts();
            return $innerHTML;
        }
    }
}
?>