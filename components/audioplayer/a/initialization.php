<?php
namespace Pure\Components\AudioPlayer\A{
    class Configuration extends \Pure\Components\Configuration{
        public $version     = '0.01';
        public $name        = 'Slider component of WordPress';
    }
    class Initialization extends \Pure\Components\Initialization{
        static private $self;
        static function instance(){
            if (!self::$self){
                $namespace  = preg_split('/(\\\)/', __NAMESPACE__);
                $namespace  = array_splice($namespace, 2);
                self::$self = new self(new Configuration($namespace));
            }
            return self::$self;
        }
        public function call_scripts($echo = false){
            $innerHTML = '';
            if (\Pure\Configuration::instance()->globals->requests->AJAX === true){
                $innerHTML =    '<!--JS:['.\Pure\Components\AudioPlayer\A\Initialization::instance()->configuration->urls->js.'/audioplayer.js'.']-->'.
                                '<!--SETTING:[pure.settings.components.audioplayer_A|'.\Pure\Components\AudioPlayer\A\Initialization::instance()->configuration->urls->resources.'/player.swf'.']-->'.
                                '<!--INIT:[pure.components.audioplayer.A]-->';
            }else{
                $innerHTML =    '<script type="text/javascript">'.
                                    '(function(){'.
                                        '"use strict";'.
                                        'if (typeof window.pure                         !== "object") { window.pure                         = {}; }'.
                                        'if (typeof window.pure.settings                !== "object") { window.pure.settings                = {}; }'.
                                        'if (typeof window.pure.settings.components     !== "object") { window.pure.settings.components     = {}; }'.
                                        'pure.settings.components.audioplayer_A = "'.\Pure\Components\AudioPlayer\A\Initialization::instance()->configuration->urls->resources.'/player.swf'.'";'.
                                    '}());'.
                                '</script>';
            }
            if ($echo === true){
                echo $innerHTML;
            }else{
                return $innerHTML;
            }
        }
        public function playlist($SRCs, $ID, $echo = false){
            $innerHTML =    '<script type="text/javascript">'.
                                '(function(){'.
                                    '"use strict";'.
                                    'if (typeof window.pure                                     !== "object") { window.pure                                     = {}; }'.
                                    'if (typeof window.pure.components                          !== "object") { window.pure.components                          = {}; }'.
                                    'if (typeof window.pure.components.audioplayer              !== "object") { window.pure.components.audioplayer              = {}; }'.
                                    'if (typeof window.pure.components.audioplayer.playlists    !== "object") { window.pure.components.audioplayer.playlists    = {}; }'.
                                    'window.pure.components.audioplayer.playlists.'.$ID.'=[';
            foreach($SRCs as $src){
                $innerHTML .=       '{ src: "'.$src->guid.'", type: "'.$src->post_mime_type.'" },';
            }
            $innerHTML .=           '];'.
                                '}());'.
                            '</script>';
            if ($echo === true){
                echo $innerHTML;
            }
            return (object)array(
                'innerHTML' =>$innerHTML,
                'property'  =>'window.pure.components.audioplayer.playlists.'.$ID
            );
        }
    }
}
?>