<?php
namespace Pure\Templates\Layout\Page\ByScheme\Defaults{
    class Community{
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
                    'pure-'.$class.'-front-top'     => array(
                        0 => array(
                            'id'        =>'puretheme_search',
                            'settings'  => array (
                                'title'         => __('Our community', 'pure'),
                                'background'    => $data->attachments[0],
                                'template'      => 'B',
                            )
                        ),
                    ),
                    'pure-'.$class.'-row-1'         => array(
                        0 => array(
                            'id'        =>'puretheme_inserts',
                            'settings'  => array (
                                'title'         => '',
                                'title_type'    => '',
                                'target'        => $data->inserts[0],
                                'template'      => 'C',
                            )
                        ),
                        1 => array(
                            'id'        =>'puretheme_highlights',
                            'settings'  =>array(
                                'title'         => '',
                                'title_type'    => '',
                                'template'      => 'B',
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
                    'pure-'.$class.'-row-2'         => array(
                        0 => array(
                            'id'        =>'puretheme_posts_thumbnails',
                            'settings'  => array (
                                'title'             => __('Last on site','pure'),
                                'title_type'        => 'G',
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
                    'pure-'.$class.'-row-3'         => array(
                        0 => array(
                            'id'        =>'puretheme_authors_thumbnails',
                            'settings'  => array (
                                'title'                 => __('Most creative', 'pure'),
                                'title_type'            => 'G_for_dark',
                                'template'              => 'C',
                                'content'               => 'users_creative',
                                'targets'               => '',
                                'maxcount'              => 4,
                                'only_with_avatar'      => '',
                                'top'                   => '',
                                'displayed'             => '',
                                'profile'               => '',
                                'days'                  => '3650',
                                'from_date'             => '',
                                'more'                  => '',
                                'templates_settings'    => '',
                                'wrapper'               => true,
                                'min_width'             => 300,
                            ),
                        ),
                    ),
                    'pure-'.$class.'-row-4'         => array(
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
                    'pure-'.$class.'-row-5'         => array(
                        0 => array(
                            'id'        =>'puretheme_posts_thumbnails',
                            'settings'  => array (
                                'title'             => __('Nearest events', 'pure'),
                                'title_type'        => 'G',
                                'content'           => 'last',
                                'post_type'         => 'event',
                                'hidetitle'         => '',
                                'targets'           => '',
                                'maxcount'          => '8',
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
                                'wrapper_width'     => '30',
                                'wrapper_space'     => '1',
                                'more'              => '',
                            )
                        ),
                        1 => array(
                            'id'        =>'puretheme_posts_thumbnails',
                            'settings'  => array (
                                'title'             => __('Last reports', 'pure'),
                                'title_type'        => 'G',
                                'content'           => 'last',
                                'post_type'         => 'report',
                                'hidetitle'         => '',
                                'targets'           => '',
                                'maxcount'          => '8',
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
                    'pure-'.$class.'-row-6'         => array(
                        0 => array(
                            'id'        =>'puretheme_posts_thumbnails',
                            'settings'  => array (
                                'title'             => __('Solved questions', 'pure'),
                                'title_type'        => 'G_for_dark',
                                'content'           => 'questions_solved',
                                'post_type'         => 'question',
                                'hidetitle'         => '',
                                'targets'           => '',
                                'maxcount'          => '10',
                                'thumbnails'        => '',
                                'profile'           => '',
                                'days'              => '3650',
                                'from_date'         => '',
                                'template'          => 'QuestionA',
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
                        1 => array(
                            'id'        =>'puretheme_posts_thumbnails',
                            'settings'  => array (
                                'title'             => __('Padding for solution', 'pure'),
                                'title_type'        => 'G_for_dark',
                                'content'           => 'questions_unsolved',
                                'post_type'         => 'question',
                                'hidetitle'         => '',
                                'targets'           => '',
                                'maxcount'          => '12',
                                'thumbnails'        => '',
                                'profile'           => '',
                                'days'              => '3650',
                                'from_date'         => '',
                                'template'          => 'QuestionB',
                                'displayed'         => 'none',
                                'slider_template'   => '',
                                'tab_template'      => '',
                                'presentation'      => 'wrapper',
                                'tabs_columns'      => '',
                                'wrapper_width'     => '40',
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
                            'id'        =>'puretheme_authors_thumbnails',
                            'settings'  => array (
                                'title'                 => '',
                                'title_type'            => '',
                                'template'              => 'OnlyAvatars',
                                'content'               => 'last',
                                'targets'               => '',
                                'maxcount'              => 20,
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
                        2 => array(
                            'id'        =>'puretheme_categories',
                            'settings'  => array (
                                'title'         => __('Categories', 'pure'),
                                'title_type'    => 'C',
                                'template'      => 'D',
                            ),
                        ),
                        3 => array(
                            'id'        =>'puretheme_openmenu',
                            'settings'  => array (
                                'title'         => __('Hot links', 'pure'),
                                'title_type'    => 'C',
                                'target'        => '-1',
                                'template'      => 'A',
                            )
                        ),
                    ),
                );
            }
        }
    }
}