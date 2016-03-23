<?php
namespace Pure\Templates\Authorization\Reset{
    class A{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->echo = (isset($parameters->echo) === true ? $parameters->echo : false);
        }
        private function resources(){
            \Pure\Components\Styles\Buttons\C\          Initialization::instance()->attach(false, 'after');
            \Pure\Components\WordPress\Authorization\   Initialization::instance()->attach(false, 'after');
            \Pure\Components\Dialogs\B\                 Initialization::instance()->attach(false, 'after');
            \Pure\Templates\ProgressBar\Initialization::instance()->get('A', 'after');
        }
        public function innerHTML($parameters){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            $innerHTML  = '';
            if ($current === false){
                $this->validate($parameters);
                $this->resources();
                $innerHTML =    '<!--BEGIN: Login form A -->'.
                                '<div data-element-type="Pure.ResetForm.A.Fader" style="display:none" '.
                                    'data-engine-reset-form="Container" '.
                                '>'.
                                    '<div data-element-type="Pure.LoginForm.A.Table">'.
                                        '<div data-element-type="Pure.LoginForm.A.Table.Cell">'.
                                            '<div data-element-type="Pure.ResetForm.A.Container" '.
                                                'data-engine-reset-form="Container.Modal" '.
                                            '>'.
                                                '<div data-element-type="Pure.ResetForm.A.Header">'.
                                                    '<div data-element-type="Pure.ResetForm.A.Header.Layer" data-layer-number="0" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_0.jpg'.')"></div>'.
                                                    '<div data-element-type="Pure.ResetForm.A.Header.Layer" data-layer-number="1" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_1.png'.')"></div>'.
                                                    '<div data-element-type="Pure.ResetForm.A.Header.Layer" data-layer-number="2" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_2.png'.')"></div>'.
                                                    '<div data-element-type="Pure.ResetForm.A.Header.Layer" data-layer-number="3" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_3.png'.')"></div>'.
                                                    '<p data-element-type="Pure.ResetForm.A.Header">'.get_bloginfo( 'name' ).'</p>'.
                                                '</div>'.
                                                '<div data-element-type="Pure.ResetForm.A.Content">'.
                                                    '<div data-element-type="Pure.ResetForm.A.Fields">'.
                                                        '<p data-element-type="Pure.ResetForm.A.Field">'.__('Your login', 'pure').'</p>'.
                                                        '<input autocomplete="on" data-element-type="Pure.ResetForm.A.Field" type="text" '.
                                                            'data-engine-reset-form="Field.Login" '.
                                                        '/>'.
                                                        '<p data-element-type="Pure.ResetForm.A.Field">'.__('Your email', 'pure').'</p>'.
                                                        '<input autocomplete="on" data-element-type="Pure.ResetForm.A.Field" type="text" '.
                                                            'data-engine-reset-form="Field.Email" '.
                                                        '/>'.
                                                    '</div>'.
                                                    '<div data-element-type="Pure.ResetForm.A.Controls">'.
                                                        '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" '.
                                                            'data-engine-reset-form="Button.Cancel" '.
                                                        '>'.__('CANCEL', 'pure').'</a>'.
                                                        '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" '.
                                                            'data-engine-reset-form="Button.Reset" '.
                                                        '>'.__('RESET', 'pure').'</a>'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                '</div>'.
                                '<!--END: Login form A -->';
                if ($parameters->echo !== false){
                    echo $innerHTML;
                }
            }
            return $innerHTML;
        }
    }
}
?>