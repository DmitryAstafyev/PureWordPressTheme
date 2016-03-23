<?php
namespace Pure\Components\Tools\HTMLParser{
    class Parser{
        private function check_node(&$nodes, $childNodes, $tagName){
            foreach($childNodes as $child){
                if (isset($child->nodeName) === true){
                    if ($child->nodeName === $tagName){
                        $nodes[] = $child;
                    }
                }
                if (isset($child->childNodes) === true){
                    $this->check_node($nodes, $child->childNodes, $tagName);
                }
            }
        }
        public function get_tags($innerHTML, $tagName){
            $document = new \DOMDocument();
            if ($document->loadHTML('<?xml encoding="UTF-8">'.$innerHTML) === true){
                $nodes = array();
                $this->check_node($nodes, $document->childNodes, $tagName);
                return $nodes;
            }else{
                return false;
            }
        }
        public function remove_tags_except(&$childNodes, $exceptions){
            foreach($childNodes as $childNode){
                if (isset($childNode->tagName) === true){
                    if (in_array($childNode->tagName, $exceptions) === false){
                        $childNode->parentNode->removeChild($childNode);
                        $this->remove_tags_except($childNodes, $exceptions);
                        return;
                    }
                }
                if (isset($childNode->childNodes) === true){
                    $this->remove_tags_except($childNode->childNodes, $exceptions);
                }
            }
        }
        public function get_innerHTML_outside_tags($innerHTML){
            $document = new \DOMDocument();
            if ($document->loadHTML('<?xml encoding="UTF-8">'.$innerHTML) === true){
                $this->remove_tags_except($document->childNodes, array('body', 'html', 'xml', '#document', 'strong', 'em', 'del', 'li', 'b', 'i', 'ul', 'ol'));
                return (isset($document->textContent) === true ? $document->textContent : '');
            }else{
                return false;
            }
        }
    }
}
?>