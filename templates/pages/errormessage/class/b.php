<?php
namespace Pure\Templates\Pages\ErrorMessage{
    class B{
        public function innerHTML($title, $message, $echo = false){
            \Pure\Configuration::instance()->globals->ErrorMessage = true;
            $innerHTML      = Initialization::instance()->html(
                'B/wrapper',
                array(
                    array('image',      Initialization::instance()->configuration->urls->images.'/B/denied.png'),
                    array('title',      $title),
                    array('message',    $message),
                )
            );
            if ($echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
    }
}
?>