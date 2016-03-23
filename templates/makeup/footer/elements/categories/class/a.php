<?php
namespace Pure\Templates\Makeup\Footer\Elements\Categories{
    class A{
        private function validate(&$parameters){
            return true;
        }
        public function innerHTML($parameters = false){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $categories = get_terms('category', 'orderby=count&hide_empty=0');
                $innerHTML .=       '<!--BEGIN: Footer.Elements.Categories.A -->'.
                                    '<div data-page-element-type="Pure.Footer.Elements.Categories.A">';
                if (is_array($categories) !== false){
                    $innerHTML .=       '<p data-page-element-type="Pure.Footer.Elements.Categories.A">'.__('Categories cloud', 'pure').'</p>';
                    foreach($categories as $category){
                    $innerHTML .=       '<p data-page-element-type="Pure.Footer.Elements.Categories.A.Categories">'.
                                            '<a data-page-element-type="Pure.Footer.Elements.Categories.A" href="'.get_category_link($category->term_id).'">'.$category->name.' (<span>'.$category->count.'</span>)</a>'.
                                        '</p>';
                    }
                }else{
                    $innerHTML .=       '<p data-page-element-type="Pure.Footer.Elements.Categories.A">'.__('no categories found', 'pure').'</p>';
                }
                $innerHTML .=       '</div>'.
                                    '<!--END: Footer.Elements.Categories.A -->';
            }
            return $innerHTML;
        }
    }
}
?>