<?php
namespace Pure\Templates\Makeup\Footer\Elements\Tags{
    class A{
        private function validate(&$parameters){
            return true;
        }
        public function innerHTML($parameters = false){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $tags = wp_tag_cloud('format=array' );
                $innerHTML .=       '<!--BEGIN: Footer.Elements.Tags.A -->'.
                                    '<div data-page-element-type="Pure.Footer.Elements.Tags.A">';
                if (is_array($tags) !== false){
                    $innerHTML .=       '<p data-page-element-type="Pure.Footer.Elements.Tags.A">'.__('Tags cloud', 'pure').'</p>';
                    foreach($tags as $tag){
                    $innerHTML .=       '<p data-page-element-type="Pure.Footer.Elements.Tags.A.Tags">'.
                                            $tag.
                                        '</p>';
                    }
                }else{
                    $innerHTML .=       '<p data-page-element-type="Pure.Footer.Elements.Tags.A">'.__('no tags found', 'pure').'</p>';
                }
                $innerHTML .=       '</div>'.
                                    '<!--END: Footer.Elements.Tags.A -->';
            }
            return $innerHTML;
        }
    }
}
?>