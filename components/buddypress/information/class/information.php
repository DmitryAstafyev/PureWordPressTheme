<?php
namespace Pure\Components\BuddyPress\Information{
    class Core{
        //NOTIFICATIONS=====================================================
        public function notifications_unread_count(){
            if (function_exists('bp_notifications_get_unread_notification_count') && function_exists('bp_loggedin_user_id')){
                $count = bp_notifications_get_unread_notification_count(bp_loggedin_user_id());
                $count = ($count === 0 ? "" : $count);
                return $count;
            }
            return "";
        }
        public function notifications_total_count(){
            if (function_exists('bp_notifications_get_notifications_for_user') && function_exists('bp_loggedin_user_id')){
                $notifications  = bp_notifications_get_notifications_for_user(bp_loggedin_user_id(), 'object');
                $count          = !empty($notifications) ? count($notifications) : "";
                return $count;
            }
            return "";
        }
        //FRIENDS=====================================================
        public function user_friends_count(){
            if (function_exists('friends_get_total_friend_count') && function_exists('bp_loggedin_user_id')){
                $count = friends_get_total_friend_count(bp_loggedin_user_id());
                $count = ($count === 0 ? "" : $count);
                return $count;
            }
            return "";
        }
        //GROUPS=====================================================
        public function user_groups_count(){
            if (function_exists('groups_get_user_groups') && function_exists('bp_loggedin_user_id')){
                $groups     = groups_get_user_groups(bp_loggedin_user_id());
                $count      = !empty($groups) ? count($groups) : "";
                return $count;
            }
            return "";
        }
        public function user_is_memeber_groups_count(){
            if (function_exists('groups_total_groups_for_user') && function_exists('bp_loggedin_user_id')){
                $count = groups_total_groups_for_user(bp_loggedin_user_id());
                $count = ($count === 0 ? "" : $count);
                return $count;
            }
            return "";
        }
        //MESSAGES=====================================================
        public function messages_unread_count(){
            if (function_exists('messages_get_unread_count') && function_exists('bp_loggedin_user_id')){
                $count = messages_get_unread_count(bp_loggedin_user_id());
                $count = ($count === 0 ? "" : $count);
                return $count;
            }
            return "";
        }
        public function messages_inbox_count(){
            if (function_exists('bp_has_message_threads')){
                bp_has_message_threads('type=all&box=inbox');
                global $messages_template;
                $count = $messages_template->total_thread_count;
                $count = ($count === 0 ? "" : $count);
                return $count;
            }
            return "";
        }
        public function messages_sentbox_count(){
            if (function_exists('bp_has_message_threads')){
                bp_has_message_threads('box=sentbox');
                global $messages_template;
                $count = $messages_template->total_thread_count;
                $count = ($count === 0 ? "" : $count);
                return $count;
                //echo "<p> fsdfsdfsdf ".var_dump($messages)."</p>";
            }
            return "";
        }
        public function messages_notices_count(){
            if (function_exists('bp_has_message_threads')){
                bp_has_message_threads('box=notices');
                global $messages_template;
                $count = $messages_template->total_thread_count;
                $count = ($count === 0 ? "" : $count);
                return $count;
            }
            return "";
        }
    }
}
?>