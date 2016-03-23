<?php
namespace Pure\Templates\Layout\Page\ByScheme\Defaults{
    class Loader{
        private $class;
        function __construct($class_name){
            $this->class    = $class_name;
        }
        private function getClassName($to_lower_case = true){
            return ($to_lower_case === false ? $this->class : strtolower($this->class));
        }
        private function isNeededSetDefault(){
            \Pure\Components\WordPress\Sidebars\Initialization::instance()->attach();
            $Sidebars       = new \Pure\Components\WordPress\Sidebars\Core();
            $DefaultScheme  = \Pure\Templates\Layout\Page\ByScheme\Initialization::instance()->get($this->getClassName(false));
            $isEmpty        = true;
            if ($DefaultScheme !== false){
                $sidebars   = $DefaultScheme->listSidebars();
                foreach($sidebars as $sidebar){
                    if (count($Sidebars->getWidgetsFromSideBar($sidebar['id'])) > 0){
                        $isEmpty = false;
                        break;
                    }
                }
            }
            $Sidebars       = NULL;
            $DefaultScheme  = NULL;
            return $isEmpty;
        }
        public function init(){
            $WordPress      = new \Pure\Components\WordPress\UserData\Data();
            $current        = $WordPress->get_current_user();
            $WordPress      = NULL;
            if ($current !== false){
                if ($this->isNeededSetDefault() !== false){
                    $DefaultScheme  = \Pure\Templates\Layout\Page\ByScheme\Initialization::instance()->get($this->getClassName(false));
                    if ($DefaultScheme !== false){
                        if (file_exists(\Pure\Configuration::instance()->dir(__DIR__.'/'.$this->getClassName(false).'/defaults.php')) !== false){
                            require_once(\Pure\Configuration::instance()->dir(__DIR__.'/'.$this->getClassName(false).'/defaults.php'));
                            $ClassName = '\Pure\Templates\Layout\Page\ByScheme\Defaults\\'.$this->getClassName(false);
                            if (class_exists($ClassName) !== false){
                                $Defaults       = new $ClassName();
                                $Sidebars       = new \Pure\Components\WordPress\Sidebars\Core();
                                foreach($Defaults->sidebars as $sidebar_id=>$sidebar){
                                    foreach($sidebar as $index=>$widget){
                                        $Sidebars->addWidgetToSidebar(
                                            $sidebar_id,
                                            $widget['id'],
                                            $widget['settings']
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}