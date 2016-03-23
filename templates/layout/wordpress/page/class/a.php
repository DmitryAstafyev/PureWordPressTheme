<?php
namespace Pure\Templates\Layout\WordPress\Page{
    class A{
        private function innerHTMLTitle($post_id){
            $PostData   = \Pure\Providers\Posts\Initialization::instance()->getCommon();
            $post       = $PostData->get($post_id, false);
            $PostData   = NULL;
            return Initialization::instance()->html(
                'A/about',
                array(
                    array('name',   $post->post->title                                  ),
                    array('info',   __('views', 'pure').': '.$post->post->views  ),
                )
            );
        }
        private function innerHTMLContent($post){
            \Pure\Components\WordPress\Post\Elements\Initialization::instance()->attach();
            $PostElements   = new \Pure\Components\WordPress\Post\Elements\Setup($post);
            $PostElements->setup();
            $innerHTML      = $post->post_content;
            $innerHTML      = apply_filters('the_content', $innerHTML);
            $innerHTML      = $PostElements->parseImages($innerHTML);
            $PostElements   = NULL;
            return Initialization::instance()->html(
                'A/one_column_segment_content',
                array(
                    array('title',      $post->post_title                           ),
                    array('content',    str_replace(']]>', ']]&gt;', $innerHTML)    ),
                )
            );
        }
        public function get($post){
            $innerHTML              = Initialization::instance()->html(
                'A/layout',
                array(
                    array('content',    $this->innerHTMLContent($post)),
                )
            );
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