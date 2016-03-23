<?php
namespace Pure\Requests\Authorization\Requests\Settings{
    class Initialization{
        private function define($parameters){
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\WordPress\Location\Requests\Initialization::instance()->attach(true);
            $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.templates.authorization.login.configuration.requestURL',
                $Requests->url,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.templates.authorization.login.configuration.requests.login',
                'command'.      '=templates_of_authorization_login'.    '&'.
                'login'.        '='.'[login]'.                          '&'.
                'password'.     '='.'[password]'.                       '&'.
                'remember'.     '='.'[remember]',
                false,
                true
            );
            if ($current !== false){
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.templates.authorization.login.configuration.requests.actual',
                    'command'.      '=templates_of_authorization_actual',
                    false,
                    true
                );
            }
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.templates.authorization.registration.configuration.requests.try',
                'command'.      '=templates_of_authorization_registration'. '&'.
                'login'.        '='.'[login]'.                              '&'.
                'password'.     '='.'[password]'.                           '&'.
                'email'.        '='.'[email]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.templates.authorization.registration.configuration.requests.resend',
                'command'.      '=templates_of_authorization_resendactivation'. '&'.
                'email'.        '='.'[email]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.templates.authorization.reset.configuration.requests.reset',
                'command'.      '=templates_of_authorization_resetpassword'.    '&'.
                'login'.        '='.'[login]'.                                  '&'.
                'email'.        '='.'[email]',
                false,
                true
            );
            $Requests = NULL;
        }
        public function init($parameters){
            $this->define($parameters);
        }
    }
}
?>