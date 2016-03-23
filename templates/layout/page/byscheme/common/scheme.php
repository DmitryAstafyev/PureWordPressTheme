<?php
namespace Pure\Templates\Layout\Page\ByScheme{
    abstract class AbstractScheme{
        abstract public     function listSidebars();
        abstract protected  function getClassName($to_lower_case = true);
        public function loadSidebars(){
            $sidebars = $this->listSidebars();
            \Pure\Components\WordPress\Sidebars\Initialization::instance()->attach();
            $Sidebars = new \Pure\Components\WordPress\Sidebars\Core();
            $Sidebars->registerSidebars($sidebars);
            $Sidebars = NULL;
            return $sidebars;
        }
        public function defaults(){
            require_once(\Pure\Configuration::instance()->dir(Initialization::instance()->configuration->paths->base.'/defaults/loader.php'));
            $DefaultsLoader = new \Pure\Templates\Layout\Page\ByScheme\Defaults\Loader($this->getClassName(false));
            $DefaultsLoader->init();
            $DefaultsLoader = NULL;
        }
        public function innerHTMLSidebars(){
            \Pure\Components\WordPress\Sidebars\Initialization::instance()->attach();
            $SideBarsCore   = new \Pure\Components\WordPress\Sidebars\Render();
            $sidebars       = $this->listSidebars();
            $arguments      = array();
            foreach($sidebars as $sidebar){
                $arguments[] = array(
                    $sidebar['mark'],
                    $SideBarsCore->innerHTMLSidebarControls($sidebar['id'], $sidebar['description'])
                );
            }
            $innerHTML      = Initialization::instance()->html(
                $this->getClassName().'/admin_sidebars',
                $arguments
            );
            \Pure\Resources\Compressor::instance()->CSS(
                \Pure\Templates\Layout\Page\ByScheme\Initialization::instance()->configuration->paths->css.'/'.$this->getClassName().'Admin.css'
            );
            $SideBarsCore   = NULL;
            return $innerHTML;
        }
        public function get($echo = true){
            //Load sidebars
            $this->loadSidebars();
            //Get sidebars
            $sidebars       = $this->listSidebars();
            //Get background
            \Pure\Components\WordPress\PageBackground\Initialization::instance()->attach();
            $Background     = new \Pure\Components\WordPress\PageBackground\Core();
            //Render
            \Pure\Components\WordPress\Sidebars\Initialization::instance()->attach();
            $SideBarsCore   = new \Pure\Components\WordPress\Sidebars\Render();
            $FooterClass    = \Pure\Templates\Layout\Page\Footer\Standard\Initialization::instance()->get('A');
            $arguments      = array(
                array('background_image', $Background->get_background_url())
            );
            foreach($sidebars as $sidebar){
                if ($sidebar['mark'] === 'footer'){
                    $arguments[] = array(
                        $sidebar['mark'],
                        $FooterClass->get($sidebar['id'])
                    );
                }else{
                    $arguments[] = array(
                        $sidebar['mark'],
                        $SideBarsCore->innerHTMLSidebar($sidebar['id'])
                    );
                }
            }
            $innerHTML      = Initialization::instance()->html(
                $this->getClassName().'/structure',
                $arguments
            );
            $SideBarsCore   = NULL;
            $FooterClass    = NULL;
            \Pure\Components\LockPage\A\Initialization::instance()->attach();
            if ($echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
    }
}