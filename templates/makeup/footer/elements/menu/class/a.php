<?php
namespace Pure\Templates\Makeup\Footer\Elements\Menu{
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
                $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->footer->properties;
                $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                if ((int)$settings->menu > 0){
                    \Pure\Components\WordPress\Menus\Parser\Initialization::instance()->attach(true);
                    $Parser = new \Pure\Components\WordPress\Menus\Parser\Core();
                    $menu   = $Parser->get_items((int)$settings->menu, false);
                    $Parser = NULL;
                    if (is_array($menu) !== false){
                        $innerHTML .=   '<!--BEGIN: Footer.Elements.Menu.A -->'.
                                        '<div data-page-element-type="Pure.Footer.Elements.Menu.A">'.
                                            '<p data-page-element-type="Pure.Footer.Elements.Menu.A">'.get_bloginfo( 'name' ).'</p>';
                        $innerHTML .= $this->innerHTMLItems($menu);
                        $innerHTML .=   '</div>'.
                                        '<!--END: Footer.Elements.Menu.A -->';
                    }
                }
            }
            return $innerHTML;
        }
    }
}
?>