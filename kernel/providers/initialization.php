<?php
namespace Pure\Providers{
    class Configuration {
        public $version     = '0.01';
        public $paths       = NULL;
        public $namespace   = NULL;
        function __construct($namespace){
            $this->paths                    = new \stdClass();
            $this->paths->base              = \Pure\Configuration::instance()->dir(__DIR__.'/'.implode('/', $namespace));
            $this->paths->class             = $this->paths->base.'\class';
            $this->namespace                = __NAMESPACE__.'\\'.implode('\\', $namespace);
        }
    }
    class Initialization{
        public  $instances;
        private $_instances;
        public  $configuration;
        private function available($descriptions){
            $filesSystem    = new \Pure\Resources\FileSystem();
            $classFiles     = $filesSystem->getFilesList($this->configuration->paths->class);
            $filesSystem    = NULL;
            $_instances      = array();
            $instances      = array();
            if (is_null($classFiles) === false){
                foreach($classFiles as $classFile){
                    if (stripos ($classFile, '.php') !== false){
                        $classKey                               = preg_replace("/(\.php)$/", '', $classFile);
                        $instances[$classKey]                   = new \stdClass();
                        $instances[$classKey]->key              = $classKey;
                        $instances[$classKey]->description      = (isset($descriptions[$classKey]) === true ? $descriptions[$classKey] : $classKey);
                        $_instances[$classKey]                  = new \stdClass();
                        $_instances[$classKey]->key             = $classKey;
                        $_instances[$classKey]->file            = \Pure\Configuration::instance()->dir($this->configuration->paths->class.'/'.$classFile);
                        $_instances[$classKey]->common          = \Pure\Configuration::instance()->dir($this->configuration->paths->base. '/common.php');
                        $_instances[$classKey]->class           = $this->configuration->namespace.   '\\'.$classKey;
                    }
                }
            }
            $this->_instances   = (count($_instances) > 0 ? $_instances : false);
            $this->instances    = (count($instances ) > 0 ? $instances  : false);
        }
        public function get($classKey){
            if (isset($this->_instances[$classKey]) === true){
                if (file_exists(\Pure\Configuration::instance()->dir($this->_instances[$classKey]->file)) === true){
                    require_once(\Pure\Configuration::instance()->dir($this->_instances[$classKey]->file));
                    if (file_exists(\Pure\Configuration::instance()->dir($this->_instances[$classKey]->common)) === true){
                        require_once(\Pure\Configuration::instance()->dir($this->_instances[$classKey]->common));
                    }
                    if (class_exists($this->_instances[$classKey]->class) === true){
                        $className  = $this->_instances[$classKey]->class;
                        $instance   = new $className();
                        return $instance;
                    }
                }
            }
            return false;
        }
        public function getCommon(){
            if (file_exists(\Pure\Configuration::instance()->dir($this->configuration->paths->base. '/common.php')) === true){
                require_once(\Pure\Configuration::instance()->dir($this->configuration->paths->base. '/common.php'));
                if (class_exists($this->configuration->namespace.'\\Common') === true){
                    $className  = $this->configuration->namespace.'\\Common';
                    $instance   = new $className();
                    return $instance;
                }
            }
            return false;
        }
        public function is_available($classKey){
            return isset($this->_instances[$classKey]);
        }
        function __construct($configuration, $descriptions){
            $this->configuration    = $configuration;
            $this->available($descriptions);
        }
    }
    interface Provider{
        public function get($parameters);
    }
    function initialization($folder){
        $filesSystem        = new \Pure\Resources\FileSystem();
        $providersFolders   = $filesSystem->getFilesList($folder);
        $filesSystem        = NULL;
        if (is_null($providersFolders) === false){
            foreach($providersFolders as $providerFolder){
                if (is_dir(\Pure\Configuration::instance()->dir($folder.'/'.$providerFolder)) === true && stripos($providerFolder, '.') === false ){
                    if (file_exists(\Pure\Configuration::instance()->dir($folder.'/'.$providerFolder.'/initialization.php')) === true){
                        require_once(\Pure\Configuration::instance()->dir($folder.'/'.$providerFolder.'/initialization.php'));
                    }else{
                        initialization(\Pure\Configuration::instance()->dir($folder.'/'.$providerFolder));
                    }
                }
            }
        }
    }
    initialization(__DIR__);
}
?>