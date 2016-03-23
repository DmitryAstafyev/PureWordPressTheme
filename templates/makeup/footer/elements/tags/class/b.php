<?php
namespace Pure\Templates\Makeup\Footer\Elements\Tags{
    class B{
        private function validate(&$parameters){
            return true;
        }
        public function innerHTML($parameters = false){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $tags = wp_tag_cloud('format=array' );
                $innerHTML .=       '<!--BEGIN: Footer.Elements.Tags.B -->'.
                                    '<div data-page-element-type="Pure.Footer.Elements.Tags.B">';
                if (is_array($tags) !== false){
                    foreach($tags as $tag){
                        $innerHTML .=       '<p data-page-element-type="Pure.Footer.Elements.Tags.B.Tags">'.
                                                $tag.
                                            '</p>';
                    }
                }else{
                    $innerHTML .=       '<p data-page-element-type="Pure.Footer.Elements.Tags.B">'.__('no tags found', 'pure').'</p>';
                }
                $innerHTML .=       '</div>'.
                                    '<!--END: Footer.Elements.Tags.B -->';
            }
            return $innerHTML;
        }
    }
}
?>