<?php
namespace Pure\Components\WordPress\Post\Parser{
    class Parser{
        private $allowed_media_types = array( 'gallery', 'audio', 'video', 'object', 'embed', 'iframe' );
        public function get_excerpt($id = -1, $type = 'post', $length = 500, $minimal = 10){
            if ($id !== -1){
                global $wpdb;
                switch($type){
                    case 'post':
                        $excerpt = $wpdb->get_results( 'SELECT post_excerpt, post_content FROM wp_posts WHERE ID='.$id );
                        if (count($excerpt) === 1){
                            if ($excerpt[0]->post_excerpt === ''){
                                $innerHTML  = false;
                                \Pure\Components\Tools\HTMLParser\Initialization::instance()->attach(true);
                                $HTMLParser = new \Pure\Components\Tools\HTMLParser\Parser();
                                //Try get content of paragraphs
                                $paragraphs = $HTMLParser->get_tags($excerpt[0]->post_content, 'p');
                                if ($paragraphs !== false){
                                    if (count($paragraphs) > 0){
                                        $innerHTML = '';
                                        foreach($paragraphs as $paragraph){
                                            $innerHTML .= (isset($paragraph->nodeValue) === true ? preg_replace("/\[.*\]/", '', $paragraph->nodeValue) : '');
                                            if (mb_strlen($innerHTML) > $length){
                                                break;
                                            }
                                        }
                                    }
                                    $innerHTML = (mb_strlen(preg_replace("/[\s\f\r\n]/", '', $innerHTML)) > $minimal ? $innerHTML : false);
                                }
                                if ($innerHTML === false){
                                    //Try get content outside of paragraphs
                                    $innerHTML = esc_html(preg_replace("/\[.*\]/", '', $HTMLParser->get_innerHTML_outside_tags($excerpt[0]->post_content)));
                                    $innerHTML = (mb_strlen(preg_replace("/[\s\f\r\n]/", '', $innerHTML)) > $minimal ? $innerHTML : false);
                                }
                                $HTMLParser = NULL;
                                return ($innerHTML !== '' ? $innerHTML : false);
                            }else{
                                return stripcslashes($excerpt[0]->post_excerpt);
                            }
                        }else{
                            return false;
                        }
                        break;
                    case 'comment':
                        $comments = $wpdb->get_results( 'SELECT comment_content FROM wp_comments WHERE comment_approved=1 AND user_id='.$id.' ORDER BY comment_date_gmt DESC LIMIT 1' );
                        if (count($comments) === 1){
                            return esc_html(preg_replace("/\[.*\]/", '', $comments[0]->comment_content));
                        }else{
                            return false;
                        }
                        break;
                }
            }
            return false;
        }
        public function get_images($id, $max_count = 10){
            global $wpdb;
            $post = $wpdb->get_results( 'SELECT post_content FROM wp_posts WHERE ID='.$id );
            $srcs = false;
            if ($post !== false){
                if (count($post) === 1){
                    \Pure\Components\Tools\HTMLParser\Initialization::instance()->attach(true);
                    $HTMLParser = new \Pure\Components\Tools\HTMLParser\Parser();
                    $images     = $HTMLParser->get_tags($post[0]->post_content, 'img');
                    if ($images !== false){
                        if (count($images) > 0){
                            $srcs   = array();
                            $count  = 0;
                            foreach($images as $image){
                                if (method_exists($image, 'getAttribute') === true){
                                    $src = $image->getAttribute('src');
                                    $alt = $image->getAttribute('alt');
                                    if ($src !== ''){
                                        $srcs[] = (object)array('src'=>$src, 'alt'=>$alt);
                                        $count ++;
                                    }
                                }
                                if ($count >= $max_count){
                                    break;
                                }
                            }
                            $srcs = (count($srcs) > 0 ? $srcs : false);
                        }
                    }
                    $HTMLParser = NULL;
                }
            }
            return $srcs;
        }
        public function gallery($id){
            $galleries_data = false;
            $galleries      = get_post_galleries($id, false);
            $galleries      = (is_array($galleries) === true ? (count($galleries) > 0 ? $galleries : false) : false);
            if ($galleries !== false){
                $galleries_data = array();
                foreach($galleries as $gallery){
                    $IDs        = preg_split('/,/', $gallery['ids']);
                    $record     = (object)array(
                        'ids'       =>$IDs,
                        'thumbnails'=>array(),
                        'medium'    =>array(),
                        'full'      =>array(),
                    );
                    foreach ($IDs as $ID){
                        $data                   = wp_get_attachment_image_src( $ID, 'medium');
                        $record->medium[]       = (object)array('src'=>$data[0], 'width'=>$data[1], 'height'=>$data[2]);
                        $data                   = wp_get_attachment_image_src( $ID, 'full');
                        $record->full[]         = (object)array('src'=>$data[0], 'width'=>$data[1], 'height'=>$data[2]);
                        $data                   = wp_get_attachment_image_src( $ID, 'thumbnail');
                        $record->thumbnail[]    = (object)array('src'=>$data[0], 'width'=>$data[1], 'height'=>$data[2]);
                    }
                    $galleries_data[] = $record;
                }
            }
            return $galleries_data;
        }
        private function audio($id){
            $audio = get_attached_media('audio', $id);
            return (is_array($audio) === true ? (count($audio) > 0 ? $audio : false) : false);
        }
        private function video($id){
            $video = get_attached_media('video', $id);
            return (is_array($video) === true ? (count($video) > 0 ? $video : false) : false);
        }
        public function get_media($id){
            global $wpdb;
            $post   = $wpdb->get_results( 'SELECT post_content FROM wp_posts WHERE ID='.$id );
            $media  = new \stdClass();
            foreach($this->allowed_media_types as $media_type){
                $media->$media_type = false;
            }
            if ($post !== false) {
                if (count($post) === 1){
                    $post       = $post[0];
                    $pattern    = get_shortcode_regex();
                    if (preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) > 0 && array_key_exists( 2, $matches ) ){
                        for($index = 0; $index < count($matches[2]); $index ++){
                            $media_type = $matches[2][$index];
                            switch($media_type){
                                case 'gallery':
                                    if ($media->$media_type === false){
                                        $media->$media_type = $this->gallery($id);
                                    }
                                    break;
                                case 'audio':
                                    if ($media->$media_type === false){
                                        $media->$media_type = $this->audio($id);
                                    }
                                    break;
                                case 'playlist':
                                    //playlist is like audio, so, just parse it like audio
                                    if ($media->audio === false){
                                        $media->audio = $this->audio($id);
                                    }
                                    break;
                                case 'video':
                                    if ($media->$media_type === false){
                                        $media->$media_type = $this->video($id);
                                    }
                                    break;
                                case 'embed':
                                    $media->$media_type = (is_array($media->$media_type) === true ? $media->$media_type : array());
                                    array_push($media->$media_type, $matches[5][$index]);
                                    break;
                            }
                        }
                    }
                    foreach($this->allowed_media_types as $media_type){
                        $media->$media_type = (count($media->$media_type) === 0 ? false : $media->$media_type);
                    }
                }
            }
            return $media;
        }
    }
}
?>