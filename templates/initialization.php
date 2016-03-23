<?php
namespace Pure\Templates{
    class Configuration {
        public $version     = '0.01';
        public $paths       = NULL;
        public $urls        = NULL;
        public $namespace   = NULL;
        public $settings    = NULL;
        public $flags       = NULL;
        function __construct($namespace){
            $this->paths                    = new \stdClass();
            $this->paths->base              = \Pure\Configuration::instance()->dir(__DIR__.'/'.implode('/', $namespace));
            $this->paths->class             = \Pure\Configuration::instance()->dir($this->paths->base.'/class');
            $this->paths->css               = \Pure\Configuration::instance()->dir($this->paths->base.'/css');
            $this->paths->js                = \Pure\Configuration::instance()->dir($this->paths->base.'/js');
            $this->paths->images            = \Pure\Configuration::instance()->dir($this->paths->base.'/images');
            $this->paths->html              = \Pure\Configuration::instance()->dir($this->paths->base.'/html');
            $this->paths->thumbnails        = \Pure\Configuration::instance()->dir($this->paths->base.'/thumbnails');
            $this->paths->description       = \Pure\Configuration::instance()->dir($this->paths->base.'/description');
            $this->paths->common            = \Pure\Configuration::instance()->dir($this->paths->base.'/common');
            $this->paths->settings          = new \stdClass();
            $this->paths->settings->base    = \Pure\Configuration::instance()->dir($this->paths->base.'/settings');
            $this->paths->settings->class   = \Pure\Configuration::instance()->dir($this->paths->settings->base.'/class');
            $this->paths->settings->css     = \Pure\Configuration::instance()->dir($this->paths->settings->base.'/css');
            $this->paths->settings->js      = \Pure\Configuration::instance()->dir($this->paths->settings->base.'/js');
            $this->urls                     = new \stdClass();
            $this->urls->base               = \Pure\Configuration::instance()->url(\Pure\Configuration::instance()->themeURL.'/templates/'.implode('/', $namespace));
            $this->urls->class              = \Pure\Configuration::instance()->url($this->urls->base.'/class');
            $this->urls->css                = \Pure\Configuration::instance()->url($this->urls->base.'/css');
            $this->urls->js                 = \Pure\Configuration::instance()->url($this->urls->base.'/js');
            $this->urls->images             = \Pure\Configuration::instance()->url($this->urls->base.'/images');
            $this->urls->thumbnails         = \Pure\Configuration::instance()->url($this->urls->base.'/thumbnails');
            $this->urls->settings           = new \stdClass();
            $this->urls->settings->base     = \Pure\Configuration::instance()->url(\Pure\Configuration::instance()->themeURL.'/templates/'.implode('/', $namespace).'/settings');
            $this->urls->settings->js       = \Pure\Configuration::instance()->url($this->urls->settings->base.'/js');
            $this->urls->settings->css      = \Pure\Configuration::instance()->url($this->urls->settings->base.'/css');
            $this->urls->settings->images   = \Pure\Configuration::instance()->url($this->urls->settings->base.'/images');
            $this->namespace                = __NAMESPACE__.'\\'.implode('\\', $namespace);
            $this->settings                 = new \stdClass();
            $this->settings->id             = preg_replace('/\\\/',  '_',    $this->namespace);
            $this->settings->base           = $namespace;
            $this->settings->field          = '';
            $this->settings->classKey       = '';
            $this->flags                    = (object)array(
                'common_inited'=>false
            );
        }
    }
    class Initialization{
        public  $templates;
        private $_templates;
        public  $configuration;
        private function available(){
            $filesSystem    = new \Pure\Resources\FileSystem();
            $classFiles     = $filesSystem->getFilesList($this->configuration->paths->class);
            $filesSystem    = NULL;
            $templates      = array();
            $_templates     = array();
            if (is_null($classFiles) === false){
                foreach($classFiles as $classFile){
                    if (stripos ($classFile, '.php') !== false){
                        $classKey                               = preg_replace("/(\.php)$/", '', $classFile);
                        $templates[$classKey]                   = new \stdClass();
                        $templates[$classKey]->key              = $classKey;
                        $templates[$classKey]->thumbnail        = (file_exists(\Pure\Configuration::instance()->dir($this->configuration->paths->thumbnails.'/'.$classKey.'.png')) === true ? $this->configuration->urls->thumbnails.'/'.$classKey.'.png' : '');
                        $_templates[$classKey]                  = new \stdClass();
                        $_templates[$classKey]->key             = $classKey;
                        $_templates[$classKey]->file            = \Pure\Configuration::instance()->dir($this->configuration->paths->class.           '/'.$classFile);
                        $_templates[$classKey]->class           = $this->configuration->namespace.'\\'.$classKey;
                        $_templates[$classKey]->description     = \Pure\Configuration::instance()->dir($this->configuration->paths->description.     '/'.$classFile);
                        $_templates[$classKey]->settings        = new \stdClass();
                        $_templates[$classKey]->settings->file  = \Pure\Configuration::instance()->dir($this->configuration->paths->settings->class. '/'.$classFile);
                        $_templates[$classKey]->settings->class = $this->configuration->namespace.'\\Settings\\'.$classKey;
                    }
                }
            }
            $this->_templates   = (count($_templates) > 0 ? $_templates : false);
            $this->templates    = (count($templates ) > 0 ? $templates  : false);
        }
        /* (!)Attention(!)
         * Do not forget remove BOM from JS file, because word "redirect" should be first
         */
        private function is_redirect($file){
            $content = file_get_contents($file, NULL, NULL, 0, 100);
            if (strpos($content, 'redirect') === 0){
                $filename =  preg_replace("/redirect:/", '', $content);
                return preg_replace("/\s/", '', $filename);
            }else{
                return false;
            }
        }
        private function resources($classKey, $settings = false, $resourcesLoadMade = false){
            $classKey = isset($this->_templates[$classKey]) ? $classKey : (isset($this->_templates[strtolower($classKey)]) ? strtolower($classKey) : false);
            if ($classKey !== false){
                if ($resourcesLoadMade !== false){
                    \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                }
                switch($settings){
                    case true:
                        if (file_exists(\Pure\Configuration::instance()->dir($this->configuration->paths->settings->css.'/'.$classKey.'.css')) === true){
                            if ($resourcesLoadMade === false){
                                \Pure\Resources\Compressor::instance()->CSS(\Pure\Configuration::instance()->dir($this->configuration->paths->settings->css.'/'.$classKey.'.css'));
                            }else{
                                if ($resourcesLoadMade !== 'not load'){
                                    \Pure\Components\Attacher\Module\Attacher::instance()->addCSS(
                                        $this->configuration->urls->settings->css.'/'.$classKey.'.css',
                                        ($resourcesLoadMade === 'after'         ? false : true),
                                        ($resourcesLoadMade === 'immediately'   ? false : true)
                                    );
                                }
                            }
                        }
                        if (file_exists(\Pure\Configuration::instance()->dir($this->configuration->paths->settings->js.'/'.$classKey.'.js')) === true){
                            if ($resourcesLoadMade === false){
                                \Pure\Resources\Compressor::instance()->JS(\Pure\Configuration::instance()->dir($this->configuration->paths->settings->js.'/'.$classKey.'.js'));
                            }else{
                                if ($resourcesLoadMade !== 'not load'){
                                    \Pure\Components\Attacher\Module\Attacher::instance()->addJS(
                                        $this->configuration->urls->settings->js.'/'.$classKey.'.js',
                                        ($resourcesLoadMade === 'after'         ? false : true),
                                        ($resourcesLoadMade === 'immediately'   ? false : true)
                                    );
                                }
                            }
                        }
                        break;
                    case false:
                        if (file_exists(\Pure\Configuration::instance()->dir($this->configuration->paths->css.'/'.$classKey.'.css')) === true){
                            if ($resourcesLoadMade === false){
                                \Pure\Resources\Compressor::instance()->CSS(\Pure\Configuration::instance()->dir($this->configuration->paths->css.'/'.$classKey.'.css'));
                            }else{
                                if ($resourcesLoadMade !== 'not load'){
                                    \Pure\Components\Attacher\Module\Attacher::instance()->addCSS(
                                        $this->configuration->urls->css.'/'.$classKey.'.css',
                                        ($resourcesLoadMade === 'after'         ? false : true),
                                        ($resourcesLoadMade === 'immediately'   ? false : true)
                                    );
                                }
                            }
                        }
                        if (file_exists(\Pure\Configuration::instance()->dir($this->configuration->paths->js.'/'.$classKey.'.js')) === true){
                            $redirect = $this->is_redirect(\Pure\Configuration::instance()->dir($this->configuration->paths->js.'/'.$classKey.'.js'));
                            if ($redirect === false){
                                if ($resourcesLoadMade === false){
                                    \Pure\Resources\Compressor::instance()->JS(\Pure\Configuration::instance()->dir($this->configuration->paths->js.'/'.$classKey.'.js'));
                                }else{
                                    if ($resourcesLoadMade !== 'not load'){
                                        \Pure\Components\Attacher\Module\Attacher::instance()->addJS(
                                            $this->configuration->urls->js.'/'.$classKey.'.js',
                                            ($resourcesLoadMade === 'after'         ? false : true),
                                            ($resourcesLoadMade === 'immediately'   ? false : true)
                                        );
                                    }
                                }
                            }else{
                                if (file_exists(\Pure\Configuration::instance()->dir($this->configuration->paths->js.'/'.$redirect)) === true){
                                    if ($resourcesLoadMade === false){
                                        \Pure\Resources\Compressor::instance()->JS(\Pure\Configuration::instance()->dir($this->configuration->paths->js.'/'.$redirect));
                                    }else{
                                        if ($resourcesLoadMade !== 'not load'){
                                            \Pure\Components\Attacher\Module\Attacher::instance()->addJS(
                                                $this->configuration->urls->js.'/'.$redirect,
                                                ($resourcesLoadMade === 'after'         ? false : true),
                                                ($resourcesLoadMade === 'immediately'   ? false : true)
                                            );
                                        }
                                    }
                                }
                            }
                        }
                        break;
                }
            }
        }
        public function get_resources_of($classKey){
            $innerHTML  = '';
            $classKey   = isset($this->_templates[$classKey]) ? $classKey : (isset($this->_templates[strtolower($classKey)]) ? strtolower($classKey) : false);
            if ($classKey !== false){
                    if (file_exists(\Pure\Configuration::instance()->dir($this->configuration->paths->css.'/'.$classKey.'.css')) === true){
                        \Pure\Resources\Compressor::instance()->CSS(\Pure\Configuration::instance()->dir($this->configuration->paths->css.'/'.$classKey.'.css'));
                    }
                    if (file_exists(\Pure\Configuration::instance()->dir($this->configuration->paths->js.'/'.$classKey.'.js')) === true){
                        $redirect = $this->is_redirect(\Pure\Configuration::instance()->dir($this->configuration->paths->js.'/'.$classKey.'.js'));
                        if ($redirect === false) {
                            \Pure\Resources\Compressor::instance()->JS(\Pure\Configuration::instance()->dir($this->configuration->paths->js.'/'.$classKey . '.js'));
                        }else{
                            if (file_exists(\Pure\Configuration::instance()->dir($this->configuration->paths->js.'/'.$redirect)) === true) {
                                \Pure\Resources\Compressor::instance()->JS(\Pure\Configuration::instance()->dir($this->configuration->paths->js.'/'.$redirect));
                            }
                        }
                    }
            }
            return $innerHTML;
        }
        public function attach_resources_of($classKey, $settings = false, $resourcesLoadMade = false){
            $this->resources($classKey, $settings, $resourcesLoadMade);
        }
        private function initCommon(){
            if ($this->configuration->flags->common_inited === false){
                if (file_exists(\Pure\Configuration::instance()->dir($this->configuration->paths->common)) !== false){
                    $filesSystem    = new \Pure\Resources\FileSystem();
                    $commonFiles    = $filesSystem->getFilesList($this->configuration->paths->common);
                    $filesSystem    = NULL;
                    foreach($commonFiles as $commonFile){
                        require_once(\Pure\Configuration::instance()->dir($this->configuration->paths->common.'/'.$commonFile));
                    }
                }
                $this->configuration->flags->common_inited = true;
            }
        }
        public function get($classKey, $resourcesLoadMade = false){
            $classKey = isset($this->_templates[$classKey]) ? $classKey : (isset($this->_templates[strtolower($classKey)]) ? strtolower($classKey) : false);
            if ($classKey !== false){
                if (file_exists(\Pure\Configuration::instance()->dir($this->_templates[$classKey]->file)) === true){
                    $this->initCommon();
                    require_once(\Pure\Configuration::instance()->dir($this->_templates[$classKey]->file));
                    if (class_exists($this->_templates[$classKey]->class) === true){
                        $this->resources($classKey, false, $resourcesLoadMade);
                        $className          = $this->_templates[$classKey]->class;
                        $instance           = new $className();
                        if (method_exists($instance, 'initialize') !== false){
                            $instance->initialize();
                        }
                        return $instance;
                    }
                }
            }
            return false;
        }
        public function settings($classKey){
            $classKey = isset($this->_templates[$classKey]) ? $classKey : (isset($this->_templates[strtolower($classKey)]) ? strtolower($classKey) : false);
            if ($classKey !== false){
                if (file_exists(\Pure\Configuration::instance()->dir($this->_templates[$classKey]->settings->file)) === true){
                    require_once(\Pure\Configuration::instance()->dir($this->_templates[$classKey]->settings->file));
                    if (class_exists($this->_templates[$classKey]->settings->class) === true){
                        $this->resources($classKey, true);
                        $className  = $this->_templates[$classKey]->settings->class;
                        $instance   = new $className($this->configuration->settings, $classKey);
                        (method_exists($instance, 'initialize') === true ? $instance->initialize() : NULL);
                        return $instance;
                    }
                }
            }
            return false;
        }
        public function get_settings($data){
            $_settings  = array();
            $settings   = &$_settings;
            foreach($this->configuration->settings->base as $step){
                $settings[$step] = array();
                $settings        = &$settings[$step];
            }
            //echo var_dump($data);
            foreach($this->_templates as $classKey=>$classData){
                $instance = $this->settings($classKey);
                if ($instance !== false){
                    $setting                = $instance->parse($data);
                    $settings[$classKey]    = $setting;
                    $setting                = NULL;
                    $instance               = NULL;
                }
            }
            unset($settings);
            return $_settings;
        }
        public function description($classKey){
            $classKey = isset($this->_templates[$classKey]) ? $classKey : (isset($this->_templates[strtolower($classKey)]) ? strtolower($classKey) : false);
            if ($classKey !== false){
                if (file_exists(\Pure\Configuration::instance()->dir($this->_templates[$classKey]->description)) === true){
                    require($this->_templates[$classKey]->description);
                }
            }
            return NULL;
        }
        public function html($file_name, $properties = false){
            $full_file_name = \Pure\Configuration::instance()->dir($this->configuration->paths->html.'/'.(strpos(strtolower($file_name), '.html') === false ? $file_name.'.html' : $file_name));
            $cache          = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, array($full_file_name));
            if (!$html = $cache->value){
                if (file_exists($full_file_name) !== false) {
                    $html = file_get_contents($full_file_name);
                    if ($html !== false) {
                        \Pure\Components\Tools\Cache\Cache::set($cache->key, $html);
                    }
                }
            }
            if ($html !== false){
                if (is_array($properties) !== false){
                    foreach($properties as $property){
                        $html = str_replace('%'.$property[0].'%', $property[1], $html);
                    }
                }
                return $html;
            }
            return false;
        }
        function __construct($configuration){
            $this->configuration = $configuration;
            $this->available();
        }
    }
    interface iSettings {
        public function show($settings, $wrap_group, $widget);
    }
    class Settings{
        private $settings       = NULL;
        public function basic_validate(&$settings){
            $settings = (is_null  ($settings) === false ? $settings : array());
            $settings = (is_array ($settings) === true  ? $settings : array());
            $settings = (isset($settings[$this->settings->classKey]) === true ? $settings[$this->settings->classKey] : array());
        }
        private function get_path($field_key, $field_prefix){
            $path = preg_replace('/('.$field_prefix.')/', '', $field_key);
            $path = preg_replace('/____/',  '|',    $path);
            $path = preg_replace('/__/',    '|',    $path);
            $path = preg_replace('/(\|)$/', '',     $path);
            $path = preg_replace('/^(\|)/', '',     $path);
            $path = preg_split  ('/(\|)/',          $path);
            return $path;
        }
        private function add_to_settings(&$settings, $path, $value){
            $current = &$settings;
            for ($index = 0, $max_index = count($path); $index < $max_index; $index ++){
                if ($index < $max_index - 1){
                    if (isset($current[$path[$index]]) === false){
                        $current[$path[$index]] = array();
                    }
                    $current = &$current[$path[$index]];
                }else{
                    $current[$path[$index]] = $value;
                }
            }
            unset($current);
        }
        public function parse($data){
            $settings   = NULL;
            if (isset($data) === true && isset($this->settings->id) === true){
                if (is_array($data) === true){
                    $fields     = array();
                    $settings   = array();
                    foreach($data as $key=>$value){
                        if (strpos($key, $this->settings->id.$this->settings->field) !== false){
                            $fields[$key] = $value;
                        }
                    }
                    foreach($fields as $key=>$value){
                        $path = $this->get_path($key, $this->settings->id.$this->settings->field);
                        $this->add_to_settings($settings, $path, $value);
                    }
                    //echo var_dump($globalsettings);
                }
            }
            return $settings;
        }
        public function field($field, $widget = false){
            if ($widget === false){
                return $this->settings->id.$this->settings->field.'__'.$field.'__';
            }else{
                //echo var_dump($widget.'['.$this->globalsettings->classKey.']['.$field.']').'<br/>';
                return $widget.'['.$this->settings->classKey.']['.$field.']';
            }
        }
        function __construct($settings, $classKey){
            $this->settings             = $settings;
            $this->settings->field      = '__'.$classKey.'__';
            $this->settings->classKey   = $classKey;
        }
    }
    class GlobalSettings {
        private function set_list($folder, &$list, $path = ''){
            $filesSystem        = new \Pure\Resources\FileSystem();
            $templatesFolders   = $filesSystem->getFilesList($folder);
            $filesSystem        = NULL;
            if (is_null($templatesFolders) === false){
                foreach($templatesFolders as $templateFolder){
                    if (is_dir(\Pure\Configuration::instance()->dir($folder.'/'.$templateFolder)) === true && stripos($templateFolder, '.') === false ){
                        if (file_exists(\Pure\Configuration::instance()->dir($folder.'/'.$templateFolder.'/settings')) === true){
                            $list[] = \Pure\Configuration::instance()->dir(($path !== '' ? $path.'/' : '').$templateFolder);
                        }else{
                            $this->set_list(\Pure\Configuration::instance()->dir($folder.'/'.$templateFolder), $list, $templateFolder);
                        }
                    }
                }
            }
        }
        public function get_from_POST($data){
            $list       = array();
            $settings   = array();
            $this->set_list(__DIR__, $list);
            if (count($list) > 0){
                foreach($list as $element){
                    $className = '\\Pure\\Templates\\'.$element.'\\Initialization';
                    if (class_exists($className) === true){
                        $setting = $className::instance()->get_settings($data);
                        if ($setting !== false){
                            foreach ($setting as $key=>$value){
                                $settings[$key] = $value;
                            }
                        }
                    }
                }
            }
            return (count($settings) > 0 ? $settings : false);
        }
    }
    function initialization($folder){
        $filesSystem        = new \Pure\Resources\FileSystem();
        $templatesFolders   = $filesSystem->getFilesList($folder);
        $filesSystem        = NULL;
        if (is_null($templatesFolders) === false){
            foreach($templatesFolders as $templateFolder){
                if (is_dir(\Pure\Configuration::instance()->dir($folder.'/'.$templateFolder)) === true && stripos($templateFolder, '.') === false ){
                    if (file_exists(\Pure\Configuration::instance()->dir($folder.'/'.$templateFolder.'/initialization.php')) === true){
                        require_once(\Pure\Configuration::instance()->dir($folder.'/'.$templateFolder.'/initialization.php'));
                    }else{
                        initialization(\Pure\Configuration::instance()->dir($folder.'/'.$templateFolder));
                    }
                }
            }
        }
    }
    initialization(__DIR__);
}
?>