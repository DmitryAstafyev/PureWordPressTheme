<?php
namespace Pure\Templates\PresentationTabs{
    class B{
        private function validateCaption($item){
            $result = true;
            $result = ($result !== false ? isset($item->caption     ) : false);
            $result = ($result !== false ? isset($item->icon        ) : false);
            return $result;
        }
        private function validateItem($item){
            $result = true;
            $result = ($result !== false ? isset($item->title       ) : false);
            $result = ($result !== false ? isset($item->description ) : false);
            $result = ($result !== false ? isset($item->image       ) : false);
            $result = ($result !== false ? isset($item->url         ) : false);
            return $result;
        }
        private function innerHTMLCaption($id, $item){
            $innerHTML = '';
            if ($this->validateCaption($item) !== false){
                if ($item->icon !== ''){
                    $innerHTML = Initialization::instance()->html(
                        'B/caption_with_icon',
                        array(
                            array('tab_id',         $id             ),
                            array('caption_icon',   $item->icon     ),
                            array('caption',        $item->caption  ),
                        )
                    );
                }else{
                    $innerHTML = Initialization::instance()->html(
                        'B/caption_no_icon',
                        array(
                            array('tab_id',         $id             ),
                            array('caption',        $item->caption  ),
                        )
                    );
                }
            }
            return $innerHTML;
        }
        private function innerHTMLItem($id, $group, $item, $selected){
            $innerHTML = '';
            if ($this->validateItem($item) !== false){
                if ($item->title !== '' && $item->description !== ''){
                    $innerHTMLReadMore = '';
                    if ($item->url !== ''){
                        $innerHTMLReadMore = Initialization::instance()->html(
                            'B/item_read_more',
                            array(
                                array('url',  $item->url                    ),
                                array('more', __('read more', 'pure')),
                            )
                        );
                    }
                    $innerHTML = Initialization::instance()->html(
                        'B/item_with_text',
                        array(
                            array('selected',       $selected           ),
                            array('tab_id',         $id                 ),
                            array('group_id',       $group              ),
                            array('image',          $item->image        ),
                            array('title',          $item->title        ),
                            array('description',    $item->description  ),
                            array('read_more',      $innerHTMLReadMore  ),
                        )
                    );
                }else{
                    $innerHTML = Initialization::instance()->html(
                        'B/item_no_text',
                        array(
                            array('selected',       $selected           ),
                            array('tab_id',         $id                 ),
                            array('group_id',       $group              ),
                            array('image',          $item->icon         ),
                        )
                    );
                }
            }
            return $innerHTML;
        }
        public function get($items){
            $innerHTMLCaptions  = '';
            $innerHTMLItems     = '';
            $id_basic           = uniqid();
            $index              = 0;
            foreach($items as $item){
                $innerHTMLCaptions  .= $this->innerHTMLCaption  ($id_basic.$index, $item);
                $innerHTMLItems     .= $this->innerHTMLItem     ($id_basic.$index, $id_basic, $item, ($index === 0 ? 'checked' : ''));
                $index ++;
            }
            $innerHTML = Initialization::instance()->html(
                'B/wrapper',
                array(
                    array('captions',   preg_replace('/\n\r/','', $innerHTMLCaptions)   ),
                    array('items',      $innerHTMLItems                                 ),
                )
            );
            return $innerHTML;
        }
    }
}
?>