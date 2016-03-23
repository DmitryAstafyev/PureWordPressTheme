<?php
namespace Pure\Components\WordPress\Menus\Parser{
    class Core{
        public function get_items($menu_id = false, $menu_name = false){
            $arguments = array(
                'menu_class'        => '',
                'container'         => false,
                'container_class'   => false,
                'container_id'      => false,
                'menu_class'        => false,
                'echo'              => false,
                'fallback_cb'       => 'wp_page_menu',
                'before'            => false,
                'after'             => false,
                'link_before'       => false,
                'link_after'        => false,
                'items_wrap'        => '<ul>%3$s</ul>',
                'depth'             => 0
            );
            $arguments['menu']      = ($menu_name   !== false ? $menu_name  : '');
            $arguments['menu_id']   = ($menu_id     !== false ? $menu_id    : '');
            if ($menu_id !== false || $menu_name !== false){
                $menu_content = wp_nav_menu($arguments);
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
                        if (is_string($href_matche[0]) === false){
                            preg_match("/'.*?''/i", $matche, $href_matche);
                            $symbol = "'";
                        }
                        if (is_string($href_matche[0]) === true){
                            $href  = $Strings->mb_str_replace($symbol,'', $href_matche[0]);
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
                    return $array["item"];
                }catch (\Exception $e){
                    return false;
                }
            }
            return false;
        }
    }
}
?>