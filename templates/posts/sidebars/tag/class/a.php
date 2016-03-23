<?php
namespace Pure\Templates\Posts\Sidebars\Tag{
    class A{
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
        private function innerHTMLCategories(){
            $Template   = \Pure\Templates\Makeup\Footer\Elements\Categories\Initialization::instance()->get('B');
            $innerHTML  = $Template->innerHTML();
            $Template   = NULL;
            if ($innerHTML === '' || $innerHTML === false){
                $innerHTML = '<p data-post-element-type="Pure.Posts.SideBar.A.Info">'.__('Nothing to show', 'pure').'</p>';
            }
            return $innerHTML;
        }
        private function innerHTMLTags(){
            $Template   = \Pure\Templates\Makeup\Footer\Elements\Tags\Initialization::instance()->get('B');
            $innerHTML  = $Template->innerHTML();
            $Template   = NULL;
            if ($innerHTML === '' || $innerHTML === false){
                $innerHTML = '<p data-post-element-type="Pure.Posts.SideBar.A.Info">'.__('Nothing to show', 'pure').'</p>';
            }
            return $innerHTML;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $innerHTML .=   '<!--BEGIN: Post.Sidebar.A -->'.
                                '<div data-post-element-type="Pure.Posts.SideBar.A.Container">'.
                                        $this->innerHTMLTitle(__('Other tags', 'pure')).
                                        $this->innerHTMLTags().
                                        $this->innerHTMLTitle(__('Categories', 'pure')).
                                        $this->innerHTMLCategories().
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