<?php
namespace Pure\Components\BuddyPress\Admonitions{
    class Core{
        private $table;
        private function validate($parameters, $method){
            $result = true;
            switch($method){
                case 'get':
                    $result = ($result === false ? $result : (isset($parameters->user   ) === true ? (gettype($parameters->user     ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->group  ) === true ? (gettype($parameters->group    ) == 'integer'  ? true : false) : false));
                    break;
                case 'add':
                    $result = ($result === false ? $result : (isset($parameters->user   ) === true ? (gettype($parameters->user     ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->group  ) === true ? (gettype($parameters->group    ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->comment) === true ? (gettype($parameters->comment  ) == 'string'   ? true : false) : false));
                    break;
            }
            return $result;
        }
        private function sanitize(&$parameters, $method){
            switch($method){
                case 'add':
                    $parameters->comment = sanitize_text_field($parameters->comment);
                    break;
            }
        }
        public function count($parameters){
            if ($this->validate($parameters, 'get') === true) {
                global $wpdb;
                $GroupsCommon               = \Pure\Providers\Groups\Initialization::instance()->getCommon();
                $available_groups_request   = $GroupsCommon->available_groups_request(false, false);
                $GroupsCommon               = NULL;
                $selector                   =   'SELECT admonitions.* '.
                                                    'FROM ('.$available_groups_request.') AS groups, '.$this->table.' AS admonitions ' .
                                                        'WHERE '.
                                                            'groups.id=admonitions.group_id '.              'AND '.
                                                            'admonitions.group_id='.$parameters->group.' '. 'AND '.
                                                            'admonitions.user_id='.$parameters->user;
                $result                     = $wpdb->get_results($selector);
                if (is_array($result) === true){
                    return count($result);
                }
            }
            return false;
        }
        public function get($parameters){
            if ($this->validate($parameters, 'get') === true){
                global $wpdb;
                $GroupsCommon               = \Pure\Providers\Groups\Initialization::instance()->getCommon();
                $available_groups_request   = $GroupsCommon->available_groups_request(false, false);
                $GroupsCommon               = NULL;
                $selector                   =   'SELECT admonitions.* '.
                                                    'FROM ('.$available_groups_request.') AS groups, '.$this->table.' AS admonitions ' .
                                                        'WHERE '.
                                                            'groups.id=admonitions.group_id '.              'AND '.
                                                            'admonitions.group_id='.$parameters->group.' '. 'AND '.
                                                            'admonitions.user_id='.$parameters->user;
                $result                     = $wpdb->get_results($selector);
                if (is_array($result) === true){
                    return $result;
                }
            }
            return false;
        }
        public function add($parameters){
            if ($this->validate($parameters, 'add') === true){
                $this->sanitize($parameters, 'add');
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                $BuddyPressGroups   = new \Pure\Components\BuddyPress\Groups\Core();
                $current            = $WordPress->get_current_user();
                $user_name          = $WordPress->get_name((int)$parameters->user);
                $group              = $BuddyPressGroups->get((object)array('id'=>$parameters->group));
                $WordPress          = NULL;
                $BuddyPressGroups   = NULL;
                if ($group !== false){
                    if (in_array($current->ID, $group->moderators) === true || in_array($current->ID, $group->administrators) === true){
                        global $wpdb;
                        try{
                            $wpdb->query(   'INSERT INTO '.
                                                $this->table.' '.
                                                    'SET '.
                                                        'group_id = '.      $parameters->group.     ','.
                                                        'user_id = '.       $parameters->user.      ','.
                                                        'date_sent ="'.     date("Y-m-d H:i:s").    '",'.
                                                        'sender_comment ="'.$parameters->comment.   '"'
                            );
                            if ($parameters->comment !== ''){
                                \Pure\Components\BuddyPress\Activities\Initialization::instance()->attach();
                                $Actions = new \Pure\Components\BuddyPress\Activities\Actions();
                                $Actions->add_admonition(
                                    $parameters->group,
                                    $current->ID,
                                    $parameters->user,
                                    $parameters->comment
                                );
                                $Actions = NULL;
                            }
                            return true;
                        }catch (\Exception $e){
                            return false;
                        }
                    }
                }
            }
            return false;
        }
        function __construct(){
            $this->table = \Pure\DataBase\TablesNames::instance()->admonitions;
        }
    }
}
?>