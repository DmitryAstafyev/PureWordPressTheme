<?php
namespace Pure\Templates\Layout\Special\Search{
    class A{
        public function get(){
            $PostsProvider          = \Pure\Providers\Posts\Initialization::instance()->getCommon();
            $Counter                = \Pure\Templates\Counter\Initialization::instance()->get('C');
            $innerHTMLCounter       = $Counter->get(
                array(
                    (object)array(
                        'value'     =>$PostsProvider->get_posts_count_of_type(-1, 'all', true),
                        'label'     =>__('Total posts','pure'),
                        'button'    =>__('show','pure'),
                        'label_id'  =>'none',
                    ),
                    (object)array(
                        'value'     =>$PostsProvider->get_posts_count_of_type(-1, 'galleries', true),
                        'label'     =>__('Images and photos','pure'),
                        'button'    =>__('show','pure'),
                        'label_id'  =>'none'
                    ),
                    (object)array(
                        'value'     =>$PostsProvider->get_posts_count_of_type(-1, 'audio', true),
                        'label'     =>__('Audio and music','pure'),
                        'button'    =>__('show','pure'),
                        'label_id'  =>'none'
                    ),
                    (object)array(
                        'value'     =>$PostsProvider->get_posts_count_of_type(-1, 'media', true),
                        'label'     =>__('All media','pure'),
                        'button'    =>__('show','pure'),
                        'label_id'  =>'none'
                    ),
                )
            );
            $innerHTMLSearch       = Initialization::instance()->html(
                'A/search',
                array(
                    array('title',          __('Looking for something?') ),
                    array('search_action',  home_url( '/' )                     ),
                    array('search_query',   get_search_query()                  ),
                )
            );
            $innerHTMLCounter       = Initialization::instance()->html(
                'A/one_column_segment_clear',
                array(
                    array('title',      ''                  ),
                    array('content',    $innerHTMLCounter   ),
                )
            );
            $Counter                = NULL;
            $innerHTML              = Initialization::instance()->html(
                'A/layout',
                array(
                    array('search',     $innerHTMLSearch    ),
                    array('counter',    $innerHTMLCounter   ),
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