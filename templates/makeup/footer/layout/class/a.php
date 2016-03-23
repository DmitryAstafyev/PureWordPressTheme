<?php
namespace Pure\Templates\Makeup\Footer\Layout{
    class A{
        private function validate(&$parameters){
            return true;
        }
        private function innerHTMLTags(){
            $Template   = \Pure\Templates\Makeup\Footer\Elements\Tags\Initialization::instance()->get('A');
            $innerHTML  = $Template->innerHTML();
            $Template   = NULL;
            return $innerHTML;
        }
        private function innerHTMLMenu(){
            $Template   = \Pure\Templates\Makeup\Footer\Elements\Menu\Initialization::instance()->get('A');
            $innerHTML  = $Template->innerHTML();
            $Template   = NULL;
            return $innerHTML;
        }
        private function innerHTMLCategories(){
            $Template   = \Pure\Templates\Makeup\Footer\Elements\Categories\Initialization::instance()->get('A');
            $innerHTML  = $Template->innerHTML();
            $Template   = NULL;
            return $innerHTML;
        }
        private function innerHTMLHotLinks(){
            $Template   = \Pure\Templates\Makeup\Footer\Elements\HotLinks\Initialization::instance()->get('A');
            $innerHTML  = $Template->innerHTML();
            $Template   = NULL;
            return $innerHTML;
        }
        private function innerHTMLLabel(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->footer->properties;
            $settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            return htmlspecialchars_decode($settings->label);
        }
        private function innerHTMLBreadcrumbs(){
            //Get breadcrumbs
            $Breadcrumbs    = \Pure\Templates\Breadcrumbs\Initialization::instance()->get('A');
            $innerHTML      = $Breadcrumbs->innerHTML();
            $Breadcrumbs    = NULL;
            return $innerHTML;
        }
        public function innerHTML($parameters = false){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $innerHTML .=       '<!--BEGIN: Footer.A -->'.
                                    '<div data-page-element-type="Pure.Footer.A">'.
                                        '<div data-page-element-type="Pure.Footer.A.Horizontal">'.
                                            $this->innerHTMLBreadcrumbs().
                                        '</div>'.
                                        '<div data-page-element-type="Pure.Footer.A.Container">'.
                                            '<div data-page-element-type="Pure.Footer.A.Column">'.
                                                $this->innerHTMLMenu().
                                            '</div>'.
                                            '<div data-page-element-type="Pure.Footer.A.Column">'.
                                                $this->innerHTMLTags().
                                            '</div>'.
                                            '<div data-page-element-type="Pure.Footer.A.Column">'.
                                                $this->innerHTMLCategories().
                                            '</div>'.
                                            '<div data-page-element-type="Pure.Footer.A.Column">'.
                                                $this->innerHTMLHotLinks().
                                            '</div>'.
                                        '</div>'.
                                        '<div data-page-element-type="Pure.Footer.A.Label">'.
                                            $this->innerHTMLLabel().
                                        '</div>'.
                                    '</div>'.
                                    '<!--END: Footer.A -->';
            }
            return $innerHTML;
        }
    }
}
?>