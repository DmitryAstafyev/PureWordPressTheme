<?php
namespace Pure\Requests\Plugins\Thumbnails\Authors{
    class More{
        private function validate(&$parameters, $method){
            switch($method){
                case 'get':
                    $parameters->count      = (integer  )($parameters->count        );
                    $parameters->maximum    = (integer  )($parameters->maximum      );
                    $parameters->template   = (string   )($parameters->template     );
                    $parameters->content    = (string   )($parameters->content      );
                    $parameters->targets    = (string   )($parameters->targets      );
                    $parameters->profile    = (string   )($parameters->profile      );
                    $parameters->days       = (integer  )($parameters->days         );
                    $parameters->from_date  = (string   )($parameters->from_date    );
                    break;
            }
        }
        public function get($parameters){
            $this->validate($parameters, 'get');
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->plugins.'/Thumbnails/Authors/inc/kernel.php'));
            $_parameters    = array(	'content'	        => $parameters->content,
                                        'targets'	        => $parameters->targets,
                                        'template'		    => $parameters->template,
                                        'title'		        => '',
                                        'title_type'        => '',
                                        'maxcount'	        => $parameters->maximum,
                                        'only_with_avatar'	=> false,
                                        'top'	            => false,
                                        'profile'	        => $parameters->profile,
                                        'days'	            => $parameters->days,
                                        'from_date'         => $parameters->from_date,
                                        'more'              => false,
                                        'group'             => $parameters->group,
                                        'templates_settings'=> array(   'H'=>array('addition_information'=>'all'),
                                                                        'I'=>array('addition_information'=>'all')),
                                        'shown'             => $parameters->count,
            );
            try{
                $widget     = new \Pure\Plugins\Thumbnails\Authors\Builder($_parameters);
                $innerHTML  = $widget->render();
                echo $innerHTML;
            }catch (\Exception $e){
                return 'error';
            }
        }
    }
    class Friendship{
        private function validate(&$parameters, $method){
            switch($method){
                case 'set':
                    $parameters->initiator  = (integer  )($parameters->initiator    );
                    $parameters->friend     = (integer  )($parameters->friend       );
                    $parameters->action     = (string   )($parameters->action       );
                    break;
            }
        }
        public function set($parameters){
            $this->validate($parameters, 'set');
            $WordPress      = new \Pure\Components\WordPress\UserData\Data();
            $current_user   = $WordPress->get_current_user();
            if ($current_user->ID === $parameters->initiator){
                \Pure\Components\BuddyPress\Friendship\Initialization::instance()->attach();
                $Friendship = new \Pure\Components\BuddyPress\Friendship\Core();
                $friendship = $Friendship->isFriends((object)array(
                    'memberIDA'=>(int)$parameters->initiator,
                    'memberIDB'=>(int)$parameters->friend
                ));
                $Friendship = NULL;
                if ($friendship === false){
                    //No friendship
                    if (function_exists('friends_add_friend') === true){
                        if (friends_add_friend($parameters->initiator, $parameters->friend, false) === true){
                            echo 'request_for_friendship_is_sent';
                        }else{
                            echo 'BuddyPress_error';
                        }
                    }else{
                        echo 'BuddyPress_error';
                    }
                }elseif ($friendship->accepted === true){
                    //Friendship exists and accepted
                    if (function_exists('friends_remove_friend') === true){
                        if (friends_remove_friend($friendship->initiator, $friendship->friend) === true){
                            echo 'request_for_cancel_friendship_is_sent';
                        }else{
                            echo 'BuddyPress_error';
                        }
                    }else{
                        echo 'BuddyPress_error';
                    }
                }elseif ($friendship->accepted === false){
                    //Friendship isn't accepted
                    if ($friendship->initiator === $parameters->initiator){
                        //Request was sent by user => cancel request
                        if (function_exists('friends_withdraw_friendship') === true){
                            if (friends_withdraw_friendship($friendship->initiator, $friendship->friend) === true){
                                echo 'cancel_request_for_friendship';
                            }else{
                                echo 'BuddyPress_error';
                            }
                        }else{
                            echo 'BuddyPress_error';
                        }
                    }else{
                        switch($parameters->action){
                            case 'accept':
                                if (function_exists('friends_accept_friendship') === true){
                                    if (friends_accept_friendship($friendship->id) === true){
                                        echo 'friendship_accepted';
                                    }else{
                                        echo 'BuddyPress_error';
                                    }
                                }else{
                                    echo 'BuddyPress_error';
                                }
                                break;
                            case 'deny':
                                if (function_exists('friends_reject_friendship') === true){
                                    if (friends_reject_friendship($friendship->id) === true){
                                        echo 'friendship_denied';
                                    }else{
                                        echo 'BuddyPress_error';
                                    }
                                }else{
                                    echo 'BuddyPress_error';
                                }
                                break;
                        }
                    }
                }
            }else{
                echo 'wrong_user';
            }
            $WordPress      = NULL;
        }
    }
}
?>