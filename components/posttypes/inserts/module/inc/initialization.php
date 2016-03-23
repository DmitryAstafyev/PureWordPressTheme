<?php
namespace Pure\Components\PostTypes\Inserts\Module{
    class InitializationFormat{
        public function make(){
            $labels = array(
                'name'                  => 'Inserts',
                'singular_name'         => 'Insert',
                'add_new'               => 'Add new',
                'add_new_item'          => 'Add new insert',
                'edit_item'             => 'Edit insert',
                'new_item'              => 'New insert',
                'view_item'             => 'Read insert',
                'search_items'          => 'Find insert',
                'not_found'             => 'No inserts found',
                'not_found_in_trash'    => 'No inserts found in the recycle',
                'parent_item_colon'     => '',
                'menu_name'             => 'Inserts'

            );
            $args = array(
                'labels'                => $labels,
                'public'                => true,
                'publicly_queryable'    => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'query_var'             => false,
                'rewrite'               => array( 'slug' => 'insert','with_front' => false),
                'exclude_from_search'   =>true,
                'capability_type'       => 'post',
                'has_archive'           => false,
                'hierarchical'          => false,
                'menu_position'         => null,
                'supports'              => array('title','editor'),
                'taxonomies'            => array()
            );
            register_post_type('insert', $args);
        }
    }
    $initializationFormat = new InitializationFormat();
    $initializationFormat->make();
    $initializationFormat = NULL;
}