<?php
namespace Pure\Templates\Elements\CounterWrapper{
    class A{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : isset($parameters->background));
                $result = ($result === false ? false : isset($parameters->items     ));
                if ($result !== false){
                    $parameters->background = (int)$parameters->background;
                    if (is_array($parameters->items) !== false){
                        return true;
                    }
                }
            }
            return false;
        }
        private function innerHTMLIcons($icons){
            $innerHTML = '';
            foreach($icons as $icon){
                if ($icon->url === ''){
                    $innerHTMLTitle = Initialization::instance()->html(
                        'A/title',
                        array(
                            array('name',   $icon->title    ),
                        )
                    );
                }else{
                    $innerHTMLTitle = Initialization::instance()->html(
                        'A/link',
                        array(
                            array('name',   $icon->title    ),
                            array('url',    $icon->url      ),
                        )
                    );
                }
                if ($icon->count !== ''){
                    $innerHTMLCount = Initialization::instance()->html(
                        'A/count',
                        array(
                            array('count',   $icon->count    ),
                        )
                    );
                }else{
                    $innerHTMLCount = '';
                }
                $innerHTML .= Initialization::instance()->html(
                    'A/icon',
                    array(
                        array('icon',   $icon->icon         ),
                        array('title',  $innerHTMLTitle     ),
                        array('count',  $innerHTMLCount     ),
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
                            array('background',     $attachment_url                             ),
                            array('top',            $parameters->offset.'%'                     ),
                            array('height',         (100 - $parameters->offset).'%'             ),
                            array('icons',          $this->innerHTMLIcons($parameters->items)   ),
                        )
                    );
                }
            }
            return $innerHTML;
        }
    }
}
?>