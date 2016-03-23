<?php
namespace Pure\Templates\Layout\Special\Top\Events{
    class A{
        private $settings = false;
        private function getSettings(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->settings->counts->properties;
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $this->settings = $settings;
        }
        private function innerHTMLBlock($container_id){
            $wrapper    = \Pure\Templates\Positioning\Initialization::instance()->get('B');
            $posts      = new \Pure\Plugins\Thumbnails\Posts\Builder(array(
                'content'           => 'last',
                'targets'	        => '',
                'template'	        => 'EventA',
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
                'post_type'         =>'event',
                'more'              => true));
            $innerHTML  = $posts->render();
            $innerHTML  = ($innerHTML !== '' ? $wrapper->get($innerHTML, (object)array('id'=>$container_id, 'column_width'=>'35em', 'node_type'=>'article', 'space'=>'1em')) : '');
            $innerHTML  = ($innerHTML !== '' ? $innerHTML : '<p data-element-type="Pure.Social.Home.A.Message">No events</p>');
            $wrapper    = NULL;
            $posts      = NULL;
            return $innerHTML;
        }
        public function get(){
            $this->getSettings();
            $innerHTMLTitle         = Initialization::instance()->html(
                'A/about',
                array(
                    array('name',   __('Last events', 'pure')       ),
                    array('info',   __('for last 180 days', 'pure')  ),
                )
            );
            $group_id               = uniqid();
            $innerHTMLContent       = Initialization::instance()->html(
                'A/one_column_segment_tab',
                array(
                    array('title',          ''                                  ),
                    array('group_id',       $group_id                           ),
                    array('content',        $this->innerHTMLBlock($group_id)    ),
                )
            );
            $innerHTML              = Initialization::instance()->html(
                'A/layout',
                array(
                    array('title',      $innerHTMLTitle     ),
                    array('content',    $innerHTMLContent   ),
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