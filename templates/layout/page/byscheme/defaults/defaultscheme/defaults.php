<?php
namespace Pure\Templates\Layout\Page\ByScheme\Defaults{
    class DefaultScheme{
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
                    'pure-scheme-'.$class.'-front-top'   => array(
                        0 => array(
                            'id'        =>'puretheme_posts_presentation',
                            'settings'  => array (
                                'content'   => 'defined',
                                'targets'   => implode(',',$data->posts_for_slider),
                                'template'  => 'A',
                                'maxcount'  => '3',
                            )
                        ),
                    ),
                    'pure-scheme-'.$class.'-row-1'       => array(
                        0 => array(
                            'id'        =>'puretheme_highlights',
                            'settings'  =>array(
                                'title'         => __('Discover', 'pure'),
                                'title_type'    => 'G',
                                'template'      => 'E',
                                'icons'         =>
                                    array (
                                        0 => $data->icons[0],
                                        1 => $data->icons[1],
                                        2 => $data->icons[2],
                                        3 => $data->icons[3],
                                    ),
                                'titles'        =>
                                    array (
                                        0 => __('Members',   'pure'),
                                        1 => __('Top',       'pure'),
                                        2 => __('Groups',    'pure'),
                                        3 => __('About us',  'pure'),
                                    ),
                                'descriptions' =>
                                    array (
                                        0 => __('All our members',               'pure'),
                                        1 => __('Best posts from our members',   'pure'),
                                        2 => __('All groups on our site',        'pure'),
                                        3 => __('A few words about project',     'pure'),
                                    ),
                                'post_ids'      =>
                                    array (
                                        0 => '-1',
                                        1 => '-1',
                                        2 => '-1',
                                        3 => '-1',
                                    ),
                                'page_ids'      =>
                                    array (
                                        0 => '-1',
                                        1 => '-1',
                                        2 => '-1',
                                        3 => '-1',
                                    ),
                                'urls' =>
                                    array (
                                        0 => get_site_url().'/members',
                                        1 => get_site_url().'/SPECIAL/TOP',
                                        2 => get_site_url().'/groups',
                                        3 => get_site_url(),
                                    ),
                            )
                        )
                    ),
                    'pure-scheme-'.$class.'-column-1'    => array(
                        0 => array(
                            'id'        =>'puretheme_posts_thumbnails',
                            'settings'  => array (
                                'title'             => '',
                                'title_type'        => '',
                                'content'           => 'last',
                                'post_type'         => 'post',
                                'hidetitle'         => '',
                                'targets'           => '',
                                'maxcount'          => '10',
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
                    'pure-scheme-'.$class.'-column-2'    => array(
                        0 => array(
                            'id'        =>'puretheme_authors_thumbnails',
                            'settings'  => array (
                                'title'                 => __('Most creative', 'pure'),
                                'title_type'            => 'C',
                                'template'              => 'B',
                                'content'               => 'users_creative',
                                'targets'               => '',
                                'maxcount'              => '7',
                                'only_with_avatar'      => '',
                                'top'                   => '',
                                'displayed'             => '',
                                'profile'               => '',
                                'days'                  => '3650',
                                'from_date'             => '',
                                'more'                  => '',
                                'templates_settings'    => '',
                            ),
                        ),
                        1 => array(
                            'id'        =>'puretheme_authors_thumbnails',
                            'settings'  => array (
                                'title'                 => __('Most active', 'pure'),
                                'title_type'            => 'C',
                                'template'              => 'B',
                                'content'               => 'users_active',
                                'targets'               => '',
                                'maxcount'              => '7',
                                'only_with_avatar'      => '',
                                'top'                   => '',
                                'displayed'             => '',
                                'profile'               => '',
                                'days'                  => '3650',
                                'from_date'             => '',
                                'more'                  => '',
                                'templates_settings'    => '',
                            )
                        ),
                    ),
                    'pure-scheme-'.$class.'-row-2'       => array(
                        0 => array(
                            'id'        =>'puretheme_groups_thumbnails',
                            'settings'  => array (
                                'title'             => __('Groups', 'pure'),
                                'template'          => 'E',
                                'title_type'        => 'G',
                                'content'           => 'last',
                                'targets'           => '',
                                'maxcount'          => '8',
                                'only_with_avatar'  => '',
                                'top'               => 'on',
                                'displayed'         => 'none',
                                'days'              => '3650',
                                'from_date'         => '',
                                'show_content'      => '',
                                'show_admin_part'   => '',
                                'show_life'         => '',
                                'more'              => '',
                            )
                        ),
                    ),
                    'pure-'.$class.'-row-3'              => array(
                        0 => array(
                            'id'        =>'puretheme_inserts',
                            'settings'  => array (
                                'title'         => '',
                                'title_type'    => 'A',
                                'target'        => $data->inserts[0],
                                'template'      => 'A',
                            )
                        ),
                    ),
                    'pure-'.$class.'-row-4'              => array(
                        0 => array(
                            'id'        =>'puretheme_posts_thumbnails',
                            'settings'  => array (
                                'title'             => '',
                                'title_type'        => '',
                                'content'           => 'last',
                                'post_type'         => 'report',
                                'hidetitle'         => '',
                                'targets'           => '',
                                'maxcount'          => '6',
                                'thumbnails'        => '',
                                'profile'           => '',
                                'days'              => '3650',
                                'from_date'         => '',
                                'template'          => 'ReportA',
                                'displayed'         => 'none',
                                'slider_template'   => '',
                                'tab_template'      => '',
                                'presentation'      => 'wrapper',
                                'tabs_columns'      => '',
                                'wrapper_width'     => '30',
                                'wrapper_space'     => '1',
                                'more'              => '',
                            )
                        ),
                    ),
                    'pure-'.$class.'-row-5'              => array(
                        0 => array(
                            'id'        =>'puretheme_inserts',
                            'settings'  => array (
                                'title'         => '',
                                'title_type'    => 'A',
                                'target'        => $data->inserts[0],
                                'template'      => 'A',
                            )
                        ),
                    ),
                    'pure-'.$class.'-row-6'              => array(
                        0 => array(
                            'id'        =>'puretheme_posts_thumbnails',
                            'settings'  => array (
                                'title'             => '',
                                'title_type'        => '',
                                'content'           => 'last',
                                'post_type'         => 'event',
                                'hidetitle'         => '',
                                'targets'           => '',
                                'maxcount'          => '4',
                                'thumbnails'        => '',
                                'profile'           => '',
                                'days'              => '3650',
                                'from_date'         => '',
                                'template'          => 'EventA',
                                'displayed'         => 'none',
                                'slider_template'   => '',
                                'tab_template'      => '',
                                'presentation'      => 'wrapper',
                                'tabs_columns'      => '',
                                'wrapper_width'     => '50',
                                'wrapper_space'     => '1',
                                'more'              => '',
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