<?php
namespace Pure\Templates\Admin\Groups{
    class B{
        private function validate(&$params, $close = false){
            if ($close === false){
                $params["echo"              ] = (isset($params["echo"               ]) ? $params["echo"             ] : true                                    );
                $params["title"             ] = (isset($params["title"              ]) ? $params["title"            ] : 'no title'                              );
                $params["group"             ] = (isset($params["group"              ]) ? $params["group"            ] : 'no_group'                              );
                $params["caption_style"     ] = (isset($params["caption_style"      ]) ? $params["caption_style"    ] : ''                                      );
                $params["container_style"   ] = (isset($params["container_style"    ]) ? $params["container_style"  ] : ''                                      );
                $params["content_style"     ] = (isset($params["content_style"      ]) ? $params["content_style"    ] : ''                                      );
                $params["id"                ] = (isset($params["id"                 ]) ? $params["id"               ] : uniqid('Admin_Groups_B_'));
                $params["opened"            ] = (isset($params["opened"             ]) ? $params["opened"           ] : false                                      );
            }else{
                $params["echo"  ] = (isset($params["echo"   ]) ? $params["echo" ] : true                                    );
            }
        }
        public function open($params){
            $this->validate($params, false);
            $innerHTML =    '<!--Pure.Configuration.Groups-->'.
                            '<div data-element-type="Pure.Groups.B.Item" style="'.$params["container_style" ].'">'.
                                '<input data-element-type="Pure.Groups.B.Item" id="'.$params["id"].'" name="'.$params["group"].'" '.($params["opened"] === false ? 'checked' : '').' type="checkbox"/>'.
                                '<div data-element-type="Pure.Groups.B.Title" style="'.$params["caption_style" ].'">'.
                                    '<label for="'.$params["id"].'">'.
                                        '<div data-element-type="Pure.Groups.B.Icon.Container">'.
                                            '<div data-element-type="Pure.Groups.B.Icon.Open"></div>'.
                                            '<div data-element-type="Pure.Groups.B.Icon.Close"></div>'.
                                        '</div>'.
                                    '</label>'.
                                    '<p data-element-type="Pure.Groups.B.Title">'.$params["title"].'</p>'.
                                '</div>'.
                                '<div data-element-type="Pure.Groups.B.Content" style="'.$params["content_style" ].'">';
            if ($params["echo"] === true){
                echo $innerHTML;
            }else{
                return $innerHTML;
            }
        }
        public function close($params){
            $this->validate($params, true);
            $innerHTML =        '</div>'.
                            '</div>'.
                            '<!--Pure.Configuration.Groups-->';
            if ($params["echo"] === true){
                echo $innerHTML;
            }else{
                return $innerHTML;
            }
        }
    }
}
?>