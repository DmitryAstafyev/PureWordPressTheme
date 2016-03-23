<?php
namespace Pure\Resources{
    class JavaScripts{
        private $debug_mode_on;
        private function get(){
            $filesSystem    = new \Pure\Resources\FileSystem();
            $jsFiles        = $filesSystem->getFilesList(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->resources.'/js'));
            wp_enqueue_script("jquery");
            if (is_null($jsFiles) === false){
                foreach($jsFiles as $jsFile){
                    if(is_file(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->resources.'/js/'.$jsFile)) === true){
                        \Pure\Resources\Attacher::instance()->js($jsFile, \Pure\Configuration::instance()->jsURL.'/', '', true);
                        //\Pure\Resources\Compressor::instance()->JS(\Pure\Configuration::instance()->jsPath.'/'.$jsFile);
                    }
                }
                return true;
            }
            return false;
        }
        public function enqueue(){
            return $this->get();
        }
        function __construct(){
            if (defined('WP_DEBUG') === true){
                $this->debug_mode_on = WP_DEBUG;
            }else{
                $this->debug_mode_on = false;
            }
        }
    }
    class CSSLinks{
        private $debug_mode_on;
        private function get(){
            $filesSystem    = new \Pure\Resources\FileSystem();
            $cssFiles       = $filesSystem->getFilesList(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->resources.'/css'));
            if (is_null($cssFiles) === false){
                foreach($cssFiles as $cssFile){
                    if(is_file(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->resources.'/css/'.$cssFile)) === true){
                        \Pure\Resources\Compressor::instance()->CSS(\Pure\Configuration::instance()->cssPath.'/'.$cssFile);
                    }
                }
                return true;
            }
            return false;
        }
        public function enqueue(){
            return $this->get();
        }
        function __construct(){
            if (defined('WP_DEBUG') === true){
                $this->debug_mode_on = WP_DEBUG;
            }else{
                $this->debug_mode_on = false;
            }
        }
    }
    class FileSystem{
        public function getFilesList($path){
            if (isset($path)){
                if (is_dir(\Pure\Configuration::instance()->dir($path))){
                    $items = scandir($path);
                    if ($items !== false){
                        $files = Array();
                        foreach($items as $item){
                            if ($item !== '.' && $item !== '..'){
                                array_push($files, $item);
                            }
                        }
                        return $files;
                    }
                }
            }
            return NULL;
        }
    }
    class Attacher{
        static private $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        private $css_corrections;
        private function isAJAX(){
            return (isset(\Pure\Configuration::instance()->globals->requests->AJAX) === true ? \Pure\Configuration::instance()->globals->requests->AJAX : false);
        }
        public function css($filename, $url_path, $no_AJAX = false){
            if ($this->isAJAX() === false){
                $id_resource    = preg_replace("/\W/", '_', $url_path).preg_replace("/\W/", '_', $filename);
                if (wp_style_is($id_resource) === false){
                    wp_register_style   ( $id_resource, $url_path.$filename, array(), '');
                    wp_enqueue_style    ( $id_resource);
                }
                return '';
            }else{
                return  ($no_AJAX === false ? '<!--CSS:['.$url_path.$filename.']-->' : '');
            }
        }
        public function js($filename, $url_path, $version = '', $no_AJAX = false){
            if ($this->isAJAX() === false){
                $id_resource    = preg_replace("/\W/", '_', $url_path).preg_replace("/\W/", '_', $filename);
                if (wp_script_is($id_resource) === false){
                    wp_register_script  ( $id_resource, $url_path.$filename, array(), $version);
                    wp_enqueue_script   ( $id_resource);
                }
                return '';
            }else{
                return  ($no_AJAX === false ? '<!--JS:['.$url_path.$filename.']-->' : '');
            }
        }
        function __construct(){
            $this->css_corrections = array();
        }
    }
    class Names{
        static private $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        private $site_url   = false;
        private $folder     = false;
        public function clearURL($url){
            return str_replace($this->site_url, '', $url);
        }
        public function clearPath($path){
            return str_replace($this->folder, '', \Pure\Configuration::instance()->dir($path));
        }
        public function repairURL($url){
            if (!preg_match('/^[A-z]*:\/\//',$url) && $url !== ''){
                if (strpos($url, $this->site_url) !== -1){
                    $url = $this->site_url.'/'.preg_replace('/^[^\w\.]{1,}/', '', $url);
                }
            }
            return $url;
        }
        public function repairPath($path){
            if (strpos($path, $this->folder) !== -1){
                $path = $this->folder.'/'.preg_replace('/^[^\w\.]{1,}/', '', $path);
            }
            return $path;
        }
        function __construct(){
            $this->folder       = $_SERVER['DOCUMENT_ROOT'];
            $this->folder       = \Pure\Configuration::instance()->dir($this->folder);
            $this->site_url     = site_url();
        }
    }
    class Compressor{
        static private $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        private $need_update    = false;
        private $meta_name      = 'pure_compressor_data';
        private $history        = array();
        private $loader         = false;
        private $resources;
        private $root;
        private $cached;
        private function getCRC32StringForFile($file){
            return crc32($file.(string)filesize($file).(string)filemtime($file));
        }
        private function prepareLoaderProgress(){
            $LoaderTemplate = \Pure\Templates\LoaderProgress\Initialization::instance()->get('A', 'not load');
            if (method_exists($LoaderTemplate, 'getCSSFile'     ) !== false &&
                method_exists($LoaderTemplate, 'getJSFile'      ) !== false &&
                method_exists($LoaderTemplate, 'getCapColor'    ) !== false){
                $innerHTML = $LoaderTemplate->get();
                if ($innerHTML !== ''){
                    $this->loader = (object)array(
                        'css'       =>\Pure\Configuration::instance()->dir($LoaderTemplate->getCSSFile()),
                        'js'        =>\Pure\Configuration::instance()->dir($LoaderTemplate->getJSFile()),
                        'innerHTML' =>$innerHTML
                    );
                    $CRC32 = (object)array(
                        'css'       =>$this->getCRC32StringForFile($this->loader->css),
                        'js'        =>$this->getCRC32StringForFile($this->loader->js),
                    );
                    $CompressorFiles = false;
                    foreach($CRC32 as $type=>$valueCRC32){
                        $update_resource = true;
                        if (in_array($this->loader->$type, $this->cached->parents) !== false){
                            $key_in_cache = array_search($this->loader->$type, $this->cached->parents);
                            if ($valueCRC32 === $this->cached->CRC32[$key_in_cache] &&
                                file_exists(\Pure\Configuration::instance()->dir($this->cached->files[$key_in_cache])) !== false){
                                //Good resource. We should not update it.
                                $this->loader->$type    = $this->cached->files[$key_in_cache];
                                $update_resource        = false;
                            }else{
                                //Bad resource. //Remove file
                                $this->removeFromCache($key_in_cache);
                            }
                        }
                        if ($update_resource !== false){
                            if ($CompressorFiles === false){
                                $this->attachTools();
                                $CompressorFiles = new CompressorFiles();
                            }
                            //Make new resource
                            $filename                       = $CompressorFiles->create($this->loader->$type, $type);
                            if ($filename !== false){
                                $this->cached->CRC32[]      = $valueCRC32;
                                $this->cached->parents[]    = $this->loader->$type;
                                $this->cached->files[]      = $filename->full;
                                $this->cached->URLs[]       = $filename->url;
                                $this->cached->sourceURLs[] = $filename->source_url;
                                $this->cached->types[]      = $type;
                                //Set flag for update settings
                                $this->need_update          = true;
                                $this->loader->$type        = $filename->full;
                            }else{
                                $this->loader               = false;
                                return false;
                            }
                        }
                    }
                    foreach($CRC32 as $type=>$valueCRC32){
                        $this->loader->$type = @file_get_contents($this->loader->$type);
                    }
                    foreach($this->loader as $key=>$value){
                        $this->loader->$key = base64_encode($value);
                    }
                    $this->loader->capColor = $LoaderTemplate->getCapColor();
                }
            }
            $LoaderTemplate = NULL;
        }
        private function addCap(){
            if ($this->loader !== false){
                $color = $this->loader->capColor;
            }else{
                $color = 'rgb(255,255,255)';
            }
            ?>
            <div id="Pure.Compressor.Cap" style="position: fixed;top:0;left:0;width: 100%;height: 100%;z-index: 999998;background:<?php echo $color; ?>;"></div>
            <?php
        }
        private function removeFromCache($key_in_cache){
            //Bad resource. //Remove file
            @unlink($this->cached->files[$key_in_cache]);
            //Remove record
            unset($this->cached->CRC32      [$key_in_cache]);
            unset($this->cached->files      [$key_in_cache]);
            unset($this->cached->parents    [$key_in_cache]);
            unset($this->cached->URLs       [$key_in_cache]);
            unset($this->cached->sourceURLs [$key_in_cache]);
            unset($this->cached->types      [$key_in_cache]);
            //Set flag for update settings
            $this->need_update = true;
        }
        private function attach($resource, $type){
            if (in_array($resource, $this->history) === false){
                $this->resources[]  = (object)array(
                    'type'      =>$type,
                    'filename'  =>\Pure\Configuration::instance()->dir($resource)
                );
                $this->history[]    = $resource;
                return true;
            }
            return false;
        }
        public function JS($filename){
            return $this->attach($filename, 'js');
        }
        public function CSS($filename){
            return $this->attach($filename, 'css');
        }
        private function generateKeys(){
            $keys = array();
            foreach($this->resources as $resource){
                if (isset($keys[$resource->type]) === false){
                    $keys[$resource->type] = array();
                }
                if (file_exists(\Pure\Configuration::instance()->dir($resource->filename)) !== false){
                    $keys[$resource->type][] = (object)array(
                        'file_name'     =>$resource->filename,
                        'file_size'     =>filesize($resource->filename),
                        'CRC32'         =>$this->getCRC32StringForFile($resource->filename),
                        'url'           =>false,
                        'source_url'    =>false,
                        'result_file'   =>false
                    );
                }
            }
            return $keys;
        }
        private function loadCached(){
            $this->cached = get_option($this->meta_name);
            if ($this->cached !== false){
                if (isset($this->cached->CRC32) !== false && isset($this->cached->files) !== false){
                    if (is_array($this->cached->CRC32) !== false && is_array($this->cached->files) !== false){
                        if (count($this->cached->CRC32) === count($this->cached->files)){
                            foreach($this->cached->CRC32 as $key=>$value){
                                $this->cached->files        [$key] = Names::instance()->repairPath  ($this->cached->files       [$key]);
                                $this->cached->parents      [$key] = Names::instance()->repairPath  ($this->cached->parents     [$key]);
                                $this->cached->URLs         [$key] = Names::instance()->repairURL   ($this->cached->URLs        [$key]);
                                $this->cached->sourceURLs   [$key] = Names::instance()->repairURL   ($this->cached->sourceURLs  [$key]);
                            }
                            return true;
                        }
                    }
                }
            }
            //Reset cached data or set default (if first-first using)
            $this->cached = (object)array(
                'CRC32'         =>array(),
                'files'         =>array(),
                'parents'       =>array(),
                'URLs'          =>array(),
                'types'         =>array(),
                'sourceURLs'    =>array(),
            );
            delete_option($this->meta_name);
        }
        private function saveCache(){
            if ($this->need_update !== false){
                foreach($this->cached->CRC32 as $key=>$value){
                    $this->cached->files        [$key] = Names::instance()->clearPath   ($this->cached->files       [$key]);
                    $this->cached->parents      [$key] = Names::instance()->clearPath   ($this->cached->parents     [$key]);
                    $this->cached->URLs         [$key] = Names::instance()->clearURL    ($this->cached->URLs        [$key]);
                    $this->cached->sourceURLs   [$key] = Names::instance()->clearURL    ($this->cached->sourceURLs  [$key]);
                }
                update_option( $this->meta_name, $this->cached);
            }
        }
        public function getCached(){
            $this->loadCached();
            return $this->cached;
        }
        private function analysisKeys($keys){
            $CompressorFiles = false;
            foreach($keys as $type=>$collection){
                foreach($collection as $index=>$resource){
                    $update_resource = true;
                    if (in_array($resource->file_name, $this->cached->parents) !== false){
                        $key_in_cache = array_search($resource->file_name, $this->cached->parents);
                        if ($resource->CRC32 === $this->cached->CRC32[$key_in_cache] &&
                            file_exists(\Pure\Configuration::instance()->dir($this->cached->files[$key_in_cache])) !== false){
                            //Good resource. We should not update it.
                            $keys[$type][$index]->result_file   = $this->cached->files      [$key_in_cache];
                            $keys[$type][$index]->url           = $this->cached->URLs       [$key_in_cache];
                            $keys[$type][$index]->source_url    = $this->cached->sourceURLs [$key_in_cache];
                            $update_resource                    = false;
                        }else{
                            //Bad resource. //Remove file
                            $this->removeFromCache($key_in_cache);
                        }
                    }
                    if ($update_resource !== false){
                        if ($CompressorFiles === false){
                            $this->attachTools();
                            $CompressorFiles = new CompressorFiles();
                        }
                        //Make new resource
                        $filename                       = $CompressorFiles->create($resource->file_name, $type);
                        if ($filename !== false){
                            $keys[$type][$index]->result_file   = $filename->full;
                            $keys[$type][$index]->url           = $filename->url;
                            $this->cached->CRC32[]              = $resource->CRC32;
                            $this->cached->parents[]            = $resource->file_name;
                            $this->cached->files[]              = $filename->full;
                            $this->cached->URLs[]               = $filename->url;
                            $this->cached->sourceURLs[]         = $filename->source_url;
                            $this->cached->types[]              = $type;
                            //Set flag for update settings
                            $this->need_update = true;
                        }else{
                            unset($keys[$type][$index]);
                        }
                    }
                }
            }
            return $keys;
        }
        private function publish($keys){
            \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
            $Requests   = new \Pure\Components\WordPress\Location\Requests\Register();
            $innerHTML  = '';
            foreach($keys as $type=>$collection){
                $innerHTML .= $type.':[';
                foreach($collection as $index=>$resource) {
                    $output = @file_get_contents($resource->result_file);
                    if ($output !== false) {
                        $innerHTML .= '{url:"'.base64_encode($resource->url).'",source_url:"'.base64_encode($resource->source_url).'",crc32:'.$resource->CRC32.'},';
                    }
                }
                $innerHTML .= '],';
                //value:"'.base64_encode($output).'"
            }
            ?>
            <script type="text/javascript">
                if (typeof window.pure                          !== "object") { window.pure                         = {}; }
                if (typeof window.pure.compressor               !== "object") { window.pure.compressor              = {}; }
                if (typeof window.pure.compressor.resources     !== "object") { window.pure.compressor.resources    = {}; }
                window.pure.compressor.resources = {
                    debug       : {
                        js  : <?php echo (\Pure\Configuration::instance()->compressor->debug->js     === false ? 'false' : 'true'); ?>,
                        css : <?php echo (\Pure\Configuration::instance()->compressor->debug->css    === false ? 'false' : 'true'); ?>
                    },
                    minif       : {
                        js  : <?php echo (\Pure\Configuration::instance()->compressor->useMinifiedForDebug->js  === false ? 'false' : 'true'); ?>,
                        css : <?php echo (\Pure\Configuration::instance()->compressor->useMinifiedForDebug->css === false ? 'false' : 'true'); ?>
                    },
                    files       : {<?php echo $innerHTML;?>}
                };
                window.pure.compressor.settings = {
                    destination : "<?php echo $Requests->url; ?>",
                    request     : "command=compressor_get_resources&crc32=[crc32]"
                };
                <?php
                if ($this->loader !== false){
                ?>
                window.pure.compressor.progress = {
                    innerHTML   : "<?php echo $this->loader->innerHTML; ?>",
                    css         : "<?php echo $this->loader->css; ?>",
                    js          : "<?php echo $this->loader->js; ?>"
                };
                <?php
                }
                ?>
            </script>
            <?php
        }
        public function init(){
            $this->loadCached();
            $this->prepareLoaderProgress();
            $keys = $this->generateKeys();
            $keys = $this->analysisKeys($keys);
            $this->publish($keys);
            $this->addCap();
            $this->saveCache();
        }
        private function attachTools(){
            require_once(\Pure\Configuration::instance()->dir('tools/compressor.php'));
        }
        function __construct(){
            $this->resources    = array();
            $this->root         = $_SERVER['DOCUMENT_ROOT'];
        }
    }
}
?>