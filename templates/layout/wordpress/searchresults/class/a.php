<?php
namespace Pure\Templates\Layout\WordPress\SearchResults{
    class A{
        private $settings = false;
        private function getSettings(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->settings->counts->properties;
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $this->settings = $settings;
        }
        private function innerHTMLBlock($container_id, $post_type, $IDs){
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
                'content'           => 'defined',
                'targets'	        => implode(',', $IDs),
                'template'	        => $template,
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => $this->settings->posts,
                'only_with_avatar'	=> false,
                'profile'	        => '',
                'days'	            => 180,
                'from_date'         => '',
                'hidetitle'	        => true,
                'thumbnails'	    => false,
                'slider_template'	=> '',
                'tab_template'	    => '',
                'presentation'	    => 'clear',
                'tabs_columns'	    => 1,
                'selection'         =>false,
                'post_status'       =>'',
                'post_type'         =>$post_type,
                'more'              => true));
            $innerHTML  = $posts->render();
            $innerHTML  = ($innerHTML !== '' ? $wrapper->get($innerHTML, (object)array('id'=>$container_id, 'column_width'=>'28em', 'node_type'=>'article', 'space'=>'1em')) : '');
            $innerHTML  = ($innerHTML !== '' ? $innerHTML : '<p data-element-type="Pure.Social.Home.A.Message">'.$no_records.'</p>');
            $wrapper    = NULL;
            $posts      = NULL;
            return $innerHTML;
        }
        public function get($IDs){
            $this->getSettings();
            $PostsProvider          = \Pure\Providers\Posts\Initialization::instance()->getCommon();
            $Counter                = \Pure\Templates\Counter\Initialization::instance()->get('D');
            $labelsIDs              = (object)array(
                'post'         =>uniqid(),
                'event'        =>uniqid(),
                'report'       =>uniqid(),
                'question'     =>uniqid(),
            );
            $counts                 = $PostsProvider->get_posts_counts_by_types($IDs);
            $innerHTMLCounter       = $Counter->get(
                array(
                    (object)array(
                        'value'     =>$counts->post->count,
                        'label'     =>__('Posts','pure'),
                        'button'    =>__('show','pure'),
                        'label_id'  =>$labelsIDs->post,
                        'icon'      =>Initialization::instance()->configuration->urls->images.'/A/posts.png',
                    ),
                    (object)array(
                        'value'     =>$counts->event->count,
                        'label'     =>__('Events','pure'),
                        'button'    =>__('show','pure'),
                        'label_id'  =>$labelsIDs->event,
                        'icon'      =>Initialization::instance()->configuration->urls->images.'/A/events.png',
                    ),
                    (object)array(
                        'value'     =>$counts->report->count,
                        'label'     =>__('Reports','pure'),
                        'button'    =>__('show','pure'),
                        'label_id'  =>$labelsIDs->report,
                        'icon'      =>Initialization::instance()->configuration->urls->images.'/A/reports.png',
                    ),
                    (object)array(
                        'value'     =>$counts->question->count,
                        'label'     =>__('Q&A','pure'),
                        'button'    =>__('show','pure'),
                        'label_id'  =>$labelsIDs->question,
                        'icon'      =>Initialization::instance()->configuration->urls->images.'/A/questions.png',
                    ),
                )
            );
            $innerHTMLTitle         = Initialization::instance()->html(
                'A/about',
                array(
                    array('name',   __('Search result',                  'pure') ),
                    array('info',   count($IDs).' '.__('records found',  'pure') ),
                )
            );
            $innerHTMLCounter       = Initialization::instance()->html(
                'A/one_column_segment_clear',
                array(
                    array('title',      ''                  ),
                    array('content',    $innerHTMLCounter   ),
                )
            );
            $Counter                = NULL;
            $innerHTMLTabs          = '';
            $group_id               = uniqid();
            foreach($labelsIDs as $key=>$value){
                $innerHTMLTabs .= Initialization::instance()->html(
                    'A/one_column_segment_tab',
                    array(
                        array('title',          ''                                          ),
                        array('container_id',   $value                                      ),
                        array('group_id',       $group_id                                   ),
                        array('checked',        ($key === 'post' ? ' checked ' :'' )        ),
                        array('content',        $this->innerHTMLBlock($value, $key, $IDs)   ),
                    )
                );
            }
            $innerHTML              = Initialization::instance()->html(
                'A/layout',
                array(
                    array('title',      $innerHTMLTitle     ),
                    array('counter',    $innerHTMLCounter   ),
                    array('tabs',       $innerHTMLTabs      ),
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