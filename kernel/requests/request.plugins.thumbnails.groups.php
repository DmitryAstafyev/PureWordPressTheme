<?php
namespace Pure\Requests\Plugins\Thumbnails\Groups{
    class More{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $parameters->count              = (integer  )($parameters->count            );
                    $parameters->maximum            = (integer  )($parameters->maximum          );
                    $parameters->template           = (string   )($parameters->template         );
                    $parameters->content            = (string   )($parameters->content          );
                    $parameters->targets            = (string   )($parameters->targets          );
                    $parameters->days               = (integer  )($parameters->days             );
                    $parameters->from_date          = (string   )($parameters->from_date        );
                    $parameters->show_content       = (boolean  )($parameters->show_content     );
                    $parameters->show_admin_part    = (boolean  )($parameters->show_admin_part  );
                    $parameters->show_life          = (boolean  )($parameters->show_life        );
                    return true;
            }
            return false;
        }
        public function get($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->plugins.'/Thumbnails/Groups/inc/kernel.php'));
                $_parameters    = array(	'content'	        => $parameters->content,
                                            'targets'	        => $parameters->targets,
                                            'template'		    => $parameters->template,
                                            'title'		        => '',
                                            'title_type'        => '',
                                            'maxcount'	        => $parameters->maximum,
                                            'only_with_avatar'	=> false,
                                            'top'	            => false,
                                            'days'	            => $parameters->days,
                                            'from_date'         => $parameters->from_date,
                                            'more'              => false,
                                            'group'             => $parameters->group,
                                            'shown'             => $parameters->count,
                                            'show_content'      => $parameters->show_content,
                                            'show_admin_part'   => $parameters->show_admin_part,
                                            'show_life'         => $parameters->show_life,
                                            'init_scripts'      => true,
                );
                try{
                    $widget     = new \Pure\Plugins\Thumbnails\Groups\Builder($_parameters);
                    $innerHTML  = $widget->render();
                    echo $innerHTML;
                }catch (\Exception $e){
                    return 'error';
                }
            }
        }
    }
    class Membership{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'set':
                    $parameters->user       = (integer  )($parameters->user  );
                    $parameters->group      = (integer  )($parameters->group );
                    return true;
            }
            return false;
        }
        public function set($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                $GroupData  = new \Pure\Components\BuddyPress\Groups\Core();
                $current    = $WordPress->get_current_user(true, false);
                if ($current !== false){
                    if ($current->ID === $parameters->user){
                        $membership = $GroupData->getMembershipData((object)array(
                            'group_id'  =>(int)$parameters->group,
                            'user_id'   =>(int)$parameters->user));
                        if (is_null($membership) === false){
                            if ($membership === false){
                                //User isn't a member of group
                                $group = $GroupData->get((object)array('id'=>(int)$parameters->group));
                                if ($group !== false){
                                    switch($group->status){
                                        case 'public':
                                            if (function_exists('groups_join_group') === true){
                                                if (groups_join_group((int)$parameters->group, (int)$parameters->user) === true){
                                                    echo 'user_joined_to_group'; return true;
                                                }else{
                                                    echo 'BuddyPress_error'; return false;
                                                }
                                            }else{
                                                echo 'BuddyPress_error'; return false;
                                            }
                                            break;
                                        case 'private':
                                            if (function_exists('groups_send_membership_request') === true){
                                                if (groups_send_membership_request((int)$parameters->user, (int)$parameters->group) === true){
                                                    echo 'request_for_membership_sent'; return true;
                                                }else{
                                                    echo 'BuddyPress_error'; return false;
                                                }
                                            }else{
                                                echo 'BuddyPress_error'; return false;
                                            }
                                            break;
                                        case 'hidden':
                                            echo 'this_is_hidden_group'; return true;
                                            break;
                                    }
                                }
                            }else{
                                if ($membership->status === 'member'){
                                    //User is a member of group
                                    if (function_exists('groups_leave_group') === true){
                                        if (groups_leave_group((int)$parameters->group, (int)$parameters->user) === true){
                                            echo 'user_removed_from_group'; return true;
                                        }else{
                                            echo 'BuddyPress_error'; return false;
                                        }
                                    }else{
                                        echo 'BuddyPress_error'; return false;
                                    }
                                }
                                if ($membership->status === 'waited'){
                                    //User waiting for acceptation
                                    if (function_exists('groups_reject_membership_request') === true){
                                        if (groups_reject_membership_request($membership->id, (int)$parameters->user , (int)$parameters->group) === true){
                                            echo 'user_rejected_request_for_membership'; return true;
                                        }else{
                                            echo 'BuddyPress_error'; return false;
                                        }
                                    }else{
                                        echo 'BuddyPress_error'; return false;
                                    }
                                }
                                if ($membership->status === 'banned') {
                                    //User was banned
                                    echo 'user_was_banned'; return true;
                                }
                            }
                        }
                    }
                }
                echo 'wrong_data';
            }
        }
    }
    class Authorization{
        static private $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        public function user_status($group_id, $user_id){
            \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
            $GroupData  = new \Pure\Components\BuddyPress\Groups\Core();
            $group      = $GroupData->get((object)array('id'=>(int)$group_id));
            $GroupData  = NULL;
            $result     = false;
            if ($group !== false){
                return (object)array(
                    'is_admin'  => in_array($user_id, $group->administrators   ),
                    'is_mod'    => in_array($user_id, $group->moderators       ),
                    'is_member' => in_array($user_id, $group->members          ),
                    'is_ban'    => in_array($user_id, $group->banned           ),
                    'is_wait'   => in_array($user_id, $group->waited           )
                );
            }
            return $result;
        }
        public function validate($parameters, $allow_admin = true, $allow_mod = false, $allow_member = false){
            $WordPress          = new \Pure\Components\WordPress\UserData\Data();
            \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
            $BuddyPressGroups   = new \Pure\Components\BuddyPress\Groups\Core();
            $current            = $WordPress->get_current_user();
            $result             = false;
            if ((int)$current->ID === $parameters->user){
                $group = $BuddyPressGroups->get((object)array('id'=>(int)$parameters->group));
                if ($group !== false){
                    $status = (object)array(
                        'is_admin'  => in_array($parameters->user, $group->administrators   ),
                        'is_mod'    => in_array($parameters->user, $group->moderators       ),
                        'is_member' => in_array($parameters->user, $group->members          ),
                        'is_ban'    => in_array($parameters->user, $group->banned           ),
                        'is_wait'   => in_array($parameters->user, $group->waited           )
                    );
                    if      ($allow_admin === true && $allow_mod === false && $allow_member === false){
                        if ($status->is_admin === true){
                            $result = true;
                        }else{
                            $result = 'no_permission';
                        }
                    }elseif ($allow_admin === true && $allow_mod === true && $allow_member === false){
                        if ($status->is_admin   === true ||
                            $status->is_mod     === true){
                            $result = true;
                        }else{
                            $result = 'no_permission';
                        }
                    }elseif ($allow_admin === true && $allow_mod === true && $allow_member === true){
                        if ($status->is_admin   === true ||
                            $status->is_mod     === true ||
                            $status->is_member  === true){
                            $result = true;
                        }else{
                            $result = 'no_permission';
                        }
                    }
                }else{
                    $result = 'wrong_group';
                }
            }else{
                $result = 'wrong_user';
            }
            $WordPress          = NULL;
            $BuddyPressGroups   = NULL;
            return $result;
        }
    }
    class Settings{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'removeAvatar':
                    $parameters->user       = (integer  )($parameters->user     );
                    $parameters->group      = (integer  )($parameters->group    );
                    return true;
                case 'avatar':
                    $parameters->user       = (integer  )($parameters->user     );
                    $parameters->group      = (integer  )($parameters->group    );
                    $parameters->path       = (string   )($parameters->path     );
                    $parameters->x          = (integer  )($parameters->x        );
                    $parameters->y          = (integer  )($parameters->y        );
                    $parameters->height     = (integer  )($parameters->height   );
                    $parameters->width      = (integer  )($parameters->width    );
                    return true;
                case 'basic':
                    $parameters->user           = (integer  )($parameters->user             );
                    $parameters->group          = (integer  )($parameters->group            );
                    $parameters->name           = (string   )($parameters->name             );
                    $parameters->description    = (string   )($parameters->description      );
                    $parameters->notifications  = (string   )($parameters->notifications    );
                    return true;
                case 'visibility':
                    $parameters->user           = (integer  )($parameters->user             );
                    $parameters->group          = (integer  )($parameters->group            );
                    $parameters->invite_status  = (string   )($parameters->invite_status    );
                    $parameters->status         = (string   )($parameters->status           );
                    $parameters->forum          = false;//Reservation for the future
                    return true;
            }
            return false;
        }
        public function removeAvatar($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                $BuddyPressGroups   = new \Pure\Components\BuddyPress\Groups\Core();
                $validate_user      = Authorization::instance()->validate($parameters, true, false, false);
                if ($validate_user === true) {
                    if ($BuddyPressGroups->removeAvatar((object)array('id'=>$parameters->group)) !== false){
                        echo 'success';
                        $BuddyPressGroups = NULL;
                        return true;
                    }
                }
                $BuddyPressGroups = NULL;
            }
            echo 'fail';
            return false;
        }
        public function avatar($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $response   = (object)array(
                    'url'       =>'',
                    'message'   =>''
                );
                \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                $BuddyPressGroups   = new \Pure\Components\BuddyPress\Groups\Core();
                $validate_user      = Authorization::instance()->validate($parameters, true, false, false);
                if ($validate_user === true){
                    if (isset($_FILES['file']) === true){
                        if ($_FILES['file']['size'] < bp_core_avatar_original_max_filesize()){
                            if ($parameters->x      !== -1 && $parameters->y        !== -1 &&
                                $parameters->height !== -1 && $parameters->width    !== -1){
                                $crop = (object)array(
                                    'x'=>$parameters->x,
                                    'y'=>$parameters->y,
                                    'h'=>$parameters->height,
                                    'w'=>$parameters->width
                                );
                            }else{
                                $crop = false;
                            }
                            if ($crop === false){
                                if ( !function_exists( 'wp_handle_upload' ) ) { require_once( \Pure\Configuration::instance()->dir(ABSPATH . 'wp-admin/includes/file.php') ); }
                                $file               = wp_handle_upload($_FILES['file'], array( 'test_form' => false ));
                                $response->url      = $file['url'];
                                $response->path     = $file['file'];
                                $response->message  = 'ready_for_crop';
                            }else{
                                if ($parameters->path !== ''){
                                    @unlink($parameters->path);
                                }
                                if ($BuddyPressGroups->setAvatar((object)array(
                                        'id'    =>(int)$parameters->group,
                                        'files' =>$_FILES,
                                        'field' =>'file',
                                        'crop'  =>$crop)) === true){
                                    $response->url      = $BuddyPressGroups->getAvatar((object)array('id'=>(int)$parameters->group));
                                    $response->message  = 'success';
                                }else{
                                    $response->message = 'error_during_saving';
                                }
                            }
                        }else{
                            $response->message = 'too_large_filesize';
                        }
                    }else{
                        $response->message = 'wrong_file';
                    }
                }else{
                    $response->message = $validate_user;
                }
                $BuddyPressGroups = NULL;
                echo json_encode($response);
            }
        }
        public function basic($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $parameters->name           = sanitize_text_field($parameters->name);
                $parameters->description    = sanitize_text_field($parameters->description);
                if ($parameters->name === ''){
                    echo 'no_name';
                    return false;
                }
                if ($parameters->description === ''){
                    echo 'no_description';
                    return false;
                }
                $validate_user = Authorization::instance()->validate($parameters, true, false, false);
                if ($validate_user === true){
                    if (groups_edit_base_group_details( $parameters->group,         $parameters->name,
                                                        $parameters->description,   ($parameters->notifications === 'on' ? true : false) ) === true){
                        echo 'success';
                        return true;
                    }else{
                        echo 'fail';
                        return false;
                    }
                }else{
                    echo $validate_user;
                    return false;
                }
            }
        }
        public function visibility($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $parameters->status           = sanitize_text_field($parameters->status);
                $parameters->invite_status    = sanitize_text_field($parameters->invite_status);
                if ($parameters->status === ''){
                    echo 'no_status';
                    return false;
                }
                if ($parameters->invite_status === ''){
                    echo 'no_invite_status';
                    return false;
                }
                if (in_array($parameters->status, array('public', 'private', 'hidden')) === false){
                    echo 'bad_status';
                    return false;
                }
                if (in_array($parameters->invite_status, array('members', 'mods', 'admins')) === false){
                    echo 'bad_invite_status';
                    return false;
                }
                $validate_user = Authorization::instance()->validate($parameters, true, false, false);
                if ($validate_user === true){
                    if (function_exists('groups_edit_group_settings') === true){
                        if (groups_edit_group_settings($parameters->group, $parameters->forum, $parameters->status, $parameters->invite_status) === true){
                            echo 'success';
                            return true;
                        }else{
                            echo 'error';
                            return false;
                        }
                    }
                }
                echo 'fail';
                return false;
            }
        }
    }
    class Actions{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'doAction':
                    $parameters->user           = (integer  )($parameters->user         );
                    $parameters->group          = (integer  )($parameters->group        );
                    $parameters->target_user    = (integer  )($parameters->target_user  );
                    $parameters->action         = (string   )($parameters->action       );
                    $parameters->comment        = (string   )($parameters->comment      );
                    return true;
            }
            return false;
        }
        public function doAction($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $target_status  = Authorization::instance()->user_status($parameters->group, $parameters->target_user);
                \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                $GroupTools     = new \Pure\Components\BuddyPress\Groups\Core();
                if ($target_status !== false){
                    switch($parameters->action){
                        case 'admin':
                            if (Authorization::instance()->validate($parameters, true, false, false) === true){
                                if ($parameters->user !== $parameters->target_user){
                                    if ($target_status->is_admin === true){
                                        if ($GroupTools->updateRole((object)array(
                                                'user_id'   =>(int)$parameters->target_user,
                                                'group_id'  =>(int)$parameters->group,
                                                'role'      =>'admin',
                                                'is_admin'  =>false)) === true){
                                            echo 'admin_removed'; return true;
                                        }
                                    }else{
                                        if ($target_status->is_ban === false){
                                            if ($GroupTools->updateRole((object)array(
                                                    'user_id'   =>(int)$parameters->target_user,
                                                    'group_id'  =>(int)$parameters->group,
                                                    'role'      =>'admin',
                                                    'is_admin'  =>true)) === true){
                                                echo 'admin_accepted'; return true;
                                            }
                                        }else{
                                            echo 'banned_user_cannot_be_admin'; return false;
                                        }
                                    }
                                }else{
                                    echo 'admin_cannot_remove_admin_rights_of_himself'; return false;
                                }
                            }
                            break;
                        case 'mod':
                            if (Authorization::instance()->validate($parameters, true, false, false) === true){
                                if ($target_status->is_mod === true){
                                    if ($GroupTools->updateRole((object)array(
                                            'user_id'   =>(int)$parameters->target_user,
                                            'group_id'  =>(int)$parameters->group,
                                            'role'      =>'mod',
                                            'is_admin'  =>false)) === true){
                                        echo 'mod_removed'; return true;
                                    }
                                }else{
                                    if ($target_status->is_ban === false){
                                        if ($GroupTools->updateRole((object)array(
                                                'user_id'   =>(int)$parameters->target_user,
                                                'group_id'  =>(int)$parameters->group,
                                                'role'      =>'mod',
                                                'is_admin'  =>true)) === true){
                                            echo 'mod_accepted'; return true;
                                        }
                                    }else{
                                        echo 'banned_user_cannot_be_moderator'; return false;
                                    }
                                }
                            }
                            break;
                        case 'admonition':
                            if (Authorization::instance()->validate($parameters, true, true, false) === true){
                                \Pure\Components\BuddyPress\Admonitions\Initialization::instance()->attach();
                                $Admonitions    = new \Pure\Components\BuddyPress\Admonitions\Core();
                                $arguments      = (object)array(
                                    'group'     =>$parameters->group,
                                    'user'      =>$parameters->target_user,
                                    'comment'   =>$parameters->comment,
                                );
                                if ($Admonitions->add($arguments) === true){
                                    echo 'warned'; return true;
                                }
                                $Admonitions    = NULL;
                            }
                            break;
                        case 'ban':
                            if (Authorization::instance()->validate($parameters, true, true, false) === true){
                                if ($target_status->is_admin === false){
                                    if ($target_status->is_mod === false){
                                        $result = false;
                                        if ($target_status->is_ban === true){
                                            if (groups_unban_member($parameters->target_user, $parameters->group) !== false){
                                                $result = 'unban';
                                            }
                                        }else{
                                            if(groups_ban_member($parameters->target_user, $parameters->group) !== false){
                                                $result = 'ban';
                                            }
                                        }
                                        if ($result !== false){
                                            \Pure\Components\BuddyPress\Activities\Initialization::instance()->attach();
                                            $Actions = new \Pure\Components\BuddyPress\Activities\Actions();
                                            $Actions->add_ban(
                                                $parameters->group,
                                                $parameters->user,
                                                $parameters->target_user,
                                                $result
                                            );
                                            $Actions = NULL;
                                            if ($result === 'ban'){
                                                echo 'banned'; return true;
                                            }else{
                                                echo 'unbanned'; return true;
                                            }
                                        }
                                    }else{
                                        echo 'moderator_cannot_be_banned'; return false;
                                    }
                                }else{
                                    echo 'admin_cannot_be_banned'; return false;
                                }
                            }
                            break;
                        case 'remove':
                            if (Authorization::instance()->validate($parameters, true, false, false) === true){
                                if ($parameters->user !== $parameters->target_user){
                                    if (groups_remove_member($parameters->target_user, $parameters->group) !== false){
                                        \Pure\Components\BuddyPress\Activities\Initialization::instance()->attach();
                                        $Actions = new \Pure\Components\BuddyPress\Activities\Actions();
                                        $Actions->add_remove(
                                            $parameters->group,
                                            $parameters->user,
                                            $parameters->target_user
                                        );
                                        $Actions = NULL;
                                        echo 'removed'; return true;
                                    }
                                }else{
                                    echo 'admin_cannot_remove_himself'; return false;
                                }
                            }
                            break;
                    }
                }
            }
            echo 'fail';
            return false;
        }
    }
    class Requests{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'doAction':
                    $parameters->user           = (integer  )($parameters->user         );
                    $parameters->group          = (integer  )($parameters->group        );
                    $parameters->request_id     = (integer  )($parameters->request_id   );
                    $parameters->waited_user    = (integer  )($parameters->waited_user  );
                    $parameters->action         = (string   )($parameters->action       );
                    return true;
            }
            return false;
        }
        public function doAction($parameters) {
            if ($this->validate($parameters, __METHOD__) === true) {
                if (Authorization::instance()->validate($parameters, true, false, false) === true){
                    $target_status  = Authorization::instance()->user_status($parameters->group, $parameters->waited_user);
                    if ($target_status !== false) {
                        if ($target_status->is_wait === true){
                            switch ($parameters->action) {
                                case 'accept':
                                    if (groups_accept_membership_request( $parameters->request_id, $parameters->waited_user, $parameters->group ) !== false){
                                        echo 'accepted'; return false;
                                    }else{
                                        echo 'error'; return false;
                                    }
                                    break;
                                case 'deny':
                                    if (groups_delete_membership_request( $parameters->request_id, $parameters->waited_user, $parameters->group ) !== false){
                                        echo 'denied'; return false;
                                    }else{
                                        echo 'error'; return false;
                                    }
                                    break;
                            }
                        }
                    }
                }
            }
            echo 'fail';
            return false;
        }
    }
    class IncomeInvites{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'doAction':
                    $parameters->user           = (integer  )($parameters->user         );
                    $parameters->group          = (integer  )($parameters->group        );
                    $parameters->action         = (string   )($parameters->action       );
                    return true;
            }
            return false;
        }
        public function doAction($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                switch($parameters->action){
                    case 'accept':
                         if (groups_accept_invite( $parameters->user, $parameters->group ) !== false){
                             echo 'accepted';   return true;
                         }else{
                             echo 'fail';       return false;
                         }
                        break;
                    case 'deny':
                        if (groups_reject_invite( $parameters->user, $parameters->group ) !== false){
                            echo 'denied';      return true;
                        }else{
                            echo 'fail';        return false;
                        }
                        break;
                }
            }
            echo 'fail'; return false;
        }
    }
    class Invites{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'doAction':
                    $parameters->user           = (integer  )($parameters->user         );
                    $parameters->group          = (integer  )($parameters->group        );
                    $parameters->members        = (string   )($parameters->members      );
                    $parameters->action         = (string   )($parameters->action       );
                    return true;
            }
            return false;
        }
        public function doAction($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                $Groups         = new \Pure\Components\BuddyPress\Groups\Core();
                $invite_status  = $Groups->getInviteStatus((object)array('id'=>(int)$parameters->group));
                $Groups         = NULL;
                if ($invite_status !== false){
                    if (Authorization::instance()->validate($parameters, true,
                            ($invite_status === 'mods' || $invite_status === 'members' ? true : false),
                            ($invite_status === 'members' ? true : false)) === true) {
                        $members_IDs = explode(',', $parameters->members);
                        if (is_array($members_IDs) === true){
                            foreach($members_IDs as $member_ID){
                                switch($parameters->action){
                                    case 'invite':
                                        if (groups_invite_user(array(
                                                'user_id'       => $member_ID,
                                                'group_id'      => $parameters->group,
                                                'inviter_id'    => $parameters->user,
                                                'is_confirmed'  => 0
                                            )) === false){
                                            echo 'error'; return false;
                                        }
                                        break;
                                    case 'reject':
                                        if (groups_uninvite_user( $member_ID, $parameters->group ) === false){
                                            echo 'error'; return false;
                                        }
                                        break;
                                }
                            }
                            if ($parameters->action === 'invite'){
                                groups_send_invites($parameters->user, $parameters->group);
                            }
                            echo ($parameters->action === 'invite' ? 'invited' : 'rejected');
                            return true;
                        }
                    }
                }
            }
            echo 'fail'; return false;
        }
    }
}
?>