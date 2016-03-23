<?php
namespace Pure\Templates\Posts\Sidebars\Groups{
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
        private function innerHTMLAuthors(){
            $authors      = new \Pure\Plugins\Thumbnails\Authors\Builder(array(
                'content'           => 'users_creative',
                'targets'	        => '',
                'template'	        => 'A',
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
                'show_content'      =>false,
                'show_admin_part'   =>false,
                'show_life'         =>false,
                'more'              => true));
            $innerHTML  = $authors->render();
            if ($innerHTML === '' || $innerHTML === false){
                $innerHTML = '<p data-post-element-type="Pure.Posts.SideBar.A.Info">'.__('Nothing to show', 'pure').'</p>';
            }
            return $innerHTML;
        }
        private function innerHTMLGroups(){
            $groups      = new \Pure\Plugins\Thumbnails\Groups\Builder(array(
                'content'           => 'popular',
                'targets'	        => '',
                'template'	        => 'A',
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
                'show_content'      =>false,
                'show_admin_part'   =>false,
                'show_life'         =>false,
                'more'              => true));
            $innerHTML  = $groups->render();
            if ($innerHTML === '' || $innerHTML === false){
                $innerHTML = '<p data-post-element-type="Pure.Posts.SideBar.A.Info">'.__('Nothing to show', 'pure').'</p>';
            }
            return $innerHTML;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $this->getSettings();
                $innerHTML .=   '<!--BEGIN: Post.Sidebar.A -->'.
                                '<div data-post-element-type="Pure.Posts.SideBar.A.Container">'.
                                    $this->innerHTMLTitle(__('Popular groups', 'pure')).
                                    $this->innerHTMLGroups().
                                    $this->innerHTMLTitle(__('Creative members', 'pure')).
                                    $this->innerHTMLAuthors().
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