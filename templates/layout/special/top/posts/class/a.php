<?php
namespace Pure\Templates\Layout\Special\Top\Posts{
    class A{
        private $settings = false;
        private function getSettings(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->settings->counts->properties;
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $this->settings = $settings;
        }
        private function innerHTMLBlock($container_id, $type){
            switch($type){
                case 'all':
                    $selection = false;
                    break;
                case 'galleries':
                    $selection = array('gallery');
                    break;
                case 'audio':
                    $selection = array('playlist', 'audio');
                    break;
                case 'media':
                    $selection = array('embed');
                    break;
                case 'drafts':
                    $selection = false;
                    break;
            }
            $wrapper    = \Pure\Templates\Positioning\Initialization::instance()->get('B');
            $posts      = new \Pure\Plugins\Thumbnails\Posts\Builder(array(
                'content'           => 'popular',
                'targets'	        => '',
                'template'	        => 'F',
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
                'selection'         =>$selection,
                'post_status'       =>($type !== 'drafts' ? '' : 'draft'),
                'post_type'         =>'post',
                'more'              => true));
            $innerHTML  = $posts->render();
            $innerHTML  = ($innerHTML !== '' ? $wrapper->get($innerHTML, (object)array('id'=>$container_id, 'column_width'=>'28em', 'node_type'=>'article', 'space'=>'1em')) : '');
            $innerHTML  = ($innerHTML !== '' ? $innerHTML : '<p data-element-type="Pure.Social.Home.A.Message">No posts</p>');
            $wrapper    = NULL;
            $posts      = NULL;
            return $innerHTML;
        }
        public function get(){
            $this->getSettings();
            $PostsProvider          = \Pure\Providers\Posts\Initialization::instance()->getCommon();
            $Counter                = \Pure\Templates\Counter\Initialization::instance()->get('D');
            $labelsIDs              = (object)array(
                'all'       =>uniqid(),
                'galleries' =>uniqid(),
                'audio'     =>uniqid(),
                'media'     =>uniqid(),
            );
            $innerHTMLCounter       = $Counter->get(
                array(
                    (object)array(
                        'value'     =>$PostsProvider->get_posts_count_of_type(-1, 'all', true),
                        'label'     =>__('Total posts','pure'),
                        'button'    =>__('show','pure'),
                        'label_id'  =>$labelsIDs->all,
                        'icon'      =>Initialization::instance()->configuration->urls->images.'/a/posts.png',
                    ),
                    (object)array(
                        'value'     =>$PostsProvider->get_posts_count_of_type(-1, 'galleries', true),
                        'label'     =>__('Images and photos','pure'),
                        'button'    =>__('show','pure'),
                        'label_id'  =>$labelsIDs->galleries,
                        'icon'      =>Initialization::instance()->configuration->urls->images.'/a/images.png',
                    ),
                    (object)array(
                        'value'     =>$PostsProvider->get_posts_count_of_type(-1, 'audio', true),
                        'label'     =>__('Audio and music','pure'),
                        'button'    =>__('show','pure'),
                        'label_id'  =>$labelsIDs->audio,
                        'icon'      =>Initialization::instance()->configuration->urls->images.'/a/audio.png',
                    ),
                    (object)array(
                        'value'     =>$PostsProvider->get_posts_count_of_type(-1, 'media', true),
                        'label'     =>__('All media','pure'),
                        'button'    =>__('show','pure'),
                        'label_id'  =>$labelsIDs->media,
                        'icon'      =>Initialization::instance()->configuration->urls->images.'/a/media.png',
                    ),
                )
            );
            $innerHTMLTitle         = Initialization::instance()->html(
                'A/about',
                array(
                    array('name',   __('Most popular', 'pure')       ),
                    array('info',   __('for last 180 days', 'pure')  ),
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
                        array('title',          ''                                  ),
                        array('container_id',   $value                              ),
                        array('group_id',       $group_id                           ),
                        array('checked',        ($key === 'all' ? ' checked ' :'' ) ),
                        array('content',        $this->innerHTMLBlock($value, $key) ),
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