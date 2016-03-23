<?php
namespace Pure\Templates\Positioning{
    class A{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->id                 = (isset($parameters->id                ) === true  ? $parameters->id               : uniqid()          );
            $parameters->min_width          = (isset($parameters->min_width         ) === true  ? $parameters->min_width        : '300'            );
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
        }
        private function innerHTMLItems($items){
            $innerHTML  = '';
            $IDs        = array();
            foreach($items as $item){
                $IDs[]      = (object)array(
                    'id'        =>'[[['.uniqid().']]]',
                    'content'   =>$item
                );
                $innerHTML .= Initialization::instance()->html(
                    'A/item',
                    array(
                        array('content',  $IDs[count($IDs) - 1]->id),
                    )
                );
            }
            $innerHTML = preg_replace('/\r\n/',   '', $innerHTML);
            $innerHTML = preg_replace('/\n/',     '', $innerHTML);
            $innerHTML = preg_replace('/\t/',     '', $innerHTML);
            foreach($IDs as $item){
                $innerHTML = str_replace($item->id, $item->content, $innerHTML);
            }
            return $innerHTML;
        }
        public function get($items, $parameters = NULL){
            $this->validate($parameters);
            $attribute_str  = ($parameters->attribute->name !== '' ? ' '.$parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $innerHTML      = Initialization::instance()->html(
                'A/wrapper',
                array(
                    array('min_width',  $parameters->min_width          ),
                    array('attribute',  $attribute_str                  ),
                    array('items',      $this->innerHTMLItems($items)   ),
                )
            );
            return $innerHTML;
        }
    }
}
?>