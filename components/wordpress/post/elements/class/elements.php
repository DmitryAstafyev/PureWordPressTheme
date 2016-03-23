<?php
namespace Pure\Components\WordPress\Post\Elements{
    class Setup{
        private $post           = false;
        private $unautopMark    = array(
            'mark'      =>'<!-- Pure.Theme.Mark:: NO WPAUTOP -->',
            'pattern'   =>'\<\!-- Pure\.Theme\.Mark\:\: NO WPAUTOP --\>',
        );
        public function unautopFilter($pee, $br = true){
            $result = preg_match('/'.$this->unautopMark['pattern'].'/', $pee);
            if ($result === 0){
                return wpautop($pee, $br = true);
            }else{
                return $pee;
            }
        }
        public function handleGallery($output, $attr){
            $post = $this->post;
            static $instance = 0;
            $instance++;
            if ( ! empty( $attr['ids'] ) ) {
                // 'ids' is explicitly ordered, unless you specify otherwise.
                if ( empty( $attr['orderby'] ) ) {
                    $attr['orderby'] = 'post__in';
                }
                $attr['include'] = $attr['ids'];
            }
            $html5  = current_theme_supports( 'html5', 'gallery' );
            $atts   = shortcode_atts( array(
                'order'      => 'ASC',
                'orderby'    => 'menu_order ID',
                'id'         => $post ? $post->ID : 0,
                'itemtag'    => $html5 ? 'figure'     : 'dl',
                'icontag'    => $html5 ? 'div'        : 'dt',
                'captiontag' => $html5 ? 'figcaption' : 'dd',
                'columns'    => 3,
                'size'       => 'thumbnail',
                'include'    => '',
                'exclude'    => '',
                'link'       => ''
            ), $attr, 'gallery' );
            $id = intval( $atts['id'] );
            if ( ! empty( $atts['include'] ) ) {
                $_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
                $attachments = array();
                foreach ( $_attachments as $key => $val ) {
                    $attachments[$val->ID] = $_attachments[$key];
                }
            } elseif ( ! empty( $atts['exclude'] ) ) {
                $attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
            } else {
                $attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
            }
            if ( empty( $attachments ) ) {
                return '';
            }
            $galley_items = (object)array(
                'large'     =>array(),
                'thumbnail' =>array(),
            );
            foreach ( $attachments as $id => $attachment ) {
                $image_data     = wp_get_attachment_image_src( $id, 'large'     );
                $thumbnail_data = wp_get_attachment_image_src( $id, 'thumbnail' );
                if (is_array($image_data) !== false && is_array($thumbnail_data) !== false){
                    $galley_items->large[] = (object)array(
                        'src'   =>$image_data[0],
                        'width' =>$image_data[1],
                        'height'=>$image_data[2],
                    );
                    $galley_items->thumbnail[] = (object)array(
                        'src'   =>$thumbnail_data[0],
                        'width' =>$thumbnail_data[1],
                        'height'=>$thumbnail_data[2],
                    );
                }
            }
            $GalleryTemplate = \Pure\Templates\Posts\Elements\Gallery\Initialization::instance()->get('A');
            $output .= $GalleryTemplate->innerHTML(
                (object)array(
                    'items' =>$galley_items,
                    'name'  =>$post->post_title
                )
            );
            $GalleryTemplate = NULL;
            return $output;
        }
        public function handleAudio($html, $attr, $content = '', $instances = 0){
            static $instances = 0;
            $instances++;
            $post_id        = $this->post->ID;
            $audio          = null;
            $default_types  = wp_get_audio_extensions();
            $defaults_atts  = array(
                'src'      => '',
                'loop'     => '',
                'autoplay' => '',
                'preload'  => 'none'
            );
            foreach ( $default_types as $type ) {
                $defaults_atts[$type] = '';
            }
            $atts       = shortcode_atts( $defaults_atts, $attr, 'audio' );
            $primary    = false;
            if ( ! empty( $atts['src'] ) ) {
                $type = wp_check_filetype( $atts['src'], wp_get_mime_types() );
                if ( ! in_array( strtolower( $type['ext'] ), $default_types ) ) {
                    return sprintf( '<a class="wp-embedded-audio" href="%s">%s</a>', esc_url( $atts['src'] ), esc_html( $atts['src'] ) );
                }
                $primary = true;
                array_unshift( $default_types, 'src' );
            } else {
                foreach ( $default_types as $ext ) {
                    if ( ! empty( $atts[ $ext ] ) ) {
                        $type = wp_check_filetype( $atts[ $ext ], wp_get_mime_types() );
                        if ( strtolower( $type['ext'] ) === $ext ) {
                            $primary = true;
                        }
                    }
                }
            }
            $audios = get_attached_media( 'audio', $post_id );
            if ( empty( $audios ) ) {
                if ( !empty( $attr ) ) {
                    if (is_array($attr) !== false){
                        $guid   = $attr[array_keys($attr)[0]];
                        $Posts  = \Pure\Providers\Posts\Initialization::instance()->getCommon();
                        $audio  = $Posts->get_attachment_post_by_url($guid);
                        $Posts  = NULL;
                        if ($audio === false){
                            return;
                        }
                    }
                }else{
                    return;
                }
            }else{
                $audio = $audios[array_keys($audios)[$instances - 1]];
            }
            $atts['src']    = wp_get_attachment_url( $audio->ID );
            if ( empty( $atts['src'] ) ) {
                return;
            }
            array_unshift( $default_types, 'src' );
            $AudioTemplate  = \Pure\Templates\Posts\Elements\Audio\Initialization::instance()->get('A');
            $output         = $AudioTemplate->innerHTML(
                (object)array(
                    'audio' =>$audio,
                )
            );
            $AudioTemplate  = NULL;
            return $output;
        }
        public function handleEmbed($html = '', $url = '', $attr = false){
            if (!is_feed()) {
                $this->setupUnautopFilter();
                $EmbedTemplate  = \Pure\Templates\Posts\Elements\Embed\Initialization::instance()->get('A');
                $output         = $this->unautopMark['mark'];
                $output        .= $EmbedTemplate->innerHTML(
                    (object)array(
                        'innerHTML' =>$html,
                        'url'       =>$url
                    )
                );
                $EmbedTemplate  = NULL;
                $this->cancelUnautopFilter();
                return $output;
            } else {
                return $html;
            }
        }
        public function handlePlaylist($output = '', $attr = array()){
            global $content_width;
            $post = $this->post;
            static $instance = 0;
            $instance++;
            if ( ! empty( $attr['ids'] ) ) {
                // 'ids' is explicitly ordered, unless you specify otherwise.
                if ( empty( $attr['orderby'] ) ) {
                    $attr['orderby'] = 'post__in';
                }
                $attr['include'] = $attr['ids'];
            }
            $atts = shortcode_atts( array(
                'type'		=> 'audio',
                'order'		=> 'ASC',
                'orderby'	=> 'menu_order ID',
                'id'		=> $post ? $post->ID : 0,
                'include'	=> '',
                'exclude'   => '',
                'style'		=> 'light',
                'tracklist' => true,
                'tracknumbers' => true,
                'images'	=> true,
                'artists'	=> true
            ), $attr, 'playlist' );
            $id = intval( $atts['id'] );
            if ( $atts['type'] !== 'audio' ) {
                $atts['type'] = 'video';
            }
            $args = array(
                'post_status'       => 'inherit',
                'post_type'         => 'attachment',
                'post_mime_type'    => $atts['type'],
                'order'             => $atts['order'],
                'orderby'           => $atts['orderby']
            );
            if ( ! empty( $atts['include'] ) ) {
                $args['include']    = $atts['include'];
                $_attachments       = get_posts( $args );
                $attachments        = array();
                foreach ( $_attachments as $key => $val ) {
                    $attachments[$val->ID] = $_attachments[$key];
                }
            } elseif ( ! empty( $atts['exclude'] ) ) {
                $args['post_parent']    = $id;
                $args['exclude']        = $atts['exclude'];
                $attachments            = get_children( $args );
            } else {
                $args['post_parent']    = $id;
                $attachments            = get_children( $args );
            }
            if ( empty( $attachments ) ) {
                return '';
            }
            if ( !is_feed() ) {
                $AudiosTemplate = \Pure\Templates\Posts\Elements\AudioPlaylist\Initialization::instance()->get('A');
                $output         = $AudiosTemplate->innerHTML(
                    (object)array(
                        'audios' =>$attachments,
                    )
                );
                $AudiosTemplate  = NULL;
            }
            return $output;
        }
        public function handleImages($a, $attr, $content = null){
            $imageHTML = '';
            if (isset($attr['caption']) !== false && is_string($content) !== false){
                if (mb_strlen($content) > 0){
                    $Images     = new Images();
                    $imageHTML  = $Images->parseImagesFromContent($content, $this->post->ID, $attr['caption']);
                    $Images     = NULL;
                }
            }
            return $imageHTML;
        }
        public function parseImages($innerHTML){
            $Images     = new Images();
            $innerHTML  = $Images->parseImagesFromInnerHTML($innerHTML, $this->post->ID);
            $Images     = NULL;
            return $innerHTML;
        }
        private function setupUnautopFilter(){
            remove_filter   ('the_content', 'wpautop'                       );
            add_filter      ('the_content', array( $this, 'unautopFilter' ) );
        }
        private function cancelUnautopFilter(){
            remove_filter   ('the_content', array( $this, 'unautopFilter' ) );
            add_filter      ('the_content', 'wpautop'                       );
        }
        private function setupGallery(){
            add_filter("post_gallery",                  array( $this, 'handleGallery'   ),10,2);
        }
        private function setupAudio(){
            add_filter("wp_audio_shortcode_override",   array( $this, 'handleAudio'     ),10,2);
        }
        private function setupPlaylist(){
            add_filter("post_playlist",                 array( $this, 'handlePlaylist'  ),10,2);
        }
        private function setupEmbed(){
            add_filter("embed_oembed_html",             array( $this, 'handleEmbed'     ),10,2);
        }
        private function setupImages(){
            add_filter("img_caption_shortcode",         array( $this, 'handleImages'    ),10,3);
        }
        public function setup(){
            if ($this->post !== false && is_null($this->post) === false){
                //$this->setupUnautopFilter   ();
                $this->setupGallery         ();
                $this->setupAudio           ();
                $this->setupEmbed           ();
                $this->setupPlaylist        ();
                $this->setupImages          ();
                return true;
            }else{
                return false;
            }
        }
        function __construct($post){
            $this->post = (gettype($post) === 'integer' ? get_post($post) : $post);
        }
    }
    class Images{
        private function parseImageAttributes($source){
            preg_match('/href[\s\r\n]*=[\s\r\n]*"(.[^"]*)"/U',  $source, $fullSRC);
            preg_match('/src[\s\r\n]*=[\s\r\n]*"(.[^"]*)"/U',   $source, $postSRC);
            if (count($postSRC) === 2) {
                preg_match('/alignleft|aligncenter|alignright/', $source, $align);
                $fullSRC    = (count($fullSRC) === 2 ? $fullSRC[1] : false);
                $postSRC    = $postSRC[1];
                $align      = (count($align) > 0 ? $align[0] : 'aligncenter');
                $align      = ($align === 'alignleft'   ? 'left'    : $align);
                $align      = ($align === 'aligncenter' ? 'center'  : $align);
                $align      = ($align === 'alignright'  ? 'right'   : $align);
                return (object)array(
                    'postSRC'   =>$postSRC,
                    'fullSRC'   =>$fullSRC,
                    'align'     =>$align
                );
            }
            return false;
        }
        private function usePattern($innerHTML, $pattern, $post_id){
            preg_match_all(
                $pattern,
                $innerHTML,
                $matches
            );
            if (count($matches) > 0){
                $images = $matches[0];
                if (count($images) > 0){
                    $ImageTemplate = \Pure\Templates\Posts\Elements\Image\Initialization::instance()->get('A');
                    foreach($images as $image){
                        $attributes = $this->parseImageAttributes($image);
                        if ($attributes !== false){
                            $imageHTML  = $ImageTemplate->innerHTML(
                                (object)array(
                                    'postSRC'   =>$attributes->postSRC,
                                    'fullSRC'   =>$attributes->fullSRC,
                                    'align'     =>$attributes->align,
                                    'post_id'   =>$post_id
                                )
                            );
                            $innerHTML = str_replace($image, $imageHTML, $innerHTML);
                        }
                    }
                    $ImageTemplate = NULL;
                }
            }
            return $innerHTML;
        }
        public function parseImagesFromInnerHTML($innerHTML, $post_id){
            $innerHTML = $this->usePattern(
                $innerHTML,
                '/<(p)[^>]*?(\/?)>(\s|\r|\n)*<(a)[^>]*?(\/?)>(\s|\r|\n)*<(img)[^>]*?(\/?)>(\s|\r|\n)*<(\/a)>(\s|\r|\n)*<(\/p)>/U',
                $post_id
            );//
            $innerHTML = $this->usePattern(
                $innerHTML,
                '/<(p)[^>]*?(\/?)>(\s|\r|\n)*<(img)[^>]*?(\/?)>(\s|\r|\n)*<(\/p)>/U',
                $post_id
            );
            $innerHTML = $this->usePattern(
                $innerHTML,
                '/<(a)[^>]*?(\/?)>(\s|\r|\n)*<(img)[^>]*?(\/?)>(\s|\r|\n)*<(\/a)>/U',
                $post_id
            );
            $innerHTML = $this->usePattern(
                $innerHTML,
                '/<(img)(?! pure-image-inited)[^>]*?(\/?)>/U',
                $post_id
            );
            return $innerHTML;
        }
        public function parseImagesFromContent($content, $post_id, $caption = false){
            $imageHTML  = '';
            $attributes = $this->parseImageAttributes($content);
            if ($attributes !== false){
                $ImageTemplate  = \Pure\Templates\Posts\Elements\Image\Initialization::instance()->get('A');
                $arguments      = (object)array(
                    'postSRC'   =>$attributes->postSRC,
                    'fullSRC'   =>$attributes->fullSRC,
                    'align'     =>$attributes->align,
                    'post_id'   =>$post_id
                );
                if ($caption !== false){
                    $arguments->caption = $caption;
                }
                $imageHTML      = $ImageTemplate->innerHTML($arguments);
                $ImageTemplate = NULL;
            }
            return $imageHTML;
        }

    }
}
?>