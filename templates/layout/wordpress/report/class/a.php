<?php
namespace Pure\Templates\Layout\WordPress\Report{
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
                    'object_type'   =>'EDITREPORT'
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
        private function innerHTMLIndexes($post){
            $Indexes    = \Pure\Templates\Reports\Indexes\Initialization::instance()->get('A');
            $innerHTML  = $Indexes->get($post->ID);
            $Indexes    = NULL;
            return ($innerHTML !== false ? $innerHTML : '');
        }
        private function innerHTMLPlace($post, $map){
            $Place      = \Pure\Templates\Posts\Elements\GoogleMap\Initialization::instance()->get('A');
            $innerHTML  = $Place->innerHTML(
                (object)array(
                    'post_id'   =>$post->ID,
                    'on_map'    =>$map->on_map,
                    'place'     =>$map->place
                )
            );
            $Place      = NULL;
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
        public function get($post){
            //Attach styles
            \Pure\Templates\Posts\Elements\Style\Initialization::instance()->get_resources_of('A');
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
            $innerHTMLIndexes = Initialization::instance()->html(
                'A/one_column_segment_indexes',
                array(
                    array('indexes', $this->innerHTMLIndexes($post)),
                )
            );
            \Pure\Components\PostTypes\Reports\Module\Initialization::instance()->attach();
            $Reports    = new \Pure\Components\PostTypes\Reports\Module\Provider();
            $map        = $Reports->getPlace($post->ID);
            $Reports    = NULL;
            if ($map->on_map !== ''){
                $innerHTMLPlace = Initialization::instance()->html(
                    'A/one_column_segment_place',
                    array(
                        array('place', $this->innerHTMLPlace($post, $map)),
                    )
                );
            }else{
                $innerHTMLPlace = '';
            }
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
                    array('thumbnail',  ''                              ),
                    array('content',    $innerHTMLContent               ),
                    array('indexes',    $innerHTMLIndexes               ),
                    array('place',      $innerHTMLPlace                 ),
                    array('comments',   $innerHTMLComments              ),
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