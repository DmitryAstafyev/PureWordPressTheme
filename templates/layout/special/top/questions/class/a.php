<?php
namespace Pure\Templates\Layout\Special\Top\Questions{
    class A{
        private $settings = false;
        private function getSettings(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->settings->counts->properties;
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $this->settings = $settings;
        }
        private function innerHTMLBlock($solved = false){
            $wrapper    = \Pure\Templates\Positioning\Initialization::instance()->get('B');
            $posts      = new \Pure\Plugins\Thumbnails\Posts\Builder(array(
                'content'           => ($solved === false ? 'questions_unsolved' : 'questions_solved'),
                'targets'	        => '',
                'template'	        => ($solved === false ? 'QuestionD' : 'QuestionC'),
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
                'post_type'         =>'question',
                'more'              => true));
            $innerHTML  = $posts->render();
            $group_id   = uniqid();
            $innerHTML  = ($innerHTML !== '' ? $wrapper->get($innerHTML, (object)array('id'=>$group_id, 'column_width'=>($solved === false ? '30em' : '20em'), 'node_type'=>'article', 'space'=>'1em')) : '');
            $innerHTML  = ($innerHTML !== '' ? $innerHTML : '<p data-element-type="Pure.Social.Home.A.Message">No questions</p>');
            $wrapper    = NULL;
            $posts      = NULL;
            return Initialization::instance()->html(
                'A/one_column_segment_tab',
                array(
                    array('title',          ''          ),
                    array('group_id',       $group_id   ),
                    array('content',        $innerHTML  ),
                )
            );
        }
        private function innerHTMLTitle($solved = false){
            return Initialization::instance()->html(
                'A/about',
                array(
                    array('name',   __('Last '.($solved === false ? 'unsolved' : 'solved').' questions',  'pure') ),
                    array('info',   __('for last 180 days',      'pure') ),
                )
            );
        }
        public function get(){
            $this->getSettings();
            $innerHTML              = Initialization::instance()->html(
                'A/layout',
                array(
                    array('title_solved',       $this->innerHTMLTitle(true)      ),
                    array('content_solved',     $this->innerHTMLBlock(true)      ),
                    array('title_unsolved',     $this->innerHTMLTitle(false)     ),
                    array('content_unsolved',   $this->innerHTMLBlock(false)     ),
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