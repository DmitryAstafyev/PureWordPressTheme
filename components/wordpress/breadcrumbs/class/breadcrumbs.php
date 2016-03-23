<?php
namespace Pure\Components\WordPress\Breadcrumbs{
    class Provider{
        public function get(){
            $parts = array(
                (object)array(
                    'title' =>__('Home', 'pure'),
                    'url'   =>get_site_url()
                )
            );
            if (is_front_page() === false) {
                \Pure\Components\BuddyPress\Location\Initialization::instance()->attach();
                switch (\Pure\Configuration::instance()->globals->requests->type) {
                    case 'BUDDY':
                        \Pure\Components\BuddyPress\Location\Initialization ::instance()->attach();
                        \Pure\Components\BuddyPress\URLs\Initialization     ::instance()->attach();
                        $BuddyPress     = new \Pure\Components\BuddyPress\Location\Core();
                        $BuddyPressURL  = new \Pure\Components\BuddyPress\URLs\Core();
                        $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                        $object_id      = $BuddyPress->getID();
                        switch (\Pure\Configuration::instance()->globals->requests->BUDDY) {
                            case 'member::activities':
                                $user       = get_userdata($object_id);
                                $user_name  = $WordPress->get_name($user);
                                $parts[]    = (object)array(
                                    'title' =>__('Members', 'pure'),
                                    'url'   =>$BuddyPressURL->members()
                                );
                                $parts[]    = (object)array(
                                    'title' =>$user_name,
                                    'url'   =>$BuddyPressURL->member($user->user_login)
                                );
                                break;
                            case 'member::profile':
                                $user       = get_userdata($object_id);
                                $user_name  = $WordPress->get_name($user);
                                $parts[]    = (object)array(
                                    'title' =>__('Members', 'pure'),
                                    'url'   =>$BuddyPressURL->members()
                                );
                                $parts[]    = (object)array(
                                    'title' =>$user_name,
                                    'url'   =>$BuddyPressURL->member($user->user_login)
                                );
                                $parts[]    = (object)array(
                                    'title' =>__('Profile', 'pure'),
                                    'url'   =>'#'
                                );
                                break;
                            case 'member::groups':
                                $user       = get_userdata($object_id);
                                $user_name  = $WordPress->get_name($user);
                                $parts[]    = (object)array(
                                    'title' =>__('Members', 'pure'),
                                    'url'   =>$BuddyPressURL->members()
                                );
                                $parts[]    = (object)array(
                                    'title' =>$user_name,
                                    'url'   =>$BuddyPressURL->member($user->user_login)
                                );
                                $parts[]    = (object)array(
                                    'title' =>__('Member groups', 'pure'),
                                    'url'   =>$BuddyPressURL->userGroups($user->user_login)
                                );
                                break;
                            case 'member::friends':
                                $user       = get_userdata($object_id);
                                $user_name  = $WordPress->get_name($user);
                                $parts[]    = (object)array(
                                    'title' =>__('Members', 'pure'),
                                    'url'   =>$BuddyPressURL->members()
                                );
                                $parts[]    = (object)array(
                                    'title' =>$user_name,
                                    'url'   =>$BuddyPressURL->member($user->user_login)
                                );
                                $parts[]    = (object)array(
                                    'title' =>__('Member groups', 'pure'),
                                    'url'   =>$BuddyPressURL->userFriends($user->user_login)
                                );
                                break;
                            case 'groups::group':
                                \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                                $BuddyPressGroups   = new \Pure\Components\BuddyPress\Groups\Core();
                                $group              = $BuddyPressGroups->get((object)array('id'=>(int)$object_id));
                                $BuddyPressGroups   = NULL;
                                $parts[]    = (object)array(
                                    'title' =>__('Groups', 'pure'),
                                    'url'   =>$BuddyPressURL->groups()
                                );
                                $parts[]    = (object)array(
                                    'title' =>$group->name,
                                    'url'   =>$BuddyPressURL->group($object_id)
                                );
                                break;
                            case 'groups':
                                $parts[]    = (object)array(
                                    'title' =>__('Groups', 'pure'),
                                    'url'   =>$BuddyPressURL->groups()
                                );
                                break;
                            case 'members':
                                $parts[]    = (object)array(
                                    'title' =>__('Members', 'pure'),
                                    'url'   =>$BuddyPressURL->members()
                                );
                                break;
                        }
                        $BuddyPress     = NULL;
                        $BuddyPressURL  = NULL;
                        $WordPress      = NULL;
                        break;
                    case 'SPECIAL':
                        switch (\Pure\Configuration::instance()->globals->requests->SPECIAL->request) {
                            case 'CREATEPOST':
                                $parts[]    = (object)array(
                                    'title' =>__('Creation new post', 'pure'),
                                    'url'   =>'#'
                                );
                                break;
                            case 'CREATEEVENT':
                                $parts[]    = (object)array(
                                    'title' =>__('Creation new event', 'pure'),
                                    'url'   =>'#'
                                );
                                break;
                            case 'CREATEREPORT':
                                $parts[]    = (object)array(
                                    'title' =>__('Creation new report', 'pure'),
                                    'url'   =>'#'
                                );
                                break;
                            case 'EDITPOST':
                                $parts[]    = (object)array(
                                    'title' =>__('Editing the post', 'pure'),
                                    'url'   =>'#'
                                );
                                break;
                            case 'EDITEVENT':
                                $parts[]    = (object)array(
                                    'title' =>__('Editing the event', 'pure'),
                                    'url'   =>'#'
                                );
                                break;
                            case 'EDITREPORT':
                                $parts[]    = (object)array(
                                    'title' =>__('Editing the report', 'pure'),
                                    'url'   =>'#'
                                );
                                break;
                            case 'TOP':
                                $parts[]    = (object)array(
                                    'title' =>__('Top of posts', 'pure'),
                                    'url'   =>'#'
                                );
                                break;
                        }
                        break;
                    case 'POST':
                        \Pure\Components\BuddyPress\URLs\Initialization     ::instance()->attach();
                        $BuddyPressURL  = new \Pure\Components\BuddyPress\URLs\Core();
                        $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                        $user       = get_userdata(\Pure\Configuration::instance()->globals->requests->POST->post_author);
                        $user_name  = $WordPress->get_name($user);
                        $parts[]    = (object)array(
                            'title' =>__('Members', 'pure'),
                            'url'   =>$BuddyPressURL->members()
                        );
                        $parts[]    = (object)array(
                            'title' =>$user_name,
                            'url'   =>$BuddyPressURL->member($user->user_login)
                        );
                        $parts[]    = (object)array(
                            'title' =>__('Content', 'pure'),
                            'url'   =>get_author_posts_url(\Pure\Configuration::instance()->globals->requests->POST->post_author)
                        );
                        $parts[]    = (object)array(
                            'title' =>\Pure\Configuration::instance()->globals->requests->POST->post_title,
                            'url'   =>get_permalink(\Pure\Configuration::instance()->globals->requests->POST->ID)
                        );
                        $BuddyPressURL  = NULL;
                        $WordPress      = NULL;
                        break;
                    case 'PAGE':
                        $path       = array();
                        $Parents    = function($post) use(&$path, &$Parents){
                            $path[] = (object)array(
                                'title' =>$post->post_title,
                                'url'   =>get_permalink($post->ID)
                            );
                            if ((int)$post->post_parent > 0){
                                $Parents(get_post((int)$post->post_parent));
                            }
                        };
                        $Parents(\Pure\Configuration::instance()->globals->requests->PAGE);
                        $path   = array_reverse($path);
                        $parts  = array_merge($parts, $path);
                        break;
                    case 'AUTHOR':
                        \Pure\Components\BuddyPress\URLs\Initialization     ::instance()->attach();
                        $BuddyPressURL  = new \Pure\Components\BuddyPress\URLs\Core();
                        $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                        //echo var_dump(\Pure\Configuration::instance()->globals->requests->AUTHOR);
                        $user       = \Pure\Configuration::instance()->globals->requests->AUTHOR;
                        $user_name  = $WordPress->get_name($user);
                        $parts[]    = (object)array(
                            'title' =>__('Members', 'pure'),
                            'url'   =>$BuddyPressURL->members()
                        );
                        $parts[]    = (object)array(
                            'title' =>$user_name,
                            'url'   =>$BuddyPressURL->member($user->user_login)
                        );
                        $parts[]    = (object)array(
                            'title' =>__('Content', 'pure'),
                            'url'   =>get_author_posts_url($user->ID)
                        );
                        break;
                    case 'CATEGORY':
                        $parts[]    = (object)array(
                            'title' =>__('Categories', 'pure'),
                            'url'   =>'#'
                        );
                        $parts[]    = (object)array(
                            'title' =>\Pure\Configuration::instance()->globals->requests->CATEGORY->name,
                            'url'   =>get_category_link(\Pure\Configuration::instance()->globals->requests->CATEGORY->cat_ID)
                        );
                        break;
                    case 'TAG':
                        $parts[]    = (object)array(
                            'title' =>__('Tags', 'pure'),
                            'url'   =>'#'
                        );
                        $parts[]    = (object)array(
                            'title' =>\Pure\Configuration::instance()->globals->requests->TAG->name,
                            'url'   =>get_tag_link(\Pure\Configuration::instance()->globals->requests->TAG->term_id)
                        );
                        break;
                }
            }
            return $parts;
        }
    }
}
?>