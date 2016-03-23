<?php
namespace Pure\Templates\Layout\Page\ByScheme\Defaults{
    class Journal{
        public $sidebars;
        private function getClassName(){
            $parts = explode('\\', __CLASS__);
            return strtolower($parts[count($parts) - 1]);
        }
        function __construct(){
            \Pure\Components\Demo\Minimal\Initialization::instance()->attach();
            $Demo   = new \Pure\Components\Demo\Minimal\Core();
            $data   = $Demo->getDemoData();
            $Demo   = NULL;
            $class  = $this->getClassName();
            if ($data !== false){
                $this->sidebars = array(
                    'pure-'.$class.'-front-top'   => array(
                        0 => array(
                            'id'        =>'puretheme_posts_thumbnails',
                            'settings'  => array (
                                'title'             => '',
                                'title_type'        => '',
                                'content'           => 'popular',
                                'post_type'         => 'all',
                                'hidetitle'         => '',
                                'targets'           => '',
                                'maxcount'          => '20',
                                'thumbnails'        => '',
                                'profile'           => '',
                                'days'              => '3650',
                                'from_date'         => '',
                                'template'          => 'SliderC',
                                'displayed'         => 'none',
                                'slider_template'   => 'C',
                                'tab_template'      => '',
                                'presentation'      => 'slider',
                                'tabs_columns'      => '',
                                'wrapper_width'     => '40',
                                'wrapper_space'     => '1',
                                'more'              => '',
                            )
                        ),
                    ),
                    'pure-'.$class.'-row-1'              => array(
                        0 => array(
                            'id'        =>'puretheme_quotes',
                            'settings'  => array (
                                'title'         => '',
                                'title_type'    => '',
                                'target'        => '',
                                'template'      => 'C',
                                'random'        => true,
                                'displayed'     => '',
                            )
                        ),
                    ),
                    'pure-'.$class.'-row-2'              => array(
                        0 => array(
                            'id'        =>'puretheme_posts_thumbnails',
                            'settings'  => array (
                                'title'             => '',
                                'title_type'        => '',
                                'content'           => 'last',
                                'post_type'         => 'post',
                                'hidetitle'         => '',
                                'targets'           => '',
                                'maxcount'          => '20',
                                'thumbnails'        => '',
                                'profile'           => '',
                                'days'              => '3650',
                                'from_date'         => '',
                                'template'          => 'F',
                                'displayed'         => 'none',
                                'slider_template'   => '',
                                'tab_template'      => '',
                                'presentation'      => 'wrapper',
                                'tabs_columns'      => '',
                                'wrapper_width'     => '23',
                                'wrapper_space'     => '1',
                                'more'              => '',
                            )
                        ),
                    ),
                    'pure-'.$class.'-row-3'              => array(
                        0 => array(
                            'id'        =>'puretheme_search',
                            'settings'  => array (
                                'title'         => '',
                                'background'    => $data->attachments[1],
                                'template'      => 'A',
                            )
                        ),
                    ),
                    'pure-'.$class.'-footer'             => array(
                        0 => array(
                            'id'        =>'puretheme_inserts',
                            'settings'  => array (
                                'title'         => '',
                                'title_type'    => 'B',
                                'target'        => $data->inserts[0],
                                'template'      => 'A',
                            )
                        ),
                        1 => array(
                            'id'        =>'puretheme_categories',
                            'settings'  => array (
                                'title'         => __('Categories', 'pure'),
                                'title_type'    => 'C',
                                'template'      => 'D',
                            ),
                        ),
                        2 => array(
                            'id'        =>'puretheme_openmenu',
                            'settings'  => array (
                                'title'         => __('Hot links', 'pure'),
                                'title_type'    => 'C',
                                'target'        => '-1',
                                'template'      => 'A',
                            )
                        ),
                        3 => array(
                            'id'        =>'puretheme_tags',
                            'settings'  => array (
                                'title'         => __('Tags', 'pure'),
                                'title_type'    => 'C',
                                'template'      => 'D',
                            )
                        ),
                    ),
                );
            }
        }
    }
}