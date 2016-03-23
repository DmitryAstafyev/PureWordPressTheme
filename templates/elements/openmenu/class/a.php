<?php
namespace Pure\Templates\Elements\OpenMenu{
    class A{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                if (isset($parameters->menu_id) !== false){
                    $parameters->menu_id = (int)$parameters->menu_id;
                    return true;
                }
            }
            return false;
        }
        private function innerHTMLItems($items){
            $innerHTML = '<ul>';
            foreach($items as $item){
                $innerHTML .= '<li><a href="'.$item['href'].'">'.$item['title'].'</a>';
                if (isset($item['item']) !== false){
                    $innerHTML .= $this->innerHTMLItems((isset($item['item']['title']) === false ? $item['item'] : array($item['item'])));
                }
                $innerHTML .= '</li>';

            }
            $innerHTML .= '</ul>';
            return $innerHTML;
        }
        public function innerHTML($parameters = false){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                if ((int)$parameters->menu_id > 0 || (int)$parameters->menu_id === -1){
                    if ((int)$parameters->menu_id === -1){
                        \Pure\Components\WordPress\Menus\Basic\Initialization::instance()->attach(true);
                        \Pure\Components\WordPress\Menus\Basic\Registration\HotLinks::generate();
                        $items = \Pure\Components\WordPress\Menus\Basic\Registration\HotLinks::$items;
                    }else{
                        \Pure\Components\WordPress\Menus\Parser\Initialization::instance()->attach(true);
                        $Parser = new \Pure\Components\WordPress\Menus\Parser\Core();
                        $items  = $Parser->get_items((int)$parameters->menu_id, false);
                        $Parser = NULL;
                    }
                    if (is_array($items) !== false){
                        $innerHTML .=   '<div data-page-element-type="Pure.Footer.Elements.Menu.A">';
                        $innerHTML .=       $this->innerHTMLItems($items);
                        $innerHTML .=   '</div>';
                    }
                }
            }
            return $innerHTML;
        }
    }
}
?>