<?php
namespace Pure\Templates\Pages\Error{
    class A{
        public function message($title, $message, $echo = false){
            $innerHTML =    '<!doctype html>'.
                            '<html>'.
                            '<head>'.
                                '<meta charset="utf-8">'.
                                '<title>Server message</title>'.
                                '<style type="text/css">'.
                                    'body {'.
                                        'margin  : 0;'.
                                        'padding : 0;'.
                                        'width   : 100%;'.
                                        'height  : 100%;'.
                                        'overflow:hidden;'.
                                    '}'.
                                    '.Background{'.
                                        'position        : absolute;'.
                                        'width           : 100%;'.
                                        'height          : 100%;'.
                                        'background      : rgb(212,228,239);'.
                                        'background      : -moz-linear-gradient(top,  rgba(212,228,239,1) 0%, rgba(134,174,204,1) 100%);'.
                                        'background      : -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(212,228,239,1)), color-stop(100%,rgba(134,174,204,1)));'.
                                        'background      : -webkit-linear-gradient(top,  rgba(212,228,239,1) 0%,rgba(134,174,204,1) 100%);'.
                                        'background      : -o-linear-gradient(top,  rgba(212,228,239,1) 0%,rgba(134,174,204,1) 100%);'.
                                        'background      : -ms-linear-gradient(top,  rgba(212,228,239,1) 0%,rgba(134,174,204,1) 100%);'.
                                        'background      : linear-gradient(to bottom,  rgba(212,228,239,1) 0%,rgba(134,174,204,1) 100%);'.
                                    '}'.
                                        '.Background:before{'.
                                            'position        : absolute;'.
                                            'content         : "";'.
                                            'width           : 100%;'.
                                            'height          : 100%;'.
                                            'opacity         :0.4;'.
                                        '}'.
                                    '.Site{'.
                                        'position    :absolute;'.
                                        'right       :2em;'.
                                        'bottom      :2em;'.
                                        'max-width   :30%;'.
                                    '}'.
                                        '.SiteName{'.
                                            'font-family :\'Lucida Sans Unicode\', \'Lucida Grande\', \'Lucida Sans\', \'DejaVu Sans Condensed\', sans-serif;'.
                                            'font-size   :3em;'.
                                            'color       :rgba(255,255,255, 0.8);'.
                                            'text-shadow : 1px 1px 1px rgba(0,0,0,0.4);'.
                                            'text-align  :right;'.
                                            'margin      :0;'.
                                            'padding     :0;'.
                                        '}'.
                                        '.SiteDescription{'.
                                            'font-family :\'Lucida Sans Unicode\', \'Lucida Grande\', \'Lucida Sans\', \'DejaVu Sans Condensed\', sans-serif;'.
                                            'font-size   :1em;'.
                                            'color       :rgba(0,0,0, 0.6);'.
                                            'text-align  :right;'.
                                        '}'.
                                     '.MessageRootContainer{'.
                                        'position            : absolute;'.
                                        'top                 :0px;'.
                                        'left                :0px;'.
                                        'width               : 100%;'.
                                        'height              : 100%;'.
                                        'overflow            : hidden;'.
                                    '}'.
                                        '.MessageContainer{'.
                                            'position            : relative;'.
                                            'display             : table;'.
                                            'width               : 100%;'.
                                            'height              : 100%;'.
                                        '}'.
                                        '.MessageSubContainer{'.
                                            'position        : relative;'.
                                            'display         : table-cell;'.
                                            'width           : 100%;'.
                                            'height          : 100%;'.
                                            'vertical-align  : middle;'.
                                        '}'.
                                            '.Message{'.
                                                'position        : relative;'.
                                                'left            : 25%;'.
                                                'width           : 50%;'.
                                                'border-radius   : 3px;'.
                                                'border          : solid 1px rgba(0,0,0,0.4);'.
                                                'box-shadow      : 0 0 0.5em rgba(0,0,0,0.4);'.
                                                'background      : rgba(240,240,240,1);'.
                                            '}'.
                                            '.MessageTitle{'.
                                                'position        : relative;'.
                                                'display         : block;'.
                                                'font-size       : 1.2em;'.
                                                'font-family     : \'Lucida Grande\', Verdana, Arial, \'Bitstream Vera Sans\', sans-serif;'.
                                                'color           : rgba(30,30,30, 1);'.
                                                'margin          : 0 0 0.5em 0;'.
                                                'padding         : 0.2em 1em 0.2em 1em;'.
                                                'background      : rgb(167,199,220);'.
                                                'background      : -moz-linear-gradient(top,  rgba(167,199,220,1) 0%, rgba(133,178,211,1) 100%);'.
                                                'background      : -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(167,199,220,1)), color-stop(100%,rgba(133,178,211,1)));'.
                                                'background      : -webkit-linear-gradient(top,  rgba(167,199,220,1) 0%,rgba(133,178,211,1) 100%);'.
                                                'background      : -o-linear-gradient(top,  rgba(167,199,220,1) 0%,rgba(133,178,211,1) 100%);'.
                                                'background      : -ms-linear-gradient(top,  rgba(167,199,220,1) 0%,rgba(133,178,211,1) 100%);'.
                                                'background      : linear-gradient(to bottom,  rgba(167,199,220,1) 0%,rgba(133,178,211,1) 100%);'.
                                                'border-bottom   : solid 1px rgba(0,0,0,0.4);'.
                                                'text-shadow     : 1px 1px 1px rgba(0,0,0,0.4);'.
                                            '}'.
                                                '.MessageTitle:before{'.
                                                    'content             :"";'.
                                                    'position            : absolute;'.
                                                    'height              : 100%;'.
                                                    'width               :100%;'.
                                                    'background-image    : url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAEBAMAAABb34NNAAAAGFBMVEUwMDCQkJDd7h7d7h7d7h7d7h6QkJBwcHAvq9PvAAAAFElEQVQImWNIZ2AoYGNgSGdgKwAACjsBuwysKbYAAAAASUVORK5CYII=);'.
                                                    'background-repeat   :repeat;'.
                                                    'border-radius       : inherit;'.
                                                    'opacity             :0.1;'.
                                                    'top                 :0;'.
                                                    'bottom              :0;'.
                                                    'left                :0;'.
                                                    'right               :0;'.
                                                '}'.
                                            '.MessageContent{'.
                                                'font-size   : 1em;'.
                                                'font-family : \'Lucida Grande\', Verdana, Arial, \'Bitstream Vera Sans\', sans-serif;'.
                                                'color       : rgba(50,50,50, 1);'.
                                                'margin      : 0;'.
                                                'padding     : 0.5em 2em 1em 2em;'.
                                            '}'.
                                '</style>'.
                            '</head>'.
                            '<body>'.
                                '<div class="Background"></div>'.
                                '<div class="Site">'.
                                    '<p class="SiteName">'.get_bloginfo( 'name' ).'</p>'.
                                    '<p class="SiteDescription">'.get_bloginfo( 'description' ).'</p>'.
                                '</div>'.
                                '<div class="MessageRootContainer">'.
                                    '<div class="MessageContainer">'.
                                        '<div class="MessageSubContainer">'.
                                            '<div class="Message">'.
                                                '<p class="MessageTitle">'.$title.'</p>'.
                                                '<p class="MessageContent">'.$message.'</p>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                '</div>'.
                            '</body>'.
                            '</html>';
            if ($echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
    }
}
?>