<?php
namespace Pure\Plugins{
    class Configuration {
        public $version     = '0.01';
        public $paths       = NULL;
        public $urls        = NULL;
        public $namespace   = NULL;
        function __construct($namespace){
            $this->paths                = new \stdClass();
            $this->paths->class         = \Pure\Configuration::instance()->dir(__DIR__.'/'.implode('/', $namespace).'/class');
            $this->paths->css           = \Pure\Configuration::instance()->dir(__DIR__.'/'.implode('/', $namespace).'/css');
            $this->paths->js            = \Pure\Configuration::instance()->dir(__DIR__.'/'.implode('/', $namespace).'/js');
            $this->paths->images        = \Pure\Configuration::instance()->dir(__DIR__.'/'.implode('/', $namespace).'/images');
            $this->paths->inc           = \Pure\Configuration::instance()->dir(__DIR__.'/'.implode('/', $namespace).'/inc');
            $this->urls                 = new \stdClass();
            $this->urls->class          = \Pure\Configuration::instance()->url(\Pure\Configuration::instance()->themeURL.'/plugins/'.implode('/', $namespace).'/class');
            $this->urls->css            = \Pure\Configuration::instance()->url(\Pure\Configuration::instance()->themeURL.'/plugins/'.implode('/', $namespace).'/css');
            $this->urls->js             = \Pure\Configuration::instance()->url(\Pure\Configuration::instance()->themeURL.'/plugins/'.implode('/', $namespace).'/js');
            $this->urls->images         = \Pure\Configuration::instance()->url(\Pure\Configuration::instance()->themeURL.'/plugins/'.implode('/', $namespace).'/images');
            $this->namespace            = __NAMESPACE__.'\\'.implode('\\', $namespace);
        }
    }
    class Initialization{
        public $configuration;
        private function require_from_folder($folder, $type){
            $filesSystem    = new \Pure\Resources\FileSystem();
            $Files          = $filesSystem->getFilesList($folder);
            $filesSystem    = NULL;
            if (is_null($Files) === false){
                foreach($Files as $File){
                    if (file_exists(\Pure\Configuration::instance()->dir($folder.'/'.$File)) === true){
                        switch($type){
                            case 'php':
                                if (stripos($File, '.php') !== false){
                                    require_once(\Pure\Configuration::instance()->dir($folder.'/'.$File));
                                }
                                break;
                            case 'css':
                                if (stripos($File, '.css') !== false){
                                    \Pure\Resources\Compressor::instance()->CSS(\Pure\Configuration::instance()->dir($this->configuration->paths->css.'/'.$File));
                                }
                                break;
                            case 'js':
                                if (stripos($File, '.js') !== false){
                                    \Pure\Resources\Compressor::instance()->JS(\Pure\Configuration::instance()->dir($this->configuration->paths->js.'/'.$File));
                                }
                                break;
                        }
                    }
                }
            }
        }
        private function resources(){
            $this->require_from_folder($this->configuration->paths->inc,    'php');
            $this->require_from_folder($this->configuration->paths->class,  'php');
            $this->require_from_folder($this->configuration->paths->css,    'css');
            $this->require_from_folder($this->configuration->paths->js,     'js' );
        }
        private function register(){
            register_widget($this->configuration->class);
        }
        public function init(){
            add_action('widgets_init', 	function () {
                $this->resources();
                $this->register ();
            });
        }
        function __construct($configuration){
            $this->configuration        = $configuration;
            $this->configuration->class = $this->configuration->namespace.'\Widget';
        }
    }
    function initialization($folder){
        $filesSystem        = new \Pure\Resources\FileSystem();
        $pluginsFolders     = $filesSystem->getFilesList($folder);
        $filesSystem        = NULL;
        if (is_null($pluginsFolders) === false){
            foreach($pluginsFolders as $pluginsFolder){
                if (is_dir(\Pure\Configuration::instance()->dir($folder.'/'.$pluginsFolder)) === true && stripos($pluginsFolder, '.') === false ){
                    if (file_exists(\Pure\Configuration::instance()->dir($folder.'/'.$pluginsFolder.'/initialization.php')) === true){
                        require_once(\Pure\Configuration::instance()->dir($folder.'/'.$pluginsFolder.'/initialization.php'));
                    }else{
                        initialization(\Pure\Configuration::instance()->dir($folder.'/'.$pluginsFolder));
                    }
                }
            }
        }
    }
    initialization(__DIR__);
}
?>