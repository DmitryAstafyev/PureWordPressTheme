<?php
namespace Pure\Components\WordPress\Settings{
    class Registration{
        public function init(){
            add_action("admin_menu", array( $this, 'setup' ));
        }
        public function setup(){
            add_menu_page(      'Pure theme settings',
                                'Pure settings',
                                'manage_options',
                                'pure_theme_settings_basic',
                                array( $this, 'setting_page_basic' )
            );
            add_submenu_page(   'pure_theme_settings_basic',
                                'Basic',
                                'Basic',
                                'manage_options',
                                'pure_theme_settings_basic',
                                array( $this, 'setting_page_basic' )
            );
            add_submenu_page(   'pure_theme_settings_basic',
                                'BuddyPress configuration',
                                'BuddyPress',
                                'manage_options',
                                'pure_theme_settings_buddypress',
                                array( $this, 'setting_page_buddypress' )
            );
            add_submenu_page(   'pure_theme_settings_basic',
                                'Global messenger configuration',
                                'Messenger',
                                'manage_options',
                                'pure_theme_settings_messenger',
                                array( $this, 'setting_page_messenger' )
            );
            add_submenu_page(   'pure_theme_settings_basic',
                                'WebSockets server configuration',
                                'WebSockets',
                                'manage_options',
                                'pure_theme_settings_websockets',
                                array( $this, 'setting_page_websockets' )
            );
            add_submenu_page(   'pure_theme_settings_basic',
                                'Reports collections',
                                'Reports collections',
                                'manage_options',
                                'pure_theme_settings_reports_collections',
                                array( $this, 'setting_page_reports_collections' )
            );
            add_submenu_page(   'pure_theme_settings_basic',
                                'External services',
                                'External services',
                                'manage_options',
                                'pure_theme_settings_external_services',
                                array( $this, 'setting_page_external_services' )
            );
            add_submenu_page(   'pure_theme_settings_basic',
                                'Front page',
                                'Front page',
                                'manage_options',
                                'pure_theme_settings_front_page',
                                array( $this, 'setting_page_front_page' )
            );
            add_submenu_page(   'pure_theme_settings_basic',
                                'Page editor',
                                'Page editor',
                                'manage_options',
                                'pure_theme_settings_page_editor',
                                array( $this, 'pure_theme_settings_page_editor' )
            );
            add_submenu_page(   'pure_theme_settings_basic',
                                'Import data',
                                'Import data',
                                'manage_options',
                                'pure_theme_settings_import',
                                array( $this, 'pure_theme_settings_import' )
            );
            //external services
            $JSLinks    = new \Pure\Resources\JavaScripts();
            $JSLinks    ->enqueue();
            $JSLinks    = NULL;
            $this->resources();
        }
        private function resources(){
            \Pure\Resources\Compressor::instance()->CSS(\Pure\Configuration::instance()->cssPath.'/'.'admin.styles.css');
        }
        public function _setting_page_basic(){
            $this->resources();
        }
        public function setting_page_basic(){
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.basic.php'        ));
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.comments.php'     ));
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.mana.php'         ));
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.counts.php'       ));
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.images.php'       ));
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.information.php'  ));
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.attachments.php'  ));
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.footer.php'       ));
        }
        public function setting_page_buddypress(){
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.buddypress.activities.php'));
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.buddypress.render.php'    ));
        }
        public function setting_page_messenger(){
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.messenger.php'    ));
        }
        public function setting_page_websockets(){
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.websockets.php'   ));
        }
        public function setting_page_reports_collections(){
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.reports.php'      ));
        }
        public function setting_page_external_services(){
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.mailer.php'       ));
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.googlemaps.php'   ));
        }
        public function setting_page_front_page(){
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.page.front.php'   ));
        }
        public function pure_theme_settings_page_editor(){
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.pages.editor.php'  ));
        }
        public function pure_theme_settings_import(){
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/settings/settings.import.php'  ));
        }
    }
}
?>