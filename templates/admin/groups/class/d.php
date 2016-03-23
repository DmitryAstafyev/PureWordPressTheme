<?php
namespace Pure\Templates\Admin\Groups{
    class D{
        private function validate(&$params, $close = false){
            if ($close === false){
                $params["echo"              ] = (isset($params["echo"               ]) ? $params["echo"             ] : true        );
                $params["title"             ] = (isset($params["title"              ]) ? $params["title"            ] : 'no title'  );
                $params["style_container"   ] = (isset($params["style_container"    ]) ? $params["style_container"  ] : ''          );
                $params["style_caption"     ] = (isset($params["style_caption"      ]) ? $params["style_caption"    ] : ''          );
                $params["style_content"     ] = (isset($params["style_content"      ]) ? $params["style_content"    ] : ''          );
                $params["container_attr"    ] = (isset($params["container_attr"     ]) ? $params["container_attr"   ] : ''          );
                $params["remove_attr"       ] = (isset($params["remove_attr"        ]) ? $params["remove_attr"      ] : ''          );
                $params["remove_title"      ] = (isset($params["remove_title"       ]) ? $params["remove_title"     ] : ''          );
                $params["on_change"         ] = (isset($params["on_change"          ]) ? $params["on_change"        ] : ''          );
                $params["id"                ] = (isset($params["id"                 ]) ? $params["id"               ] : uniqid()    );
                $params["opened"            ] = (isset($params["opened"             ]) ? $params["opened"           ] : false       );
            }else{
                $params["echo"              ] = (isset($params["echo"               ]) ? $params["echo"             ] : true        );
            }
        }
        public function open($params){
            $this->validate($params, false);
            $innerHTMLRemove        = '';
            if ($params["remove_attr"] !== '' || $params["remove_title"] !== ''){
                $innerHTMLRemove    =  Initialization::instance()->html(
                    'D/remove',
                    array(
                        array('remove_attr',        $params["remove_attr"       ]),
                        array('remove_title',       $params["remove_title"      ]),
                    )
                );
            }
            $innerHTML              =  Initialization::instance()->html(
                'D/open',
                array(
                    array('title',              $params["title"             ]),
                    array('style_container',    $params["style_container"   ]),
                    array('style_caption',      $params["style_caption"     ]),
                    array('style_content',      $params["style_content"     ]),
                    array('container_attr',     $params["container_attr"    ]),
                    array('remove',             $innerHTMLRemove             ),
                    array('id',                 $params["id"                ]),
                    array('on_change',          $params["on_change"         ]),
                    array('opened',             ($params["opened"] === false ? 'checked' : '')),
                )
            );
            if ($params["echo"] === true){
                echo $innerHTML;
            }else{
                return $innerHTML;
            }
        }
        public function close($params){
            $innerHTML =  Initialization::instance()->html(
                'D/close',
                array()
            );
            if ($params["echo"] === true){
                echo $innerHTML;
            }else{
                return $innerHTML;
            }
        }
    }
}
?>