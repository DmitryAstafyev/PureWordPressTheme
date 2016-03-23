<?php
namespace Pure\Templates\Elements\Categories{
    class B{
        public function innerHTML($items){
            $innerHTML =        '<div data-page-element-type="Pure.Footer.Elements.Categories.B">';
            if (is_array($items) !== false){
                foreach($items as $item){
                    $innerHTML .=   '<p data-page-element-type="Pure.Footer.Elements.Categories.B.Categories">'.
                                        '<a data-page-element-type="Pure.Footer.Elements.Categories.B" href="'.$item->url.'">'.$item->name.' (<span>'.$item->count.'</span>)</a>'.
                                    '</p>';
                }
            }else{
                $innerHTML .=       '<p data-page-element-type="Pure.Footer.Elements.Categories.B">'.__('no found', 'pure').'</p>';
            }
            $innerHTML .=       '</div>';
            return $innerHTML;
        }
    }
}
?>