<?php
namespace Pure\Templates\HighLights{
    class A{
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
                    'A/item_read_url',
                    array(
                        array('url',            $item->url                  ),
                        array('read',           __('read', 'pure')   ),
                    )
                );
            }
            return $innerHTML;
        }
        private function innerHTMLItem($item){
            $innerHTML = '';
            if ($this->validate($item) !== false){
                if ($item->title !== '' && $item->description !== ''){
                    if ($item->icon !== ''){
                        //Normal
                        $innerHTML = Initialization::instance()->html(
                            'A/item_normal',
                            array(
                                array('icon',           $item->icon                 ),
                                array('title',          $item->title                ),
                                array('description',    $item->description          ),
                                array('read',           $this->innerHTMLRead($item) ),
                            )
                        );
                    }else{
                        //Only text
                        $innerHTML = Initialization::instance()->html(
                            'A/item_text',
                            array(
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
                            'A/item_icon',
                            array(
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
            foreach($items as $item){
                $innerHTMLItems .= $this->innerHTMLItem($item);
            }
            $innerHTML = Initialization::instance()->html(
                'A/wrapper',
                array(
                    array('items', $innerHTMLItems),
                )
            );
            \Pure\Components\Effects\Appear\Initialization::instance()->attach();
            return $innerHTML;
        }
    }
}
?>