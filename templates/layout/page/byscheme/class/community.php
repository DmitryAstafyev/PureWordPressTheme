<?php
namespace Pure\Templates\Layout\Page\ByScheme{
    class Community extends AbstractScheme{
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
                'pure-'.$class.'-front-top'   =>array(
                    'name'          => 'Top',
                    'id'            => 'pure-'.$class.'-front-top',
                    'description'   => 'Top of page',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'top'
                ),
                'pure-'.$class.'-row-1'       =>array(
                    'name'          => 'Row 1',
                    'id'            => 'pure-'.$class.'-row-1',
                    'description'   => 'Row 1',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'row_1'
                ),
                'pure-'.$class.'-row-2'       =>array(
                    'name'          => 'Row 2',
                    'id'            => 'pure-'.$class.'-row-2',
                    'description'   => 'Row 2',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'row_2'
                ),
                'pure-'.$class.'-row-3'              =>array(
                    'name'          => 'Row 3',
                    'id'            => 'pure-'.$class.'-row-3',
                    'description'   => 'Row 3',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'row_3'
                ),
                'pure-'.$class.'-row-4'              =>array(
                    'name'          => 'Row 4',
                    'id'            => 'pure-'.$class.'-row-4',
                    'description'   => 'Row 4',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'row_4'
                ),
                'pure-'.$class.'-row-5'              =>array(
                    'name'          => 'Row 5',
                    'id'            => 'pure-'.$class.'-row-5',
                    'description'   => 'Row 5',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'row_5'
                ),
                'pure-'.$class.'-row-6'              =>array(
                    'name'          => 'Row 6',
                    'id'            => 'pure-'.$class.'-row-6',
                    'description'   => 'Row 6',
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                    'mark'          => 'row_6'
                ),
                'pure-'.$class.'-footer'             =>array(
                    'name'          => 'Footer',
                    'id'            => 'pure-'.$class.'-footer',
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