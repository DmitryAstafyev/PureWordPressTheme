<?php
namespace Pure\Components\BuddyPress\URLs{
    class Core{
        public function member($login_name = false, $template = ''){
            global $bp;
            if ($template !== ''){
                $url = str_replace("[login]", $login_name, $template);
            }else{
                if ($login_name !== false) {
                    $url = $bp->root_domain . '/'.$bp->members->root_slug.'/' . $login_name;
                } else {
                    $url = $bp->loggedin_user->domain;
                }
            }
            return $url;
        }
        public function profile($login_name = false){
            global $bp;
            if ($login_name !== false) {
                $url = $bp->root_domain . '/'.$bp->members->root_slug.'/'.$login_name.'/'.$bp->profile->slug;
            } else {
                $url = $bp->loggedin_user->domain;
            }
            return $url;
        }
        public function members(){
            global $bp;
            $url = $bp->root_domain.'/'.$bp->members->root_slug;
            return $url;
        }
        public function userFriends($login_name = false){
            global $bp;
            if ($login_name !== false) {
                $url = $bp->root_domain . '/'.$bp->members->root_slug.'/' . $login_name;
            } else {
                $url = $bp->loggedin_user->domain;
            }
            return $url.'/friends';
        }
        public function userGroups($login_name = false){
            global $bp;
            if ($login_name !== false) {
                $url = $bp->root_domain . '/'.$bp->members->root_slug.'/' . $login_name;
            } else {
                $url = $bp->loggedin_user->domain;
            }
            return $url.'/groups';
        }
        //$group - or group ID || or group slug
        public function group($group){
            global $bp;
            $url = '';
            if (is_string($group) === true){
                $url = $bp->root_domain . '/'.$bp->groups->root_slug.'/' . $group;
            }elseif(is_int($group) === true){
                \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                $BuddyPressGroups   = new \Pure\Components\BuddyPress\Groups\Core();
                $group              = $BuddyPressGroups->get((object)array('id'=>(int)$group));
                $BuddyPressGroups   = NULL;
                if (isset($group->slug) !== false){
                    $url = $bp->root_domain.'/'.$bp->groups->root_slug.'/'. $group->slug;
                }
            }
            return $url;
        }
        public function groups(){
            global $bp;
            $url = $bp->root_domain.'/'.$bp->groups->root_slug;
            return $url;
        }
    }
}
?>