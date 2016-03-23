<?php
namespace Pure\Templates\Layout\WordPress\Post{
    class A{
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
        private function innerHTMLTools($post){
            $ToolsTemplate = \Pure\Templates\Posts\Elements\Tools\Initialization::instance()->get('A');
            $innerHTML = $ToolsTemplate->innerHTML((object)array(
                    'post_id'=>$post->ID,
                    'user_id'=>$post->post_author,
                    'object_type'   =>'EDITPOST'
                )
            );
            $ToolsTemplate = NULL;
            return ($innerHTML !== false ? $innerHTML : '');
        }
        private function innerHTMLMana($post){
            \Pure\Components\Relationships\Mana\Initialization::instance()->attach(true);
            $Template   = \Pure\Templates\Mana\Icon\Initialization::instance()->get('A');
            $innerHTML  = $Template->innerHTML(
                (object)array(
                    'object'    =>'post',
                    'object_id' =>(int)$post->ID,
                    'user_id'   =>(int)$post->post_author
                )
            );
            $Template   = NULL;
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
        private function warnings($post_id){
            $Warnings = \Pure\Templates\Elements\Warnings\Initialization::instance()->get('A');
            $Warnings->attach($post_id);
            $Warnings = NULL;
        }
        public function get($post){
            $innerHTMLContent   = Initialization::instance()->html(
                'A/one_column_segment_content',
                array(
                    array('title',          $this->innerHTMLTitle($post)        ),
                    array('tools',          $this->innerHTMLTools($post)        ),
                    array('content',        $this->innerHTMLContent($post)      ),
                    array('rate',           $this->innerHTMLMana($post)         ),
                    array('rate_label',     __('Like this post? Rate it', 'pure') ),
                )
            );
            $innerHTMLComments  = Initialization::instance()->html(
                'A/one_column_segment_comments',
                array(
                    array('title',          ''                              ),
                    array('content',        $this->innerHTMLComments($post) ),
                )
            );
            $innerHTML              = Initialization::instance()->html(
                'A/layout',
                array(
                    array('thumbnail',      ''     ),
                    array('content',    $innerHTMLContent       ),
                    array('comments',   $innerHTMLComments      ),
                )
            );
            //Attach warnings
            $this->warnings($post->ID);
            //Attach effects
            \Pure\Components\Effects\Fader\Initialization::instance()->attach();
            \Pure\Components\LockPage\A\Initialization::instance()->attach();
            //Attach global layout
            \Pure\Templates\Layout\Page\Container\Initialization::instance()->attach_resources_of('A');
            //Attach styles
            \Pure\Templates\Posts\Elements\Style\Initialization::instance()->attach_resources_of('A');
            $headerClass = NULL;
            return $innerHTML;
        }
    }
}
?>