<?php
namespace Pure\Templates\Layout\Page\Container{
    class A{
        public function before_content($echo = true){
            \Pure\Components\WordPress\PageBackground\Initialization::instance()->attach();
            $BackgroundPage     = new \Pure\Components\WordPress\PageBackground\Core();
            $background         = $BackgroundPage->get_background_url();
            $BackgroundPage     = NULL;
            $innerHTML          = Initialization::instance()->html(
                'A/before_content',
                array(
                    array('background_url', ($background !== false ? $background : '')),
                )
            );
            if ($echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
        public function after_content($echo = true){
            $innerHTML = '';
            if ($echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
        public function before_footer($echo = true){
            $innerHTML      = Initialization::instance()->html(
                'A/after_content_before_footer',
                array()
            );
            if ($echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
        public function after_footer($echo = true){
            $innerHTML = '';
            if ($echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
        public function before_sidebar($echo = true){
            $innerHTML      = Initialization::instance()->html(
                'A/after_footer_before_sidebar',
                array()
            );
            if ($echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
        public function after_sidebar($echo = true){
            $innerHTML = Initialization::instance()->html(
                'A/after_sidebar',
                array()
            );
            if ($echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
    }
}
?>