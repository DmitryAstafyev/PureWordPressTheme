<?php
namespace Pure\Components\PostTypes\Events\Module{
    class InitializationFormat{
        public function make(){
            $labels = array(
                'name'                  => 'Events',
                'singular_name'         => 'Event',
                'add_new'               => 'Add new',
                'add_new_item'          => 'Add new event',
                'edit_item'             => 'Edit event',
                'new_item'              => 'New event',
                'view_item'             => 'Read event',
                'search_items'          => 'Find event',
                'not_found'             => 'No events found',
                'not_found_in_trash'    => 'No events found in the recycle',
                'parent_item_colon'     => '',
                'menu_name'             => 'Events'

            );
            $args = array(
                'labels'                => $labels,
                'public'                => true,
                'publicly_queryable'    => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'query_var'             => true,
                'rewrite'               => array( 'slug' => 'event','with_front' => FALSE),
                'capability_type'       => 'post',
                'has_archive'           => true,
                'hierarchical'          => false,
                'menu_position'         => null,
                'supports'              => array('title','editor','author','thumbnail','excerpt','comments', 'custom-fields'),
                'taxonomies'            => array('category', 'post_tag')
            );
            register_post_type('event', $args);
        }
    }
    $initializationFormat = new InitializationFormat();
    $initializationFormat->make();
    $initializationFormat = NULL;
}