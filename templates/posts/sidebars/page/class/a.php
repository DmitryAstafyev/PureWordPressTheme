<?php
namespace Pure\Templates\Posts\Sidebars\Page{
    class A{
        private $settings = false;
        private function getSettings(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->settings->counts->properties;
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $this->settings = $settings;
        }
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->post ) !== false ? true : false));
                $result = ($result === false ? false : (is_object($parameters->post ) !== false ? true : false));
                $parameters->echo = (isset($parameters->echo) === true  ? $parameters->echo : false);
                return $result;
            }
            return false;
        }
        private function innerHTMLTitle($title){
            $Title      = \Pure\Templates\Titles\Initialization::instance()->get('F');
            $innerHTML  = $Title->get($title, (object)array('echo'=>false));
            $Title      = NULL;
            return ($innerHTML !== false ? $innerHTML : '');
        }
        private function innerHTMLLastPosts(){
            $Wrapper    = \Pure\Templates\Positioning\Initialization::instance()->get('B');
            $posts      = new \Pure\Plugins\Thumbnails\Posts\Builder(array(
                'content'           => 'last',
                'targets'	        => '',
                'template'	        => 'DMini',
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => (int)$this->settings->items_on_sidebars,
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
                'more'              => true));
            $innerHTML  = $posts->render();
            if ($innerHTML === '' || $innerHTML === false){
                $innerHTML = '<p data-post-element-type="Pure.Posts.SideBar.A.Info">'.__('Nothing to show', 'pure').'</p>';
            }else{
                $innerHTML  = $Wrapper->get($innerHTML, (object)array('id'=>uniqid(), 'column_width'=>'28em', 'node_type'=>'article', 'space'=>'1em'));
            }
            $Wrapper    = NULL;
            return $innerHTML;
        }
        private function innerHTMLPopularOnSite(){
            $Wrapper    = \Pure\Templates\Positioning\Initialization::instance()->get('B');
            $posts      = new \Pure\Plugins\Thumbnails\Posts\Builder(array(
                'content'           => 'popular',
                'targets'	        => '',
                'template'	        => 'DMini',
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => (int)$this->settings->items_on_sidebars,
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
                'more'              => true));
            $innerHTML  = $posts->render();
            if ($innerHTML === '' || $innerHTML === false){
                $innerHTML = '<p data-post-element-type="Pure.Posts.SideBar.A.Info">'.__('Nothing to show', 'pure').'</p>';
            }else{
                $innerHTML  = $Wrapper->get($innerHTML, (object)array('id'=>uniqid(), 'column_width'=>'28em', 'node_type'=>'article', 'space'=>'1em'));
            }
            $Wrapper    = NULL;
            return $innerHTML;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $this->getSettings();
                $innerHTML .=   '<!--BEGIN: Post.Sidebar.A -->'.
                                '<div data-post-element-type="Pure.Posts.SideBar.A.Container">'.
                                        $this->innerHTMLTitle(__('Last on site', 'pure')).
                                        $this->innerHTMLLastPosts().
                                        $this->innerHTMLTitle(__('Most popular', 'pure')).
                                        $this->innerHTMLPopularOnSite().
                                '</div>'.
                                '<!--END: Post.Sidebar.A -->';
            }
            if ($parameters->echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
    }
}
?>