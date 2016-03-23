<?php
    namespace Pure\Components\WordPress\Menus\Social\Registration{
        class Standard{
            static $items = false;
            static function generate(){
                if (Standard::$items === false){
                    \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach(true);
                    $BuddyPressURLs = new \Pure\Components\BuddyPress\URLs\Core();
                    $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                    $current        = $WordPress->get_current_user();
                    $WordPress      = NULL;

                    Standard::$items = array(
                        0=>(object)array(
                            'id'    =>'my_',
                            'title' =>__('My', 'pure'),
                            'href'  =>'#',
                            'attr'  =>'',
                            'items' =>array(
                                0=>(object)array(
                                    'id'    =>'my_page',
                                    'title' =>__('My page', 'pure'),
                                    'href'  =>$BuddyPressURLs->member($current->user_login),
                                    'attr'  =>'',
                                    'items' =>false
                                ),
                                1=>(object)array(
                                    'id'    =>'my_friends',
                                    'title' =>__('My friends', 'pure'),
                                    'href'  =>$BuddyPressURLs->userFriends($current->user_login),
                                    'attr'  =>'',
                                    'items' =>false
                                ),
                                2=>(object)array(
                                    'id'    =>'my_groups',
                                    'title' =>__('My groups', 'pure'),
                                    'href'  =>$BuddyPressURLs->userGroups($current->user_login),
                                    'attr'  =>'',
                                    'items' =>false
                                ),
                            )
                        ),
                        1=>(object)array(
                            'id'    =>'messenger',
                            'title' =>__('Messenger', 'pure'),
                            'href'  =>'#',
                            'attr'  =>'',
                            'items' =>array(
                                0=>(object)array(
                                    'id'    =>'mails',
                                    'title' =>__('Mails', 'pure'),
                                    'href'  =>'#',
                                    'attr'  =>' data-messenger-engine-button="open" data-messenger-engine-switchTo="mails" ',
                                    'items' =>false
                                ),
                                1=>(object)array(
                                    'id'    =>'notifications',
                                    'title' =>__('Notifications', 'pure'),
                                    'href'  =>'#',
                                    'attr'  =>' data-messenger-engine-button="open" data-messenger-engine-switchTo="notifications" ',
                                    'items' =>false
                                ),
                                2=>(object)array(
                                    'id'    =>'chat',
                                    'title' =>__('Chat', 'pure'),
                                    'href'  =>'#',
                                    'attr'  =>' data-messenger-engine-button="open" data-messenger-engine-switchTo="chat" ',
                                    'items' =>false
                                ),
                            )
                        ),
                    );
                    $BuddyPressURLs = NULL;
                }
            }
        }
    }
?>