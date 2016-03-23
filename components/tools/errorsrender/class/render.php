<?php
namespace Pure\Components\Tools\ErrorsRender{
    class Render{
        public function show($message){
            if (\Pure\Configuration::instance()->wp_debug === true){
                if (is_string($message)){
                    echo    '<div style="position: fixed; top:25%;left:25%; width: 50%;height: 50%; overflow: scroll; padding:1em; background:rgb(20,20,20); z-index:100; box-shadow:0 0 2em rgba(0,0,0,0.4);">'.
                                '<p style="font-family :Verdana, Geneva, sans-serif;
                                                font-size   :1em;
                                                margin      :0;
                                                padding     :0.5em;
                                                color       :rgb(0,255,0);
                                                cursor      :default;
                                                text-align  :center;">Pure theme message</p>'.
                                '<p style="font-family :Verdana, Geneva, sans-serif;
                                                font-size   :0.8em;
                                                margin      :0;
                                                padding     :0.5em;
                                                color       :rgb(200,0,0);
                                                cursor      :default;
                                                text-align  :center;">It isn\'t very good, if you have seen this message. It means some error with theme is.</p>'.
                                '<p style="font-family :Verdana, Geneva, sans-serif;
                                            font-size   :0.8em;
                                            margin      :0;
                                            padding     :0.5em;
                                            color       :rgb(0,200,0);
                                            cursor      :default;
                                            text-align  :left;">'.$message.'</p>'.
                            '</div>';
                }
            }
        }
    }
}
?>