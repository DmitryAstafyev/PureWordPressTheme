<?php
namespace Pure\Components\PostTypes\Warnings\Module{
    class Provider{
        public function getWarningsForPost($post_id){
            $result = array();
            if ((int)$post_id > 0){
                $marks = wp_get_post_terms($post_id, 'warning_mark');
                if (is_array($marks) !== false){
                    foreach($marks as $mark){
                        $args       = array(
                            'posts_per_page'    => 1,
                            'post_type'         => 'warning',
                            'post_status'       => 'publish',
                            'tax_query'         => array(
                                array(
                                    'taxonomy'  => 'warning_mark',
                                    'field'     => 'slug',
                                    'terms'     => $mark->slug
                                )
                            )
                        );
                        $warning    = get_posts( $args );
                        if (is_array($warning) !== false){
                            if (count($warning) === 1){
                                $result[] = (object)array(
                                    'title'     =>$warning[0]->post_title,
                                    'content'   =>$warning[0]->post_content,
                                );
                            }
                        }
                    }
                }
            }
            return (count($result) > 0 ? $result : false);
        }
    }
}