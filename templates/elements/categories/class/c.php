<?php
namespace Pure\Templates\Elements\Categories{
    class C{
        public function innerHTML($items){
            $innerHTML =        '<div data-page-element-type="Pure.Footer.Elements.Categories.C">';
            if (is_array($items) !== false){
                foreach($items as $item){
                    $innerHTML .=   '<p data-page-element-type="Pure.Footer.Elements.Categories.C.Categories">'.
                                        '<a data-page-element-type="Pure.Footer.Elements.Categories.C" href="'.$item->url.'">'.$item->name.' (<span>'.$item->count.'</span>)</a>'.
                                    '</p>';
                }
            }else{
                $innerHTML .=       '<p data-page-element-type="Pure.Footer.Elements.Categories.C">'.__('no found', 'pure').'</p>';
            }
            $innerHTML .=       '</div>';
            return $innerHTML;
        }
    }
}
?>