<?php
namespace Pure\Templates\Posts\Layout\Event{
    class A{
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
            $PostTitle  = \Pure\Templates\Posts\Elements\Title\Initialization::instance()->get('A');
            $innerHTML  = $PostTitle->innerHTML(
                (object)array(
                    'post_id'=>$post->ID
                )
            );
            $PostTitle  = NULL;
            return ($innerHTML !== false ? $innerHTML : '');
        }
        private function innerHTMLAuthor($post){
            $PostAuthor = \Pure\Templates\Posts\Elements\Author\Initialization::instance()->get('A');
            $innerHTML  = $PostAuthor->innerHTML(
                (object)array(
                    'post_id'=>$post->ID
                )
            );
            $PostAuthor = NULL;
            return ($innerHTML !== false ? $innerHTML : '');
        }
        private function innerHTMLMana($post){
            \Pure\Components\Relationships\Mana\Initialization::instance()->attach(true);
            $Template   = \Pure\Templates\Mana\Icon\Initialization::instance()->get('A');
            $innerHTML  = $Template->innerHTML(
                (object)array(
                    'object'    =>'post',
                    'object_id' =>$post->ID,
                    'user_id'   =>$post->post_author
                )
            );
            $Template   = NULL;
            return ($innerHTML !== false ? $innerHTML : '');
        }
        private function innerHTMLTools($post){
            $ToolsTemplate = \Pure\Templates\Posts\Elements\Tools\Initialization::instance()->get('A');
            $innerHTML = $ToolsTemplate->innerHTML((object)array(
                    'post_id'       =>$post->ID,
                    'user_id'       =>$post->post_author,
                    'object_type'   =>'EDITEVENT'
                )
            );
            $ToolsTemplate = NULL;
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
        private function innerHTMLPlace($post){
            $EventPlace     = \Pure\Templates\Posts\Elements\Events\GoogleMap\Initialization::instance()->get('A');
            $innerHTML      = $EventPlace->innerHTML(
                (object)array(
                    'post_id'=>$post->ID
                )
            );
            $EventPlace     = NULL;
            return ($innerHTML !== false ? $innerHTML : '');
        }
        private function innerHTMLTimeAndDate($post){
            $EventPlace     = \Pure\Templates\Posts\Elements\Events\TimeAndDate\Initialization::instance()->get('A');
            $innerHTML      = $EventPlace->innerHTML(
                (object)array(
                    'post_id'=>$post->ID
                )
            );
            $EventPlace     = NULL;
            return ($innerHTML !== false ? $innerHTML : '');
        }
        private function innerHTMLMembers($post){
            $EventMembers   = \Pure\Templates\Posts\Elements\Events\Members\Initialization::instance()->get('A');
            $innerHTML      = $EventMembers->innerHTML(
                (object)array(
                    'post_id'=>$post->ID
                )
            );
            $EventMembers   = NULL;
            return ($innerHTML !== false ? $innerHTML : '');
        }
        private function innerHTMLComments($post){
            $PostComments   = \Pure\Templates\Comments\Post\Initialization::instance()->get('A');
            $innerHTML      = $PostComments->innerHTML(
                (object)array(
                    'post_id'   =>$post->ID,
                    'post'      =>$post
                )
            );
            $PostComments   = NULL;
            return ($innerHTML !== false ? $innerHTML : '');
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
                                    '<div data-post-element-type="Pure.Posts.Layout.A.SubContainer" data-responsive-engine-element="Post.Container">'.
                                        '<div data-post-element-type="Pure.Posts.Layout.A.Author" data-responsive-engine-element="Post.Author">'.
                                            '<div data-post-element-type="Pure.Posts.Layout.A.Author.User">'.
                                                $this->innerHTMLAuthor($parameters->post).
                                            '</div>'.
                                            '<div data-post-element-type="Pure.Posts.Layout.A.Author.Mana">'.
                                                $this->innerHTMLMana($parameters->post).
                                            '</div>'.
                                            '<div data-post-element-type="Pure.Posts.Layout.A.Author.Tools">'.
                                                $this->innerHTMLTools($parameters->post).
                                            '</div>'.
                                        '</div>'.
                                        '<div data-post-element-type="Pure.Posts.Layout.A.Content.Container" data-responsive-engine-element="Post.Content">'.
                                            '<div data-post-element-type="Pure.Posts.Layout.A.Content" data-layout-engine-element="Post.Content">'.
                                                $this->innerHTMLContent($parameters->post).
                                            '</div>'.
                                            '<div data-post-element-type="Pure.Posts.Layout.A.Place">'.
                                                $this->innerHTMLPlace($parameters->post).
                                            '</div>'.
                                            '<div data-post-element-type="Pure.Posts.Layout.A.Place">'.
                                                $this->innerHTMLTimeAndDate($parameters->post).
                                            '</div>'.
                                            '<div data-post-element-type="Pure.Posts.Layout.A.Place">'.
                                                $this->innerHTMLMembers($parameters->post).
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.Posts.Layout.A.Title">'.
                                        $this->innerHTMLComments($parameters->post).
                                    '</div>'.
                                '</article>'.
                                '<!--END: Post.A -->';
            }
            return $innerHTML;
        }
    }
}
?>