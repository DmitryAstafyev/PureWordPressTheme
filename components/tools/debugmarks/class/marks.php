<?php
namespace Pure\Components\Tools\DebugMarks{
    class Marks{
        private         $last_place;
        static private  $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        public function open($place, $echo_message = true){
            if (defined('WP_DEBUG') === true){
                if (WP_DEBUG === true){
                    $full_message       = "<!--[PHP DEBUG INFO][BEGIN]::: ".$place."============================ -->";
                    $this->last_place   = $place;
                    if ($echo_message === true){
                        echo $full_message;
                    }else{
                        return $full_message;
                    }
                }
            }
        }
        public function close($place = false, $echo_message = true){
            if (defined('WP_DEBUG') === true){
                if (WP_DEBUG === true){
                    $full_message       = "<!--[PHP DEBUG INFO] [END]::: ".($place === false ? $this->last_place : $place)."============================ -->";
                    if ($echo_message === true){
                        echo $full_message;
                    }else{
                        return $full_message;
                    }
                }
            }
        }
    }
}
?>