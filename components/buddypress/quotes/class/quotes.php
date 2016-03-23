<?php
namespace Pure\Components\BuddyPress\Quotes{
    class Core{
        private $table;
        private function validate($parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $result = ($result === false ? $result : (isset($parameters->user   ) === true ? (gettype($parameters->user     ) == 'integer'  ? true : false) : false));
                    break;
                case 'getByID':
                    $result = ($result === false ? $result : (isset($parameters->id     ) === true ? (gettype($parameters->id       ) == 'integer'  ? true : false) : false));
                    break;
                case 'isAttached':
                    $result = ($result === false ? $result : (isset($parameters->quote_id       ) === true ? (gettype($parameters->quote_id         ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->user_id        ) === true ? (gettype($parameters->user_id          ) == 'integer'  ? true : false) : false));
                    break;
                case 'add':
                    $result = ($result === false ? $result : (isset($parameters->user   ) === true ? (gettype($parameters->user     ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->quote  ) === true ? (gettype($parameters->quote    ) == 'string'   ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->active ) === true ? (gettype($parameters->active   ) == 'boolean'  ? true : false) : false));
                    $result = ($result === false ? $result : (mb_strlen($parameters->quote) <= 500 ? true : false));
                    break;
                case 'import':
                    $result = ($result === false ? $result : (isset($parameters->quote_id       ) === true ? (gettype($parameters->quote_id         ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->user_id        ) === true ? (gettype($parameters->user_id          ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->active ) === true ? (gettype($parameters->active   ) == 'boolean'  ? true : false) : false));
                    break;
                case 'remove':
                    $result = ($result === false ? $result : (isset($parameters->user       ) === true ? (gettype($parameters->user         ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->quote_id   ) === true ? (gettype($parameters->quote_id     ) == 'integer'  ? true : false) : false));
                    break;
                case 'state':
                    $result = ($result === false ? $result : (isset($parameters->user       ) === true ? (gettype($parameters->user         ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->quote_id   ) === true ? (gettype($parameters->quote_id     ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->active     ) === true ? (gettype($parameters->active       ) == 'boolean'  ? true : false) : false));
                    break;
            }
            return $result;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'add':
                    $parameters->quote = sanitize_text_field($parameters->quote);
                    break;
            }
        }
        public function count($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                global $wpdb;
                $selector   =   'SELECT * '.
                                    'FROM '.$this->table.' '.
                                        'WHERE '.
                                            'user_id='.$parameters->user;
                $result     = $wpdb->get_results($selector);
                if (is_array($result) === true){
                    return count($result);
                }
            }
            return false;
        }
        private function processing($quotes){
            $_quotes = array();
            if (is_array($quotes) !== false){
                $WordPress = new \Pure\Components\WordPress\UserData\Data();
                foreach($quotes as $quote){
                    $_quote = $quote;
                    if (preg_match('/\[import\:(\d*)\]/', $quote->quote, $matches) === 1){
                        if (count($matches) === 2){
                            $_quote = $this->getByID(
                                (object)array(
                                    'id'=>(int)$matches[1]
                                )
                            );
                        }
                    }
                    if ($_quote !== false){
                        $_quote->original_id    = $_quote->id;
                        $_quote->id             = $quote->id;
                        $_quote->meta           = $quote->meta;
                        $_quote->active         = $quote->active;
                        $_quote->quote          = stripcslashes($_quote->quote);
                        $_quote->user_name      = $WordPress->get_name((int)$_quote->user_id);
                        $_quotes[]              = $_quote;
                    }
                }
                $WordPress = NULL;
            }
            return (count($_quotes) > 0 ? $_quotes : false);
        }
        public function get($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $cache = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, func_get_args());
                if (! $result = $cache->value){
                    global $wpdb;
                    $selector   =   'SELECT * '.
                                        'FROM '.$this->table.' '.
                                            'WHERE '.
                                                'user_id='.$parameters->user.' '.
                                                'ORDER BY date_created DESC';
                    $result     = $wpdb->get_results($selector);
                    $result     = (count($result) > 0 ? $this->processing($result) : false);
                    \Pure\Components\Tools\Cache\Cache::set($cache->key, $result);
                }
                return $result;
            }
            return false;
        }
        public function getRandom($count = 1){
            global $wpdb;
            $selector   =   'SELECT * '.
                            'FROM '.$this->table.' '.
                            'ORDER BY RAND() LIMIT '.(int)$count;
            $result     = $wpdb->get_results($selector);
            return (count($result) > 0 ? $this->processing($result) : false);
        }
        public function getRandomOfUser($user_id, $count = 1){
            if ((int)$user_id > 0){
                global $wpdb;
                $selector   =   'SELECT * '.
                                'FROM '.$this->table.' '.
                                'WHERE '.
                                    'user_id = '.(int)$user_id.' '.
                                'ORDER BY RAND() LIMIT '.(int)$count;
                $result     = $wpdb->get_results($selector);
                return (count($result) > 0 ? $this->processing($result) : false);
            }
            return false;
        }
        public function getRandomUser($count = 1){
            global $wpdb;
            $selector   =   'SELECT * '.
                            'FROM '.$this->table.' '.
                            'ORDER BY RAND() LIMIT '.(int)$count;
            $result     = $wpdb->get_results($selector);
            return (count($result) === 1 ? $result[0]->user_id : false);
        }
        public function getByID($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                global $wpdb;
                $selector   =   'SELECT * '.
                                    'FROM '.$this->table.' '.
                                        'WHERE '.
                                            'id='.$parameters->id;
                $result     = $wpdb->get_results($selector);
                if (is_array($result) === true){
                    if (count($result) === 1){
                        $WordPress              = new \Pure\Components\WordPress\UserData\Data();
                        $result[0]->user_name   = $WordPress->get_name((int)$result[0]->user_id);
                        $result[0]->quote       = stripcslashes($result[0]->quote);
                        $WordPress              = NULL;
                        return (count($result) === 1 ? $result[0] : false);
                    }
                }
            }
            return false;
        }
        public function isAttached($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $quotes = $this->get(
                    (object)array(
                        'user'=>(int)$parameters->user_id
                    )
                );
                if ($quotes !== false){
                    foreach($quotes as $quote){
                        if ((int)$quote->original_id === (int)$parameters->quote_id){
                            return (int)$quote->id;
                        }
                    }
                }
            }
            return false;
        }
        public function remove($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $current            = $WordPress->get_current_user();
                $WordPress          = NULL;
                if ($current !== false){
                    if ((int)$current->ID === (int)$parameters->user){
                        global $wpdb;
                        try{
                            $wpdb->query(   'DELETE FROM '.
                                                $this->table.' '.
                                                'WHERE '.
                                                    'id = '.        $parameters->quote_id.' AND '.
                                                    'user_id = '.   $parameters->user
                            );
                            return true;
                        }catch (\Exception $e){
                            return false;
                        }
                    }
                }
            }
            return false;
        }
        public function state($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false){
                    if ((int)$current->ID === (int)$parameters->user){
                        global $wpdb;
                        try{
                            $wpdb->query(   'UPDATE '.
                                                $this->table.' '.
                                                'SET '.
                                                    'active = '.($parameters->active === true ? 1 : 0).' '.
                                                    'WHERE '.
                                                        'id = '.        $parameters->quote_id.' AND '.
                                                        'user_id = '.   $parameters->user
                            );
                            return true;
                        }catch (\Exception $e){
                            return false;
                        }
                    }
                }
            }
            return false;
        }
        public function add($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $this->sanitize($parameters, __METHOD__);
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $current            = $WordPress->get_current_user();
                $WordPress          = NULL;
                if ($current !== false){
                    if ((int)$current->ID === (int)$parameters->user){
                        global $wpdb;
                        $result     = $wpdb->insert(
                            $this->table,
                            array(
                                'user_id'       =>$parameters->user,
                                'date_created'  =>date("Y-m-d H:i:s"),
                                'quote'         =>$parameters->quote,
                                'meta'          =>'',
                                'active'        =>($parameters->active === true ? 1 : 0)
                            ),
                            array('%d', '%s', '%s', '%s', '%d')
                        );
                        if ($result !== false) {
                            return (int)$wpdb->insert_id;
                        }
                    }
                }
            }
            return false;
        }
        public function import($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $this->sanitize($parameters, __METHOD__);
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $current            = $WordPress->get_current_user();
                $WordPress          = NULL;
                if ($current !== false){
                    if ((int)$current->ID === (int)$parameters->user_id){
                        $quote = $this->getByID(
                            (object)array(
                                'id'=>$parameters->quote_id
                            )
                        );
                        if ($quote !== false){
                            if ((int)$quote->user_id !== (int)$parameters->user_id){
                                global $wpdb;
                                $result     = $wpdb->insert(
                                    $this->table,
                                    array(
                                        'user_id'       =>$parameters->user_id,
                                        'date_created'  =>date("Y-m-d H:i:s"),
                                        'quote'         =>'[import:'.$parameters->quote_id.']',
                                        'meta'          =>'',
                                        'active'        =>($parameters->active === true ? 1 : 0)
                                    ),
                                    array('%d', '%s', '%s', '%s', '%d')
                                );
                                if ($result !== false) {
                                    return (int)$wpdb->insert_id;
                                }
                            }
                        }
                    }
                }
            }
            return false;
        }
        function __construct(){
            $this->table = \Pure\DataBase\TablesNames::instance()->quotes;
        }
    }
}
?>