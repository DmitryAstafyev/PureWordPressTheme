<?php
namespace Pure\Components\WordPress\Menus\Basic{
    class Provider{
        private function deleteItemByID($items, $id){
            $result = Array();
            foreach ($items as $item){
                if (isset($item->id) === true){
                    if ($item->id !== $id){
                        array_push($result, $item);
                    }
                }
            }
            return $result;
        }
        private function validate(&$items, &$parent = NULL){
            \Pure\Components\Tools\HTMLStrings\Initialization::instance()->attach();
            $Strings = new \Pure\Components\Tools\HTMLStrings\HTMLParser();
            \Pure\Components\Tools\Strings\Initialization::instance()->attach(true);
            $strings = new \Pure\Components\Tools\Strings\Strings();
            foreach ($items as $item){
                $item->title = (is_null($item->title) === true ? "" : $item->title);
                $item->title = $Strings->remove_tags_from_string($item->title);
                if ($item->title === "" || $strings->remove_int_from_string($item->title) === ""){
                    $item->title = ($item->id === "wp-logo" ? "WordPress" : "no title");
                    if (is_null($item->meta) === false){
                        if (isset($item->meta["title"])){
                            $item->title = $item->meta["title"];
                        }
                    }
                    if (isset($item->items) === true && is_null($parent) === false){
                        foreach($item->items as $subitem){
                            array_push($parent->items, $subitem);
                        }
                        unset($items[array_search($item, $items)]);
                    }
                }
                if (isset($item->items) === true){
                    $item->items = $this->validate($item->items, $item);
                }
            }
            return $items;
        }
        public function parse($admin_menu_data){
            $items = $this->deleteItemByID($admin_menu_data, 'top-secondary');
            $items = $this->validate($items);
            return $items;
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
                if ($current->role->is_admin === false){
                    Registration\Standard::generate();
                    $items = Registration\Standard::$items;
                }else{
                    Registration\Admin::generate();
                    $items = Registration\Admin::$items;
                }
            }
            return $items;
        }
    }
}
?>