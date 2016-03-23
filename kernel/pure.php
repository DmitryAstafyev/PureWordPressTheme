<?php
namespace Pure{
    class Initialization{
        private function menu(){
            add_theme_support   ('menus');
            register_nav_menus  (
                array(
                    'primary'   => 'Top primary menu'
                )
            );
        }
        private  function widgets(){
            add_theme_support('widgets');
        }
        private function thumbnails(){
            add_theme_support       ( 'post-thumbnails' );
            set_post_thumbnail_size ( 672, 372, true );
            add_image_size          ( 'pure-full-width', 1038, 576, true );
        }
        private function formats(){
            add_theme_support(  'post-formats',
                                array( 'aside', 'image', 'video', 'audio', 'quote', 'link', 'gallery',)
            );
        }
        private function admin(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $Registration = new \Pure\Components\WordPress\Settings\Registration();
            $Registration->init();
            $Registration = NULL;
        }
        private function resources(){
            //\Pure\Debug\Logs\Core::instance()->open(__METHOD__);
            //Tools
            require_once('pure.resources.php'   );
            //Initialize upgrade of database
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->database.         '/'.'initialization.php'));
            //Initialize templates
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->templates->dir.   '/'.'initialization.php'));
            //Initialize components
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->components->dir.  '/'.'initialization.php'));
            //Initialize providers
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->providers->dir.   '/'.'initialization.php'));
            //Initialize plugins
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->plugins.          '/'.'initialization.php'));
            //Attach events
            require_once('pure.events.php'      );
            //\Pure\Debug\Logs\Core::instance()->close(__METHOD__);
        }
        public function apply(){
            //WordPress settings
            $this->menu         ();
            $this->thumbnails   ();
            $this->formats      ();
            $this->widgets      ();
            //Pure settings
            $this->resources    ();
            $this->admin        ();
            return true;
        }
    }
    //Initialization
    $initialization = new Initialization();
    $initialization->apply();
    $initialization = NULL;
}
?>