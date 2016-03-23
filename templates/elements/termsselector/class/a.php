<?php
namespace Pure\Templates\Elements\TermsSelector{
    class A{
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
        private function checkSandbox($taxonomy, &$terms){
            if ($taxonomy === 'category'){
                $cache = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, array('sandbox_id'));
                if (! $sandbox_id = $cache->value){
                    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                    $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->mana->properties;
                    $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                    $sandbox_id = (int)$settings->mana_threshold_manage_categories_sandbox;
                    \Pure\Components\Tools\Cache\Cache::set($cache->key, $sandbox_id);
                }
                $terms = array_filter(
                    $terms,
                    function($term) use ($sandbox_id){
                        return ((int)$term->term_id !== (int)$sandbox_id ? true : false);
                    }
                );
            }
        }
        public function innerHTML($taxonomy, $title = '', $form_id = '', $form_field = '', $input_attr = ''){
            $instance_id            = uniqid();
            $innerHTMLTerms         = '';
            $innerHTMLTermsList     = '';
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
            $this->checkSandbox($taxonomy, $all_terms);
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
                    array('title',          $title                          ),
                    array('terms_list',     $innerHTMLTermsList             ),
                    array('instance_id',    $instance_id                    ),
                    array('terms',          $innerHTMLTerms                 ),
                    array('new_item_label', __('new term', 'pure')          ),
                    array('close',          __('close', 'pure')             ),
                )
            );
            \Pure\Components\Multiitems\Initialization::instance()->attach(false, 'after');
            return $innerHTML;
        }
    }
}
?>