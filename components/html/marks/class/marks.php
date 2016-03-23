<?php
namespace Pure\Components\HTML\Marks{
    class Core {
        private $marks = array(
            'headerMenu'    =>array(
                'nodeType'      =>'div',
                'attributeName' =>'data-element-mark',
                'attributeValue'=>'header_menu_mark'
            )
        );
        public function innerHTML($mark, $echo = false){
            if (isset($this->marks[$mark]) !== false){
                $_mark          = $this->marks[$mark];
                $markInnerHTML  = '<'.$_mark['nodeType'].' '.$_mark['attributeName'].'="'.$_mark['attributeValue'].'"></'.$_mark['nodeType'].'>';
                if ($echo !== false){
                    echo $markInnerHTML;
                }
                return $markInnerHTML;
            }
            return false;
        }
        public function selector($mark){
            if (isset($this->marks[$mark]) !== false){
                $_mark          = $this->marks[$mark];
                return $_mark['nodeType'].'['.$_mark['attributeName'].'="'.$_mark['attributeValue'].'"]';
            }
            return false;
        }
        public function get($mark){
            return (isset($this->marks[$mark]) !== false ? (object)$this->marks[$mark] : false);
        }
    }
}
?>