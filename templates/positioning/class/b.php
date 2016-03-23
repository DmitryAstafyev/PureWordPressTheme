<?php
namespace Pure\Templates\Positioning{
    class B{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->id                 = (isset($parameters->id                ) === true  ? $parameters->id               : uniqid()          );
            $parameters->column_width       = (isset($parameters->column_width      ) === true  ? $parameters->column_width     : '25em'            );
            $parameters->space              = (isset($parameters->space             ) === true  ? $parameters->space            : '1em'             );
            $parameters->node_type          = (isset($parameters->node_type         ) === true  ? $parameters->node_type        : '*'               );
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
        }
        private function resources(){
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addINIT(
                'pure.positioning.B',
                false,
                true
            );
        }
        public function get($insideInnerHTML, $parameters = NULL){
            $this->resources();
            $this->validate($parameters);
            $attribute_str  = ($parameters->attribute->name !== '' ? ' '.$parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $innerHTML      =   '<!--BEGIN: Positioning.B -->'.
                                '<div data-element-type="Pure.Positioning.B" '.
                                     'data-engine-element="Positioning.B" '.
                                     'data-engine-positioning-ID="'.$parameters->id.'" '.
                                     'data-engine-columnwidth="'.   $parameters->column_width.  '" '.
                                     'data-engine-space="'.         $parameters->space.         '" '.
                                     'data-engine-nodeType="'.      $parameters->node_type.     '" '.
                                     $attribute_str.' >'.
                                    '<div data-element-type="Pure.Positioning.B.Columns.Loading">'.
                                        '<div data-element-type="Pure.Positioning.Loading.B.Particle" data-addition-type="0"></div>'.
                                        '<div data-element-type="Pure.Positioning.Loading.B.Particle" data-addition-type="1"></div>'.
                                        '<div data-element-type="Pure.Positioning.Loading.B.Particle" data-addition-type="2"></div>'.
                                        '<div data-element-type="Pure.Positioning.Loading.B.Particle" data-addition-type="3"></div>'.
                                        '<div data-element-type="Pure.Positioning.Loading.B.Particle" data-addition-type="4"></div>'.
                                        '<div data-element-type="Pure.Positioning.Loading.B.Particle" data-addition-type="5"></div>'.
                                    '</div>'.
                                    '<div data-element-type="Pure.Positioning.B.Columns.Container" style="display:none">'.
                                        $insideInnerHTML.
                                    '</div>'.
                                    '<div data-element-type="Pure.Positioning.B.OtherNodes.Container">'.
                                    '</div>'.
                                '</div>'.
                                '<!--END: Positioning.B -->';
            return $innerHTML;
        }
    }
}
?>