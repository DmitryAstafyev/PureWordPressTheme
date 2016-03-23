<?php
namespace Pure\Templates\Elements\TermsSelector{
    class B{
        public function innerHTML($post_id, $taxonomy, $form_id = '', $form_field = '', $input_attr = ''){
            $innerHTML = '';
            if ((int)$post_id > 0) {
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false) {
                    $post_author = get_post_field('post_author', $post_id);
                    if ((int)$post_author === (int)$current->ID) {
                        $all_terms              = get_terms(
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
                        $terms  = get_the_terms($post_id, $taxonomy);
                        $_terms = array();
                        if ($terms !== false){
                            foreach($terms as $term){
                                $_terms[] = (int)$term->term_id;
                            }
                        }
                        $terms = $_terms;
                        if ($all_terms !== false){
                            for($index = 0, $max_index = count($all_terms); $index < $max_index; $index ++){
                                $innerHTML .= Initialization::instance()->html(
                                    'B/term',
                                    array(
                                        array('term_name',      $all_terms[$index]->name            ),
                                        array('form_id',        $form_id                            ),
                                        array('form_field',     $form_field.'['.$index.']'          ),
                                        array('checked',        (in_array((int)$all_terms[$index]->term_id, $terms) !== false ? 'checked' : '')                         ),
                                        array('attribute',      $input_attr                         ),
                                    )
                                );
                            }
                            \Pure\Components\Styles\CheckBoxes\A\Initialization::instance()->attach();
                        }
                    }
                }
            }
            return $innerHTML;
        }
    }
}
?>