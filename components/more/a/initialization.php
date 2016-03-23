<?php
namespace Pure\Components\More\A{
    class Configuration extends \Pure\Components\Configuration{
        public $version     = '0.01';
        public $name        = 'More component of WordPress';
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
        public function init_scripts($echo = false){
            $innerHTML =    '<script type="text/javascript">'.
                                '(function(){'.
                                    '"use strict";'.
                                    'try{'.
                                        '(pure.system.getInstanceByPath("pure.components.more.A") !== null ? pure.system.getInstanceByPath("pure.components.more.A").init() : null);'.
                                    '}catch (e){}'.
                                '}());'.
                            '</script>';
            if ($echo === true){
                echo $innerHTML;
            }else{
                return $innerHTML;
            }
        }
    }
}
?>