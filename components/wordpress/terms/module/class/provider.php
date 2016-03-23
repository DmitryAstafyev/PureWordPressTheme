<?php
namespace Pure\Components\WordPress\Terms\Module{
    class Provider{
        //$terms = array('keyword', 'keyword', ... 'keyword')
        //$taxonomy = 'keyword' || 'post_tag' ...
        public function update($terms, $taxonomy){
            if (is_array($terms) !== false){
                //Get all
                $all        = get_terms(
                    $taxonomy,
                    array(
                        'orderby'       => 'name',
                        'order'         => 'ASC',
                        'hide_empty'    => true,
                        'fields'        => 'all',
                        'hierarchical'  => true,
                        'child_of'      => 0,
                        'get'           => 'all',
                        'pad_counts'    => false,
                        'cache_domain'  => 'core',
                        'childless'     => false,
                    )
                );
                $exiting    = array();
                foreach($all as $term){
                    $exiting[] = $term->name;
                }
                //Get new
                foreach($terms as $term){
                    if (in_array($term, $exiting) === false){
                        wp_insert_term($term, $taxonomy);
                    }
                }
            }
        }
        public function attach($post_id, $terms, $taxonomy, $replace = true){
            if ((int)$post_id > 0 && is_array($terms) !== false){
                if (count($terms) > 0){
                    $terms = array_filter(
                        $terms,
                        function($term) use ($taxonomy){
                            return (term_exists((int)$term, $taxonomy ) === false ? false : true);
                        }
                    );
                    $result = wp_set_post_terms(
                        (int)$post_id,
                        $terms,
                        $taxonomy,
                        ($replace === false ? true : false)
                    );
                }else{
                    $result = wp_delete_object_term_relationships($post_id, $taxonomy);
                }
                if ($result !== false && is_wp_error($result) === false){
                    return true;
                }
            }
            return false;
        }
    }
}