<?php
namespace Pure{
    class Configuration{
        public $version             = '0.01';
        public $name                = 'Pure theme';
        public $LPS                 = '/'; //Local Path Separator. Mostly works well with unix standard [/]. But if you have a problems on win - change it on [\]
        public $root                = NULL;
        public $plugins             = NULL;
        public $directory           = NULL;
        public $requests            = NULL;
        public $resources           = NULL;
        public $database            = NULL;
        public $inserts             = NULL;
        public $kernel              = NULL;
        public $html                = NULL;
        public $templates           = NULL;
        public $cssURL              = NULL;
        public $jsURL               = NULL;
        public $cssPath             = NULL;
        public $jsPath              = NULL;
        public $imagesURL           = NULL;
        public $resourcesURL        = NULL;
        public $components          = NULL;
        public $providers           = NULL;
        public $globals             = NULL;
        public $ready               = false;
        public $wp_debug            = false;
        public $use_cache           = true;
        public $do_duration_logs    = false;//Fix duration of operations into DataBase
        public $compressor          = NULL;
        static private $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        public function dir($path){
            return preg_replace('/\\\{1,}|\/{1,}/', $this->LPS, $this->root.strtolower(str_replace($this->root, '', $path)));
        }
        public function url($url){
            return strtolower($url);
        }
        function __construct(){
            $this->root             = substr(__DIR__, 0, (stripos(__DIR__, 'wp-content') - 1));
            $this->directory        = $this->dir(__DIR__);
            $this->plugins          = $this->dir(__DIR__.'/plugins');
            $this->resources        = $this->dir(__DIR__.'/resources');
            $this->kernel           = $this->dir(__DIR__.'/kernel');
            $this->themeURL         = get_bloginfo('template_directory');
            $this->requests         = $this->dir($this->kernel.'/requests');
            $this->inserts          = $this->dir($this->kernel.'/inserts');
            $this->database         = $this->dir($this->kernel.'/database');
            $this->html             = $this->dir($this->resources.'/html');
            $this->resourcesURL     = $this->url($this->themeURL.'/resources');
            $this->cssURL           = $this->url($this->resourcesURL.'/css');
            $this->jsURL            = $this->url($this->resourcesURL.'/js');
            $this->cssPath          = $this->url($this->resources.'/css');
            $this->jsPath           = $this->url($this->resources.'/js');
            $this->imagesURL        = $this->url($this->resourcesURL.'/images');
            $this->templates        = new \stdClass();
            $this->templates->url   = $this->url($this->themeURL.'/templates');
            $this->templates->dir   = $this->dir(__DIR__.'/templates');
            $this->components       = new \stdClass();
            $this->components->url  = $this->url($this->themeURL.'/components');
            $this->components->dir  = $this->dir(__DIR__.'/components');
            $this->providers        = new \stdClass();
            $this->providers->dir   = $this->dir($this->kernel.'/providers');
            $this->globals          = (object)array(
                'requests'  =>new \stdClass(),
                'flags'     =>new \stdClass(),
                'styles'    =>new \stdClass(),
                //And any what you want
            );
            $this->ready            = true;
            $this->wp_debug         = (defined('WP_DEBUG') === true ? WP_DEBUG : false);
            $this->compressor       = (object)array(
                'debug'                 =>(object)array(
                    'js'    =>false,
                    'css'   =>false
                ),
                'useMinifiedForDebug'   =>(object)array(
                    'js'    =>false,
                    'css'   =>false
                )
            );
        }
    }
    //Get debugging logs component
    require_once('kernel/pure.debug.logs.php');
    //Get initializer
    require_once('kernel/pure.php');
}
?>