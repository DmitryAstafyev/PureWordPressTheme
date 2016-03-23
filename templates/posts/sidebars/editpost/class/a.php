<?php
namespace Pure\Templates\Posts\Sidebars\EditPost{
    class A{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->post_id ) !== false ? true : false));
                $parameters->echo = (isset($parameters->echo) === true  ? $parameters->echo : false);
                return $result;
            }
            return false;
        }
        private function innerHTMLPosts($post_id){
            $innerHTML  = '';
            $post       = get_post($post_id);
            if ($post !== false){
                $Template   = \Pure\Templates\Posts\Layout\Post\Initialization::instance()->get('AShort');
                $innerHTML  = $Template->innerHTML(
                    (object)array(
                        'post'=>$post
                    )
                );
                $Template   = NULL;
            }
            return $innerHTML;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $innerHTML .=   '<!--BEGIN: Post.Sidebar.A -->'.
                                '<div data-post-element-type="Pure.Posts.SideBar.A.Container">'.
                                    $this->innerHTMLPosts($parameters->post_id).
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