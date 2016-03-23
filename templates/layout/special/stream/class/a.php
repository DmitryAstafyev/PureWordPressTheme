<?php
namespace Pure\Templates\Layout\Special\Stream{
    class A{
        private $settings = false;
        private function getSettings(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->settings->counts->properties;
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $this->settings = $settings;
        }
        private function innerHTMLBlock($users_IDs, $container_id, $post_type){
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
                'content'           => 'author',
                'targets'	        => implode(',', $users_IDs),
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
        public function get($member){
            $this->getSettings();
            $headerClass            = \Pure\Templates\BuddyPress\Headers\Initialization::instance()->get('A');
            $groups                 = NULL;
            $avatar_id              = uniqid('avatar_id');
            \Pure\Components\Stream\Module\Initialization::instance()->attach();
            $Stream             = new \Pure\Components\Stream\Module\Provider();
            $users_IDs          = $Stream->get_users_IDs_in_stream((int)$member->id);
            $Stream             = NULL;
            $posts_container_id = uniqid();
            if (is_array($users_IDs) !== false){
                $labelsIDs              = (object)array(
                    'post'         =>uniqid(),
                    'event'        =>uniqid(),
                    'report'       =>uniqid(),
                    'question'     =>uniqid(),
                );
                $PostsProvider          = \Pure\Providers\Posts\Initialization::instance()->getCommon();
                $Counter                = \Pure\Templates\Counter\Initialization::instance()->get('D');
                $counts                 = $PostsProvider->get_members_posts_counts_by_types($users_IDs);
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
                        array('title',      ''                  ),
                        array('content',    $innerHTMLCounter   ),
                    )
                );
                $Counter                = NULL;
                $innerHTMLPosts = '';
                foreach($labelsIDs as $key=>$value){
                    $innerHTMLPosts .= Initialization::instance()->html(
                        'A/one_column_segment_tab',
                        array(
                            array('title',          ''                                                      ),
                            array('container_id',   $value                                                  ),
                            array('group_id',       $posts_container_id                                     ),
                            array('checked',        ($key === 'post' ? ' checked ' :'' )                    ),
                            array('content',        $this->innerHTMLBlock($users_IDs, $value, $key)   ),
                        )
                    );
                }
                $in_steam               = new \Pure\Plugins\Thumbnails\Authors\Builder(array(
                        'content'           => 'users',
                        'targets'	        => implode(',', $users_IDs),
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
                $innerHTMLInStream      = $in_steam->render();
                $innerHTMLInStream      = ($innerHTMLInStream !== '' ? $innerHTMLInStream : '<p data-element-type="Pure.Social.Home.A.Message">'.__('No members in this stream.', 'pure').'</p>');
                $friends                = NULL;
            }else{
                $innerHTMLPosts         = '<p data-element-type="Pure.Social.Home.A.Message">'.__('No posts.', 'pure').'</p>';
                $innerHTMLInStream      = '<p data-element-type="Pure.Social.Home.A.Message">'.__('No members in this stream.', 'pure').'</p>';
                $innerHTMLCounter       = '';
            }
            $innerHTMLInStream         = Initialization::instance()->html(
                'A/one_column_segment_central',
                array(
                    array('title',          __('Members in this stream', 'pure') ),
                    array('content',        $innerHTMLInStream                          ),
                )
            );
            $innerHTML              = Initialization::instance()->html(
                'A/layout',
                array(
                    array('header_segment',     $headerClass->get((int)$member->id, (object)array('avatar_id'=>$avatar_id))),
                    array('counter',            $innerHTMLCounter       ),
                    array('tabs',               $innerHTMLPosts         ),
                    array('in_steam',           $innerHTMLInStream      ),
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