<?php
namespace Pure\Components{
    class AutoLoad{
        private $classes = [
            '\Pure\Components\WordPress\UserData',
            '\Pure\Components\Tools\Cache',
            //(!)Attention(!) Here cannot be components, which gives some output. For example GlobalSettings or something other.
            //If you place here such components, their output will be placed into responses to request (AJAX) and it will call
            //errors.
            //Also autoloaded content cannot call any resources like JS or CSS
        ];
        public function load(){
            foreach($this->classes as $class){
                $className = $class.'\Initialization';
                if (class_exists($className) !== false){
                    $instance = call_user_func(array($className, 'instance'));
                    if ($instance !== false){
                        $instance->attach();
                    }
                }
            }
        }
    }
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
            $this->paths->resources     = \Pure\Configuration::instance()->dir(__DIR__.'/'.implode('/', $namespace).'/resources');
            $this->paths->bin           = \Pure\Configuration::instance()->dir(__DIR__.'/'.implode('/', $namespace).'/bin');
            $this->urls                 = new \stdClass();
            $this->urls->class          = \Pure\Configuration::instance()->url(\Pure\Configuration::instance()->themeURL.'/components/'.implode('/', $namespace).'/class');
            $this->urls->css            = \Pure\Configuration::instance()->url(\Pure\Configuration::instance()->themeURL.'/components/'.implode('/', $namespace).'/css');
            $this->urls->js             = \Pure\Configuration::instance()->url(\Pure\Configuration::instance()->themeURL.'/components/'.implode('/', $namespace).'/js');
            $this->urls->images         = \Pure\Configuration::instance()->url(\Pure\Configuration::instance()->themeURL.'/components/'.implode('/', $namespace).'/images');
            $this->urls->resources      = \Pure\Configuration::instance()->url(\Pure\Configuration::instance()->themeURL.'/components/'.implode('/', $namespace).'/resources');
            $this->urls->bin            = \Pure\Configuration::instance()->url(\Pure\Configuration::instance()->themeURL.'/components/'.implode('/', $namespace).'/bin');
            $this->namespace            = __NAMESPACE__.'\\'.implode('\\', $namespace);
        }
    }
    class Initialization{
        public $configuration;
        private function require_from_folder($folder, $type, $resourcesLoadMade = false){
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
                                    if ($resourcesLoadMade === false){
                                        \Pure\Resources\Compressor::instance()->CSS($this->configuration->paths->css.'/'.$File);
                                    }else{
                                        \Pure\Resources\Compressor::instance()->CSS($this->configuration->paths->css.'/'.$File);
                                    }
                                }
                                break;
                            case 'js':
                                if (stripos($File, '.js') !== false){
                                    if ($resourcesLoadMade === false){
                                        \Pure\Resources\Compressor::instance()->JS($this->configuration->paths->js.'/'.$File);
                                    }else{
                                        \Pure\Components\Attacher\Module\Attacher::instance()->addJS(
                                            $this->configuration->urls->js.'/'.$File,
                                            ($resourcesLoadMade === 'after'         ? false : true),
                                            ($resourcesLoadMade === 'immediately'   ? false : true)
                                        );
                                    }
                                }
                                break;
                        }
                    }
                }
            }
        }
        private function resources($only_php, $resourcesLoadMade = false){
            //Folder [/bin] is for php files, which should be loaded manually
            $this->require_from_folder($this->configuration->paths->inc,    'php');
            $this->require_from_folder($this->configuration->paths->class,  'php');
            if ($only_php === false){
                $this->require_from_folder($this->configuration->paths->css,    'css',  $resourcesLoadMade);
                $this->require_from_folder($this->configuration->paths->js,     'js',   $resourcesLoadMade);
            }
        }
        public function attach($only_php = false, $resourcesLoadMade = false){
            $this->resources($only_php, $resourcesLoadMade);
        }
        function __construct($configuration){
            $this->configuration = $configuration;
        }
    }
    function initialization($folder, $root = false){
        $filesSystem        = new \Pure\Resources\FileSystem();
        $pluginsFolders     = $filesSystem->getFilesList($folder);
        $filesSystem        = NULL;
        if (is_null($pluginsFolders) === false){
            foreach($pluginsFolders as $pluginsFolder){
                if (is_dir(\Pure\Configuration::instance()->dir($folder.'/'.$pluginsFolder)) === true && stripos($pluginsFolder, '.') === false ){
                    if (file_exists(\Pure\Configuration::instance()->dir($folder.'/'.$pluginsFolder.'/initialization.php')) === true){
                        require_once(\Pure\Configuration::instance()->dir($folder.'/'.$pluginsFolder.'/initialization.php'));
                    }else{
                        initialization(\Pure\Configuration::instance()->dir($folder.'/'.$pluginsFolder), false);
                    }
                }
            }
            if ($root !== false){
                $AutoLoad = new AutoLoad();
                $AutoLoad->load();
                $AutoLoad = NULL;
            }
        }
    }
    initialization(__DIR__, true);
}
?>