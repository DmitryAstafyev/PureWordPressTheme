<?php
namespace Pure\Components\WordPress\Menus\Social{
    class Provider{
        private function getChildsOfItemByID($items, $id, $checkChilds){
            foreach ($items as $item){
                if (isset($item->id) === true){
                    if ($item->id === $id){
                        return (isset($item->items) === true ? $item->items : NULL);
                    }else{
                        if ($checkChilds === true){
                            if (isset($item->items) === true){
                                $result = $this->getChildsOfItemByID($item->items, $id, $checkChilds);
                                if (is_null($result) === false){
                                    return $result;
                                }
                            }
                        }
                    }
                }
            }
            return NULL;
        }
        private function parse($admin_bar_data){
            $buddyData = NULL;
            if (is_null($admin_bar_data) === false){
                $topSecondary = $this->getChildsOfItemByID($admin_bar_data, 'top-secondary', false);
                if (is_null($topSecondary) === false){
                    $buddyInformation   = new BuddyInformation();
                    $buddyData          = $this->getChildsOfItemByID($topSecondary, 'my-account-buddypress', true);
                    $buddyInformation->update($buddyData);
                    return $buddyData;
                }
            }
            return $buddyData;
        }
        public function getNative(){
            \Pure\Components\WordPress\Menus\Admin\Initialization::instance()->attach();
            $AdminMenu          = new \Pure\Components\WordPress\Menus\Admin\Provider();
            $admin_menu_data    = $AdminMenu->get();
            $AdminMenu          = NULL;
            return $this->parse($admin_menu_data);
        }
        public function getStandard(){
            $items      = false;
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ($current !== false){
                Registration\Standard::generate();
                $items = Registration\Standard::$items;
            }
            return $items;
        }
    }
    class BuddyInformation{
        private function check(&$item){
            \Pure\Components\Tools\Strings\Initialization::instance()->attach(true);
            $strings = new \Pure\Components\Tools\Strings\Strings();
            switch($item->id){
                case "my-account-messages-inbox":
                    $item->title = $strings->remove_int_from_string($item->title);
                    break;

            }
            if ($strings->get_int_from_string($item->title) !== ""){
                return;
            }else{
                \Pure\Components\BuddyPress\Information\Initialization::instance()->attach();
                $buddyInfo = new \Pure\Components\BuddyPress\Information\Core();
                switch($item->id){
                    case "my-account-notifications-unread":
                        $item->title .= $buddyInfo->notifications_unread_count();
                        break;
                    case "my-account-notifications-read":
                        $item->title .= $buddyInfo->notifications_total_count();
                        break;
                    case "my-account-notifications":
                        $item->title .= $buddyInfo->notifications_unread_count();
                        break;
                    case "my-account-messages":
                        $item->title .= $buddyInfo->messages_unread_count();
                        break;
                    case "my-account-messages-inbox":
                        $item->title .= $buddyInfo->messages_inbox_count();
                        break;
                    case "my-account-messages-sentbox":
                        $item->title .= $buddyInfo->messages_sentbox_count();
                        break;
                    case "my-account-messages-notices":
                        $item->title .= $buddyInfo->messages_notices_count();
                        break;
                    case "my-account-friends-friendships":
                        $item->title .= $buddyInfo->user_friends_count();
                        break;
                    case "my-account-friends-requests":
                        break;
                    case "my-account-groups-memberships":
                        $item->title .= $buddyInfo->user_groups_count();
                        break;
                    case "my-account-friends":
                        $item->title .= $buddyInfo->user_friends_count();
                        break;
                    case "my-account-groups":
                        $item->title .= $buddyInfo->user_is_memeber_groups_count();
                        break;
                    case "my-account-activity-friends":
                        //$item->title .= $buddyInfo->user_friends_count();
                        break;
                    case "my-account-activity-groups":
                        //$item->title .= $buddyInfo->user_is_memeber_groups_count();
                        break;
                }
            }
        }
        public function update(&$items){
            if (is_null($items) === false){
                foreach($items as $item){
                    $this->check($item);
                    if (isset($item->items) === true){
                        $this->update($item->items);
                    }
                }
            }
        }
    }
}
?>