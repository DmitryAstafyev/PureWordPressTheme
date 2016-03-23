<?php
namespace Pure\Components\PostAttachments\Module{
    class Permissions{
        private function checkPost($object_id, $user_id){
            $author = get_post_field( 'post_author', $object_id);
            return ((int)$author === (int)$user_id ? true : false);
        }
        private function checkComment($object_id, $user_id){
            $comment = get_comment($object_id);
            if (is_object($comment) !== false){
                return ((int)$comment->user_id === (int)$user_id ? true : false);
            }
        }
        public function isAllow($object_id, $object_type){
            $WordPress          = new \Pure\Components\WordPress\UserData\Data();
            $current            = $WordPress->get_current_user();
            $WordPress          = NULL;
            if ($current !== false){
                switch($object_type){
                    case 'addition':
                        return $this->checkPost($object_id, $current->ID);
                    case 'post':
                        return $this->checkPost($object_id, $current->ID);
                    case 'comment':
                        return $this->checkComment($object_id, $current->ID);
                }
            }
            return false;
        }
    }
    class Provider{
        private $settings;
        public function getCount($object_id, $object_type){
            if ((int)$object_id > 0){
                global $wpdb;
                $result = $wpdb->query( 'SELECT '.
                                            '* '.
                                        'FROM '.
                                            \Pure\DataBase\TablesNames::instance()->attachments.' '.
                                        'WHERE '.
                                            'object_type'.  '= "'.esc_sql($object_type).'" '.
                                            'AND object_id'.'= '.(int)$object_id
                );
                return ($result !== false ? (int)$result : false);
            }
            return false;
        }
        public function getCountPerMonth($user_id){
            if ((int)$user_id > 0){
                global $wpdb;
                $result = $wpdb->query( 'SELECT '.
                                            '* '.
                                        'FROM '.
                                            \Pure\DataBase\TablesNames::instance()->attachments.' '.
                                        'WHERE '.
                                            'user_id'.'= '.(int)$user_id.' '.
                                            'AND added BETWEEN '.
                                                'CAST( "'.date("Y").'-'.date("m").'-01 00:00:00" AS DATETIME ) '.
                                                    'AND '.
                                                'CAST( "'.date("Y").'-'.date("m").'-'.cal_days_in_month(CAL_GREGORIAN, (int)date("m"), (int)date("Y")).' 00:00:00" AS DATETIME )'
                );
                return ($result !== false ? (int)$result : false);
            }
            return false;
        }
        public function isAllow($object_id, $object_type, $user_id){
            $count_in_object    = $this->getCount($object_id, $object_type);
            $count_in_month     = $this->getCountPerMonth($user_id);
            if ($count_in_month !== false && $count_in_object !== false){
                if ((int)$count_in_object   < (int)$this->settings->max_count_per_object &&
                    (int)$count_in_month    < (int)$this->settings->max_count_per_month){
                    return true;
                }else{
                    if ((int)$count_in_object   >= (int)$this->settings->max_count_per_object){
                        return (object)array(
                            'reason'=>'count_in_object',
                            'max'   =>(int)$this->settings->max_count_per_object
                        );
                    }
                    if ((int)$count_in_month    >= (int)$this->settings->max_count_per_month){
                        return (object)array(
                            'reason'=>'count_in_month',
                            'max'   =>(int)$this->settings->max_count_per_month
                        );
                    }
                }
            }
            return false;
        }
        public function get($object_id, $object_type){
            if ((int)$object_id > 0){
                global $wpdb;
                $result = $wpdb->get_results(   'SELECT '.
                                                    '* '.
                                                'FROM '.
                                                    \Pure\DataBase\TablesNames::instance()->attachments.' '.
                                                'WHERE '.
                                                    'object_type'.  '= "'.esc_sql($object_type).'" '.
                                                    'AND object_id'.'= '.(int)$object_id.' '.
                                                'ORDER BY '.
                                                    'added DESC'
                );
                if ($result !== false){
                    foreach($result as $key=>$field){
                        $result[$key]->url = \Pure\Resources\Names::instance()->repairURL($result[$key]->url);
                    }
                    return $result;
                }
            }
            return false;
        }
        public function getAttachment($object_id, $object_type, $url, $user_id){
            if ((int)$object_id > 0){
                $url = \Pure\Resources\Names::instance()->clearURL($url);
                global $wpdb;
                $result = $wpdb->get_results(   'SELECT '.
                                                    '* '.
                                                'FROM '.
                                                    \Pure\DataBase\TablesNames::instance()->attachments.' '.
                                                'WHERE '.
                                                    'object_type'.  '= "'.esc_sql($object_type).'" '.
                                                    'AND object_id'.'= '.(int)$object_id.' '.
                                                    'AND user_id'.  '= '.(int)$user_id.' '.
                                                    'AND url'.      '= "'.esc_sql($url).'"'
                );
                return (is_array($result) !== false ? (count($result) === 1 ? $result[0] : false) : false);
            }
            return false;
        }
        public function isAttached($object_id, $object_type, $url){
            if ((int)$object_id > 0 && strlen($object_type) > 0 && strlen($url) > 0){
                $url = \Pure\Resources\Names::instance()->clearURL($url);
                global $wpdb;
                $result = $wpdb->query( 'SELECT '.
                                            '* '.
                                        'FROM '.
                                            \Pure\DataBase\TablesNames::instance()->attachments.' '.
                                        'WHERE '.
                                            'object_type'.  '= "'.esc_sql($object_type).'" '.
                                            'AND object_id'.'= '.(int)$object_id.' '.
                                            'AND url'.      '= "'.esc_sql($url).'" '.
                                        'ORDER BY '.
                                            'added DESC'
                );
                return ((int)$result > 0 ? true : false);
            }
            return false;
        }
        public function add($object_id, $object_type, $url, $file_name, $full_file_name){
            if ((int)$object_id > 0){
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $current            = $WordPress->get_current_user();
                $WordPress          = NULL;
                if ($current !== false){
                    if ($this->isAttached($object_id, $object_type, $url) === false){
                        if ($this->isAllow($object_id, $object_type, $current->ID) === true){
                            global $wpdb;
                            $full_file_name = \Pure\Resources\Names::instance()->clearPath  ($full_file_name);
                            $url            = \Pure\Resources\Names::instance()->clearURL   ($url           );
                            $result         = $wpdb->insert(
                                \Pure\DataBase\TablesNames::instance()->attachments,
                                array(
                                    'object_id'     =>(int)$object_id,
                                    'object_type'   =>esc_sql($object_type),
                                    'user_id'       =>(int)$current->ID,
                                    'file_name'     =>esc_sql($file_name),
                                    'file'          =>base64_encode($full_file_name),
                                    'url'           =>esc_sql($url),
                                    'added'         =>date("Y-m-d H:i:s")
                                ),
                                array('%d', '%s', '%d', '%s', '%s', '%s', '%s')
                            );
                            if ($result !== false){
                                return $wpdb->insert_id;
                            }
                        }
                    }
                }
            }
            return false;
        }
        public function remove($object_id, $object_type, $url, $user_id){
            if ((int)$object_id > 0){
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $current            = $WordPress->get_current_user();
                $WordPress          = NULL;
                if ($current !== false){
                    if ((int)$current->ID === (int)$user_id){
                        $attachment = $this->getAttachment($object_id, $object_type, $url, $user_id);
                        if ($attachment !== false){
                            $url    = \Pure\Resources\Names::instance()->clearURL($url);
                            $file   = base64_decode($attachment->file);
                            $file   = \Pure\Resources\Names::instance()->repairPath($file);
                            if (file_exists(\Pure\Configuration::instance()->dir($file)) !== false){
                                @unlink($file);
                            }
                            global $wpdb;
                            $result = $wpdb->delete(
                                \Pure\DataBase\TablesNames::instance()->attachments,
                                array(
                                    'object_id'     =>(int)$object_id,
                                    'object_type'   =>esc_sql($object_type),
                                    'user_id'       =>(int)$user_id,
                                    'url'           =>esc_sql($url)
                                ),
                                array('%d', '%s', '%d', '%s')
                            );
                            return ($result > 0 ? true : false);
                        }
                    }
                }
            }
            return false;
        }
        public function removeAll($object_id, $object_type){
            if ((int)$object_id > 0){
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $current            = $WordPress->get_current_user();
                $WordPress          = NULL;
                if ($current !== false){
                    $attachments = $this->get($object_id, $object_type);
                    if ($attachments !== false){
                        foreach($attachments as $attachment){
                            $file = base64_decode($attachment->file);
                            $file = \Pure\Resources\Names::instance()->repairPath($file);
                            if (file_exists(\Pure\Configuration::instance()->dir($file)) !== false){
                                @unlink($file);
                            }
                        }
                        global $wpdb;
                        $result = $wpdb->delete(
                            \Pure\DataBase\TablesNames::instance()->attachments,
                            array(
                                'object_id'     =>(int)$object_id,
                                'object_type'   =>esc_sql($object_type)
                            ),
                            array('%d', '%s')
                        );
                        return ($result > 0 ? true : false);
                    }
                }
            }
            return false;
        }
        function __construct(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->settings->attachments->properties;
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $this->settings = $settings;
        }
    }
}