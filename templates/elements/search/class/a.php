<?php
namespace Pure\Templates\Elements\Search{
    class A{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : isset($parameters->background));
                $result = ($result === false ? false : isset($parameters->title     ));
                if ($result !== false){
                    $parameters->background = (int)$parameters->background;
                    $parameters->title      = (string)$parameters->title;
                    return true;
                }
            }
            return false;
        }
        private function innerHTMLIcons(){
            \Pure\Components\WordPress\Location\Special\Initialization::instance()->attach(true);
            $Posts          = \Pure\Providers\Posts\Initialization::instance()->getCommon();
            $SpecialURLs    = new \Pure\Components\WordPress\Location\Special\Register();
            $icons = array(
                (object)array(
                    'title' =>__('Posts', 'pure'),
                    'icon'  =>Initialization::instance()->configuration->urls->images.'/A/posts.png',
                    'count' =>$Posts->get_posts_count_of_post_type('post'),
                    'url'   =>$SpecialURLs->getURL('TOP',array('type'=>'post'))
                ),
                (object)array(
                    'title' =>__('Events', 'pure'),
                    'icon'  =>Initialization::instance()->configuration->urls->images.'/A/events.png',
                    'count' =>$Posts->get_posts_count_of_post_type('event'),
                    'url'   =>$SpecialURLs->getURL('TOP',array('type'=>'event'))
                ),
                (object)array(
                    'title' =>__('Reports', 'pure'),
                    'icon'  =>Initialization::instance()->configuration->urls->images.'/A/reports.png',
                    'count' =>$Posts->get_posts_count_of_post_type('report'),
                    'url'   =>$SpecialURLs->getURL('TOP',array('type'=>'report'))
                ),
                (object)array(
                    'title' =>__('Q&A', 'pure'),
                    'icon'  =>Initialization::instance()->configuration->urls->images.'/A/questions.png',
                    'count' =>$Posts->get_posts_count_of_post_type('question'),
                    'url'   =>$SpecialURLs->getURL('TOP',array('type'=>'question'))
                ),
            );
            $Posts          = NULL;
            $SpecialURLs    = NULL;
            $innerHTML = '';
            foreach($icons as $icon){
                $innerHTML .= Initialization::instance()->html(
                    'A/icon',
                    array(
                        array('icon',   $icon->icon     ),
                        array('name',   $icon->title    ),
                        array('count',  $icon->count    ),
                        array('url',    $icon->url      ),
                    )
                );
            }
            $innerHTML = preg_replace('/\r\n/',   '', $innerHTML);
            $innerHTML = preg_replace('/\n/',     '', $innerHTML);
            $innerHTML = preg_replace('/\t/',     '', $innerHTML);
            return $innerHTML;
        }
        public function innerHTML($parameters = false){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $attachment_url = false;
                if ($parameters->background > 0){
                    $attachment = wp_get_attachment_image_src( $parameters->background, 'full', false );
                    if (is_array($attachment) !== false){
                        $attachment_url = $attachment[0];
                    }
                }
                if ($attachment_url !== false){
                    $innerHTML   = Initialization::instance()->html(
                        'A/wrapper',
                        array(
                            array('title',          $parameters->title      ),
                            array('background',     $attachment_url         ),
                            array('icons',          $this->innerHTMLIcons() ),
                            array('search_action',  home_url( '/' )         ),
                            array('search_query',   get_search_query()      ),
                        )
                    );
                }
            }
            return $innerHTML;
        }
    }
}
?>