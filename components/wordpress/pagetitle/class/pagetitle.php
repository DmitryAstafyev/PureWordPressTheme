<?php
namespace Pure\Components\WordPress\PageTitle{
    class Core{
        public function get(){
            $title = '';
            if (is_front_page() !== false){
                $title = get_bloginfo('name');
            }else{
                switch (\Pure\Configuration::instance()->globals->requests->type) {
                    case 'BUDDY':
                        \Pure\Components\BuddyPress\Location\Initialization ::instance()->attach();
                        $BuddyPress     = new \Pure\Components\BuddyPress\Location\Core();
                        $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                        $object_id      = $BuddyPress->getID();
                        switch (\Pure\Configuration::instance()->globals->requests->BUDDY) {
                            case 'member::activities':
                                $user       = get_userdata($object_id);
                                $user_name  = $WordPress->get_name($user);
                                $title      = $user_name.' '.__('(home page)', 'pure');
                                break;
                            case 'member::groups':
                                $user       = get_userdata($object_id);
                                $user_name  = $WordPress->get_name($user);
                                $title      = __('Groups of', 'pure').' '.$user_name;
                                break;
                            case 'member::friends':
                                $user       = get_userdata($object_id);
                                $user_name  = $WordPress->get_name($user);
                                $title      = __('Friends of', 'pure').' '.$user_name;
                                break;
                            case 'groups::group':
                                \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                                $BuddyPressGroups   = new \Pure\Components\BuddyPress\Groups\Core();
                                $group              = $BuddyPressGroups->get((object)array('id'=>(int)$object_id));
                                $title              = __('Group:', 'pure').' '.$group->name;
                                break;
                            case 'groups':
                                $title              = __('List of groups', 'pure');
                                break;
                            case 'members':
                                $title              = __('List of members', 'pure');
                                break;
                        }
                        $BuddyPress     = NULL;
                        $WordPress      = NULL;
                        break;
                    case 'SPECIAL':
                        switch (\Pure\Configuration::instance()->globals->requests->SPECIAL->request) {
                            case 'CREATEPOST':
                                $title              = __('Create new post', 'pure');
                                break;
                            case 'CREATEEVENT':
                                $title              = __('Create new event', 'pure');
                                break;
                            case 'EDITPOST':
                                $title              = __('Editing the post', 'pure');
                                break;
                            case 'EDITEVENT':
                                $title              = __('Editing the event', 'pure');
                                break;
                            case 'TOP':
                                $title              = __('Top of posts', 'pure');
                                break;
                        }
                        break;
                    case 'POST':
                        $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                        $user           = get_userdata(\Pure\Configuration::instance()->globals->requests->POST->post_author);
                        $user_name      = $WordPress->get_name($user);
                        $title          = $user_name.': '.\Pure\Configuration::instance()->globals->requests->POST->post_title;
                        $WordPress      = NULL;
                        break;
                    case 'PAGE':
                        $title          = \Pure\Configuration::instance()->globals->requests->PAGE->post_title;
                        break;
                    case 'AUTHOR':
                        $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                        $user           = \Pure\Configuration::instance()->globals->requests->AUTHOR;
                        $user_name      = $WordPress->get_name($user);
                        $title          = __('Posts of', 'pure').' '.$user_name;
                        $WordPress      = NULL;
                        break;
                    case 'CATEGORY':
                        $title          = __('Category: ', 'pure').' '.\Pure\Configuration::instance()->globals->requests->CATEGORY->name;
                        break;
                    case 'TAG':
                        $title          = __('All about: ', 'pure').' '.\Pure\Configuration::instance()->globals->requests->TAG->name;
                        break;
                }
            }
            return ($title === '' ? wp_title('|', false) : $title);
        }
    }
}
?>