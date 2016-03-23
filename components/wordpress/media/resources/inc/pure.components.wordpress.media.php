<?php
namespace Pure\Components\WordPress\Media\Resources{
    if (did_action( 'wp_enqueue_media' ) === 0 ) {
        wp_enqueue_media();
    }
    /*
    if (class_exists('WP_Embed') === false){
        require_once(\Pure\Configuration::instance()->root.'\wp-includes\class-wp-embed.php');
    }
    if (class_exists('WP_oEmbed') === false){
        require_once(\Pure\Configuration::instance()->root.'\wp-includes\class-oembed.php');
    }
    */
}
?>