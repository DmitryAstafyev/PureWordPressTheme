<?php
namespace Pure\Components\WordPress\Menus\Primary{
    class Provider{
        private function correction($items){
            if (isset($items['title']) !== false){
                $items = array($items);
            }
            foreach($items as $key=>$item){
                if (isset($items[$key]['item']) !== false){
                    $items[$key]['item'] = $this->correction($items[$key]['item']);
                }
            }
            return $items;
        }
        private function get_items($menu, $theme_location){
            $menu_content = wp_nav_menu( array(
                'theme_location'    => $theme_location,
                'menu_class'        => '',
                'menu'              => $menu,
                'container'         => false,
                'container_class'   => false,
                'container_id'      => false,
                'menu_class'        => false,
                'menu_id'           => '',
                'echo'              => false,
                'fallback_cb'       => 'wp_page_menu',
                'before'            => false,
                'after'             => false,
                'link_before'       => false,
                'link_after'        => false,
                'items_wrap'        => '<ul>%3$s</ul>',
                'depth'             => 0
            ) );
            \Pure\Components\Tools\Strings\Initialization::instance()->attach(true);
            $Strings        = new \Pure\Components\Tools\Strings\Strings();
            $menu_content   = preg_replace("/<div.*?>/i",     '',         $menu_content);
            $menu_content   = preg_replace("/<\/div.*?>/i",   '',         $menu_content);
            $menu_content   = preg_replace("/<ul.*?>/i",      '',         $menu_content);
            $menu_content   = preg_replace("/<\/ul.*?>/i",    '',         $menu_content);
            $menu_content   = preg_replace("/<li.*?>/i",      '<item>',   $menu_content);
            $menu_content   = preg_replace("/<\/li.*?>/i",    '</item>',  $menu_content);
            preg_match_all('/<a.*?a>/i', $menu_content, $matches);
            if (is_array($matches[0])){
                $id = 0;
                foreach($matches[0] as $matche){
                    $href   = "";
                    $symbol = '"';
                    preg_match('/".*?"/i', $matche, $href_matche);
                    if (count($href_matche) > 0){
                        if (is_string($href_matche[0]) === false){
                            preg_match("/'.*?''/i", $matche, $href_matche);
                            $symbol = "'";
                        }
                        if (is_string($href_matche[0]) === true){
                            $href  = $Strings->mb_str_replace($symbol,'', $href_matche[0]);
                        }
                    }else{
                        $href = '#';
                    }
                    $title          = preg_replace("/<.*?>/i", '', $matche);
                    $menu_content   = $Strings->mb_str_replace($matche,'<title>'.$title.'</title><href>'.$href.'</href><id>'.$id.'</id>', $menu_content);
                    $id ++;
                }
            }
            try{
                $xml    = simplexml_load_string('<menu>'.$menu_content.'</menu>');
                $json   = json_encode($xml);
                $array  = json_decode($json, TRUE);
                return $this->correction($array["item"]);
            }catch (\Exception $e){
                return NULL;
            }
        }
        private function validate(&$sourceArray){
            foreach($sourceArray as $key=>$record){
                if (isset($record["item"])){
                    if (isset($record["item"]["id"])){
                        $sourceArray[$key]["item"] = Array($record["item"]);
                    }
                    $this->validate($sourceArray[$key]["item"]);
                }
            }
        }
        public function get($menu = '', $theme_location = ''){
            if (is_string($menu) === true && is_string($theme_location) === true){
                $structure = $this->get_items($menu, $theme_location);
                $this->validate($structure);
                if (is_null($structure) === false){
                    return $structure;
                }
            }
            return false;
        }
    }
}
?>