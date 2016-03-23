<?php
namespace Pure\Templates\Counter{
    class D{
        public function get($items){
            $innerHTML      = '';
            $innerHTMLItems = '';
            if (is_array($items) !== false){
                foreach($items as $item){
                    $innerHTMLItems .= Initialization::instance()->html(
                        'D/item',
                        array(
                            array('label_id',           (isset($item->label_id          ) !== false ? $item->label_id               : ''        )),
                            array('value',              (isset($item->value             ) !== false ? $item->value                  : ''        )),
                            array('label',              (isset($item->label             ) !== false ? $item->label                  : ''        )),
                            array('href',               (isset($item->href              ) !== false ? ' href="'.$item->href.'" '    : ''        )),
                            array('button',             (isset($item->button            ) !== false ? $item->button                 : ''        )),
                            array('icon',               (isset($item->icon              ) !== false ? $item->icon                   : ''        )),
                            array('progress_node_id',   (isset($item->progress_node_id  ) !== false ? $item->progress_node_id       : uniqid()  )),
                        )
                    );
                }
                if ($innerHTMLItems !== ''){
                    $innerHTML .= Initialization::instance()->html(
                        'D/wrapper',
                        array(
                            array('items', $innerHTMLItems),
                        )
                    );
                }
            }
            return $innerHTML;
        }
    }
}
?>