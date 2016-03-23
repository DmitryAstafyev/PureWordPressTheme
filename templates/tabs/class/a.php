<?php
namespace Pure\Templates\Tabs{
    class A{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
            $parameters->columns            = (isset($parameters->columns           ) === true  ? $parameters->columns          : 1                 );
        }
        public function get($data, $parameters = NULL){
            $this->validate($parameters);
            $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $navigation_id  = 'Pure.Tabs.A.Novigation.'.(string)rand(100000, 999999);
            $innerHTML =    '<!--BEGIN: Tabs.A -->'.
                            '<div data-element-type="Pure.Tabs.A.Container" data-engine-element="Tabs.A" data-engine-active-attr-name="data-tab-type" data-engine-active-attr-value="active"'.$attribute_str.'>'.
                                '<!--BEGIN: Tabs.A.Titles -->'.
                                '<div data-element-type="Pure.Tabs.A.Titles" data-engine-element="Tabs.A.Titles.Container">'.
                                    '<div data-element-type="Pure.Tabs.A.Titles.Container" data-engine-element="Tabs.A.Line">';
            foreach ($data->tabs as $tab){
                $innerHTML .=           '<div data-element-type="Pure.Tabs.A.Title" data-engine-element="Tabs.A.Title">'.
                                            '<p>'.$tab->title.'</p>'.
                                        '</div>';
            }
            $innerHTML .=           '</div>'.
                                '</div>'.
                                '<!--END: Tabs.A.Titles -->';
            $innerHTML .=       '<!--BEGIN: Tabs.A.Novigation -->'.
                                '<div data-element-type="Pure.Tabs.A.Novigation" data-engine-element="Tabs.A.Novigation.Panel">'.
                                    '<div data-element-type="Pure.Tabs.A.Novigation.Left" data-engine-element="Tabs.A.Novigation.Left">'.
                                    '</div>'.
                                    '<input data-element-type="Pure.Tabs.A.Novigation.List" id="'.$navigation_id.'" type="checkbox" />'.
                                    '<label for="'.$navigation_id.'">'.
                                        '<div data-element-type="Pure.Tabs.A.Novigation.List">'.
                                        '</div>'.
                                        '<div data-element-type="Pure.Tabs.A.Novigation.List.Menu">';
            foreach ($data->tabs as $tab){
                $innerHTML .=               '<p data-element-type="Pure.Tabs.A.Novigation.List.Menu.Item" data-engine-element="Tabs.A.Item">'.$tab->title.'</p>';
            }
            $innerHTML .=               '</div>'.
                                    '</label>'.
                                    '<div data-element-type="Pure.Tabs.A.Novigation.Right" data-engine-element="Tabs.A.Novigation.Right">'.
                                    '</div>'.
                                '</div>'.
                                '<!--END: Tabs.A.Novigation -->';
            $innerHTML .=       '<!--BEGIN: Tabs.A.Tabs -->'.
                                '<div data-element-type="Pure.Tabs.A.Tabs">';
            foreach ($data->tabs as $tab){
                $innerHTML .=       '<!--BEGIN: Tabs.A.Tab.Value -->'.
                                    '<div data-element-type="Pure.Tabs.A.Tab.Content" data-engine-element="Tabs.A.Tab">';
                if ($parameters->columns === 1){
                    foreach ($tab->items as $item){
                        $innerHTML .= $item;
                    }
                    $innerHTML .=   '<div style="clear: both;"></div>';
                }else{
                    $innerHTML .=   '<!--BEGIN: Two columns area -->'.
                                    '<div data-element-type="Pure.TwoColumnsArea.Container">'.
                                        '<div data-element-type="Pure.TwoColumnsArea.Sub">'.
                                            '<div data-element-type="Pure.TwoColumnsArea.Column" data-column-type="half">';
                    $last = 0;
                    for ($index = 0, $max = count($tab->items) / 2; $index < $max; $index ++){
                        $innerHTML .=           $tab->items[$index];
                        $last       = $index;
                    }
                    $innerHTML .=           '</div>'.
                                            '<div data-element-type="Pure.TwoColumnsArea.Column" data-column-type="half">';
                    for ($index = $last + 1, $max = count($tab->items); $index < $max; $index ++){
                        $innerHTML .=           $tab->items[$index];
                    }
                    $innerHTML .=           '</div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<!--END: Two columns area -->';
                }
                $innerHTML .=       '<div style="clear: both;"></div>';
                $innerHTML .=       '</div>'.
                                    '<!--END: Tabs.A.Tab.Value -->';
            }
            $innerHTML .=       '</div>'.
                                '<!--END: Tabs.A.Tabs -->'.
                            '</div>'.
                            '<!--END: Tabs.A -->';
            return $innerHTML;
        }
    }
}
?>