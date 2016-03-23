<?php
namespace Pure\Templates\Posts\Elements\TermsEditor{
    class A{
        private function innerHTMLTerm($instance_id, $name, $id, $count, $form_id, $form_field, $input_attr){
            return Initialization::instance()->html(
                'A/term',
                array(
                    array('name',           $name                       ),
                    array('id',             $id                         ),
                    array('count',          $count                      ),
                    array('form_id',        $form_id                    ),
                    array('form_field',     $form_field.'['.$id.']'     ),
                    array('attribute',      $input_attr                 ),
                    array('instance_id',    $instance_id                ),
                )
            );
        }
        private function innerHTMLTermInList($name, $id, $count, $instance_id){
            return Initialization::instance()->html(
                'A/term_list',
                array(
                    array('name',           $name           ),
                    array('id',             $id             ),
                    array('count',          $count          ),
                    array('instance_id',    $instance_id    ),
                )
            );
        }
        public function innerHTML($post_id, $taxonomy, $form_id = '', $form_field = '', $input_attr = ''){
            $innerHTML = '';
            if ((int)$post_id > 0){
                $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                $current        = $WordPress->get_current_user();
                $WordPress      = NULL;
                if ($current !== false){
                    $post_author = get_post_field( 'post_author', $post_id );
                    if ((int)$post_author === (int)$current->ID){
                        $instance_id            = uniqid();
                        $innerHTMLTerms      = '';
                        $innerHTMLTermsList  = '';
                        $terms               = get_the_terms($post_id, $taxonomy);
                        $all_terms           = get_terms(
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
                        if ($terms !== false){
                            foreach($terms as $term){
                                $innerHTMLTerms .= $this->innerHTMLTerm(
                                    $instance_id,
                                    mb_strtolower($term->name),
                                    $term->term_id,
                                    $term->count,
                                    $form_id,
                                    $form_field,
                                    $input_attr
                                );
                            }
                        }
                        $innerHTMLTerms .= Initialization::instance()->html(
                            'A/term_template',
                            array(
                                array('instance_id',    $instance_id            ),
                                array('form_id',        $form_id                ),
                                array('form_field',     $form_field.'[[index]]' ),
                                array('attribute',      $input_attr             ),
                            )
                        );
                        if ($all_terms !== false){
                            foreach($all_terms as $term){
                                $innerHTMLTermsList .= $this->innerHTMLTermInList(
                                    mb_strtolower($term->name),
                                    $term->term_id,
                                    $term->count,
                                    $instance_id
                                );
                            }
                        }
                        $innerHTML = Initialization::instance()->html(
                            'A/wrapper',
                            array(
                                array('terms_list',     $innerHTMLTermsList             ),
                                array('instance_id',    $instance_id                    ),
                                array('terms',          $innerHTMLTerms                 ),
                                array('new_item_label', __('new term', 'pure')   ),
                            )
                        );
                        \Pure\Components\Multiitems\Initialization::instance()->attach(false, 'after');
                    }
                }
            }
            return $innerHTML;
        }
    }
}
?>