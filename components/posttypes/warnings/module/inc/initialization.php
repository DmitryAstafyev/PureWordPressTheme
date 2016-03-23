<?php
namespace Pure\Components\PostTypes\Warnings\Module{
    class InitializationFormat{
        private function create_taxonomy(){
            $labels = array(
                'name'              => 'Warning mark',
                'singular_name'     => 'Warning mark',
                'search_items'      => 'Search warning marks',
                'all_items'         => 'All warning marks',
                'parent_item'       => 'Parent warning mark',
                'parent_item_colon' => 'Parent warning mark:',
                'edit_item'         => 'Edit warning mark',
                'update_item'       => 'Update warning mark',
                'add_new_item'      => 'Add new warning mark',
                'new_item_name'     => 'New genre warning mark',
                'menu_name'         => 'Warning marks',
            );
            // параметры
            $args = array(
                'label'                 => '',
                'labels'                => $labels,
                'public'                => true,
                'show_in_nav_menus'     => true,
                'show_ui'               => true,
                'show_tagcloud'         => 'edit.php?post_type=warning',
                'hierarchical'          => false,
                'update_count_callback' => '',
                'rewrite'               => true,
                'capabilities'          => array(),
                'meta_box_cb'           => null,
                'show_admin_column'     => false,
                '_builtin'              => false,
                'show_in_quick_edit'    => null,
            );
            register_taxonomy('warning_mark', array('post', 'event', 'report', 'question', 'warning'), $args );
        }
        private function warnings(){
            $labels = array(
                'name'                  => 'Warning',
                'singular_name'         => 'Warning',
                'add_new'               => 'Add warning',
                'add_new_item'          => 'Add new warning',
                'edit_item'             => 'Edit warning',
                'new_item'              => 'New warning',
                'view_item'             => 'Read warning',
                'search_items'          => 'Find warning',
                'not_found'             => 'No warning found',
                'not_found_in_trash'    => 'No warning found in the recycle',
                'parent_item_colon'     => '',
                'menu_name'             => 'Warnings'

            );
            $args = array(
                'labels'                => $labels,
                'public'                => true,
                'publicly_queryable'    => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'exclude_from_search'   => true,
                'query_var'             => false,
                'rewrite'               => false,
                'capability_type'       => 'post',
                'has_archive'           => false,
                'hierarchical'          => false,
                'menu_position'         => null,
                'supports'              => array('title','editor'),
            );
            register_post_type('warning', $args);
        }
        public function make(){
            $this->warnings();
            $this->create_taxonomy();
        }
    }
    $initializationFormat   = new InitializationFormat();
    $initializationFormat   ->make();
    $initializationFormat   = NULL;
}