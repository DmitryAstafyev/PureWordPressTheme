<?php
namespace Pure\Components\PostTypes\Reports\Module{
    class InitializationFormat{
        public function make(){
            $labels = array(
                'name'                  => 'Reports',
                'singular_name'         => 'Report',
                'add_new'               => 'Add new',
                'add_new_item'          => 'Add new report',
                'edit_item'             => 'Edit report',
                'new_item'              => 'New report',
                'view_item'             => 'Read report',
                'search_items'          => 'Find report',
                'not_found'             => 'No reports found',
                'not_found_in_trash'    => 'No reports found in the recycle',
                'parent_item_colon'     => '',
                'menu_name'             => 'Reports'

            );
            $args = array(
                'labels'                => $labels,
                'public'                => true,
                'publicly_queryable'    => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'query_var'             => true,
                'rewrite'               => array( 'slug' => 'report','with_front' => FALSE),
                'capability_type'       => 'post',
                'has_archive'           => true,
                'hierarchical'          => false,
                'menu_position'         => null,
                'supports'              => array('title','editor','author','thumbnail','excerpt','comments', 'custom-fields'),
                'taxonomies'            => array('category', 'post_tag')
            );
            register_post_type('report', $args);
        }
    }
    class Defaults{
        public function check(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            \Pure\Components\WordPress\Settings\Instance::instance()->reload();
            $properties = \Pure\Components\WordPress\Settings\Instance::instance()->settings->reports->properties;
            if ($properties->collections->value === ''){
                $collections = array(
                    0 => (object)array(
                        'name'      =>__('Restaurants, cafes, food', 'pure'),
                        'indexes'   =>array(
                            0=>(object)array(
                                'name'  =>__('Food', 'pure'),
                                'max'   =>10,
                            ),
                            1=>(object)array(
                                'name'  =>__('Service', 'pure'),
                                'max'   =>10,
                            ),
                            2=>(object)array(
                                'name'  =>__('Atmosphere', 'pure'),
                                'max'   =>10,
                            ),
                            3=>(object)array(
                                'name'  =>__('Location', 'pure'),
                                'max'   =>10,
                            ),
                        )
                    ),
                    1 => (object)array(
                        'name'      =>__('Movie', 'pure'),
                        'indexes'   =>array(
                            0=>(object)array(
                                'name'  =>__('Screenplay', 'pure'),
                                'max'   =>10,
                            ),
                            1=>(object)array(
                                'name'  =>__('Direction', 'pure'),
                                'max'   =>10,
                            ),
                            2=>(object)array(
                                'name'  =>__('Entertainment', 'pure'),
                                'max'   =>10,
                            ),
                            3=>(object)array(
                                'name'  =>__('Actors', 'pure'),
                                'max'   =>10,
                            ),
                        )
                    )
                );
                $collections                    = @serialize($collections);
                $collections                    = base64_encode($collections);
                $Settings                       = new \Pure\Components\WordPress\Settings\Settings();
                $Settings->try_save_by_name('reports', 'collections', $collections);
                $Settings                       = NULL;
                \Pure\Components\WordPress\Settings\Instance::instance()->reload();
            }
        }
    }
    $initializationFormat   = new InitializationFormat();
    $initializationFormat   ->make();
    $initializationFormat   = NULL;
    $defaults               = new Defaults();
    $defaults               ->check();
    $defaults               = NULL;
}