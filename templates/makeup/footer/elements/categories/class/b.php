<?php
namespace Pure\Templates\Makeup\Footer\Elements\Categories{
    class B{
        private function validate(&$parameters){
            return true;
        }
        public function innerHTML($parameters = false){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $categories = get_terms('category', 'orderby=count&hide_empty=0');
                $innerHTML .=       '<!--BEGIN: Footer.Elements.Categories.B -->'.
                                    '<div data-page-element-type="Pure.Footer.Elements.Categories.B">';
                if (is_array($categories) !== false){
                    foreach($categories as $category){
                        $innerHTML .=   '<p data-page-element-type="Pure.Footer.Elements.Categories.B.Categories">'.
                                            '<a data-page-element-type="Pure.Footer.Elements.Categories.B" href="'.get_category_link($category->term_id).'">'.$category->name.' (<span>'.$category->count.'</span>)</a>'.
                                        '</p>';
                    }
                }else{
                    $innerHTML .=       '<p data-page-element-type="Pure.Footer.Elements.Categories.B">'.__('no categories found', 'pure').'</p>';
                }
                $innerHTML .=       '</div>'.
                                    '<!--END: Footer.Elements.Categories.B -->';
            }
            return $innerHTML;
        }
    }
}
?>