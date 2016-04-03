<?php
namespace Pure\Components\Token\Module{
    class Token{
        private $TOKEN_LIFE_DURATION    = 6000;//in milliseconds
        private $COOKIE_KEY             = 'TOKEN_FIELD_VALUE';
        public function update(){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ($current !== false){
                $parts = array(
                    0=>$current->ID,
                    1=>$current->user_login,
                    2=>$current->user_email,
                    3=>date("Y-m-d H:i:s")
                );
                $token  = wp_hash($parts[0].$parts[1].$parts[2].$parts[3]);
                //$token = $parts[0].'-'.$parts[1].'-'.$parts[2].'-'.$parts[3];
                return $this->set($current->ID, $token, $parts[3]);
            }
            return false;
        }
        private function isCookieValid($token){
            if (isset($_COOKIE[$this->COOKIE_KEY]) !== false){
                return ($token === $_COOKIE[$this->COOKIE_KEY] ? true : false);
            } else {
                return false;
            }
        }
        private function setCookieValid($token){
            if (isset($_COOKIE[$this->COOKIE_KEY]) !== false){
                unset($_COOKIE[$this->COOKIE_KEY]);
            }
            setcookie($this->COOKIE_KEY, $token, time() + $this->TOKEN_LIFE_DURATION);
        }
        private function set($user_id, $token, $date){
            global $wpdb;
            $result     = $wpdb->get_results(  'SELECT '.
                                                        '* '.
                                                    'FROM '.
                                                        \Pure\DataBase\TablesNames::instance()->security_tokens.' '.
                                                        'WHERE '.
                                                            'user_id='.$user_id);
            $token = esc_sql($token);
            $token_is_in = false;
            if (is_array($result) !== false){
                if (count($result) > 0){
                    $token_is_in = true;
                }
            }
            if ($token_is_in === true){
                if ($this->isCookieValid($result[0]->token) === false){
                    $wpdb->query(   'UPDATE '.\Pure\DataBase\TablesNames::instance()->security_tokens.' '.
                                    'SET '.
                                        'token = "'.$token.'", '.
                                        'created = "'.$date.'"'.
                                    'WHERE '.
                                        'user_id='.$user_id.' '.
                                    'LIMIT 1');
                    $this->setCookieValid($token);
                } else {
                    $token = $result[0]->token;
                }
            }else{
                $wpdb->query(   'INSERT '.\Pure\DataBase\TablesNames::instance()->security_tokens.' '.
                                    'SET '.
                                        'user_id = '.$user_id.', '.
                                        'token = "'.$token.'", '.
                                        'created = "'.$date.'"');
            }
            return (object)array(
                'user_id'   =>$user_id,
                'token'     =>$token
            );
        }
        public function isValid($user_id, $token){
            if (gettype($user_id) === 'integer'){
                global $wpdb;
                $token      = esc_sql($token);
                $result     = $wpdb->get_results(   'SELECT '.
                                                        '* '.
                                                        'FROM '.
                                                            \Pure\DataBase\TablesNames::instance()->security_tokens.' '.
                                                            'WHERE '.
                                                                'user_id='.$user_id);
                if (is_array($result) !== false){
                    if (count($result) === 1){
                        $result = $result[0];
                        if ($result->token === $token){
                            $checkpoint = strtotime("now");
                            $created    = strtotime($result->created);
                            if ($checkpoint - $created < $this->TOKEN_LIFE_DURATION){
                                return true;
                            }
                        }
                    }
                }
            }
            return false;
        }
    }
}
?>