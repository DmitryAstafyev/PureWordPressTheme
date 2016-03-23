<?php
namespace Pure\Templates\Layout\WordPress\Question{
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
                    'post_id'       =>$post->ID,
                    'user_id'       =>$post->post_author,
                    'object_type'   =>'EDITQUESTION'
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
        private function innerHTMLAdditions($post){
            $Addition   = \Pure\Templates\Posts\Elements\Questions\Additions\Initialization::instance()->get('A');
            $innerHTML  = $Addition->innerHTML(
                (object)array(
                    'post_id'=>$post->ID
                )
            );
            return $innerHTML;
        }
        private function innerHTMLAnswers($post){
            $PostAnswers    = \Pure\Templates\Posts\Elements\Questions\Answers\Initialization::instance()->get('A');
            $innerHTML      = $PostAnswers->innerHTML(
                (object)array(
                    'post_id'   =>$post->ID,
                    'post'      =>$post
                )
            );
            $PostAnswers   = NULL;
            return ($innerHTML !== false ? $innerHTML : '');
        }
        public function get($post){
            //Attach styles
            \Pure\Templates\Posts\Elements\Style\Initialization::instance()->get_resources_of('A');
            $innerHTMLContent   = Initialization::instance()->html(
                'A/one_column_segment_content',
                array(
                    array('title',          $this->innerHTMLTitle($post)        ),
                    array('tools',          $this->innerHTMLTools($post)        ),
                    array('content',        $this->innerHTMLContent($post).
                                            $this->innerHTMLAdditions($post)    ),
                    array('rate',           $this->innerHTMLMana($post)         ),
                    array('rate_label',     __('Like this post? Rate it', 'pure') ),
                )
            );
            $RelatedPosts       = \Pure\Templates\Posts\Elements\Questions\RelatedPosts\Initialization::instance()->get('A');
            $innerHTMLPosts     = Initialization::instance()->html(
                'A/one_column_segment_comments',
                array(
                    array('title',   ''),
                    array('content', $RelatedPosts->innerHTML((object)array('post_id'=>$post->ID))),
                )
            );
            $RelatedPosts       = NULL;
            $RelatedQuestions   = \Pure\Templates\Posts\Elements\Questions\RelatedQuestions\Initialization::instance()->get('A');
            $innerHTMLQuestions = Initialization::instance()->html(
                'A/one_column_segment_comments',
                array(
                    array('title',   ''),
                    array('content', $RelatedQuestions->innerHTML((object)array('post_id'=>$post->ID))),
                )
            );
            $RelatedQuestions   = NULL;
            $innerHTMLAnswers   = Initialization::instance()->html(
                'A/one_column_segment_comments',
                array(
                    array('title',          ''                              ),
                    array('content',        $this->innerHTMLAnswers($post)  ),
                )
            );
            $innerHTML              = Initialization::instance()->html(
                'A/layout',
                array(
                    array('thumbnail',  ''                              ),
                    array('content',    $innerHTMLContent               ),
                    array('posts',      $innerHTMLPosts                 ),
                    array('questions',  $innerHTMLQuestions             ),
                    array('answers',    $innerHTMLAnswers               ),
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