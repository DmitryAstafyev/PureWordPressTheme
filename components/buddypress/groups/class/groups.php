<?php
namespace Pure\Components\BuddyPress\Groups{
    class Core{
        private $visibility     = array('public', 'private', 'hidden');
        private $invitations    = array('members', 'mods', 'admins');
        private function validate(&$parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'getAvatar':
                    $result             = ($result === false ? $result : (isset($parameters->id) === true ? (gettype($parameters->id) == 'integer'  ? true : false) : false));
                    $parameters->width  = (isset($parameters->width ) === true ? (gettype($parameters->width    ) == 'integer'  ? $parameters->width    : false) : false);
                    $parameters->height = (isset($parameters->height) === true ? (gettype($parameters->height   ) == 'integer'  ? $parameters->height   : false) : false);
                    break;
                case 'getInviteStatus':
                    $result             = ($result === false ? $result : (isset($parameters->id) === true ? (gettype($parameters->id) == 'integer'  ? true : false) : false));
                    break;
                case 'get':
                    $result             = ($result === false ? $result : (isset($parameters->id) === true ? (gettype($parameters->id) == 'integer'  ? true : false) : false));
                    break;
                case 'getMembershipData':
                    $result             = ($result === false ? $result : (isset($parameters->group_id   ) === true ? (gettype($parameters->group_id ) == 'integer'  ? true : false) : false));
                    $result             = ($result === false ? $result : (isset($parameters->user_id    ) === true ? (gettype($parameters->user_id  ) == 'integer'  ? true : false) : false));
                    break;
                case 'removeAvatar':
                    $result             = ($result === false ? $result : (isset($parameters->id     ) === true ? (gettype($parameters->id   ) == 'integer'  ? true : false) : false));
                    break;
                case 'setAvatar':
                    $result             = ($result === false ? $result : (isset($parameters->id     ) === true ? (gettype($parameters->id   ) == 'integer'  ? true : false) : false));
                    $result             = ($result === false ? $result : (isset($parameters->files  ) === true ? (gettype($parameters->files) == 'array'    ? true : false) : false));
                    $result             = ($result === false ? $result : (isset($parameters->field  ) === true ? (gettype($parameters->field) == 'string'   ? true : false) : false));
                    $parameters->crop   = (isset($parameters->crop) === true ? (gettype($parameters->crop) == 'object'  ? $parameters->crop : false) : false);
                    break;
                case 'updateRole':
                    $result             = ($result === false ? $result : (isset($parameters->group_id   ) === true ? (gettype($parameters->group_id ) == 'integer'  ? true : false) : false));
                    $result             = ($result === false ? $result : (isset($parameters->user_id    ) === true ? (gettype($parameters->user_id  ) == 'integer'  ? true : false) : false));
                    $result             = ($result === false ? $result : (isset($parameters->role       ) === true ? (gettype($parameters->role     ) == 'string'   ? true : false) : false));
                    $result             = ($result === false ? $result : (isset($parameters->is_admin   ) === true ? (gettype($parameters->is_admin ) == 'boolean'  ? true : false) : false));
                    break;
                case 'getUserPermissions':
                    $parameters->group  = (isset($parameters->group ) === true ? (gettype($parameters->group) == 'object'  ? $parameters->group : (gettype($parameters->group) == 'integer' ? $parameters->group : false)) : false);
                    $parameters->user   = (isset($parameters->user  ) === true ? (gettype($parameters->user ) == 'object'  ? $parameters->user  : false) : false);
                    break;
                case 'create':
                    $result             = ($result === false ? $result : (isset($parameters->user_id    ) === true ? (gettype($parameters->user_id      ) == 'integer'  ? true : false) : false));
                    $result             = ($result === false ? $result : (isset($parameters->name       ) === true ? (gettype($parameters->name         ) == 'string'   ? true : false) : false));
                    $result             = ($result === false ? $result : (isset($parameters->description) === true ? (gettype($parameters->description  ) == 'string'   ? true : false) : false));
                    $result             = ($result === false ? $result : (isset($parameters->visibility ) === true ? (gettype($parameters->visibility   ) == 'string'   ? true : false) : false));
                    $result             = ($result === false ? $result : (isset($parameters->invitations) === true ? (gettype($parameters->invitations  ) == 'string'   ? true : false) : false));
                    break;
            }
            if ($result === false && \Pure\Configuration::instance()->wp_debug === true){
                \Pure\Components\Tools\ErrorsRender\Initialization::instance()->attach();
                $ErrorMessages = new \Pure\Components\Tools\ErrorsRender\Render();
                $ErrorMessages->show('method: ['.$method.'] did not pass validation in [Pure\Components\BuddyPress\Groups] parameters:'.var_dump($parameters));
                $ErrorMessages = NULL;
            }
            return $result;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'create':
                    $parameters->name           = sanitize_text_field($parameters->name         );
                    $parameters->description    = sanitize_text_field($parameters->description  );
                    $parameters->visibility     = sanitize_text_field($parameters->visibility   );
                    $parameters->invitations    = sanitize_text_field($parameters->invitations  );
                    break;
            }
        }
        public function getAvatar($parameters){
            $result = '';
            if ($this->validate($parameters, __METHOD__) === true){
                if (function_exists('bp_core_fetch_avatar') === true){
                    $result = bp_core_fetch_avatar(array (
                        'item_id'       => $parameters->id,
                        'object'        => 'group',
                        'type'          => 'full',
                        'avatar_dir'    => 'group-avatars',
                        'width'         => ($parameters->width  !== false ? $parameters->width  : ''),
                        'height'        => ($parameters->height !== false ? $parameters->height : ''),
                        'html'          => false ));
                }
            }
            return $result;
        }
        public function getInviteStatus($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                global $wpdb;
                $selector =     'SELECT meta_value AS invite_status '.
                                    'FROM wp_bp_groups_groupmeta '.
                                        'WHERE '.
                                            'group_id='.$parameters->id.' AND '.
                                            'meta_key="invite_status"';
                $group = $wpdb->get_results($selector);
                if ($group === false){
                    return 'members';
                }
                if (count($group) !== 1){
                    return 'members';
                }
                return $group[0]->invite_status;
            }
            return false;
        }
        public function get($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                global $wpdb;
                $GroupsCommon               = \Pure\Providers\Groups\Initialization::instance()->getCommon();
                $available_groups_request   = $GroupsCommon->available_groups_request(false, false);
                $selector                   =   'SELECT groups.* '.
                                                    'FROM ('.$available_groups_request.') AS groups '.
                                                        'WHERE groups.id='.$parameters->id.' '.
                                                        'ORDER BY groups.date_created DESC';
                $group                      = $wpdb->get_results($selector);
                if (count($group) === 1){
                    \Pure\Components\Tools\Arrays\Initialization::instance()->attach(true);
                    $Helpers                = new \Pure\Components\Tools\Arrays\Arrays();
                    $group                  = $group[0];
                    $group->members         = array();
                    $group->banned          = array();
                    $group->waited          = array();
                    $group->moderators      = array();
                    $group->administrators  = array();
                    $selector               =   'SELECT user_id AS ID '.
                                                    'FROM wp_bp_groups_members '.
                                                        'WHERE '.
                                                            'is_confirmed = 1 '.    'AND '.
                                                            'is_banned = 0 '.       'AND '.
                                                            'group_id = '.$parameters->id;
                    $group->members         = $wpdb->get_results($selector);
                    $group->members         = $Helpers->make_array_by_property_of_array_objects($group->members, 'ID', 'integer');
                    $selector               =   'SELECT user_id AS ID '.
                                                    'FROM wp_bp_groups_members '.
                                                        'WHERE '.
                                                            'is_banned = 1 AND '.
                                                            'group_id = '.$parameters->id;
                    $group->banned          = $wpdb->get_results($selector);
                    $group->banned          = $Helpers->make_array_by_property_of_array_objects($group->banned, 'ID', 'integer');
                    $selector               =   'SELECT user_id AS ID '.
                                                    'FROM wp_bp_groups_members '.
                                                        'WHERE '.
                                                            'is_confirmed = 0 '.    'AND '.
                                                            'is_banned = 0 '.       'AND '.
                                                            'group_id = '.$parameters->id;
                    $group->waited          = $wpdb->get_results($selector);
                    $group->waited          = $Helpers->make_array_by_property_of_array_objects($group->waited, 'ID', 'integer');
                    $selector               =   'SELECT user_id AS ID '.
                                                    'FROM wp_bp_groups_members '.
                                                        'WHERE '.
                                                            'is_confirmed = 1 '.    'AND '.
                                                            'is_mod = 1 '.          'AND '.
                                                            'is_banned = 0 '.       'AND '.
                                                            'group_id = '.$parameters->id;
                    $group->moderators      = $wpdb->get_results($selector);
                    $group->moderators      = $Helpers->make_array_by_property_of_array_objects($group->moderators, 'ID', 'integer');
                    $selector               =   'SELECT user_id AS ID '.
                                                    'FROM wp_bp_groups_members '.
                                                        'WHERE '.
                                                            'is_confirmed = 1 '.    'AND '.
                                                            'is_admin = 1 '.        'AND '.
                                                            'is_banned = 0 '.       'AND '.
                                                            'group_id = '.$parameters->id;
                    $group->administrators  = $wpdb->get_results($selector);
                    $group->administrators  = $Helpers->make_array_by_property_of_array_objects($group->administrators, 'ID', 'integer');
                    $Helpers                = NULL;
                    $avatar                 = $this->getAvatar((object)array('id'=>(int)$group->id));
                    $group->avatar          = (is_string($avatar) === true ? ($avatar !== '' ? $avatar : false) : false);
                    $group->invite_status   = $this->getInviteStatus((object)array('id'=>(int)$group->id));
                    return $group;
                }
            }
            return false;
        }
        public function getMembershipData($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                global $wpdb;
                $GroupsCommon               = \Pure\Providers\Groups\Initialization::instance()->getCommon();
                $available_groups_request   = $GroupsCommon->available_groups_request(false, false);
                $selector                   =   'SELECT wp_bp_groups_members.* '.
                                                    'FROM ('.$available_groups_request.') AS groups, wp_bp_groups_members '.
                                                        'WHERE '.
                                                            'groups.id=wp_bp_groups_members.group_id '.     'AND '.
                                                            'groups.id='.$parameters->group_id.' '.         'AND '.
                                                            'wp_bp_groups_members.user_id = '.$parameters->user_id.' '.
                                                        'GROUP BY wp_bp_groups_members.user_id ';
                $result                      = $wpdb->get_results($selector);
                if ($result === false || count($result) > 1){
                    return NULL;//Some error
                }elseif(count($result) === 0){
                    return false;//User isn't a member
                }else{
                    $result = $result[0];
                    if ($result->is_banned == 1){
                        $status = 'banned';
                    }elseif($result->is_confirmed == 0 && $result->invite_sent == 0){
                        $status = 'waited';
                    }elseif($result->is_confirmed == 0 && $result->invite_sent == 1){
                        $status = 'invited';
                    }else{
                        $status = 'member';
                    }
                    $result->status = $status;
                    return $result;
                }
            }
            return NULL;
        }
        public function removeAvatar($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                if (bp_core_delete_existing_avatar(array('item_id'=>(int)$parameters->id, 'object'=>'group')) !== false){
                    return true;
                }
            }
            return false;
        }
        public function setAvatar($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                if (count($parameters->files) === 1){
                    if (isset($parameters->files[$parameters->field]['type']) === true){
                        \Pure\Components\GlobalSettings\MIMETypes\Initialization::instance()->attach(true);
                        if (stripos(\Pure\Components\GlobalSettings\MIMETypes\Types::$images, $parameters->files[$parameters->field]['type']) !== false){
                            global $bp;
                            $_POST['action']            = 'bp_avatar_upload';
                            $current_group              = groups_get_group(array('group_id' => $parameters->id));
                            $bp->groups->current_group  = $current_group;
                            $bp->avatar_admin           = new \stdClass();
                            $size                       = @getimagesize( $parameters->files[$parameters->field]['tmp_name'] );
                            if (is_array($size) === true){
                                $max_width  = bp_core_avatar_original_max_width();
                                $ratio      = ($size[0] > $max_width ? $max_width / $size[0] : 1);
                                if (bp_core_avatar_handle_upload($parameters->files, 'groups_avatar_upload_dir') === true) {
                                    $bp->avatar_admin->step = 'crop-image';
                                    if ($parameters->crop === false){
                                        return bp_core_avatar_handle_crop( array(
                                                'object'        => 'group',
                                                'avatar_dir'    => 'group-avatars',
                                                'item_id'       => $bp->groups->current_group->id,
                                                'original_file' => $bp->avatar_admin->image->dir)
                                        );
                                    }else{
                                        return bp_core_avatar_handle_crop( array(
                                                'object'        => 'group',
                                                'avatar_dir'    => 'group-avatars',
                                                'item_id'       => $bp->groups->current_group->id,
                                                'original_file' => $bp->avatar_admin->image->dir,
                                                'crop_w'        => (int)($parameters->crop->w * $ratio),
                                                'crop_h'        => (int)($parameters->crop->h * $ratio),
                                                'crop_x'        => (int)($parameters->crop->x * $ratio),
                                                'crop_y'        => (int)($parameters->crop->y * $ratio))

                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return false;
        }
        public function updateRole($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                if ($parameters->role === 'admin' || $parameters->role === 'mod'){
                    $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                    $current    = $WordPress->get_current_user();
                    $WordPress  = NULL;
                    $group      = $this->get((object)array('id'=>$parameters->group_id));
                    if ($current !== false && $group !== false){
                        if (in_array($current->ID, $group->administrators) === true){
                            global $wpdb;
                            try{
                                $wpdb->update( 'wp_bp_groups_members',
                                    array( ($parameters->role === 'admin' ? 'is_admin' : 'is_mod') => ($parameters->is_admin === true ? 1 : 0) ),
                                    array(
                                        'group_id'  => (int)$parameters->group_id,
                                        'user_id'   => (int)$parameters->user_id ,
                                    ),
                                    array( '%d' ),
                                    array( '%d', '%d' )
                                );
                                if ($parameters->role === 'admin'){
                                    $action = ($parameters->is_admin === true ? 'is admin' : 'is not admin');
                                }else{
                                    $action = ($parameters->is_admin === true ? 'is mod' : 'is not mod');
                                }
                                \Pure\Components\BuddyPress\Activities\Initialization::instance()->attach();
                                $Actions = new \Pure\Components\BuddyPress\Activities\Actions();
                                $Actions->add_role(
                                    $parameters->group_id,
                                    $current->ID,
                                    $parameters->user_id,
                                    $action
                                );
                                $Actions = NULL;
                                return true;
                            }catch (\Exception $e){
                                return false;
                            }
                        }
                    }
                }
            }
            return false;
        }
        public function getUserPermissions($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                if ($parameters->group !== false){
                    if (gettype($parameters->group) === 'integer'){
                        $parameters->group = $this->get((object)array('id'=>$parameters->group));
                    }
                    if ($parameters->user === false){
                        $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                        $parameters->user   = $WordPress->get_current_user();
                        $WordPress          = NULL;
                    }
                    $permissions = (object)array(
                        'details'               =>false,
                        'visibility'            =>false,
                        'invite'                =>false,
                        'requests'              =>false,
                        'roles_and_remove'      =>false,
                        'ban_and_admonition'    =>false
                    );
                    if ($parameters->group !== false){
                        if ($parameters->user !== false){
                            $permissions->details               = in_array((int)$parameters->user->ID, $parameters->group->administrators);
                            $permissions->visibility            = in_array((int)$parameters->user->ID, $parameters->group->administrators);
                            $permissions->invite                = ($parameters->group->invite_status === 'members' ? (in_array((int)$parameters->user->ID, $parameters->group->members) === true ? true : false) : false);
                            $permissions->invite                = ($permissions->invite === false ? ($parameters->group->invite_status === 'mods' ? (in_array((int)$parameters->user->ID, $parameters->group->administrators) === true || in_array((int)$parameters->user->ID, $parameters->group->moderators) === true ? true : false) : false) : $permissions->invite);
                            $permissions->invite                = ($permissions->invite === false ? ($parameters->group->invite_status === 'admins' ? (in_array((int)$parameters->user->ID, $parameters->group->administrators) === true ? true : false) : false) : $permissions->invite);
                            $permissions->requests              = (in_array((int)$parameters->user->ID, $parameters->group->administrators) === true || in_array((int)$parameters->user->ID, $parameters->group->moderators) === true ? true : false);
                            $permissions->roles_and_remove      = in_array((int)$parameters->user->ID, $parameters->group->administrators);
                            $permissions->ban_and_admonition    = (in_array((int)$parameters->user->ID, $parameters->group->administrators) === true || in_array((int)$parameters->user->ID, $parameters->group->moderators) === true ? true : false);
                        }
                    }
                    $has_rights = false;
                    foreach($permissions as $permission){
                        $has_rights = ($has_rights === false ? $permission : $has_rights);
                    }
                    $permissions->has_rights = $has_rights;
                    return $permissions;
                }
            }
            return false;
        }
        public function create($parameters) {
            if ($this->validate($parameters, __METHOD__) === true) {
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->user_id){
                    $this->sanitize($parameters, __METHOD__);
                    return $this->unsafeCreate($parameters);
                }else{
                    return 'no_permissions';
                }
            }
            return false;
        }
        //This is unsafe way to create group. Better to use create. But this method used by DEMO creator
        public function unsafeCreate($parameters) {
            if (mb_strlen($parameters->name) < 3 || mb_strlen($parameters->name) > 255){
                return 'wrong_name';
            }
            if (mb_strlen($parameters->description) < 10 || mb_strlen($parameters->description) > 500){
                return 'wrong_description';
            }
            if (in_array($parameters->visibility, $this->visibility) === false){
                return 'wrong_visibility';
            }
            if (in_array($parameters->invitations, $this->invitations) === false){
                return 'wrong_invitations';
            }
            if (function_exists('groups_create_group') === true){
                $id = groups_create_group(array(
                        'creator_id'   => $parameters->user_id,
                        'name'         => $parameters->name,
                        'description'  => $parameters->description,
                        'status'       => $parameters->visibility)
                );
                if ($id !== false){
                    if (groups_edit_group_settings(
                            $id,
                            0,
                            $parameters->visibility,
                            $parameters->invitations) === true){
                        \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach();
                        $URLs   = new \Pure\Components\BuddyPress\URLs\Core();
                        $url    = $URLs->group($id);
                        $URLs   = NULL;
                        return (object)array(
                            'id'    =>$id,
                            'name'  =>$parameters->name,
                            'url'   =>$url
                        );
                    }else{
                        return 'error_during_edit_settings';
                    }
                }else{
                    return 'error_during_creation';
                }
            }
        }
        function __construct(){
        }
    }
}
?>