<?php
namespace Pure\Templates\Elements\Search{
    class B{
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
        public function innerHTML($parameters = false){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $SpecialURLs    = new \Pure\Components\WordPress\Location\Special\Register();
                $form_action    = $SpecialURLs->getURL(
                    'ASEARCH',
                    array(
                        'categories'=>'[categories]',
                        'tags'      =>'[tags]'
                    )
                );
                $SpecialURLs    = NULL;
                $form_id        = uniqid();
                $attachment_url = false;
                if ($parameters->background > 0){
                    $attachment = wp_get_attachment_image_src( $parameters->background, 'full', false );
                    if (is_array($attachment) !== false){
                        $attachment_url = $attachment[0];
                    }
                }
                $Terms          = \Pure\Templates\Elements\TermsSelector\Initialization::instance()->get('A');
                $innerHTML      = Initialization::instance()->html(
                    'B/wrapper',
                    array(
                        array('title_site',         get_bloginfo('name')        ),
                        array('description_site',   get_bloginfo('description') ),
                        array('search_holder',      __('write here what are you looking for', 'pure')   ),
                        array('label_0',            __('or choose what you what to see', 'pure')        ),
                        array('label_1',            __('show posts', 'pure')                            ),
                        array('category',           $Terms->innerHTML(
                                'category',
                                __('select categories', 'pure'),
                                $form_id,
                                'search_categories',
                                'data-engine-advanced-search-category="'.$form_id.'"'
                            )
                        ),
                        array('tag',                $Terms->innerHTML(
                                'post_tag',
                                __('select tags', 'pure'),
                                $form_id,
                                'search_tags',
                                'data-engine-advanced-search-tag="'.$form_id.'"'
                            )
                        ),
                        array('background',                 $attachment_url         ),
                        array('search_action',              home_url( '/' )         ),
                        array('search_query',               get_search_query()      ),
                        array('advanced_search_form_id',    $form_id                ),
                        array('advanced_search_action',     $form_action            ),
                    )
                );
                $Terms          = NULL;
                \Pure\Components\Dialogs\B\Initialization::instance()->attach();
            }
            return $innerHTML;
        }
    }
}
?>