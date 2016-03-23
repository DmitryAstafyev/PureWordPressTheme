<?php
    namespace Pure\Components\WordPress\Menus\Basic\Registration{
        class Standard{
            static $items = false;
            static function generate(){
                if (Standard::$items === false){
                    \Pure\Components\WordPress\Location\Special\Initialization::instance()->attach(true);
                    $SpecialURLs        = new \Pure\Components\WordPress\Location\Special\Register();
                    $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                    $current            = $WordPress->get_current_user();
                    $WordPress          = NULL;
                    Standard::$items    = array(
                        0=>(object)array(
                            'id'    =>'create',
                            'title' =>__('Create', 'pure'),
                            'href'  =>'#',
                            'attr'  =>'',
                            'items' =>array(
                                0=>(object)array(
                                    'id'    =>'create_post',
                                    'title' =>__('Create post', 'pure'),
                                    'href'  =>$SpecialURLs->getURL('CREATEPOST', array()),
                                    'attr'  =>'',
                                    'items' =>false
                                ),
                                1=>(object)array(
                                    'id'    =>'create_event',
                                    'title' =>__('Create event', 'pure'),
                                    'href'  =>$SpecialURLs->getURL('CREATEEVENT', array()),
                                    'attr'  =>'',
                                    'items' =>false
                                ),
                                2=>(object)array(
                                    'id'    =>'create_report',
                                    'title' =>__('Create report', 'pure'),
                                    'href'  =>$SpecialURLs->getURL('CREATEREPORT', array()),
                                    'attr'  =>'',
                                    'items' =>false
                                ),
                                3=>(object)array(
                                    'id'    =>'create_question',
                                    'title' =>__('Create question', 'pure'),
                                    'href'  =>$SpecialURLs->getURL('CREATEQUESTION', array()),
                                    'attr'  =>'',
                                    'items' =>false
                                ),
                                4=>(object)array(
                                    'id'    =>'my_drafts',
                                    'title' =>__('My drafts', 'pure'),
                                    'href'  =>$SpecialURLs->getURL('DRAFTS', array('user_id'=>$current->ID)),
                                    'attr'  =>'',
                                    'items' =>false
                                ),
                            )
                        ),
                        1=>(object)array(
                            'id'    =>'my_content',
                            'title' =>__('My posts', 'pure'),
                            'href'  =>get_author_posts_url($current->ID),
                            'attr'  =>'',
                            'items' =>false
                        ),
                        2=>(object)array(
                            'id'    =>'logoff',
                            'title' =>__('Logoff', 'pure'),
                            'href'  =>wp_logout_url( home_url() ),
                            'attr'  =>'',
                            'items' =>false
                        ),
                    );
                    $SpecialURLs = NULL;
                }
            }
        }
        class HotLinks{
            static $items = false;
            static function generate(){
                if (HotLinks::$items === false){
                    \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach(true);
                    \Pure\Components\WordPress\Location\Special\Initialization::instance()->attach(true);
                    $BuddyPressURLs = new \Pure\Components\BuddyPress\URLs\Core();
                    $SpecialURLs    = new \Pure\Components\WordPress\Location\Special\Register();
                    HotLinks::$items = array(
                        0=>array(
                            'id'    =>'members',
                            'title' =>__('Top of posts', 'pure'),
                            'href'  =>$SpecialURLs->getURL('TOP',array('type'=>'post')),
                            'attr'  =>'',
                            'items' =>false
                        ),
                        1=>array(
                            'id'    =>'members',
                            'title' =>__('All members', 'pure'),
                            'href'  =>$BuddyPressURLs->members(),
                            'attr'  =>'',
                            'items' =>false
                        ),
                        2=>array(
                            'id'    =>'groups',
                            'title' =>__('All groups', 'pure'),
                            'href'  =>$BuddyPressURLs->groups(),
                            'attr'  =>'',
                            'items' =>false
                        ),
                    );
                    $BuddyPressURLs = NULL;
                    $SpecialURLs    = NULL;
                }
            }
        }
        class Admin{
            static $items = false;
            static function generate(){
                if (Admin::$items === false) {
                    \Pure\Components\WordPress\Location\Special\Initialization::instance()->attach(true);
                    $SpecialURLs    = new \Pure\Components\WordPress\Location\Special\Register();
                    $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                    $current        = $WordPress->get_current_user();
                    $WordPress      = NULL;
                    Admin::$items = array(
                        0=>(object)array(
                            'id'    =>'create',
                            'title' =>__('Create', 'pure'),
                            'href'  =>'#',
                            'attr'  =>'',
                            'items' =>array(
                                0=>(object)array(
                                    'id'    =>'create_post',
                                    'title' =>__('Create post', 'pure'),
                                    'href'  =>$SpecialURLs->getURL('CREATEPOST', array()),
                                    'attr'  =>'',
                                    'items' =>false
                                ),
                                1=>(object)array(
                                    'id'    =>'create_event',
                                    'title' =>__('Create event', 'pure'),
                                    'href'  =>$SpecialURLs->getURL('CREATEEVENT', array()),
                                    'attr'  =>'',
                                    'items' =>false
                                ),
                                2=>(object)array(
                                    'id'    =>'create_report',
                                    'title' =>__('Create report', 'pure'),
                                    'href'  =>$SpecialURLs->getURL('CREATEREPORT', array()),
                                    'attr'  =>'',
                                    'items' =>false
                                ),
                                3=>(object)array(
                                    'id'    =>'create_question',
                                    'title' =>__('Create question', 'pure'),
                                    'href'  =>$SpecialURLs->getURL('CREATEQUESTION', array()),
                                    'attr'  =>'',
                                    'items' =>false
                                ),
                                4=>(object)array(
                                    'id'    =>'my_drafts',
                                    'title' =>__('My drafts', 'pure'),
                                    'href'  =>$SpecialURLs->getURL('DRAFTS', array('user_id'=>$current->ID)),
                                    'attr'  =>'',
                                    'items' =>false
                                ),
                            )
                        ),
                        1=>(object)array(
                            'id'    =>'my_content',
                            'title' =>__('My posts', 'pure'),
                            'href'  =>get_author_posts_url($current->ID),
                            'attr'  =>'',
                            'items' =>false
                        ),
                        2=>(object)array(
                            'id'    =>'admin_console',
                            'title' =>__('Console', 'pure'),
                            'href'  =>admin_url(),
                            'attr'  =>'',
                            'items' =>false
                        ),
                        3=>(object)array(
                            'id'    =>'logoff',
                            'title' =>__('Logoff', 'pure'),
                            'href'  =>wp_logout_url( home_url() ),
                            'attr'  =>'',
                            'items' =>false
                        ),
                    );
                    $SpecialURLs = NULL;
                }
            }
        }
    }
?>