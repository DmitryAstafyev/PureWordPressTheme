<?php
namespace Pure\Templates\Footage{
    class A{
        private function innerHTMLMore($parameters){
            $innerHTML = '';
            if ($parameters->link !== '' && $parameters->link_label !== ''){
                $innerHTML = Initialization::instance()->html(
                    'A/more',
                    array(
                        array('url',    $parameters->link       ),
                        array('more',   $parameters->link_label ),
                    )
                );
            }
            return $innerHTML;
        }
        private function innerHTMLText($parameters){
            $innerHTML = '';
            if ($parameters->description !== ''){
                $innerHTML = Initialization::instance()->html(
                    'A/text',
                    array(
                        array('title',          $parameters->title                  ),
                        array('description',    $parameters->description            ),
                        array('more',           $this->innerHTMLMore($parameters)   ),
                    )
                );
            }
            return $innerHTML;
        }
        public function get($parameters){
            $innerHTMLSources = '';
            foreach($parameters->sources as $source){
                $innerHTMLSources .= Initialization::instance()->html(
                    'A/source',
                    array(
                        array('type',   $source->type),
                        array('src',    $source->src),
                    )
                );
            }
            $innerHTML = Initialization::instance()->html(
                'A/wrapper',
                array(
                    array('background', $parameters->alt_background         ),
                    array('sources',    $innerHTMLSources                   ),
                    array('text',       $this->innerHTMLText($parameters)   ),
                )
            );
            return $innerHTML;
        }
    }
}
?>