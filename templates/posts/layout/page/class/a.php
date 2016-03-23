<?php
namespace Pure\Templates\Posts\Layout\Page{
    class A{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->post     ) !== false ? true : false));
                $result = ($result === false ? false : (is_object($parameters->post ) !== false ? true : false));
                return $result;
            }
            return false;
        }
        private function innerHTMLTitle($post){
            $PostTitle  = \Pure\Templates\Posts\Elements\Title\Initialization::instance()->get('A');
            $innerHTML  = $PostTitle->innerHTML(
                (object)array(
                    'post_id'=>$post->ID
                )
            );
            $PostTitle  = NULL;
            return ($innerHTML !== false ? $innerHTML : '');
        }
        private function innerHTMLContent($post){
            \Pure\Components\WordPress\Post\Elements\Initialization::instance()->attach();
            $PostElements   = new \Pure\Components\WordPress\Post\Elements\Setup($post);
            $PostElements->setup();
            $innerHTML      = $post->post_content;
            $innerHTML      = apply_filters('the_content', $innerHTML);
            $innerHTML      = $PostElements->parseImages($innerHTML);
            $PostElements   = NULL;
            return str_replace(']]>', ']]&gt;', $innerHTML);
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                //Attach styles
                \Pure\Templates\Posts\Elements\Style\Initialization::instance()->get_resources_of('A');
                //Get breadcrumbs
                $Breadcrumbs = \Pure\Templates\Breadcrumbs\Initialization::instance()->get('A');
                $innerHTML .=   '<!--BEGIN: Post.A -->'.
                                '<article data-post-element-type="Pure.Posts.Layout.A.Container" data-engine-post-ID="'.$parameters->post->ID.'">'.
                                    '<div data-post-element-type="Pure.Posts.Layout.A.Title">'.
                                        $this->innerHTMLTitle($parameters->post).
                                        $Breadcrumbs->innerHTML().
                                    '</div>'.
                                    '<div data-post-element-type="Pure.Posts.Layout.A.SubContainer">'.
                                        '<div data-post-element-type="Pure.Posts.Layout.A.Content.Container">'.
                                            '<div data-post-element-type="Pure.Posts.Layout.A.Content" data-layout-engine-element="Post.Content">'.
                                                $this->innerHTMLContent($parameters->post).
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                '</article>'.
                                '<!--END: Post.A -->';
            }
            return $innerHTML;
        }
    }
}
?>