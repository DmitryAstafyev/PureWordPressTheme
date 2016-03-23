<?php
namespace Pure\Components\BuddyPress\Activities{
    class Core{
        public function get_by_id($activity_id){
            $data = bp_activity_get(array ( 'page'              => 1,
                'per_page'          => 100000,
                'display_comments'  => true,
                'in'                => (string)$activity_id));
            //echo '<p>'.var_dump($data["activities"]).'</p>';
            return (isset($data["activities"]) === true ? (count($data["activities"]) === 1 ? $data["activities"][0] : false) : false);
        }
        public function get_root_by_child_id($child){
            $parent = $this->get_by_id($child->item_id);
            if ($parent !== false){
                if ((integer)$parent->item_id !== 0){
                    $_parent = $this->get_root_by_child_id($parent);
                    return ($_parent->children  === false ? $child : (is_array($_parent->children)  === true ? $_parent : $child));
                }else{
                    return ($parent->children   === false ? $child : (is_array($parent->children)   === true ? $parent  : $child));
                }
            }else{
                return $child;
            }
        }
        public function is_favorite($user_id, $activity_id){
            $my_favs = bp_get_user_meta( $user_id, 'bp_favorite_activities', true );
            if ( empty( $my_favs ) || ! is_array( $my_favs ) ) {
                $my_favs = array();
            }
            return in_array( $activity_id, $my_favs );
        }
    }
    class Actions{
        public $admonition_type = 'admonition_member';
        public $ban_type        = 'ban_member';
        public $role_type       = 'role_member';
        public $remove_type     = 'remove_member';
        public function init(){
            $bp = buddypress();
            bp_activity_set_action(
                $bp->groups->id,
                $this->admonition_type,
                'Admonitions in group for members.',
                array('\Pure\Components\BuddyPress\Activities\Actions', 'admonition_activity_string'),
                'Admonition',
                ['group'],
                0
            );
            bp_activity_set_action(
                $bp->groups->id,
                $this->ban_type,
                'Ban / unban members of group.',
                array('\Pure\Components\BuddyPress\Activities\Actions', 'ban_activity_string'),
                'Ban / unban',
                ['group'],
                0
            );
            bp_activity_set_action(
                $bp->groups->id,
                $this->role_type,
                'Role information',
                array('\Pure\Components\BuddyPress\Activities\Actions', 'role_activity_string'),
                'Roles',
                ['group'],
                0
            );
            bp_activity_set_action(
                $bp->groups->id,
                $this->remove_type,
                'Remove information',
                array('\Pure\Components\BuddyPress\Activities\Actions', 'remove_activity_string'),
                'Remove',
                ['group'],
                0
            );
        }
        public function add_admonition($group_id, $author_id, $member_id, $comment){
            groups_record_activity(
                array(
                    'user_id'           => $author_id,
                    'type'              => $this->admonition_type,
                    'action'            => $comment,
                    'item_id'           => $group_id,
                    'secondary_item_id' => $member_id
                )
            );
        }
        public function add_ban($group_id, $author_id, $member_id, $action){
            groups_record_activity(
                array(
                    'user_id'           => $author_id,
                    'type'              => $this->ban_type,
                    'action'            => $action,
                    'item_id'           => $group_id,
                    'secondary_item_id' => $member_id
                )
            );
        }
        public function add_role($group_id, $author_id, $member_id, $role){
            groups_record_activity(
                array(
                    'user_id'           => $author_id,
                    'type'              => $this->role_type,
                    'action'            => $role,
                    'item_id'           => $group_id,
                    'secondary_item_id' => $member_id
                )
            );
        }
        public function add_remove($group_id, $author_id, $member_id){
            groups_record_activity(
                array(
                    'user_id'           => $author_id,
                    'type'              => $this->remove_type,
                    'action'            => 'remove',
                    'item_id'           => $group_id,
                    'secondary_item_id' => $member_id
                )
            );
        }
        static function admonition_activity_string($action, $activity){
            $Provider       = \Pure\Providers\Members\Initialization::instance()->getCommon();
            $member         = $Provider->get($activity->secondary_item_id, 'name_avatar_id');
            $Provider       = NULL;
            if ($member !== false){
                \Pure\Components\BuddyPress\Admonitions\Initialization::instance()->attach();
                $Admonitions    = new \Pure\Components\BuddyPress\Admonitions\Core();
                $admonitions    = $Admonitions->count((object)array(
                    'group'=>(int)$activity->item_id,
                    'user' =>(int)$activity->secondary_item_id
                ));
                $Admonitions    = NULL;
                if ($admonitions !== false){
                    return  __('Member', 'pure').
                    ' <a href="'.$member->profile.'">'.$member->name.'</a> '.
                    __('get admonition. The reason is:', 'pure').' <strong>'.$action.'</strong>. '.
                    __('Now', 'pure').' '.$member->name.' '.__('has', 'pure').
                    ' '.$admonitions.' '.__('admonition(s).', 'pure');
                }else{
                    return $action;
                }
            }
        }
        static function ban_activity_string($action, $activity){
            $Provider       = \Pure\Providers\Members\Initialization::instance()->getCommon();
            $member         = $Provider->get($activity->secondary_item_id, 'name_avatar_id');
            $Provider       = NULL;
            if ($member !== false){
                \Pure\Components\BuddyPress\Admonitions\Initialization::instance()->attach();
                $Admonitions    = new \Pure\Components\BuddyPress\Admonitions\Core();
                $admonitions    = $Admonitions->count((object)array(
                    'group'=>(int)$activity->item_id,
                    'user' =>(int)$activity->secondary_item_id
                ));
                $Admonitions    = NULL;
                if ($admonitions !== false){
                    $_ban = ($action === 'ban' ? __('banned', 'pure') : __('unbanned', 'pure'));
                    return  __('Member', 'pure').
                            ' <a href="'.$member->profile.'">'.$member->name.'</a> '.
                            __('was', 'pure').' <strong>'.$_ban.'</strong>. '.
                            __('Before', 'pure').' '.$member->name.' '.__('had', 'pure').
                            ' '.$admonitions.' '.__('admonition(s).', 'pure');
                }else{
                    return $action;
                }
            }
        }
        static function role_activity_string($action, $activity){
            $Provider       = \Pure\Providers\Members\Initialization::instance()->getCommon();
            $member         = $Provider->get($activity->secondary_item_id, 'name_avatar_id');
            $Provider       = NULL;
            if ($member !== false){
                switch($action){
                    case 'is admin':
                        $role = __('is administrator', 'pure');
                        break;
                    case 'is mod':
                        $role = __('is moderator', 'pure');
                        break;
                    case 'is not admin':
                        $role = __('is not administrator', 'pure');
                        break;
                    case 'is not mod':
                        $role = __('is not moderator', 'pure');
                        break;
                }
                return  __('Member', 'pure').
                        ' <a href="'.$member->profile.'">'.$member->name.'</a> '.
                        __('from now', 'pure').' <strong>'.$role.'</strong>';
            }
        }
        static function remove_activity_string($action, $activity){
            $Provider       = \Pure\Providers\Members\Initialization::instance()->getCommon();
            $member         = $Provider->get($activity->secondary_item_id, 'name_avatar_id');
            $Provider       = NULL;
            if ($member !== false){
                \Pure\Components\BuddyPress\Admonitions\Initialization::instance()->attach();
                $Admonitions    = new \Pure\Components\BuddyPress\Admonitions\Core();
                $admonitions    = $Admonitions->count((object)array(
                    'group'=>(int)$activity->item_id,
                    'user' =>(int)$activity->secondary_item_id
                ));
                $Admonitions    = NULL;
                if ($admonitions !== false){
                    $_action = ($action === 'remove' ? __('removed', 'pure') : '');
                    return  __('Member', 'pure').
                            ' <a href="'.$member->profile.'">'.$member->name.'</a> '.
                            __('was', 'pure').' <strong>'.$_action.'</strong>. '.
                            __('Before', 'pure').' '.$member->name.' '.__('had', 'pure').
                            ' '.$admonitions.' '.__('admonition(s).', 'pure');
                }else{
                    return $action;
                }
            }
        }
    }
}
?>