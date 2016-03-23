<?php
namespace Pure\Templates\Sliders{
    class A{
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
            if (isset($data->title) === true){
                $title =    '<!--BEGIN: Slider.A.Title -->'.
                            '<div data-element-type="Pure.Slider.A.Title">'.
                                '<div data-element-type="Pure.Slider.A.Title.Cover">'.
                                    '<p data-element-type="Pure.Slider.A.Title">'.$data->title.'</p>'.
                                '</div>'.
                            '</div>'.
                            '<!--END: Slider.A.Title -->';
            }else{
                $title = '';
            }
            $innerHTML =    '<!--BEGIN: Slider.A -->'.
                            '<div data-element-type="Pure.Slider.A" data-engine-element="Slider.A"'.$attribute_str.' style="background-image:url('.\Pure\Templates\Sliders\Initialization::instance()->configuration->urls->images.'/A.background.jpg);">'.
                                $title.
                                '<!--BEGIN: Slider.A.Content -->'.
                                '<div data-element-type="Pure.Slider.A.Content" >'.
                                    '<div data-element-type="Pure.Slider.A.Content.Container" data-engine-type="Slider.A.Content">';
            if (isset($data->items) === true){
                foreach($data->items as $item){
                    $innerHTML .=       '<!--BEGIN: Slider.A.Content.Item -->'.
                                        '<div data-element-type="Pure.Slider.A.Content.Item" data-engine-type="Slider.A.Item">';
                    $innerHTML .=           $item;
                    $innerHTML .=       '</div>'.
                                        '<!--END: Slider.A.Content.Item -->';
                }
            }
            $innerHTML .=           '</div>'.
                                '</div>'.
                                '<!--END: Slider.A.Content -->'.
                                '<!--BEGIN: Slider.A.Controls -->'.
                                '<div data-element-type="Pure.Slider.A.Pages" >'.
                                    '<div data-element-type="Pure.Slider.A.Pages.Cover">'.
                                        '<div data-element-type="Pure.Slider.A.Pages.Content" data-engine-type="Slider.A.Controls">'.
                                            '<div data-element-type="Pure.Slider.A.Pages.Content.Point.Left" data-slider-element="Slider.A.Button" data-engine-type="Slider.A.Button.Left">'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Slider.A.Pages.Content.Points" data-engine-type="Slider.A.Points" data-engine-active-attr-name="data-slider-point" data-engine-active-attr-value="active">'.
                                                '<div data-element-type="Pure.Slider.A.Pages.Content.Point" data-engine-type="Slider.A.Point">'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Slider.A.Pages.Content.Point.Right" data-slider-element="Slider.A.Button" data-engine-type="Slider.A.Button.Right">'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                '</div>'.
                                '<!--END: Slider.A.Controls -->'.
                            '</div>'.
                            '<!--END: Slider.A -->';
            return $innerHTML;
        }
    }
}
?>