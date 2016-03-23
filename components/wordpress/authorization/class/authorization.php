<?php
namespace Pure\Components\WordPress\Authorization{
    class Core{
        private function validate(&$parameters, $method){
            $result = false;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'login':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->login     ));
                    $result = ($result === false ? false : isset($parameters->password  ));
                    $result = ($result === false ? false : isset($parameters->remember  ));
                    break;
                case 'registration':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->login     ));
                    $result = ($result === false ? false : isset($parameters->password  ));
                    $result = ($result === false ? false : isset($parameters->email     ));
                    break;
                case 'confirm':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->code     ));
                    break;
                case 'resend':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->email     ));
                    break;
                case 'reset':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->login     ));
                    $result = ($result === false ? false : isset($parameters->email     ));
                    break;
                case 'addUserWithoutConfirmation':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->login     ));
                    $result = ($result === false ? false : isset($parameters->password  ));
                    $result = ($result === false ? false : isset($parameters->email     ));
                    break;
            }
            return $result;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'login':
                    $parameters->login = esc_sql($parameters->login);
                    break;
                case 'registration':
                    $parameters->login = esc_sql($parameters->login);
                    $parameters->email = filter_var($parameters->email, FILTER_VALIDATE_EMAIL);
                    break;
                case 'confirm':
                    $parameters->code = esc_sql($parameters->code);
                    break;
                case 'resend':
                    $parameters->email = filter_var($parameters->email, FILTER_VALIDATE_EMAIL);
                    break;
                case 'reset':
                    $parameters->login = esc_sql($parameters->login);
                    $parameters->email = filter_var($parameters->email, FILTER_VALIDATE_EMAIL);
                    break;
                case 'addUserWithoutConfirmation':
                    $parameters->login = esc_sql($parameters->login);
                    $parameters->email = filter_var($parameters->email, FILTER_VALIDATE_EMAIL);
                    break;
            }
        }
        private function sendActivationMail($email, $code){
            \Pure\Components\WordPress\Location\Requests\Initialization::instance()->attach(true);
            $Requests   = new \Pure\Components\WordPress\Location\Requests\Register();
            $request    = $Requests->url.'?'.
                            'command'.  '=templates_of_registration_email_confirm'.'&'.
                            'code'.     '='.$code;
            $headers    = array('Content-Type: text/html; charset=UTF-8');
            $message    =   '<h1>'.__('Thank you, for registration on', 'pure').' '.get_bloginfo( 'name' ).'</h1>'.
                            '<p>'.__('To finish procedure of registration you should confirm your email. To do it, just follow next link.', 'pure').'</p>'.
                            '<a href="'.$request.'">'.__('FINISH REGISTRATION', 'pure').'</a>'.
                            '<p>'.__('This link will be actual only 2 days.', 'pure').'</p>'.
                            '<p>'.__('Thank you, have a nice day with a great mood.', 'pure').'</p>';
            return wp_mail( $email, __('Registration', 'pure'), $message, $headers);
        }
        private function sendPasswordMail($email, $password){
            \Pure\Components\WordPress\Location\Requests\Initialization::instance()->attach(true);
            $headers    = array('Content-Type: text/html; charset=UTF-8');
            $message    =   '<h1>'.__('Your password was reset', 'pure').'</h1>'.
                            '<p>'.__('Here is your new password:', 'pure').'</p>'.
                            '<p><strong>'.$password.'</strong></p>'.
                            '<p>'.__('Thank you, have a nice day with a great mood.', 'pure').'</p>';
            return wp_mail( $email, __('Reset password', 'pure'), $message, $headers);
        }
        public function login($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $email  = filter_var($parameters->login, FILTER_VALIDATE_EMAIL);
                $login  = $parameters->login;
                if ($email !== false){
                    $user_id = email_exists($email);
                    if ((int)$user_id > 0){
                        $user = get_userdata($user_id);
                        if ($user !== false){
                            $login = $user->user_login;
                        }else{
                            return false;
                        }
                    }else{
                        return false;
                    }
                }
                $user   = wp_signon(
                    array(
                        'user_login'    =>$login,
                        'user_password' =>$parameters->password,
                        'remember'      =>($parameters->remember === 'on' ? true : false),
                    ),
                    false
                );
                if ( is_wp_error($user) === false){
                    return true;
                }
            }
            return false;
        }
        public function registration($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                $can_register   = $WordPress->can_register();
                $WordPress      = NULL;
                if ($parameters->email !== false && $can_register !== false){
                    $is_busy = username_exists( $parameters->login);
                    $is_busy = (is_null($is_busy) !== false ? false : $is_busy);
                    $is_busy = ($is_busy !== false ? true : $is_busy);
                    if ($is_busy === false){
                        if (email_exists($parameters->email) ===  false){
                            $Provider   = new Provider();
                            $record     = $Provider->getByEmail($parameters->email);
                            if ($record === false){
                                $code       = $Provider->add($parameters);
                                $Provider   = NULL;
                                if ($code !== false){
                                    $this->sendActivationMail($parameters->email, $code);
                                    return true;
                                }
                            }
                            $Provider   = NULL;
                        }
                    }
                }
            }
            return false;
        }
        public function bp_registration_needs_activation(){
            return false;
        }
        public function bp_core_signup_send_activation_key($value, $user_id, $user_email, $activation_key, $usermeta){
            return false;
        }
        public function confirm($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $Provider   = new Provider();
                $record     = $Provider->getByCode($parameters->code);
                if ($record !== false){
                    $Provider->removeByCode($parameters->code);
                    if (email_exists($record->email) ===  false){
                        if (isset(\Pure\Configuration::instance()->globals->flags->bp_core_signup_send_activation_key) === false){
                            add_filter( 'bp_core_signup_send_activation_key', array($this, 'bp_core_signup_send_activation_key'), 10, 5 );
                            add_filter( 'bp_registration_needs_activation', array($this, 'bp_registration_needs_activation') );
                            \Pure\Configuration::instance()->globals->flags->bp_core_signup_send_activation_key = true;
                        }
                        $user_id = (int)bp_core_signup_user(
                            $record->login,
                            $record->password,
                            $record->email,
                            array()
                        );
                        if ((int)$user_id > 0){
                            $key = bp_get_user_meta( $user_id, 'activation_key', true );
                            bp_core_activate_signup($key);
                            wp_update_user( array( 'ID' => $user_id, 'role' => 'author' ) );
                            $this->login(
                                (object)array(
                                    'login'     =>$record->login,
                                    'password'  =>$record->password,
                                    'remember'  =>false
                                )
                            );
                        }
                        return ((int)$user_id > 0 ? (int)$user_id : false);
                    }
                }
                $Provider   = NULL;
            }
            return false;
        }
        public function addUserWithoutConfirmation($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($parameters->email !== false){
                    $is_busy = username_exists( $parameters->login);
                    $is_busy = (is_null($is_busy) !== false ? false : $is_busy);
                    $is_busy = ($is_busy !== false ? true : $is_busy);
                    if ($is_busy === false){
                        if (email_exists($parameters->email) ===  false){
                            $Provider   = new Provider();
                            $record     = $Provider->getByEmail($parameters->email);
                            if ($record === false){
                                $code       = $Provider->add($parameters);
                                $Provider   = NULL;
                                if ($code !== false){
                                    if (isset(\Pure\Configuration::instance()->globals->flags->bp_core_signup_send_activation_key) === false){
                                        add_filter( 'bp_core_signup_send_activation_key', array($this, 'bp_core_signup_send_activation_key'), 10, 5 );
                                        add_filter( 'bp_registration_needs_activation', array($this, 'bp_registration_needs_activation') );
                                        \Pure\Configuration::instance()->globals->flags->bp_core_signup_send_activation_key = true;
                                    }
                                    $user_id = (int)bp_core_signup_user(
                                        $parameters->login,
                                        $parameters->password,
                                        $parameters->email,
                                        array()
                                    );
                                    if ((int)$user_id > 0){
                                        $key = bp_get_user_meta( $user_id, 'activation_key', true );
                                        bp_core_activate_signup($key);
                                        if (isset($parameters->first_name) !== false && isset($parameters->last_name) !== false){
                                            wp_update_user(
                                                array(
                                                    'ID'            => $user_id,
                                                    'role'          => 'author',
                                                    'first_name'    => $parameters->first_name,
                                                    'last_name'     => $parameters->last_name,
                                                )
                                            );
                                        }else{
                                            wp_update_user(
                                                array(
                                                    'ID'            => $user_id,
                                                    'role'          => 'author'
                                                )
                                            );
                                        }
                                    }
                                    return ((int)$user_id > 0 ? (int)$user_id : false);
                                }
                            }
                            $Provider   = NULL;
                        }
                    }
                }
            }
            return false;
        }
        public function resend($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($parameters->email !== false){
                    $Provider   = new Provider();
                    $record     = $Provider->getByEmail($parameters->email);
                    if ($record !== false){
                        $this->sendActivationMail($record->email, $record->code);
                        return true;
                    }
                    $Provider   = NULL;
                }
            }
            return false;
        }
        public function reset($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($parameters->email !== false){
                    $user_id = username_exists( $parameters->login);
                    $user_id = (is_null($user_id) !== false ? false : $user_id);
                    if ($user_id !== false){
                        $_user_id = email_exists($parameters->email);
                        if ($_user_id !==  false){
                            if ((int)$_user_id === (int)$user_id && (int)$user_id > 0){
                                $password = wp_generate_password(10, false, false);
                                wp_set_password( $password, $user_id );
                                $this->sendPasswordMail($parameters->email, $password);
                                return true;
                            }
                        }
                    }
                }
            }
            return false;
        }
    }
    class Provider{
        private $table;
        private function validate(&$parameters, $method){
            $result = false;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'add':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->login     ));
                    $result = ($result === false ? false : isset($parameters->password  ));
                    $result = ($result === false ? false : isset($parameters->email     ));
                    break;
            }
            return $result;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'add':
                    $parameters->login      = esc_sql($parameters->login    );
                    $parameters->password   = esc_sql($parameters->password );
                    $parameters->email      = filter_var($parameters->email, FILTER_VALIDATE_EMAIL);
                    break;
            }
        }
        public function getByEmail($email){
            global $wpdb;
            $selector   =   'SELECT '.
                                '* '.
                            'FROM '.
                                $this->table.' '.
                            'WHERE '.
                                'email = "'.esc_sql($email).'"';
            $fields    = $wpdb->get_results($selector);
            if (is_array($fields) !== false) {
                if (count($fields) === 1) {
                    return $fields[0];
                }
            }
            return false;
        }
        public function getByCode($code){
            global $wpdb;
            $selector   =   'SELECT '.
                                '* '.
                            'FROM '.
                                $this->table.' '.
                            'WHERE '.
                                'code = "'.esc_sql($code).'"';
            $fields    = $wpdb->get_results($selector);
            if (is_array($fields) !== false) {
                if (count($fields) === 1) {
                    return $fields[0];
                }
            }
            return false;
        }
        public function removeByCode($code){
            global $wpdb;
            $selector = 'DELETE '.
                        'FROM '.
                            $this->table.' '.
                        'WHERE '.
                            'code = "'.esc_sql($code).'"';
            $result = $wpdb->query($selector);
            return ((int)$result > 0 ? true : false);
        }
        public function add($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($this->getByEmail($parameters->email) === false){
                    $this->clear();
                    $code = wp_generate_password(55, false, false);
                    global $wpdb;
                    $result = $wpdb->insert(
                        $this->table,
                        array(
                            'login'         =>(string)$parameters->login,
                            'password'      =>(string)$parameters->password,
                            'email'         =>(string)$parameters->email,
                            'code'          =>$code,
                            'created'       =>date("Y-m-d H:i:s")
                        ),
                        array('%s', '%s', '%s', '%s', '%s')
                    );
                    return ($result !== false ? $code : false);
                }

            }
        }
        public function clear(){
            global $wpdb;
            $selector = 'DELETE '.
                        'FROM '.
                            $this->table.' '.
                        'WHERE '.
                            'TO_DAYS(NOW()) - TO_DAYS(created) > 2';
            $result = $wpdb->query($selector);
            return ((int)$result > 0 ? true : false);
        }
        function __construct(){
            $this->table = \Pure\DataBase\TablesNames::instance()->user_registration;
        }
    }
}
?>