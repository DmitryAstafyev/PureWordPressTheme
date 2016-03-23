<?php
namespace Pure\Events{
    class WordPressEvents {
        static public function registration(){
            //Add
            add_action ('init',                 array( '\\Pure\Events\\WordPressEvents', 'WordPress_INIT'               ));
            add_action ('wp_loaded',            array( '\\Pure\Events\\WordPressEvents', 'WordPress_WP_LOADED'          ));
            add_action ('widgets_init',         array( '\\Pure\Events\\WordPressEvents', 'WordPress_WIDGETS_INIT'       ));
            add_action ('phpmailer_init',       array( '\\Pure\Events\\WordPressEvents', 'WordPress_PHPMAILER_INIT'     ));
            add_action ('admin_menu',           array( '\\Pure\Events\\WordPressEvents', 'WordPress_ADMIN_MENU'         ), 999 );
            add_action ('after_switch_theme',   array( '\\Pure\Events\\WordPressEvents', 'WordPress_AFTER_SWITCH_THEME' ));
            add_action ('switch_theme',         array( '\\Pure\Events\\WordPressEvents', 'WordPress_SWITCH_THEME'       ));
            add_action ('get_header',           array( '\\Pure\Events\\WordPressEvents', 'WordPress_GET_HEADER'         ));
            add_action ('admin_footer',         array( '\\Pure\Events\\WordPressEvents', 'WordPress_ADMIN_FOOTER'       ));
            add_action( 'after_setup_theme',    array( '\\Pure\Events\\WordPressEvents', 'WordPress_AFTER_SETUP_THEME'  ));
            //================================================================================
            \Pure\Components\BuddyPress\PreventResources\Initialization::instance()->attach();
            \Pure\Components\BuddyPress\PreventResources\Core::init();
            //================================================================================
            //Remove
            remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
        }
        static public function WordPress_INIT(){
            //[!]Attention this is calling before any header was sent[!]
            //================================================================================
            \Pure\Components\PostTypes\Events\Module\Initialization     ::instance()->attach();
            \Pure\Components\PostTypes\Inserts\Module\Initialization    ::instance()->attach();
            \Pure\Components\PostTypes\Reports\Module\Initialization    ::instance()->attach();
            \Pure\Components\PostTypes\Questions\Module\Initialization  ::instance()->attach();
            \Pure\Components\PostTypes\Warnings\Module\Initialization   ::instance()->attach();
            //================================================================================
            \Pure\Components\WordPress\Location\Module\Initialization::instance()->attach();
            $Location = new \Pure\Components\WordPress\Location\Module\Core();
            $Location->proceedWP_LOGIN();
            $Location->proceedADMIN();
            $Location = NULL;
            //================================================================================
            \Pure\Components\WordPress\Media\Separator\Initialization::instance()->attach();
            \Pure\Components\WordPress\Media\Separator\Core::init();
            //================================================================================
            \Pure\Components\BuddyPress\Activities\Initialization::instance()->attach();
            $Actions = new \Pure\Components\BuddyPress\Activities\Actions();
            $Actions->init();
            $Actions = NULL;
            //================================================================================
            if (!is_admin()) {
                add_filter( 'show_admin_bar', '__return_false' );
            }
        }
        static public function WordPress_WP_LOADED(){
            //================================================================================
            //WordPressEvents::registrationAfterWP_LOADED();
            //================================================================================
            //[!]Attention all commands here will be applied and for requests[!]
        }
        static public function WordPress_WIDGETS_INIT(){
            //================================================================================
            //Registration of sidebars
            \Pure\Components\WordPress\Sidebars\Initialization::instance()->attach();
            $Sidebars = new \Pure\Components\WordPress\Sidebars\Core();
            $Sidebars->init();
            $Sidebars = NULL;
            //================================================================================
        }
        static public function WordPress_PHPMAILER_INIT(\PHPMailer $phpmailer){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->mailer->properties;
            $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $phpmailer->Host        = $settings->host;
            $phpmailer->Port        = $settings->port;
            $phpmailer->Username    = $settings->username;
            $phpmailer->Password    = $settings->password;
            $phpmailer->SMTPAuth    = $settings->SMTPAuth;
            $phpmailer->SMTPSecure  = $settings->SMTPSecure;
            $phpmailer->IsSMTP();
        }
        static public function WordPress_ADMIN_MENU(){
            global $submenu;
            unset($submenu['themes.php'][6]);
            //http://stackoverflow.com/questions/25788511/remove-submenu-page-customize-php-in-wordpress-4-0
        }
        static public function WordPress_AFTER_SWITCH_THEME(){
            //================================================================================
            //Init settings
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            \Pure\Components\WordPress\Settings\Instance::instance()->initDefaults();
            //================================================================================
            //Generate basic demo content
            \Pure\Components\Demo\Minimal\Initialization::instance()->attach();
            $Demo   = new \Pure\Components\Demo\Minimal\Core();
            $Demo->init();
            $Demo   = NULL;
            //================================================================================
            //Try to restore sidebars and widgets settings
            \Pure\Components\WordPress\Sidebars\Initialization::instance()->attach();
            $SaveLoadStateCore = new \Pure\Components\WordPress\Sidebars\SaveLoadState();
            if ($SaveLoadStateCore->load() === false){
                //It seems, this is first activation of theme (because there are no settings of widgets and sidebars).
                //So, in this case try to make default content
                //Set defaults data for each scheme
                $SchemeTemplates    = \Pure\Templates\Layout\Page\ByScheme\Initialization::instance()->templates;
                foreach($SchemeTemplates as $SchemeTemplate){
                    $Template = \Pure\Templates\Layout\Page\ByScheme\Initialization::instance()->get($SchemeTemplate->key);
                    if (method_exists($Template, 'defaults') !== false){
                        $Template->defaults();
                    }
                    $Template = NULL;
                }
            }
            $SaveLoadStateCore = NULL;
            //================================================================================
        }
        static public function WordPress_SWITCH_THEME(){
            //================================================================================
            //Save settings of sidebars and widgets
            \Pure\Components\WordPress\Sidebars\Initialization::instance()->attach();
            $SaveLoadStateCore = new \Pure\Components\WordPress\Sidebars\SaveLoadState();
            $SaveLoadStateCore->save();
            $SaveLoadStateCore = NULL;
            //================================================================================
        }
        static public function WordPress_GET_HEADER(){
            if (!is_admin()) {
                remove_action('wp_head', '_admin_bar_bump_cb');
            }
        }
        static public function WordPress_ADMIN_FOOTER(){
            \Pure\Resources\Compressor::instance()->init();
        }
        static public function Pure_BEFORE_LOAD_PAGE(){
            //================================================================================
            \Pure\Components\webSocketServer\Module\Initialization::instance()->attach();
            //================================================================================
            \Pure\Components\Messenger\Module\Initialization::instance()->attach();
            //================================================================================
            \Pure\Components\WordPress\LastLogin\Initialization::instance()->attach(true);
            $LastLogin = new \Pure\Components\WordPress\LastLogin\Provider();
            $LastLogin->update();
            $LastLogin = NULL;
            //================================================================================
        }
        static public function WordPress_AFTER_SETUP_THEME(){
            //================================================================================
            load_theme_textdomain( 'pure', get_template_directory() . '/languages' );
            //================================================================================
        }
    }
    WordPressEvents::registration();
}
?>