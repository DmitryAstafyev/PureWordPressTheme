<?php
namespace Pure\Templates\Authorization\Login{
    class A{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->echo = (isset($parameters->echo) === true ? $parameters->echo : false);
        }
        private function basicResources(){
            \Pure\Components\WordPress\Authorization\   Initialization::instance()->attach(false, 'after');
        }
        private function resources(){
            \Pure\Components\Styles\Buttons\C\          Initialization::instance()->attach(false, 'after');
            \Pure\Templates\ProgressBar\Initialization::instance()->get('A', 'after');
        }
        public function innerHTML($parameters){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            $innerHTML  = '';
            $this->basicResources();
            if ($current === false){
                $this->validate($parameters);
                $this->resources();
                $innerHTML =    '<!--BEGIN: Login form A -->'.
                                '<div data-element-type="Pure.LoginForm.A.Fader" style="display:none" '.
                                    'data-engine-login-form="Container" '.
                                '>'.
                                    '<div data-element-type="Pure.LoginForm.A.Table">'.
                                        '<div data-element-type="Pure.LoginForm.A.Table.Cell">'.
                                            '<div data-element-type="Pure.LoginForm.A.Container" '.
                                                'data-engine-login-form="Container.Modal" '.
                                            '>'.
                                                '<div data-element-type="Pure.LoginForm.A.Header">'.
                                                    '<div data-element-type="Pure.LoginForm.A.Header.Layer" data-layer-number="0" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_0.jpg'.')"></div>'.
                                                    '<div data-element-type="Pure.LoginForm.A.Header.Layer" data-layer-number="1" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_1.png'.')"></div>'.
                                                    '<div data-element-type="Pure.LoginForm.A.Header.Layer" data-layer-number="2" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_2.png'.')"></div>'.
                                                    '<div data-element-type="Pure.LoginForm.A.Header.Layer" data-layer-number="3" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_3.png'.')"></div>'.
                                                    '<p data-element-type="Pure.LoginForm.A.Header">'.get_bloginfo( 'name' ).'</p>'.
                                                '</div>'.
                                                '<div data-element-type="Pure.LoginForm.A.Content">'.
                                                    '<div data-element-type="Pure.LoginForm.A.Fields">'.
                                                        '<p data-element-type="Pure.LoginForm.A.Field">'.__('Login or email', 'pure').'</p>'.
                                                        '<input autocomplete="on" data-element-type="Pure.LoginForm.A.Field" type="text" name="login" autocomplete="on" '.
                                                            'data-engine-login-form="Field.Login" '.
                                                        '/>'.
                                                        '<p data-element-type="Pure.LoginForm.A.Field">'.__('Password', 'pure').'</p>'.
                                                        '<input autocomplete="on" data-element-type="Pure.LoginForm.A.Field" type="password" name="password" autocomplete="on" '.
                                                            'data-engine-login-form="Field.Password" '.
                                                        '/>'.
                                                        '<label>'.
                                                            '<input data-element-type="Pure.LoginForm.A.Field.Remember" type="checkbox" checked '.
                                                                'data-engine-login-form="Field.Remember" '.
                                                            '/>'.
                                                            '<div data-element-type="Pure.LoginForm.A.Field.Remember">'.
                                                            '</div>'.
                                                        '</label>'.
                                                    '</div>'.
                                                    '<div data-element-type="Pure.LoginForm.A.Controls">'.
                                                        '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" '.
                                                            'data-engine-login-form="Button.Cancel" '.
                                                        '>'.__('CANCEL', 'pure').'</a>'.
                                                        '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" '.
                                                            'data-engine-login-form="Button.Login" '.
                                                        '>'.__('LOGIN', 'pure').'</a>'.
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