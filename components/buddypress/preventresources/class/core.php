<?php
namespace Pure\Components\BuddyPress\PreventResources{
    class Core{
        static function init(){
            add_action(
                'wp_enqueue_scripts',
                array( '\\Pure\\Components\\BuddyPress\\PreventResources\\Core', 'wp_enqueue_scripts'),
                100,
                5
            );
            add_action(
                'wp_enqueue_style',
                array( '\\Pure\\Components\\BuddyPress\\PreventResources\\Core', 'wp_enqueue_style'),
                100,
                5
            );
        }
        static function wp_enqueue_scripts($handle, $src = false, $deps = array(), $ver = false, $in_footer = false) {
            if (is_admin() === false){
                global $wp_scripts;
                $url = site_url().'/wp-content/plugins/buddypress';
                if ($handle === '' || $handle === false){
                    foreach($wp_scripts->queue as $_handle){
                        if (strpos($wp_scripts->registered[$_handle]->src, $url) !== false){
                            wp_dequeue_script($_handle);
                        }
                    }
                }else{
                    $_src   = ($src !== false ? $src : ($handle !== '' ? $wp_scripts->registered[$handle]->src : ''));
                    if (strpos($_src, $url) !== false){
                        wp_dequeue_script($handle);
                    }
                }
            }
        }
        static function wp_enqueue_style($handle, $src = false, $deps = array(), $ver = false, $media = 'all') {
            if (is_admin() === false){
                global $wp_styles;
                $url = site_url().'/wp-content/plugins/buddypress';
                if ($handle === '' || $handle === false){
                    foreach($wp_styles->queue as $_handle){
                        if (strpos($wp_styles->registered[$_handle]->src, $url) !== false){
                            wp_dequeue_style($_handle);
                        }
                    }
                }else{
                    $_src   = ($src !== false ? $src : ($handle !== '' ? $wp_styles->registered[$handle]->src : ''));
                    if (strpos($_src, $url) !== false){
                        wp_dequeue_style($handle);
                    }
                }
            }
        }
    }
}
?>