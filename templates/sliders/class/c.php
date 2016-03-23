<?php
namespace Pure\Templates\Sliders{
    class C{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
        }
        public function get($data, $parameters = NULL){
            $this->validate($parameters);
            $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $innerHTML =    '<!--BEGIN: Slider.C -->'.
                            '<div data-element-type="Pure.Slider.C" data-engine-element="Slider.C"'.$attribute_str.'>'.
                                '<!--BEGIN: Slider.C.Content -->'.
                                '<div data-element-type="Pure.Slider.C.Content">';
            if (isset($data->items) === true){
                foreach($data->items as $item){
                    $innerHTML .=   '<!--BEGIN: Slider.C.Content.Item -->'.
                                    '<div data-element-type="Pure.Slider.C.Content.Item.Container" data-engine-element="Slider.C.Item.Container">'.
                                        '<div data-element-type="Pure.Slider.C.Content.Item.Value" data-engine-element="Slider.C.Item.Value">'.
                                            $item.
                                        '</div>'.
                                    '</div>'.
                                    '<!--END: Slider.C.Content.Item -->';
                }
            }
            $innerHTML .=       '</div>'.
                                '<!--END: Slider.C.Content -->'.
                                '<!--BEGIN: Slider.C.Controls -->'.
                                '<a data-element-type="Pure.Slider.C.Button" data-slider-element="Left" data-engine-slider-control="Slider.C.Button.Left">'.
                                '</a>'.
                                '<a data-element-type="Pure.Slider.C.Button" data-slider-element="Right" data-engine-slider-control="Slider.C.Button.Right">'.
                                '</a>'.
                                '<!--END: Slider.C.Controls -->'.
                            '</div>'.
                            '<!--END: Slider.C -->';
            return $innerHTML;
        }
    }
}
?>