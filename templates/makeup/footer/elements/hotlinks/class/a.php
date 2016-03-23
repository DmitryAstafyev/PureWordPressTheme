<?php
namespace Pure\Templates\Makeup\Footer\Elements\HotLinks{
    class A{
        private function validate(&$parameters){
            return true;
        }
        private function innerHTMLItems($items){
            $innerHTML = '<ul>';
            foreach($items as $item){
                $innerHTML .= '<li><a href="'.$item['href'].'">'.$item['title'].'</a>';
                if (isset($item['item']) !== false){
                    $innerHTML .= $this->innerHTMLItems($item['item']);
                }
                $innerHTML .= '</li>';

            }
            $innerHTML .= '</ul>';
            return $innerHTML;
        }
        public function innerHTML($parameters = false){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->footer->properties;
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                $items      = false;
                if ((int)$settings->hot_links > 0){
                    \Pure\Components\WordPress\Menus\Parser\Initialization::instance()->attach(true);
                    $Parser = new \Pure\Components\WordPress\Menus\Parser\Core();
                    $items  = $Parser->get_items((int)$settings->hot_links, false);
                    $Parser = NULL;
                }
                if ((int)$settings->hot_links === -1){
                    \Pure\Components\WordPress\Menus\Basic\Initialization::instance()->attach(true);
                    \Pure\Components\WordPress\Menus\Basic\Registration\HotLinks::generate();
                    $items = \Pure\Components\WordPress\Menus\Basic\Registration\HotLinks::$items;
                }
                if (is_array($items) !== false){
                    $innerHTML .=   '<!--BEGIN: Footer.Elements.HotLinks.A -->'.
                                    '<div data-page-element-type="Pure.Footer.Elements.HotLinks.A">'.
                                        '<p data-page-element-type="Pure.Footer.Elements.HotLinks.A">'.__('Hot links', 'pure').'</p>';
                    $innerHTML .= $this->innerHTMLItems($items);
                    $innerHTML .=   '</div>'.
                                    '<!--END: Footer.Elements.HotLinks.A -->';
                }
            }
            return $innerHTML;
        }
    }
}
?>