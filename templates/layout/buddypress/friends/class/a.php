<?php
namespace Pure\Templates\Layout\BuddyPress\Friends{
    class A{
        private $settings = false;
        private function getSettings(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->settings->counts->properties;
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $this->settings = $settings;
        }
        private function innerHTMLFriends($user_id, $member_full){
            $friends                = new \Pure\Plugins\Thumbnails\Authors\Builder(array(
                    'content'           => 'friends_of_user',
                    'targets'	        => (int)$user_id,
                    'template'	        => 'F',
                    'title'		        => '',
                    'title_type'        => '',
                    'maxcount'	        => 5,
                    'only_with_avatar'	=> false,
                    'top'	            => false,
                    'profile'	        => '',
                    'days'	            => 3650,
                    'from_date'         => '',
                    'more'              => true)
            );
            $innerHTMLFriends       = $friends->render();
            $innerHTMLFriends       = ($innerHTMLFriends !== '' ? $innerHTMLFriends : '<p data-element-type="Pure.Social.Home.A.Message">'.$member_full->author->name.' '.__('has not any friend here yet.', 'pure').'</p>');
            $friends                = NULL;
            return $innerHTMLFriends;
        }
        private function innerHTMLFriendsGroups($user_id, $member_full){
            $MembersCommon          = \Pure\Providers\Members\Initialization::instance()->getCommon();
            $friends_IDs            = $MembersCommon->get_friends_ids((int)$user_id);
            $MembersCommon          = NULL;
            $groups_targets         = false;
            if ($friends_IDs !== false){
                if (count($friends_IDs) > 0){
                    $groups_targets = implode(',', $friends_IDs);
                }
            }
            if ($groups_targets === false){
                $groups_targets     = (int)$user_id;
            }
            $groups                 = new \Pure\Plugins\Thumbnails\Groups\Builder(array(
                'content'           => 'users',
                'targets'	        => $groups_targets,
                'template'	        => 'H',
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => 5,
                'only_with_avatar'	=> false,
                'top'	            => false,
                'profile'	        => '',
                'days'	            => 3650,
                'from_date'         => '',
                'show_content'      => false,
                'show_admin_part'   => false,
                'show_life'         => false,
                'more'              => true,
                'group'             => uniqid()
            ));
            $innerHTMLGroups        = $groups->render();
            $innerHTMLGroups        = ($innerHTMLGroups !== '' ? $innerHTMLGroups : '<p data-element-type="Pure.Social.Home.A.Message">'.$member_full->author->name.' '.__('are not a member of any group yet.', 'pure').'</p>');
            $groups                 = NULL;
            return $innerHTMLGroups;
        }
        private function innerHTMLBlock($user_id, $container_id, $post_type){
            switch($post_type){
                case 'post':
                    $template   = 'F';
                    $no_records = __('No posts found', 'pure');
                    break;
                case 'event':
                    $template   = 'EventA';
                    $no_records = __('No events found', 'pure');
                    break;
                case 'report':
                    $template   = 'ReportA';
                    $no_records = __('No reports found', 'pure');
                    break;
                case 'question':
                    $template   = 'QuestionA';
                    $no_records = __('No questions found', 'pure');
                    break;
            }
            $wrapper    = \Pure\Templates\Positioning\Initialization::instance()->get('B');
            $posts      = new \Pure\Plugins\Thumbnails\Posts\Builder(array(
                'content'           => 'friends_author',
                'targets'	        => $user_id,
                'template'	        => $template,
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => $this->settings->posts,
                'only_with_avatar'	=> false,
                'profile'	        => '',
                'days'	            => 3650,
                'from_date'         => '',
                'hidetitle'	        => true,
                'thumbnails'	    => false,
                'slider_template'	=> '',
                'tab_template'	    => '',
                'presentation'	    => 'clear',
                'tabs_columns'	    => 1,
                'selection'         =>false,
                'post_type'         =>$post_type,
                'post_status'       =>'',
                'more'              => true));
            $innerHTML  = $posts->render();
            $innerHTML  = ($innerHTML !== '' ? $wrapper->get($innerHTML, (object)array('id'=>$container_id, 'column_width'=>'28em', 'node_type'=>'article', 'space'=>'1em')) : '');
            $innerHTML  = ($innerHTML !== '' ? $innerHTML : '<p data-element-type="Pure.Social.Home.A.Message">'.$no_records.'</p>');
            $wrapper    = NULL;
            $posts      = NULL;
            return $innerHTML;
        }
        private function innerHTMLContent($user_id, $friends_IDs){
            if (is_array($friends_IDs) !== false){
                if (count($friends_IDs) > 0){
                    $PostsProvider          = \Pure\Providers\Posts\Initialization::instance()->getCommon();
                    $Counter                = \Pure\Templates\Counter\Initialization::instance()->get('D');
                    $labelsIDs              = (object)array(
                        'post'         =>uniqid(),
                        'event'        =>uniqid(),
                        'report'       =>uniqid(),
                        'question'     =>uniqid(),
                    );
                    $counts                 = $PostsProvider->get_members_posts_counts_by_types($friends_IDs);
                    $innerHTMLCounter       = $Counter->get(
                        array(
                            (object)array(
                                'value'     =>$counts->post->count,
                                'label'     =>__('Posts','pure'),
                                'button'    =>__('show','pure'),
                                'label_id'  =>$labelsIDs->post,
                                'icon'      =>Initialization::instance()->configuration->urls->images.'/a/posts.png',
                            ),
                            (object)array(
                                'value'     =>$counts->event->count,
                                'label'     =>__('Events','pure'),
                                'button'    =>__('show','pure'),
                                'label_id'  =>$labelsIDs->event,
                                'icon'      =>Initialization::instance()->configuration->urls->images.'/a/events.png',
                            ),
                            (object)array(
                                'value'     =>$counts->report->count,
                                'label'     =>__('Reports','pure'),
                                'button'    =>__('show','pure'),
                                'label_id'  =>$labelsIDs->report,
                                'icon'      =>Initialization::instance()->configuration->urls->images.'/a/reports.png',
                            ),
                            (object)array(
                                'value'     =>$counts->question->count,
                                'label'     =>__('Q&A','pure'),
                                'button'    =>__('show','pure'),
                                'label_id'  =>$labelsIDs->question,
                                'icon'      =>Initialization::instance()->configuration->urls->images.'/a/questions.png',
                            ),
                        )
                    );
                    $innerHTMLCounter       = Initialization::instance()->html(
                        'A/one_column_segment_normal',
                        array(
                            array('title',      __('FRIENDS WRITES', 'pure') ),
                            array('content',    $innerHTMLCounter                   ),
                        )
                    );
                    $Counter                = NULL;
                    $innerHTMLTabs          = '';
                    $group_id               = uniqid();
                    foreach($labelsIDs as $key=>$value){
                        $innerHTMLTabs .= Initialization::instance()->html(
                            'A/one_column_segment_tab',
                            array(
                                array('title',          ''                                                      ),
                                array('container_id',   $value                                                  ),
                                array('group_id',       $group_id                                               ),
                                array('checked',        ($key === 'post' ? ' checked ' :'' )                    ),
                                array('content',        $this->innerHTMLBlock((int)$user_id, $value, $key)   ),
                            )
                        );
                    }
                    return $innerHTMLCounter.$innerHTMLTabs;
                }
            }
            return '';
        }
        public function get($member){
            $this->getSettings();
            $UserData               = \Pure\Providers\Members\Initialization::instance()->getCommon();
            $member_full            = $UserData->get((int)$member->id);
            $friends_IDs            = $UserData->get_friends_ids((int)$member->id);
            $UserData               = NULL;
            $headerClass            = \Pure\Templates\BuddyPress\Headers\Initialization::instance()->get('A');
            $avatar_id              = uniqid('avatar_id');
            $innerHTMLPostCount     = Initialization::instance()->html(
                'A/about',
                array(
                    array('name',       $member_full->author->name     ),
                    array('info',       __( 'Created', 'pure' ).': '.$member_full->posts->count.' '.__( 'posts', 'pure' ).' '.__( 'and', 'pure' ).' '.$member_full->comments->count.' '.__( 'comments', 'pure' )),
                )
            );
            $innerHTMLFriends       = Initialization::instance()->html(
                'A/one_column_segment_central',
                array(
                    array('title',      __('Friends', 'pure')                                ),
                    array('content',    $this->innerHTMLFriends((int)$member->id, $member_full)     ),
                )
            );
            $innerHTMLHistory       = Initialization::instance()->html(
                'A/about',
                array(
                    array('name',       $member_full->author->name     ),
                    array('info',       __( 'With us from', 'pure' ).' '.$member_full->author->date.' ('.$member_full->author->how_long.')'),
                )
            );
            $innerHTMLGroups        = Initialization::instance()->html(
                'A/one_column_segment_central',
                array(
                    array('title',      __('Friends groups', 'pure')                             ),
                    array('content',    $this->innerHTMLFriendsGroups((int)$member->id, $member_full)   ),
                )
            );
            $innerHTMLSocial        = Initialization::instance()->html(
                'A/about',
                array(
                    array('name',       $member_full->author->name     ),
                    array('info',       __( 'Has', 'pure' ).' '.$member_full->author->friends.' '.__( 'friends and member of', 'pure' ).' '.$member_full->author->groups.' '.__( 'groups', 'pure' )),
                )
            );
            $innerHTML              = Initialization::instance()->html(
                'A/layout',
                array(
                    array('header_segment',     $headerClass->get((int)$member->id, (object)array('avatar_id'=>$avatar_id))),
                    array('history',            $innerHTMLHistory                                       ),
                    array('friends',            $innerHTMLFriends                                       ),
                    array('posts',              $this->innerHTMLContent((int)$member->id, $friends_IDs) ),
                    array('post_count',         $innerHTMLPostCount                                     ),
                    array('groups',             $innerHTMLGroups                                        ),
                    array('social',             $innerHTMLSocial                                        ),
                )
            );
            //Attach effects
            \Pure\Components\Effects\Fader\Initialization::instance()->attach();
            \Pure\Components\LockPage\A\Initialization::instance()->attach();
            //Attach global layout
            \Pure\Templates\Layout\Page\Container\Initialization::instance()->attach_resources_of('A');
            $headerClass = NULL;
            return $innerHTML;
        }
    }
}
?>