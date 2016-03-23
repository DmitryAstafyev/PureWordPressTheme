<?php
namespace Pure\Templates\Breadcrumbs{
    class A{
        private function validate(&$parameters){
            $parameters = (is_object($parameters) === true ? $parameters : new \stdClass());
        }
        public function innerHTML($parameters = false){
            $this->validate($parameters);
            \Pure\Components\WordPress\Breadcrumbs\Initialization::instance()->attach();
            $Breadcrumbs    = new \Pure\Components\WordPress\Breadcrumbs\Provider();
            $breadcrumbs    = $Breadcrumbs->get();
            $Breadcrumbs    = NULL;
            $innerHTML      =   '<!--BEGIN: Breadcrumbs.A -->'.
                                '<div data-element-type="Pure.Breadcrumbs.A.Container">'.
                                    '<p data-element-type="Pure.Breadcrumbs.A.Item">';
            for($index = 0, $max_index = count($breadcrumbs); $index < $max_index; $index ++){
                $innerHTML     .=       '<a data-element-type="Pure.Breadcrumbs.A.Item" href="'.$breadcrumbs[$index]->url.'">'.$breadcrumbs[$index]->title.'</a>'.($index < $max_index - 1 ? '/' : '');
            }
            $innerHTML     .=       '</p>'.
                                    '<div data-element-type="Pure.Breadcrumbs.A.ResetFloat"></div>'.
                                '</div>'.
                                '<!--END: Breadcrumbs.A -->';

            return $innerHTML;
        }
    }
}
?>