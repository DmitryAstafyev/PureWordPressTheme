<?php
namespace Pure\Components\Messenger\Notifications{
    class Provider{
        private function validate(&$parameters, $method){
            $result = false;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->user_id   ));
                    $result = ($result === false ? false : isset($parameters->shown     ));
                    $result = ($result === false ? false : isset($parameters->maxcount  ));
                    if ($result !== false){
                        $parameters->user_id    = filter_var($parameters->user_id,  FILTER_VALIDATE_INT     );
                        $parameters->shown      = filter_var($parameters->shown,    FILTER_VALIDATE_INT     );
                        $parameters->maxcount   = filter_var($parameters->maxcount, FILTER_VALIDATE_INT     );
                        if ($parameters->user_id    === false ||
                            $parameters->shown      === false ||
                            $parameters->maxcount   === false){
                            $result = false;
                        }
                    }
                    break;
                case 'setAsRead':
                    $result = true;
                    $result = ($result === false ? false : isset($parameters->user_id           ));
                    $result = ($result === false ? false : isset($parameters->notification_id   ));
                    if ($result !== false){
                        $parameters->user_id            = filter_var($parameters->user_id,          FILTER_VALIDATE_INT     );
                        $parameters->notification_id    = filter_var($parameters->notification_id,  FILTER_VALIDATE_INT     );
                        if ($parameters->user_id            === false ||
                            $parameters->notification_id    === false ){
                            $result = false;
                        }
                    }
                    break;
            }
            return $result;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        private function get_description($notification) {
            // Setup local variables
            $description  = '';
            $bp           = buddypress();
            // Callback function exists
            if ( isset( $bp->{ $notification->component_name }->notification_callback ) && is_callable( $bp->{ $notification->component_name }->notification_callback ) ) {
                $description = call_user_func( $bp->{ $notification->component_name }->notification_callback, $notification->component_action, $notification->item_id, $notification->secondary_item_id, 1 );
                // @deprecated format_notification_function - 1.5
            } elseif ( isset( $bp->{ $notification->component_name }->format_notification_function ) && function_exists( $bp->{ $notification->component_name }->format_notification_function ) ) {
                $description = call_user_func( $bp->{ $notification->component_name }->format_notification_function, $notification->component_action, $notification->item_id, $notification->secondary_item_id, 1 );
                // Allow non BuddyPress components to hook in
            } else {
                $description = apply_filters_ref_array( 'bp_notifications_get_notifications_for_user', array( $notification->component_action, $notification->item_id, $notification->secondary_item_id, 1 ) );
            }
            // Filter and return
            return apply_filters( 'bp_get_the_notification_description', $description );
        }
        private function filter($_notifications){
            $notifications = array();
            foreach($_notifications as $notification){
                if ($notification->component_name === 'friends' || $notification->component_name === 'groups'){
                    $notifications[] = $notification;
                }
            }
            return $notifications;
        }
        private function addActions(&$notification, $current_user_id){
            switch($notification->component_name){
                case 'friends':
                    switch($notification->component_action){
                        case 'friendship_request':
                            $notification->actions = array(
                                (object)array(
                                    'title'             =>  __( 'accept', 'pure' ),
                                    'request'           =>  'command=templates_of_authors_set_friendship'.  '&'.
                                                            'initiator='.   $notification->user_id.         '&'.
                                                            'friend='.      $notification->item_id.         '&'.
                                                            'action=accept',
                                    'expected_response' =>  'friendship_accepted'
                                ),
                                (object)array(
                                    'title'             =>  __( 'deny', 'pure' ),
                                    'request'           =>  'command=templates_of_authors_set_friendship'.  '&'.
                                                            'initiator='.   $notification->user_id.         '&'.
                                                            'friend='.      $notification->item_id.         '&'.
                                                            'action=deny',
                                    'expected_response' =>  'friendship_denied'
                                )
                            );
                            $Provider               = \Pure\Providers\Members\Initialization::instance()->getCommon();
                            $notification->target   = $Provider->get((int)$notification->item_id, 'name_avatar_id');
                            $Provider               = NULL;
                            break;
                        default:
                            $notification->actions = array(
                                (object)array(
                                    'title'             =>  __( 'remove', 'pure' ),
                                    'request'           =>  'command=templates_of_messenger_notifications_set_read'.  '&'.
                                        'user_id='.         $current_user_id. '&'.
                                        'notification_id='. $notification->id,
                                    'expected_response' =>  'success'
                                )
                            );
                            break;
                    }
                    break;
                case 'groups':
                    switch($notification->component_action){
                        case 'new_membership_request':
                            $ProviderGroups = \Pure\Providers\Groups\Initialization::instance()->getCommon();
                            $requestIDs     = $ProviderGroups->get_group_membership_requests_for_user($notification->item_id, $notification->secondary_item_id);
                            $ProviderGroups = NULL;
                            $requestIDs     = (is_array($requestIDs) !== false ? (count($requestIDs) === 1 ? $requestIDs[0] : false) : false);
                            if ($requestIDs !== false){
                                $notification->actions = array(
                                    (object)array(
                                        'title'             =>  __( 'accept', 'pure' ),
                                        'request'           =>  'command=templates_of_groups_request_action'.           '&'.
                                                                'user='.        $notification->user_id.                 '&'.
                                                                'group='.       $notification->item_id.                 '&'.
                                                                'waited_user='. $notification->secondary_item_id.       '&'.
                                                                'request_id='.  $requestIDs->request_id.                '&'.
                                                                'action=accept',
                                        'expected_response' =>  'accepted'
                                    ),
                                    (object)array(
                                        'title'             =>  __( 'deny', 'pure' ),
                                        'request'           =>  'command=templates_of_groups_request_action'.           '&'.
                                                                'user='.        $notification->user_id.                 '&'.
                                                                'group='.       $notification->item_id.                 '&'.
                                                                'waited_user='. $notification->secondary_item_id.       '&'.
                                                                'request_id='.  $requestIDs->request_id.                '&'.
                                                                'action=deny',
                                        'expected_response' =>  'denied'
                                    )
                                );
                            }else{
                                return false;
                            }
                            break;
                        case 'group_invite':
                            $notification->actions = array(
                                (object)array(
                                    'title'             =>  __( 'accept', 'pure' ),
                                    'request'           =>  'command=templates_of_groups_income_invite_action'. '&'.
                                        'user='.        $notification->user_id.                                 '&'.
                                        'group='.       $notification->item_id.                                 '&'.
                                        'action=accept',
                                    'expected_response' =>  'accepted'
                                ),
                                (object)array(
                                    'title'             =>  __( 'reject', 'pure' ),
                                    'request'           =>  'command=templates_of_groups_income_invite_action'. '&'.
                                        'user='.        $notification->user_id.                                 '&'.
                                        'group='.       $notification->item_id.                                 '&'.
                                        'action=deny',
                                    'expected_response' =>  'denied'
                                )
                            );
                            break;
                        default:
                            $notification->actions = array(
                                (object)array(
                                    'title'             =>  __( 'remove', 'pure' ),
                                    'request'           =>  'command=templates_of_messenger_notifications_set_read'.  '&'.
                                        'user_id='.         $current_user_id. '&'.
                                        'notification_id='. $notification->id,
                                    'expected_response' =>  'success'
                                )
                            );
                            break;
                    }
                    $ProviderGroups         = \Pure\Providers\Groups\Initialization::instance()->get('groups');
                    $groups                 = $ProviderGroups->get(array(
                        'shown'                     =>0,
                        'only_with_avatar'          =>false,
                        'maxcount'                  =>1,
                        'profile'                   =>'',
                        'from_date'                 =>date("Y-m-d"),
                        'days'                      =>9999,
                        'targets_array'             =>array($notification->item_id),
                        'load_all_members'          =>false,
                        'load_membership_requests'  =>false,
                    ));
                    $notification->target   = ($groups !== false ? (is_array($groups->groups) !== false ? (count($groups->groups) === 1 ? $groups->groups[0] : false) : false) : false);
                    $ProviderGroups         = NULL;
                    break;
            }
        }
        public function get($parameters) {
            $notifications  = false;
            $total          = 0;
            $shown          = 0;
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if (function_exists('bp_notifications_get_all_notifications_for_user') !== false){
                    $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                    $current    = $WordPress->get_current_user();
                    $WordPress  = NULL;
                    if ((int)($current !== false ? $current->ID : -1) === $parameters->user_id && $parameters->user_id > 0) {
                        \Pure\Components\Tools\HTMLStrings\Initialization::instance()->attach();
                        $Strings        = new \Pure\Components\Tools\HTMLStrings\HTMLParser();
                        $notifications  = bp_notifications_get_all_notifications_for_user($parameters->user_id);
                        if ($notifications !== false){
                            $notifications  = array_reverse($notifications);
                            $notifications  = $this->filter($notifications);
                            $total          = count($notifications);
                            if (count($notifications) > $parameters->shown){
                                if (count($notifications) > $parameters->maxcount){
                                    $notifications = array_splice($notifications, $parameters->shown, $parameters->maxcount);
                                }
                                $shown = count($notifications);
                                $_notifications = array();
                                foreach($notifications as $key=>$notification){
                                    $notifications[$key]->description = $Strings->remove_tags_from_string($this->get_description($notification));
                                    $_notification = $notifications[$key];
                                    if ($this->addActions($_notification, $current->ID) !== false){
                                        $_notifications[] = $_notification;
                                    }
                                }
                                $notifications = $_notifications;
                            }
                        }
                        $Strings        = NULL;
                    }
                }
            }
            return (object)array(
                'notifications' =>$notifications,
                'total'         =>$total,
                'shown'         =>$shown
            );
        }
        public function setAsRead($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if (function_exists('bp_notifications_mark_notification') !== false) {
                    $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                    $current    = $WordPress->get_current_user();
                    $WordPress  = NULL;
                    if ((int)($current !== false ? $current->ID : -1) === $parameters->user_id && $parameters->user_id > 0) {
                        return bp_notifications_mark_notification((int)$parameters->notification_id, 0);
                    }
                }
            }
            return false;
        }
    }
}
?>