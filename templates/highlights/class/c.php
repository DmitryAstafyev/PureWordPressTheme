<?php
namespace Pure\Templates\HighLights{
    class C{
        private function validate($item){
            $result = true;
            $result = ($result !== false ? isset($item->title       ) : false);
            $result = ($result !== false ? isset($item->description ) : false);
            $result = ($result !== false ? isset($item->icon        ) : false);
            $result = ($result !== false ? isset($item->url         ) : false);
            return $result;
        }
        private function innerHTMLRead($item){
            $innerHTML = '';
            if ($item->url !== ''){
                $innerHTML = Initialization::instance()->html(
                    'C/item_read_url',
                    array(
                        array('url',            $item->url                  ),
                        array('read',           __('read', 'pure')   ),
                    )
                );
            }
            return $innerHTML;
        }
        private function innerHTMLItem($item, $group_id, $is_first){
            $innerHTML = '';
            if ($this->validate($item) !== false){
                $item_id = uniqid();
                if ($item->title !== '' && $item->description !== ''){
                    if ($item->icon !== ''){
                        //Normal
                        $innerHTML = Initialization::instance()->html(
                            'C/item_normal',
                            array(
                                array('checked',        ($is_first === false ? '' : ' checked')),
                                array('group',          $group_id                   ),
                                array('item_id',        $item_id                    ),
                                array('icon',           $item->icon                 ),
                                array('title',          $item->title                ),
                                array('description',    $item->description          ),
                                array('read',           $this->innerHTMLRead($item) ),
                            )
                        );
                    }else{
                        //Only text
                        $innerHTML = Initialization::instance()->html(
                            'C/item_text',
                            array(
                                array('checked',        ($is_first === false ? '' : ' checked')),
                                array('group',          $group_id                   ),
                                array('item_id',        $item_id                    ),
                                array('title',          $item->title                ),
                                array('description',    $item->description          ),
                                array('read',           $this->innerHTMLRead($item) ),
                            )
                        );
                    }
                }else{
                    if ($item->icon !== ''){
                        //Only icon
                        $innerHTML = Initialization::instance()->html(
                            'C/item_icon',
                            array(
                                array('checked',        ($is_first === false ? '' : ' checked')),
                                array('group',          $group_id                   ),
                                array('item_id',        $item_id                    ),
                                array('icon',           $item->icon                 ),
                                array('url',            $item->url                  ),
                            )
                        );
                    }
                }
            }
            return $innerHTML;
        }
        public function get($items){
            $innerHTMLItems = '';
            $group_id       = uniqid();
            $first          = true;
            foreach($items as $item){
                $innerHTMLItems .= $this->innerHTMLItem($item, $group_id, $first);
                $first          = false;
            }
            $innerHTML = Initialization::instance()->html(
                'C/wrapper',
                array(
                    array('items', $innerHTMLItems),
                )
            );
            return $innerHTML;
        }
    }
}
?>