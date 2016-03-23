<?php
namespace Pure\Templates\Titles{
    class G{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->echo           = (isset($parameters->echo          ) === true  ? $parameters->echo         : false             );
            $parameters->link           = (isset($parameters->link          ) === true  ? $parameters->link         : new \stdClass()   );
            $parameters->link->tagret   = (isset($parameters->link->tagret  ) === true  ? $parameters->link->tagret : 'blank'           );
            $parameters->link->href     = (isset($parameters->link->href    ) === true  ? $parameters->link->href   : ''                );
            $parameters->reset_float    = (isset($parameters->reset_float   ) === true  ? $parameters->reset_float  : true              );
        }
        public function get($title, $parameters = NULL){
            $this->validate($parameters);
            $innerHTML =    '<!--BEGIN: Title -->'.
                            ($parameters->reset_float === true ? '<div style="clear: both;"></div>' : '').
                            '<div data-element-type="Pure.Title.G">'.
                                '<div data-element-type="Pure.Title.G.Container">';
            if ($parameters->link->href !== ''){
                $innerHTML .=       '<a data-element-type="Pure.Title.G" href="'.$parameters->link->href.'" target="'.$parameters->link->tagret.'"><strong>'.$title.'</strong></a>';
            }else{
                $innerHTML .=       '<p data-element-type="Pure.Title.G">'.$title.'</p>';
            }
            $innerHTML .=       '</div>'.
                            '</div>'.
                            '<!--END: Title -->';
            if ($parameters->echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
    }
}
?>