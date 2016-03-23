<?php
namespace Pure\Components\Tools\System{
    class Core {
        public function run($command){
            if (substr(php_uname(), 0, 7) == "Windows"){
                $WshShell   = new \COM("WScript.Shell");
                $WshShell->Run('cmd /C '.$command, 0, false);
                //Key [/C] - close CMD after command will be finished
            }else {
                exec($command . " > /dev/null &");
            }
        }
    }
}
?>