<?php
namespace Pure\Templates\HeaderMenu{
    class C{
        private function innerHTMLMenuItemsContainer($innerHTMLItems, $root = false){
            return Initialization::instance()->html(
                'C/area.menu.items.container',
                array(
                    array('engine_mark',        ($root === false ? '' : ' data-menu-engine="Menu.Container" ')  ),
                    array('customize_style',    'menu-popup-background'                                         ),
                    array('global_mark',        ($root === false ? 'data-global-makeup-marks="menu-popup-items"' : 'data-global-makeup-marks="menu-top-line-items"')  ),
                    array('items',              $innerHTMLItems                                                 ),
                )
            );
        }
        private function innerHTMLMenuItem($item, $root = false){
            $innerHTMLSubItems = '';
            if (isset($item['item']) !== false){
                foreach($item['item'] as $sub_item){
                    $innerHTMLSubItems .= $this->innerHTMLMenuItem($sub_item, false);
                }
                $innerHTMLSubItems = $this->innerHTMLMenuItemsContainer($innerHTMLSubItems, false);
            }
            $pointer_mark   = ($innerHTMLSubItems !== '' ? ($root === false ? ' data-pointer="right" ' : ' data-pointer="bottom" ') : '');
            $drop_mark      = ($innerHTMLSubItems !== '' ? ($root === false ? '' : ' data-height-drop="1.5em" ') : '');
            $innerHTML      = Initialization::instance()->html(
                'C/area.menu.item',
                array(
                    array('id',                 $item['id']                                                     ),
                    array('engine_mark',        ($root === false ? '' : ' data-menu-engine="Menu.Item" ')       ),
                    array('is_in_block_menu',   ($root === false ? '' : ' data-menu-engine-to-block="true" '    )                            ),
                    array('pointer_mark',       $pointer_mark                                                   ),
                    array('drop_mark',          $drop_mark                                                      ),
                    array('customize_style',    ($root === false ? 'menu-popup-color' : 'menu-top-line-color')  ),
                    array('global_mark',        ($root === false ? 'data-global-makeup-marks="menu-popup-item"' : 'data-global-makeup-marks="menu-top-line-item"')  ),
                    array('label',              $item['title']                                                  ),
                    array('href',               $item['href']                                                   ),
                    array('items',              $innerHTMLSubItems                                              ),
                )
            );
            return $innerHTML;
        }
        private function innerHTMLMenu($menu = '', $theme_location = ''){
            $MenuProvider       = new \Pure\Components\WordPress\Menus\Primary\Provider();
            $items              = $MenuProvider->get($menu, $theme_location);
            $MenuProvider       = NULL;
            $innerHTMLMenuItems = '';
            if (is_array($items) !== false){
                foreach($items as $item){
                    $innerHTMLMenuItems .= $this->innerHTMLMenuItem($item, true);
                }
                $innerHTMLMenuItems .= Initialization::instance()->html(
                    'C/area.menu.more',
                    array(
                        array('more',                   __('more', 'pure')                       ),
                        array('global_mark_container',  'data-global-makeup-marks="menu-popup-items"'   ),
                        array('global_mark_item',       'data-global-makeup-marks="menu-popup-item"'    ),
                    )
                );
                $innerHTMLMenuItems = $this->innerHTMLMenuItemsContainer($innerHTMLMenuItems, true);
            }
            return $innerHTMLMenuItems;
        }
        private function innerHTMLPersonalUserItemsContainer($innerHTMLItems, $root = false){
            return Initialization::instance()->html(
                'C/area.personal.user.items.container',
                array(
                    array('customize_style',    'menu-popup-background'         ),
                    array('global_mark',        'data-global-makeup-marks="menu-popup-items"'   ),
                    array('menu_align',         ($root === false ? 'left' : '') ),
                    array('items',              $innerHTMLItems                 ),
                )
            );
        }
        private function innerHTMLPersonalUserItem($item, $root = false){
            $innerHTMLSubItems = '';
            if (isset($item->items) !== false){
                if (is_array($item->items) !== false){
                    foreach($item->items as $sub_item){
                        $innerHTMLSubItems .= $this->innerHTMLPersonalUserItem($sub_item, false);
                    }
                    $innerHTMLSubItems = $this->innerHTMLPersonalUserItemsContainer($innerHTMLSubItems, false);
                }
            }
            $innerHTML      = Initialization::instance()->html(
                'C/area.personal.user.item',
                array(
                    array('id',                 $item->id                                                   ),
                    array('href',               $item->href                                                 ),
                    array('title',              $item->title                                                ),
                    array('pointer',            ($innerHTMLSubItems !== '' ? ' data-pointer="left" ' : '')  ),
                    array('attributes',         ($item->attr !== '' ? ' '.$item->attr.' ' : '')             ),
                    array('customize_style',    'menu-popup-color'                                          ),
                    array('global_mark',        'data-global-makeup-marks="menu-popup-item"'                ),
                    array('items',              $innerHTMLSubItems                                          ),
                )
            );
            return $innerHTML;
        }
        private function innerHTMLPersonalUser($current){
            \Pure\Components\WordPress\Menus\Basic\Initialization::instance()->attach();
            \Pure\Components\WordPress\Menus\Social\Initialization::instance()->attach();
            $WordPressMenu  = new \Pure\Components\WordPress\Menus\Basic\Provider();
            $BuddyPressMenu = new \Pure\Components\WordPress\Menus\Social\Provider();
            $items          = array_merge($BuddyPressMenu->getStandard(), $WordPressMenu->getStandard());
            $WordPressMenu  = NULL;
            $BuddyPressMenu = NULL;
            $innerHTMLItems = '';
            foreach($items as $item){
                $innerHTMLItems .= $this->innerHTMLPersonalUserItem($item, true);
            }
            $innerHTMLItems = $this->innerHTMLPersonalUserItemsContainer($innerHTMLItems, true);
            return Initialization::instance()->html(
                'C/area.personal.user',
                array(
                    array('items',  $innerHTMLItems ),
                    array('avatar', $current->avatar),
                )
            );
        }
        private function innerHTMLPersonalLogin(){
            return Initialization::instance()->html(
                'C/area.personal.login',
                array(
                    array('global_mark_container',  'data-global-makeup-marks="menu-popup-items"'   ),
                    array('global_mark_item',       'data-global-makeup-marks="menu-popup-item"'  ),
                    array('label',          __('Account', 'pure')                ),
                    array('login',          __('Login', 'pure')                  ),
                    array('restore',        __('Restore password', 'pure')       ),
                    array('registration',   __('Registration', 'pure')           ),
                )
            );
        }
        private function innerHTMLPersonalCommon(){
            \Pure\Components\WordPress\Location\Special\Initialization::instance()->attach();
            $Special    = new \Pure\Components\WordPress\Location\Special\Register();
            $search_url = $Special->getURL('SEARCH',array());
            $Special    = NULL;
            return Initialization::instance()->html(
                'C/area.personal.common',
                array(
                    array('search_href', $search_url),
                )
            );
        }
        private function innerHTMLPersonalNotifications(){
            return Initialization::instance()->html(
                'C/area.personal.notifications',
                array()
            );
        }
        private function innerHTMLPersonalMessages(){
            return Initialization::instance()->html(
                'C/area.personal.messages',
                array()
            );
        }
        private function innerHTMLPersonal(){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user(false, false, true);
            $WordPress  = NULL;
            if ($current !== false){
                $innerHTMLPersonalUser          = $this->innerHTMLPersonalUser($current);
                $innerHTMLPersonalNotifications = $this->innerHTMLPersonalNotifications();
                $innerHTMLPersonalMessages      = $this->innerHTMLPersonalMessages();
            }else{
                $innerHTMLPersonalUser          = $this->innerHTMLPersonalLogin();
                $innerHTMLPersonalNotifications = '';
                $innerHTMLPersonalMessages      = '';
            }
            $innerHTMLPersonalCommon            = $this->innerHTMLPersonalCommon();
            return Initialization::instance()->html(
                'C/area.personal',
                array(
                    array('user',           $innerHTMLPersonalUser          ),
                    array('messages',       $innerHTMLPersonalMessages      ),
                    array('notifications',  $innerHTMLPersonalNotifications ),
                    array('common',         $innerHTMLPersonalCommon        ),
                )
            );
        }
        private function innerHTMLLogo(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->settings->images->properties;
            $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->less($parameters);
            $attachment = false;
            $logo_url   = '';
            if (isset(\Pure\Configuration::instance()->globals->styles->logo_mode) !== false){
                if (\Pure\Configuration::instance()->globals->styles->logo_mode === 'dark'){
                    $attachment = $parameters->logo_dark;
                }else{
                    $attachment = $parameters->logo_light;
                }
            }
            if ((int)$attachment > 0){
                $logo_url = wp_get_attachment_image_src( (int)$attachment, 'full', false );
                $logo_url = (is_array($logo_url) !== false ? $logo_url[0] : '');
            }
            $logo_url  = ($logo_url === '' ? Initialization::instance()->configuration->urls->images.'/c/logo.png' : $logo_url);
            return Initialization::instance()->html(
                'C/area.logo',
                array(
                    array('logo', $logo_url),
                )
            );
        }
        private function innerHTMLBreadcrumbs(){
            \Pure\Components\WordPress\Breadcrumbs\Initialization::instance()->attach();
            $Breadcrumbs    = new \Pure\Components\WordPress\Breadcrumbs\Provider();
            $breadcrumbs    = $Breadcrumbs->get();
            $Breadcrumbs    = NULL;
            $innerHTML      = '';
            foreach($breadcrumbs as $breadcrumb){
                $innerHTML .= Initialization::instance()->html(
                    'C/area.breadcrumbs.item',
                    array(
                        array('global_mark',    'data-global-makeup-marks="menu-popup-item"'  ),
                        array('href',           $breadcrumb->url    ),
                        array('title',          $breadcrumb->title  ),
                    )
                );
            }
            $innerHTML = Initialization::instance()->html(
                'C/area.breadcrumbs',
                array(
                    array('parts', $innerHTML),
                )
            );
            return $innerHTML;
        }
        public function innerHTMLSupport(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->settings->information->properties;
            $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->less($parameters);
            return Initialization::instance()->html(
                'C/support',
                array(
                    array('mail',       ($parameters->mail      !== '' ? '<span data-record-type="mail"></span>'.   $parameters->mail                   : '')  ),
                    array('phone',      ($parameters->phone     !== '' ? '<span data-record-type="phone"></span>'.  $parameters->phone                  : '')  ),
                    array('facebook',   ($parameters->facebook  !== '' ? '<li data-element-type="Pure.HeaderMenu.C.Areas.Social"><a data-social-type="facebook" href="'.   $parameters->facebook.  '"></a></li>'    : '')  ),
                    array('google',     ($parameters->google    !== '' ? '<li data-element-type="Pure.HeaderMenu.C.Areas.Social"><a data-social-type="google" href="'.     $parameters->google.    '"></a></li>'    : '')  ),
                    array('linkin',     ($parameters->linkin    !== '' ? '<li data-element-type="Pure.HeaderMenu.C.Areas.Social"><a data-social-type="linkin" href="'.     $parameters->linkin.    '"></a></li>'    : '')  ),
                    array('twitter',    ($parameters->twitter   !== '' ? '<li data-element-type="Pure.HeaderMenu.C.Areas.Social"><a data-social-type="twitter" href="'.    $parameters->twitter.   '"></a></li>'    : '')  ),
                )
            );
        }
        public function get($menu = '', $theme_location = '', $echo = false){
            \Pure\Components\WordPress\Menus\Primary\Initialization::instance()->attach(true);
            $innerHTMLMenu          = $this->innerHTMLMenu($menu, $theme_location);
            $innerHTMLBreadcrumbs   = $this->innerHTMLBreadcrumbs();
            $innerHTMLPersonal      = $this->innerHTMLPersonal();
            $innerHTMLLogo          = $this->innerHTMLLogo();
            $innerHTMLSupport       = $this->innerHTMLSupport();
            $innerHTML              = Initialization::instance()->html(
                'C/container',
                array(
                    array('area_logo',          $innerHTMLLogo          ),
                    array('area_menu',          $innerHTMLMenu          ),
                    array('area_breadcrumbs',   $innerHTMLBreadcrumbs   ),
                    array('area_personal',      $innerHTMLPersonal      ),
                    array('support',            $innerHTMLSupport       ),
                )
            );
            if ($echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
    }
}
?>