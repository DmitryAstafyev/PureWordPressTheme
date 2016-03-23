<?php
namespace Pure\Components\BuddyPress\Location{
    class Core{
        private function redirect($component){
            global $bp;
            switch($component){
                case 'member':
                    if (isset($bp->displayed_user) !== false){
                        if (isset($bp->displayed_user->userdata) !== false){
                            \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach(true);
                            $BuddyPressURLs = new \Pure\Components\BuddyPress\URLs\Core();
                            $url            = $BuddyPressURLs->member($bp->displayed_user->userdata->user_login);
                            $BuddyPressURLs = NULL;
                            header('location:'.$url);
                            exit;
                        }
                    }
                    break;
            }
            return false;
        }
        public function getID(){
            global $bp;
            if (is_object($bp) === true){
                if (isset($bp->current_component) !== false && isset($bp->current_item) !== false && isset($bp->current_action) !== false){
                    switch($bp->current_component){
                        case 'activity':
                            switch($bp->current_action){
                                case 'just-me':
                                    if (isset($bp->displayed_user) !== false){
                                        return (isset($bp->displayed_user->id) !== false ? ((int)$bp->displayed_user->id > 0 ? (int)$bp->displayed_user->id : false) : false);
                                    }
                                    break;
                            }
                            break;
                        case 'profile':
                            if (isset($bp->displayed_user) !== false){
                                return (isset($bp->displayed_user->id) !== false ? ((int)$bp->displayed_user->id > 0 ? (int)$bp->displayed_user->id : false) : false);
                            }
                            break;
                        case 'friends':
                            switch($bp->current_action){
                                case 'my-friends':
                                    if (isset($bp->displayed_user) !== false){
                                        return (isset($bp->displayed_user->id) !== false ? ((int)$bp->displayed_user->id > 0 ? (int)$bp->displayed_user->id : false) : false);
                                    }
                                    break;
                            }
                            break;
                        case 'groups':
                            switch($bp->current_action){
                                case 'my-groups':
                                    if (isset($bp->displayed_user) !== false){
                                        return (isset($bp->displayed_user->id) !== false ? ((int)$bp->displayed_user->id > 0 ? (int)$bp->displayed_user->id : false) : false);
                                    }
                                    break;
                                case 'home':
                                    $group_id = groups_get_id($bp->current_item);
                                    if ((int)$group_id > 0){
                                        return (int)$group_id;
                                    }
                                    break;
                            }
                            break;
                    }
                }
            }
            return false;

        }
        public function getTypePage(){
            global $bp;
            if (is_object($bp) === true){
                if (isset($bp->current_component) !== false && isset($bp->current_item) !== false && isset($bp->current_action) !== false){
                    switch($bp->current_component){
                        case 'activity':
                            switch($bp->current_action){
                                case 'just-me':
                                    if (isset($bp->displayed_user) !== false){
                                        return 'member::activities';
                                    }
                                    break;
                                default:
                                    //REDIRECT TO: Member page
                                    $this->redirect('member');
                                    //404
                                    break;
                            }
                            break;
                        case 'profile':
                            if (isset($bp->displayed_user) !== false){
                                return 'member::profile';
                            }
                            break;
                        case 'notifications':
                            //REDIRECT TO: Member page
                            $this->redirect('member');
                            //404
                            break;
                        case 'messages':
                            //REDIRECT TO: Member page
                            $this->redirect('member');
                            //404
                            break;
                        case 'settings':
                            //REDIRECT TO: Member page
                            $this->redirect('member');
                            //404
                            break;
                        case 'friends':
                            switch($bp->current_action){
                                case 'my-friends':
                                    if (isset($bp->displayed_user) !== false){
                                        return 'member::friends';
                                    }
                                    break;
                            }
                            break;
                        case 'groups':
                            switch($bp->current_action){
                                case 'my-groups':
                                    if (isset($bp->displayed_user) !== false){
                                        return 'member::groups';
                                    }
                                    break;
                                case 'home':
                                    $group_id = groups_get_id($bp->current_item);
                                    if ((int)$group_id > 0){
                                        return "groups::group";
                                    }
                                    break;
                                default:
                                    return 'groups';
                                    break;
                            }
                            break;
                        case 'members':
                            return 'members';

                    }
                }
            }
            return false;
        }
        public function is(){
            return ($this->getTypePage() !== false ? true : false);
        }
    }
}
?>