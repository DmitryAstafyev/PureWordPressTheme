<?php
namespace Pure\Templates\Authorization\Registration{
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
            $WordPress      = new \Pure\Components\WordPress\UserData\Data();
            $current        = $WordPress->get_current_user();
            $can_register   = $WordPress->can_register();
            $WordPress      = NULL;
            $innerHTML      = '';
            if ($current === false && $can_register !== false){
                $this->validate($parameters);
                $this->resources();
                $innerHTML =    '<!--BEGIN: Login form A -->'.
                                '<div data-element-type="Pure.RegistrationForm.A.Fader" style="display:none" '.
                                    'data-engine-registration-form="Container" '.
                                '>'.
                                    '<div data-element-type="Pure.LoginForm.A.Table">'.
                                        '<div data-element-type="Pure.LoginForm.A.Table.Cell">'.
                                            '<div data-element-type="Pure.RegistrationForm.A.Container" '.
                                                'data-engine-registration-form="Container.Modal" '.
                                            '>'.
                                                '<div data-element-type="Pure.RegistrationForm.A.Header">'.
                                                    '<div data-element-type="Pure.RegistrationForm.A.Header.Layer" data-layer-number="0" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_0.jpg'.')"></div>'.
                                                    '<div data-element-type="Pure.RegistrationForm.A.Header.Layer" data-layer-number="1" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_1.png'.')"></div>'.
                                                    '<div data-element-type="Pure.RegistrationForm.A.Header.Layer" data-layer-number="2" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_2.png'.')"></div>'.
                                                    '<div data-element-type="Pure.RegistrationForm.A.Header.Layer" data-layer-number="3" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_3.png'.')"></div>'.
                                                    '<p data-element-type="Pure.RegistrationForm.A.Header">'.get_bloginfo( 'name' ).'</p>'.
                                                '</div>'.
                                                '<div data-element-type="Pure.RegistrationForm.A.Content">'.
                                                    '<div data-element-type="Pure.RegistrationForm.A.Fields">'.
                                                        '<p data-element-type="Pure.RegistrationForm.A.Field">'.__('Login', 'pure').'</p>'.
                                                        '<input autocomplete="on" data-element-type="Pure.RegistrationForm.A.Field" type="text" '.
                                                            'data-engine-registration-form="Field.Login" '.
                                                        '/>'.
                                                        '<p data-element-type="Pure.RegistrationForm.A.Field">'.__('Password', 'pure').'</p>'.
                                                        '<input autocomplete="on" data-element-type="Pure.RegistrationForm.A.Field" type="password" '.
                                                            'data-engine-registration-form="Field.Password" '.
                                                        '/>'.
                                                        '<p data-element-type="Pure.RegistrationForm.A.Field">'.__('Repeat password', 'pure').'</p>'.
                                                        '<input autocomplete="on" data-element-type="Pure.RegistrationForm.A.Field" type="password" '.
                                                            'data-engine-registration-form="Field.Password" '.
                                                        '/>'.
                                                        '<p data-element-type="Pure.RegistrationForm.A.Field">'.__('Email', 'pure').'</p>'.
                                                        '<input autocomplete="on" data-element-type="Pure.RegistrationForm.A.Field" type="email" '.
                                                            'data-engine-registration-form="Field.Email" '.
                                                        '/>'.
                                                        '<a data-element-type="Pure.RegistrationForm.A.Field" '.
                                                            'data-engine-registration-form="Button.Resend" '.
                                                        '>'.__('Resend activation link', 'pure').'</a>'.
                                                    '</div>'.
                                                    '<div data-element-type="Pure.RegistrationForm.A.Controls">'.
                                                        '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" '.
                                                            'data-engine-registration-form="Button.Cancel" '.
                                                        '>'.__('CANCEL', 'pure').'</a>'.
                                                        '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" '.
                                                            'data-engine-registration-form="Button.Try" '.
                                                        '>'.__('TRY', 'pure').'</a>'.
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
            }else if ($current === false && $can_register === false){
                $this->validate($parameters);
                $this->resources();
                $innerHTML =    '<!--BEGIN: Login form A -->'.
                                '<div data-element-type="Pure.RegistrationForm.A.Fader" style="display:none" '.
                                    'data-engine-registration-form="Container" '.
                                '>'.
                                    '<div data-element-type="Pure.LoginForm.A.Table">'.
                                        '<div data-element-type="Pure.LoginForm.A.Table.Cell">'.
                                            '<div data-element-type="Pure.RegistrationForm.A.Container" '.
                                                'data-engine-registration-form="Container.Modal" '.
                                            '>'.
                                                '<div data-element-type="Pure.RegistrationForm.A.Header">'.
                                                    '<div data-element-type="Pure.RegistrationForm.A.Header.Layer" data-layer-number="0" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_0.jpg'.')"></div>'.
                                                    '<div data-element-type="Pure.RegistrationForm.A.Header.Layer" data-layer-number="1" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_1.png'.')"></div>'.
                                                    '<div data-element-type="Pure.RegistrationForm.A.Header.Layer" data-layer-number="2" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_2.png'.')"></div>'.
                                                    '<div data-element-type="Pure.RegistrationForm.A.Header.Layer" data-layer-number="3" style="background-image:url('.Initialization::instance()->configuration->urls->images.'/layer_3.png'.')"></div>'.
                                                    '<p data-element-type="Pure.RegistrationForm.A.Header">'.get_bloginfo( 'name' ).'</p>'.
                                                '</div>'.
                                                '<div data-element-type="Pure.RegistrationForm.A.Content">'.
                                                    '<div data-element-type="Pure.RegistrationForm.A.Fields">'.
                                                        '<p data-element-type="Pure.RegistrationForm.A.Field">'.__('Sorry, but at current time registration is unavailable.', 'pure').'</p>'.
                                                    '</div>'.
                                                    '<div data-element-type="Pure.RegistrationForm.A.Controls">'.
                                                        '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" '.
                                                            'data-engine-registration-form="Button.Cancel" '.
                                                        '>'.__('BACK', 'pure').'</a>'.
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