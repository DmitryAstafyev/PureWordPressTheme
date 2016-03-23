<?php
namespace Pure\Components\Attacher\Module{
    class Attacher{
        private $after_load_commands;
        static private  $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        function __construct(){
            $this->after_load_commands = array();
        }
        private function registerAfterLoadCommand($command){
            $this->after_load_commands[] = $command;
        }
        public function publishAfterLoadCommand($echo = true){
            $innerHTML = '';
            if (count($this->after_load_commands) > 0){
                $innerHTML = '<div id="PureComponentsAttacherAfterLoadCommands">';
                foreach($this->after_load_commands as $command){
                    $innerHTML .= $command;
                }
                $innerHTML .= '</div>';
                $this->after_load_commands = array();
                if ($echo === true){
                    echo $innerHTML;
                }
            }
            return $innerHTML;
        }
        public function addINIT($command, $echo = true, $after_load = false){
            $result = '<!--INIT:['.$command.']-->';
            if ($echo === true && $after_load === false){
                echo $result;
            }elseif($after_load === true){
                $this->registerAfterLoadCommand($result);
            }
            return $result;
        }
        public function addJS($url, $echo = true, $after_load = false){
            $result = '<!--JS:['.$url.']-->';
            if ($echo === true && $after_load === false){
                echo $result;
            }elseif($after_load === true){
                $this->registerAfterLoadCommand($result);
            }
            return $result;
        }
        public function addCSS($url, $echo = true, $after_load = false){
            $result = '<!--CSS:['.$url.']-->';
            if ($echo === true && $after_load === false){
                echo $result;
            }elseif($after_load === true){
                $this->registerAfterLoadCommand($result);
            }
            return $result;
        }
        public function addCSSValue($value, $echo = true, $after_load = false){
            $result = '<!--CSSValue:['.$value.']-->';
            if ($echo === true && $after_load === false){
                echo $result;
            }elseif($after_load === true){
                $this->registerAfterLoadCommand($result);
            }
            return $result;
        }
        public function addSETTING($field, $value, $echo = true, $after_load = false){
            $result = '<!--SETTING:['.$field.'|'.$value.']-->';
            if ($echo === true && $after_load === false){
                echo $result;
            }elseif($after_load === true){
                $this->registerAfterLoadCommand($result);
            }
            return $result;
        }
    }
}
?>