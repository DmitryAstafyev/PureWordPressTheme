<?php
namespace Pure\Templates\Layout\Page\Footer\Page{
    class A{
        private function innerHTMLWidget($widget){
            ob_start();
            the_widget($widget->class, $widget->settings);
            $innerHTML = ob_get_contents();
            ob_get_clean();
            return $innerHTML;
        }
        private function getColumns($widgets){
            $columns_count  = (count($widgets) > 4 ? 4 : count($widgets));
            $columns        = array();
            $index          = 0;
            foreach($widgets as $widget){
                $columns[$index]    = (isset($columns[$index]) === false ? '' : $columns[$index]);
                $columns[$index]    .= $this->innerHTMLWidget($widget);
                $index ++;
                if ($index === $columns_count){
                    $index = 0;
                }
            }
            return $columns;
        }
        private function innerHTMLBreadcrumbs(){
            $Template   = \Pure\Templates\Breadcrumbs\Initialization::instance()->get('A');
            $innerHTML  = $Template->innerHTML();
            $Template   = NULL;
            return $innerHTML;
        }
        private function innerHTMLToTop(){
            $Template   = \Pure\Templates\OnTop\Initialization::instance()->get('A');
            $innerHTML  = $Template->innerHTML();
            $Template   = NULL;
            return $innerHTML;
        }
        private function innerHTMLAfterFooter(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->settings->footer->properties;
            $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->less($parameters);
            $innerHTML  = '';
            if ($parameters->after !== ''){
                if (strpos($parameters->after, '<') === false){
                    $innerHTML = '<p>'.$parameters->after.'</p>';
                }else{
                    $innerHTML = $parameters->after;
                }
            }
            return $innerHTML;
        }
        private function render($widgets){
            $columns = $this->getColumns($widgets);
            foreach($columns as $key=>$column){
                $columns[$key] = Initialization::instance()->html(
                    'A/column',
                    array(
                        array('content', $column),
                    )
                );
            }
            $innerHTMLColumns = '';
            foreach($columns as $column){
                $innerHTMLColumns .= $column;
            }
            $innerHTML = Initialization::instance()->html(
                'A/wrapper',
                array(
                    array('columns',        $innerHTMLColumns               ),
                    array('columns_count',  count($columns)                 ),
                    array('breadcrumbs',    $this->innerHTMLBreadcrumbs()   ),
                    array('ontopbutton',    $this->innerHTMLToTop()         ),
                    array('after',          $this->innerHTMLAfterFooter()   ),
                )
            );
            return $innerHTML;
        }
        public function get($sidebar_id){
            \Pure\Components\WordPress\Sidebars\Initialization::instance()->attach();
            $SideBars   = new \Pure\Components\WordPress\Sidebars\Core();
            $widgets    = $SideBars->getWidgetsFromSideBar($sidebar_id);
            $SideBars   = NULL;
            if (count($widgets) > 0){
                return $this->render($widgets);
            }
            return '';
        }
    }
}
?>