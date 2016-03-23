<?php
namespace Pure\Templates\Posts\Layout\Post{
    class AShort{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->post ) !== false ? true : false));
                $result = ($result === false ? false : (is_object($parameters->post ) !== false ? true : false));
                return $result;
            }
            return false;
        }
        private function innerHTMLTitle($post){
            $Title      = \Pure\Templates\Titles\Initialization::instance()->get('F');
            $innerHTML  = $Title->get($post->post_title, (object)array('echo'=>false));
            $Title      = NULL;
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
                $innerHTML .=   '<!--BEGIN: Post.AShort -->'.
                                '<article data-post-element-type="Pure.Posts.Layout.AShort.Container" data-engine-post-ID="'.$parameters->post->ID.'">'.
                                    $this->innerHTMLTitle($parameters->post).
                                    //$this->innerHTMLAuthor($parameters->post).
                                    '<div data-post-element-type="Pure.Posts.Layout.AShort.Content" data-layout-engine-element="Post.Content">'.
                                        $this->innerHTMLContent($parameters->post).
                                    '</div>'.
                                '</article>'.
                                '<!--END: Post.AShort -->';
                $Breadcrumbs = NULL;
            }
            return $innerHTML;
        }
    }
}
?>