<?php
namespace Pure\Requests\Authorization\Requests{
    class Core{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'login':
                    $parameters->login      = (string  )($parameters->login     );
                    $parameters->password   = (string  )($parameters->password  );
                    $parameters->remember   = (string  )($parameters->remember  );
                    return true;
                    break;
                case 'registration':
                    $parameters->login      = (string  )($parameters->login     );
                    $parameters->password   = (string  )($parameters->password  );
                    $parameters->email      = (string  )($parameters->email     );
                    return true;
                    break;
                case 'confirm':
                    $parameters->code       = (string  )($parameters->code      );
                    return true;
                    break;
                case 'resend':
                    $parameters->email      = (string  )($parameters->email      );
                    return true;
                    break;
                case 'actual':
                    return true;
                    break;
                case 'reset':
                    $parameters->login      = (string  )($parameters->login     );
                    $parameters->email      = (string  )($parameters->email     );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        public function login($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                \Pure\Components\WordPress\Authorization\Initialization::instance()->attach(true);
                $Provider = new \Pure\Components\WordPress\Authorization\Core();
                if ($Provider->login($parameters) !== false){
                    echo 'success';
                }else{
                    echo 'fail';
                }
                $Provider = NULL;
                return true;
            }
            echo 'error';
            return false;
        }
        public function registration($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if (filter_var($parameters->email, FILTER_VALIDATE_EMAIL) !== false){
                    $is_busy = username_exists( $parameters->login);
                    $is_busy = (is_null($is_busy) !== false ? false : $is_busy);
                    $is_busy = ($is_busy !== false ? true : $is_busy);
                    if ($is_busy === false){
                        if (email_exists($parameters->email) ===  false){
                            \Pure\Components\WordPress\Authorization\Initialization::instance()->attach(true);
                            $Provider = new \Pure\Components\WordPress\Authorization\Core();
                            if ($Provider->registration($parameters) !== false){
                                echo 'success';
                            }else{
                                echo 'fail';
                            }
                            $Provider = NULL;
                            return true;
                        }else{
                            echo 'email exists';
                            return false;
                        }
                    }else{
                        echo 'login is busy';
                        return false;
                    }
                }else{
                    echo 'bad email';
                    return false;
                }
            }
            echo 'error';
            return false;
        }
        public function confirm($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                \Pure\Components\WordPress\Authorization\Initialization::instance()->attach(true);
                $Provider   = new \Pure\Components\WordPress\Authorization\Core();
                $user_id    = $Provider->confirm($parameters);
                if ($user_id !== false){
                    header("Location: ".home_url());
                    exit;
                }else{
                    $this->error('We are sorry, but your registration was failed. Possibly, your activation link was too old. Activation link is actual only for 2 days.');
                }
            }
        }
        public function resend($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if (filter_var($parameters->email, FILTER_VALIDATE_EMAIL) !== false){
                    \Pure\Components\WordPress\Authorization\Initialization::instance()->attach(true);
                    $Provider   = new \Pure\Components\WordPress\Authorization\Core();
                    $result     = $Provider->resend($parameters);
                    $Provider   = NULL;
                    if ($result !== false){
                        echo 'success';
                        return true;
                    }else{
                        echo 'fail';
                        return false;
                    }
                }
            }
            echo 'error';
            return false;
        }
        public function actual($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                echo ($current !== false ? 'inside' : 'need login');
                return true;
            }
            echo 'error';
            return false;
        }
        public function reset($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if (filter_var($parameters->email, FILTER_VALIDATE_EMAIL) !== false) {
                    $is_busy = username_exists($parameters->login);
                    $is_busy = (is_null($is_busy) !== false ? false : $is_busy);
                    $is_busy = ($is_busy !== false ? true : $is_busy);
                    if ($is_busy !== false) {
                        if (email_exists($parameters->email) !== false) {
                            \Pure\Components\WordPress\Authorization\Initialization::instance()->attach(true);
                            $Provider   = new \Pure\Components\WordPress\Authorization\Core();
                            $result     = $Provider->reset($parameters);
                            $Provider   = NULL;
                            if ($result !== false){
                                echo 'success';
                                return true;
                            }else{
                                echo 'fail';
                                return false;
                            }
                        }
                        echo 'no such email';
                        return false;
                    }
                    echo 'no such login';
                    return false;
                }
                echo 'bad email';
                return false;
            }
            echo 'error';
            return false;
        }
        private function error($message){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->basic->properties;
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $ErrorPage  = \Pure\Templates\Pages\Error\Initialization::instance()->get($settings->error_page_template);
            $ErrorPage->message('Registration error', $message, true);
            exit;
        }
    }
}
?>