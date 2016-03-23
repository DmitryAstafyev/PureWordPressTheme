<?php
namespace Pure\Templates\Elements\Categories{
    class A{
        public function innerHTML($items){
            $innerHTML =        '<div data-page-element-type="Pure.Footer.Elements.Categories.A">';
            if (is_array($items) !== false){
                foreach($items as $item){
                    $innerHTML .=   '<p data-page-element-type="Pure.Footer.Elements.Categories.A.Categories">'.
                                        '<a data-page-element-type="Pure.Footer.Elements.Categories.A" href="'.$item->url.'">'.$item->name.' (<span>'.$item->count.'</span>)</a>'.
                                    '</p>';
                }
            }else{
                $innerHTML .=       '<p data-page-element-type="Pure.Footer.Elements.Categories.A">'.__('no found', 'pure').'</p>';
            }
            $innerHTML .=       '</div>';
            return $innerHTML;
        }
    }
}
?>