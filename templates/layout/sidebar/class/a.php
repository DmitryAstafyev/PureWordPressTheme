<?php
namespace Pure\Templates\Layout\SideBar{
    class A{
        public function attach(){
            \Pure\Components\Effects\FixScroll\Initialization::instance()->attach();
        }
    }
}
?>