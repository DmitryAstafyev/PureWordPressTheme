<?php
namespace Pure\Templates\Layout\Page\ByScheme{
    class PresentationOld extends AbstractScheme{
        protected $sidebars;
        protected function getClassName($to_lower_case = true){
            $parts = explode('\\', __CLASS__);
            return ($to_lower_case === false ? $parts[count($parts) - 1] : strtolower($parts[count($parts) - 1]));
        }
        public function listSidebars(){
            return $this->sidebars;
        }
        function __construct(){
            $class          = $this->getClassName();
            $this->sidebars = array(
                'pure-scheme-a-front-top'   =>array(
                    'name'          => 'Top',
                    'id'            => 'pure-scheme-a-front-top',
                    'description'   => 'Top of page',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'top'
                ),
                'pure-scheme-a-row-1'       =>array(
                    'name'          => 'Row 1',
                    'id'            => 'pure-scheme-a-row-1',
                    'description'   => 'Row 1',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'row_1'
                ),
                'pure-scheme-a-column-1'    =>array(
                    'name'          => 'Column 1',
                    'id'            => 'pure-scheme-a-column-1',
                    'description'   => 'Column 1',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'column_1'
                ),
                'pure-scheme-a-column-2'    =>array(
                    'name'          => 'Column 2',
                    'id'            => 'pure-scheme-a-column-2',
                    'description'   => 'Column 2',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'column_2'
                ),
                'pure-scheme-a-row-2'       =>array(
                    'name'          => 'Row 2',
                    'id'            => 'pure-scheme-a-row-2',
                    'description'   => 'Row 2',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'row_2'
                ),
                'pure-a-full-line'          =>array(
                    'name'          => 'Full line',
                    'id'            => 'pure-a-full-line',
                    'description'   => 'Full line',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'full_line'
                ),
                'pure-a-row-3'              =>array(
                    'name'          => 'Row 3',
                    'id'            => 'pure-a-row-3',
                    'description'   => 'Row 3',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'row_3'
                ),
                'pure-a-footer'             =>array(
                    'name'          => 'Footer',
                    'id'            => 'pure-a-footer',
                    'description'   => 'Footer',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'footer'
                ),
            );
        }
    }
}
?>