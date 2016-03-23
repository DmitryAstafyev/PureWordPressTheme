<?php
namespace Pure\Templates\Elements\Categories{
    class D{
        public function innerHTML($items){
            $innerHTML =        '<div data-page-element-type="Pure.Footer.Elements.Categories.D">';
            if (is_array($items) !== false){
                foreach($items as $item){
                    $innerHTML .=   '<p data-page-element-type="Pure.Footer.Elements.Categories.D.Categories">'.
                                        '<a data-page-element-type="Pure.Footer.Elements.Categories.D" href="'.$item->url.'">'.$item->name.' (<span>'.$item->count.'</span>)</a>'.
                                    '</p>';
                }
            }else{
                $innerHTML .=       '<p data-page-element-type="Pure.Footer.Elements.Categories.D">'.__('no found', 'pure').'</p>';
            }
            $innerHTML .=       '</div>';
            return $innerHTML;
        }
    }
}
?>