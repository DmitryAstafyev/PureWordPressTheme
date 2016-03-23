<?php
namespace Pure\Components\WordPress\Sidebars{
    class Registration{
        //(!)Use only LOWER CASE in IDs of sidebars (!)
        public $positions   = array(
            'SPECIAL'   =>array(
                'DRAFTS'            =>'special-drafts',
                'GROUPCONTENT'      =>'special-group-content',
                'STREAM'            =>'special-stream',
                'TOP'               =>'special-top',
                'SEARCH'            =>'special-search',
                'ASEARCH'           =>'special-search',
                'CREATEPOST'        =>'post-editor',
                'CREATEEVENT'       =>'post-editor',
                'CREATEREPORT'      =>'post-editor',
                'CREATEQUESTION'    =>'post-editor',
                'EDITPOST'          =>'event-editor',
                'EDITEVENT'         =>'event-editor',
                'EDITREPORT'        =>'post-editor',
                'EDITQUESTION'      =>'post-editor',
            ),
            'BUDDY'     =>array(
                'member::activities'=>'buddypress-member-personal',
                'member::profile'   =>'buddypress-member-personal',
                'member::groups'    =>'buddypress-member-page',
                'member::friends'   =>'buddypress-member-page',
                'groups::group'     =>'buddypress-group-page',
                'members'           =>'buddypress-allmembers-page',
                'groups'            =>'buddypress-allgroups-page',
            ),
            'POST'      =>'wordpress-post',
            'PAGE'      =>'wordpress-page',
            'AUTHOR'    =>'buddypress-member-page',
            'CATEGORY'  =>'wordpress-category-page',
            'TAG'       =>'wordpress-tag-page',
            'SEARCH'    =>'wordpress-search-results',
        );
        public $sidebars    = array(
            'buddypress-member-page'        =>array(
                'name'          => 'Member page',
                'id'            => 'buddypress-member-page',
                'description'   => 'Member content page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'buddypress-member-personal'    =>array(
                'name'          => 'Member personal',
                'id'            => 'buddypress-member-personal',
                'description'   => 'Member personal page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'buddypress-group-page'         =>array(
                'name'          => 'Group page',
                'id'            => 'buddypress-group-page',
                'description'   => 'Group page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'buddypress-allgroups-page'     =>array(
                'name'          => 'Groups list page',
                'id'            => 'buddypress-allgroups-page',
                'description'   => 'Groups list page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'buddypress-allmembers-page'    =>array(
                'name'          => 'Members list page',
                'id'            => 'buddypress-allmembers-page',
                'description'   => 'Members list page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'special-drafts'                =>array(
                'name'          => 'Draft page',
                'id'            => 'special-drafts',
                'description'   => 'Draft page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'special-group-content'         =>array(
                'name'          => 'Group content page',
                'id'            => 'special-group-content',
                'description'   => 'Group content page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'special-stream'                =>array(
                'name'          => 'Stream page',
                'id'            => 'special-stream',
                'description'   => 'Stream page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'special-top'                   =>array(
                'name'          => 'Page of tops',
                'id'            => 'special-top',
                'description'   => 'Page of tops',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'special-search'                =>array(
                'name'          => 'Search page',
                'id'            => 'special-search',
                'description'   => 'Search page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'wordpress-category-page'       =>array(
                'name'          => 'Category page',
                'id'            => 'wordpress-category-page',
                'description'   => 'Category page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'wordpress-tag-page'            =>array(
                'name'          => 'Tag page',
                'id'            => 'wordpress-tag-page',
                'description'   => 'Tag page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'wordpress-search-results'      =>array(
                'name'          => 'Search results',
                'id'            => 'wordpress-search-results',
                'description'   => 'Search results page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'wordpress-page'                =>array(
                'name'          => 'Page',
                'id'            => 'wordpress-page',
                'description'   => 'Single page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'wordpress-post'                =>array(
                'name'          => 'Post',
                'id'            => 'wordpress-post',
                'description'   => 'Single post',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'post-editor'                   =>array(
                'name'          => 'Post editor',
                'id'            => 'post-editor',
                'description'   => 'Post editor page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'event-editor'                  =>array(
                'name'          => 'Event editor',
                'id'            => 'event-editor',
                'description'   => 'Event editor page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'footer-page'                   =>array(
                'name'          => 'Page footer',
                'id'            => 'footer-page',
                'description'   => 'Footer of single page',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            'footer-standard'               =>array(
                'name'          => 'Standard footer',
                'id'            => 'footer-standard',
                'description'   => 'Footer for all pages',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
        );
        public function getSets($insert_id){
            return array(
                'buddypress-member-page'    =>array(
                    0=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => '',
                            'title_type'            => '',
                            'template'              => 'A',
                            'content'               => 'users',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '1',
                            'only_with_avatar'      => '',
                            'top'                   => 'on',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Has friends',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'friends_of_user',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '30',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Are in groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'users',
                            'displayed'         => 'member',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                    3=> array(
                        'id'        =>'puretheme_comments_thumbnails',
                        'settings'  =>array(
                            'title'         => 'Just said',
                            'title_type'    => 'C',
                            'content'       => 'where_post_author',
                            'targets'       => '',
                            'maxcount'      => '10',
                            'members'       => 'on',
                            'displayed'     => 'member',
                            'top'           => '',
                            'profile'       => '#',
                            'days'          => '3650',
                            'from_date'     => '',
                            'template'      => 'B'
                        )
                    ),
                    4=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Friends posts',
                            'title_type'        => 'C',
                            'content'           => 'friends_author',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '5',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'member',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    5=> array(
                        'id'        =>'puretheme_inserts',
                        'settings'  =>array(
                            'title'             => '',
                            'title_type'        => '',
                            'target'            => $insert_id,
                            'template'          => 'A',
                        )
                    ),
                ),
                'buddypress-member-personal'=>array(
                    0=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => '',
                            'title_type'            => '',
                            'template'              => 'A',
                            'content'               => 'users',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '1',
                            'only_with_avatar'      => '',
                            'top'                   => 'on',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_quotes',
                        'settings'  =>array(
                            'title'             => '',
                            'title_type'        => '',
                            'target'            => '',
                            'template'          => 'A',
                            'displayed'         => 'on',
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Has friends',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'friends_of_user',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '30',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Are in groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'users',
                            'displayed'         => 'member',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                    4=> array(
                        'id'        =>'puretheme_comments_thumbnails',
                        'settings'  =>array(
                            'title'         => 'Just said',
                            'title_type'    => 'C',
                            'content'       => 'where_post_author',
                            'targets'       => '',
                            'maxcount'      => '10',
                            'members'       => 'on',
                            'displayed'     => 'member',
                            'top'           => '',
                            'profile'       => '#',
                            'days'          => '3650',
                            'from_date'     => '',
                            'template'      => 'B'
                        )
                    ),
                    5=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Friends posts',
                            'title_type'        => 'C',
                            'content'           => 'friends_author',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '5',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'member',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    6=> array(
                        'id'        =>'puretheme_inserts',
                        'settings'  =>array(
                            'title'             => '',
                            'title_type'        => '',
                            'target'            => $insert_id,
                            'template'          => 'A',
                        )
                    ),
                ),
                'buddypress-group-page'     =>array(
                    0=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => '',
                            'template'          => 'C',
                            'title_type'        => '',
                            'content'           => 'groups',
                            'displayed'         => 'group',
                            'targets'           => '',
                            'maxcount'          => '1',
                            'only_with_avatar'  => '',
                            'top'               => 'on',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                    1=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Members',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'users_of_group',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '30',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Group posts',
                            'title_type'        => 'C',
                            'content'           => 'group',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '5',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'group',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Other groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'displayed'         => '',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                    4=> array(
                        'id'        =>'puretheme_inserts',
                        'settings'  =>array(
                            'title'             => '',
                            'title_type'        => '',
                            'target'            => $insert_id,
                            'template'          => 'A',
                        )
                    ),
                ),
                'buddypress-allgroups-page' =>array(
                    0=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Last members',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'last',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '30',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Last posts',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '10',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'group',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_inserts',
                        'settings'  =>array(
                            'title'             => '',
                            'title_type'        => '',
                            'target'            => $insert_id,
                            'template'          => 'A',
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    4=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                ),
                'buddypress-allmembers-page'=>array(
                    0=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Last posts',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '10',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'group',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'displayed'         => '',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                    2=> array(
                        'id'        =>'puretheme_inserts',
                        'settings'  =>array(
                            'title'             => '',
                            'title_type'        => '',
                            'target'            => $insert_id,
                            'template'          => 'A',
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    4=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                ),
                'wordpress-category-page'   =>array(
                    0=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Last members',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'last',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '30',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'displayed'         => '',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                    2=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                ),
                'wordpress-tag-page'        =>array(
                    0=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Last members',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'last',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '30',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'displayed'         => '',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                    2=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                ),
                'wordpress-search-results'  =>array(
                    0=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Last posts',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '10',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'group',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'displayed'         => '',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                ),
                'wordpress-page'            =>array(
                    0=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Last posts',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '10',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'group',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Members',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'last',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '30',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'displayed'         => '',
                            'targets'           => '',
                            'maxcount'          => '10',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                    3=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    4=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                ),
                'wordpress-post'            =>array(
                    0=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => '',
                            'title_type'            => '',
                            'template'              => 'A',
                            'content'               => 'users',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '1',
                            'only_with_avatar'      => '',
                            'top'                   => 'on',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_quotes',
                        'settings'  =>array(
                            'title'             => '',
                            'title_type'        => '',
                            'target'            => '',
                            'template'          => 'A',
                            'displayed'         => 'on',
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Other posts',
                            'title_type'        => 'C',
                            'content'           => 'category',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '5',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'post',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Has friends',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'friends_of_user',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '30',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    4=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Are in groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'users',
                            'displayed'         => 'member',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                    5=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Other posts',
                            'title_type'        => 'C',
                            'content'           => 'author',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '5',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'member',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    6=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    7=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                ),
                'special-drafts'            =>array(
                    0=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Last posts',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '10',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'group',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Most active',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'users_active',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '10',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Most creative',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'users_creative',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '10',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'displayed'         => '',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                    4=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    5=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                ),
                'special-group-content'         =>array(
                    0=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Last posts',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '10',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'group',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Most active',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'users_active',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '10',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Most creative',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'users_creative',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '10',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'displayed'         => '',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                    4=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    5=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                ),
                'special-stream'            =>array(
                    0=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => '',
                            'title_type'            => '',
                            'template'              => 'A',
                            'content'               => 'users',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '1',
                            'only_with_avatar'      => '',
                            'top'                   => 'on',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'In stream',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'in_stream_of_users',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '30',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Friends',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'friends_of_user',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '30',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'users',
                            'displayed'         => 'member',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                    4=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Friends posts',
                            'title_type'        => 'C',
                            'content'           => 'friends_author',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '5',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'member',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    5=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    6=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                ),
                'special-top'               =>array(
                    0=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Last posts',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '10',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'group',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Most active',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'users_active',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '10',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Most creative',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'users_creative',
                            'displayed'             => 'on',
                            'targets'               => '',
                            'maxcount'              => '10',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'displayed'         => '',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                    4=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    5=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                ),
                'special-search'            =>array(
                    0=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Last posts',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '10',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'group',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'displayed'         => '',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                ),
                'post-editor'               =>array(
                    0=> array(
                        'id'        =>'puretheme_inserts',
                        'settings'  =>array(
                            'title'             => '',
                            'title_type'        => '',
                            'target'            => $insert_id,
                            'template'          => 'A',
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Last posts',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '10',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'group',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    4=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'displayed'         => '',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                ),
                'event-editor'              =>array(
                    0=> array(
                        'id'        =>'puretheme_inserts',
                        'settings'  =>array(
                            'title'             => '',
                            'title_type'        => '',
                            'target'            => $insert_id,
                            'template'          => 'A',
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_posts_thumbnails',
                        'settings'  =>array(
                            'title'             => 'Last posts',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'hidetitle'         => '',
                            'targets'           => '',
                            'maxcount'          => '10',
                            'thumbnails'        => '',
                            'profile'           => '',
                            'days'              => '3655',
                            'displayed'         => 'group',
                            'from_date'         => '',
                            'template'          => 'E',
                            'slider_template'   => '',
                            'tab_template'      => '',
                            'presentation'      => 'clear',
                            'tabs_columns'      => '',
                            'more'              => ''
                        )
                    ),
                    4=> array(
                        'id'        =>'puretheme_groups_thumbnails',
                        'settings'  =>array (
                            'title'             => 'Groups',
                            'template'          => 'B',
                            'title_type'        => 'C',
                            'content'           => 'last',
                            'displayed'         => '',
                            'targets'           => '',
                            'maxcount'          => '30',
                            'only_with_avatar'  => 'on',
                            'top'               => '',
                            'days'              => '3650',
                            'from_date'         => '',
                            'show_content'      => '',
                            'show_admin_part'   => '',
                            'show_life'         => '',
                            'more'              => '',
                        ),
                    ),
                ),
                'footer-page'               =>array(
                    0=> array(
                        'id'        =>'puretheme_inserts',
                        'settings'  =>array(
                            'title'             => '',
                            'title_type'        => '',
                            'target'            => $insert_id,
                            'template'          => 'A',
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Best of us',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'users_creative',
                            'displayed'             => '',
                            'targets'               => '',
                            'maxcount'              => '20',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                ),
                'footer-standard'           =>array(
                    0=> array(
                        'id'        =>'puretheme_inserts',
                        'settings'  =>array(
                            'title'             => '',
                            'title_type'        => '',
                            'target'            => $insert_id,
                            'template'          => 'A',
                        )
                    ),
                    1=> array(
                        'id'        =>'puretheme_authors_thumbnails',
                        'settings'  =>array(
                            'title'                 => 'Best of us',
                            'title_type'            => 'C',
                            'template'              => 'OnlyAvatars',
                            'content'               => 'users_creative',
                            'displayed'             => '',
                            'targets'               => '',
                            'maxcount'              => '20',
                            'only_with_avatar'      => '',
                            'top'                   => 'off',
                            'profile'               => '',
                            'days'                  => '3650',
                            'from_date'             => '',
                            'more'                  => '',
                            'templates_settings'    => ''
                        )
                    ),
                    2=> array(
                        'id'        =>'puretheme_tags',
                        'settings'  =>array(
                            'title'             => 'Tags',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                    3=> array(
                        'id'        =>'puretheme_categories',
                        'settings'  =>array(
                            'title'             => 'Categories',
                            'title_type'        => 'C',
                            'template'          => 'A',
                        )
                    ),
                ),
            );
        }
    }
}
?>