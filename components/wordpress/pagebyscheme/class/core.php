<?php
namespace Pure\Components\WordPress\PageByScheme{
    class Core{
        public function loadSidebars($_template){
            $template = \Pure\Templates\Layout\Page\ByScheme\Initialization::instance()->get($_template);
            $sidebars = false;
            if ($template !== false){
                $sidebars = $template->loadSidebars();
            }
            $template = NULL;
            return $sidebars;
        }
        public function getSchemes($current){
            $containers = \Pure\Templates\Admin\Groups\Initialization::instance()->get('D');
            $innerHTML  = '<form action="" method="post">';
            $templates  = \Pure\Templates\Layout\Page\ByScheme\Initialization::instance()->templates;
            if ($templates !== false){
                foreach($templates as $template){
                    $innerHTML .=   '<p>'.
                                        '<input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="'.$template->key.'" '.($template->key === $current ? 'checked' : '').' id="LayoutScheme'.$template->key.'" name="PureLayoutScheme" />'.
                                        '<label for="LayoutScheme'.$template->key.'">Template '.$template->key.'<br />'.
                                            '<img alt="" data-type="Pure.Configuration.Input.Fader"  style="position:relative;width:10rem;left:50%;margin-left: -5rem;" src="'.$template->thumbnail.'">'.
                                        '</label>'.
                                    '</p>';
                }
                if ($innerHTML !== ''){
                    $innerHTML .= '<p><input type="submit" name="" id="" class="button button-primary alignright" value="Switch to"><br class="clear" /></p>';
                    $innerHTML =
                        $containers->open(
                            array(
                                "title"             =>'Available schemes',
                                "opened"            =>false,
                                'style_content'     =>'padding:0.5rem;',
                                "on_change"         =>'',
                                "echo"              =>false,
                            )
                        ).
                        $innerHTML.
                        $containers->close(
                            array(
                                "echo"              =>false,
                            )
                        );
                }
            }
            $innerHTML .= '</form>';
            $template   = NULL;
            $containers = NULL;
            return $innerHTML;
        }
        public function getSidebars($_template){
            $innerHTML  = '';
            $template   = \Pure\Templates\Layout\Page\ByScheme\Initialization::instance()->get($_template);
            if ($template !== false){
                $innerHTML  = $template->innerHTMLSidebars();
            }
            $template   = NULL;
            return $innerHTML;
        }
    }
}
?>