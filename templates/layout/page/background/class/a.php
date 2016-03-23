<?php
namespace Pure\Templates\Layout\Page\Background{
    class A{
        public function background($background_url, $echo = false){
            $innerHTML      = Initialization::instance()->html(
                'A/wrapper',
                array(
                    array('background_url', \Pure\Configuration::instance()->url($background_url)),
                )
            );
            if ($echo !== false){
                echo $innerHTML;
            }
            \Pure\Components\Effects\Parallax\Initialization::instance()->attach();
            return $innerHTML;
        }
    }
}
?>