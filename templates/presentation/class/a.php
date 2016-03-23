<?php
namespace Pure\Templates\Presentation{
    class A{
        public function get($posts){
            $innerHTML = '';
            if (is_array($posts) !== false){
                if (count($posts) > 0){
                    $innerHTMLItems = '';
                    foreach($posts as $post){
                        if (has_post_thumbnail($post->post->id) !== false && $post->post->excerpt !== ''){
                            $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->post->id), 'full');
                            if (is_array($thumbnail) !== false){
                                $innerHTMLItems .= Initialization::instance()->html(
                                    'A/item',
                                    array(
                                        array('style',          ($innerHTMLItems === '' ? '' : 'display:none;') ),
                                        array('thumbnail_url',  $thumbnail[0]                                   ),
                                        array('title',          $post->post->title                              ),
                                        array('author',         $post->author->name                             ),
                                        array('excerpt',        $post->post->excerpt                            ),
                                        array('read_url',       $post->post->url                                ),
                                    )
                                );
                            }
                        }
                    }
                    if ($innerHTMLItems !== ''){
                        $innerHTML = Initialization::instance()->html(
                            'A/wrapper',
                            array(
                                array('items',          $innerHTMLItems                 ),
                                array('previous',       __('previous', 'pure')   ),
                                array('read',           __('read', 'pure')       ),
                                array('next',           __('next', 'pure')       ),
                            )
                        );
                    }
                }
            }
            return $innerHTML;
        }
    }
}
?>