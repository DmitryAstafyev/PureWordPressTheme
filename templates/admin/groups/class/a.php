<?php
namespace Pure\Templates\Admin\Groups{
    class A{
        private function validate(&$params, $close = false){
            if ($close === false){
                $params["echo"              ] = (isset($params["echo"               ]) ? $params["echo"             ] : true        );
                $params["title"             ] = (isset($params["title"              ]) ? $params["title"            ] : 'no title'  );
                $params["group"             ] = (isset($params["group"              ]) ? $params["group"            ] : 'no_group'  );
                $params["caption_style"     ] = (isset($params["caption_style"      ]) ? $params["caption_style"    ] : ''          );
                $params["container_style"   ] = (isset($params["container_style"    ]) ? $params["container_style"  ] : ''          );
                $params["id"                ] = (isset($params["id"                 ]) ? $params["id"               ] : uniqid()    );
            }else{
                $params["echo"              ] = (isset($params["echo"               ]) ? $params["echo" ] : true                    );
            }
        }
        public function open($params){
            $this->validate($params, false);
            $innerHTML =    '<!--Pure.Groups.A-->'.
                            '<div data-type="Pure.Groups.A.Container">'.
                                '<input name="'.$params["group"].'" id="'.$params["id"].'" data-type="Pure.Groups.A" type="checkbox" />'.
                                '<label for="'.$params["id"].'">'.
                                    '<h3 data-type="Pure.Groups.A" style="'.$params["caption_style" ].'">'.$params["title"].'</h3>'.
                                '</label>'.
                                '<div data-type="Pure.Groups.A" style="'.$params["container_style" ].'">';
            if ($params["echo"] === true){
                echo $innerHTML;
            }else{
                return $innerHTML;
            }
        }
        public function close($params){
            $this->validate($params, true);

            $innerHTML =        '</div>'.
                            '</div>';
                            '<!--Pure.Groups.A-->';
            if ($params["echo"] === true){
                echo $innerHTML;
            }else{
                return $innerHTML;
            }
        }
    }
}
?>