<?php
namespace Pure\Components\PostTypes\Questions\Module{
    class InitializationFormat{
        private function create_taxonomy(){
            $labels = array(
                'name'              => 'Keywords',
                'singular_name'     => 'keyword',
                'search_items'      => 'Search Keywords',
                'all_items'         => 'All Keywords',
                'parent_item'       => 'Parent keyword',
                'parent_item_colon' => 'Parent keyword:',
                'edit_item'         => 'Edit keyword',
                'update_item'       => 'Update keyword',
                'add_new_item'      => 'Add New keyword',
                'new_item_name'     => 'New Genre keyword',
                'menu_name'         => 'Keywords',
            );
            // параметры
            $args = array(
                'label'                 => '',
                'labels'                => $labels,
                'public'                => true,
                'show_in_nav_menus'     => true,
                'show_ui'               => true,
                'show_tagcloud'         => true,
                'hierarchical'          => false,
                'update_count_callback' => '',
                'rewrite'               => true,
                'capabilities'          => array(),
                'meta_box_cb'           => null,
                'show_admin_column'     => false,
                '_builtin'              => false,
                'show_in_quick_edit'    => null,
            );
            register_taxonomy('keyword', array('question'), $args );
        }
        private function additions(){
            $labels = array(
                'name'                  => 'Additions to questions',
                'singular_name'         => 'Addition to question',
                'add_new'               => 'Add new',
                'add_new_item'          => 'Add new addition',
                'edit_item'             => 'Edit addition',
                'new_item'              => 'New addition',
                'view_item'             => 'Read addition',
                'search_items'          => 'Find addition',
                'not_found'             => 'No addition found',
                'not_found_in_trash'    => 'No addition found in the recycle',
                'parent_item_colon'     => '',
                'menu_name'             => 'Additions'

            );
            $args = array(
                'labels'                => $labels,
                'public'                => true,
                'publicly_queryable'    => true,
                'show_ui'               => true,
                'show_in_menu'          => 'edit.php?post_type=question',
                'exclude_from_search'   => true,
                'query_var'             => false,
                'rewrite'               => false,
                'capability_type'       => 'post',
                'has_archive'           => false,
                'hierarchical'          => false,
                'menu_position'         => null,
                'supports'              => array('title','editor','author', 'custom-fields'),
            );
            register_post_type('question_addition', $args);
        }
        public function make(){
            $labels = array(
                'name'                  => 'Questions',
                'singular_name'         => 'Question',
                'add_new'               => 'Add new',
                'add_new_item'          => 'Add new question',
                'edit_item'             => 'Edit question',
                'new_item'              => 'New question',
                'view_item'             => 'Read question',
                'search_items'          => 'Find question',
                'not_found'             => 'No question found',
                'not_found_in_trash'    => 'No question found in the recycle',
                'parent_item_colon'     => '',
                'menu_name'             => 'Questions'

            );
            $args = array(
                'labels'                => $labels,
                'public'                => true,
                'publicly_queryable'    => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'query_var'             => true,
                'rewrite'               => array( 'slug' => 'question','with_front' => FALSE),
                'capability_type'       => 'post',
                'has_archive'           => true,
                'hierarchical'          => false,
                'menu_position'         => null,
                'supports'              => array('title','editor','author','comments', 'custom-fields'),
                'taxonomies'            => array('category', 'post_tag')
            );
            register_post_type('question', $args);
            $this->additions();
            $this->create_taxonomy();
        }
    }
    $initializationFormat   = new InitializationFormat();
    $initializationFormat   ->make();
    $initializationFormat   = NULL;
}