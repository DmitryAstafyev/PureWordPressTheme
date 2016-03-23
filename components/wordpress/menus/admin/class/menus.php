<?php
namespace Pure\Components\WordPress\Menus\Admin{
    class Provider{
        private function children(&$nodes, &$parent){
            foreach($nodes as $node){
                if (!is_null($node)){
                    if ($node->parent === $parent->id){
                        if (isset($node->id)){
                            if (!isset($parent->items)){
                                $parent->items = Array();
                            }
                            $item               = new \stdClass();
                            $item->id           = $node->id;
                            $item->title        = (isset($node->title   ) ? ($node->title   === false ? NULL : $node->title ) : NULL);
                            $item->href         = (isset($node->href    ) ? ($node->href    === false ? NULL : $node->href  ) : NULL);
                            $item->meta         = (isset($node->meta    ) ? ($node->meta    === false ? NULL : $node->meta  ) : NULL);
                            $nodes[$node->id]   = NULL;
                            $this->children ($nodes,            $item);
                            array_push      ($parent->items,    $item);
                            $item               = NULL;
                        }
                    }
                }
            }
        }
        public function get(){
            $cache = \Pure\Components\Tools\Cache\Cache::get(__METHOD__, func_get_args());
            if (! $result = $cache->value){
                global $wp_admin_bar;
                if (is_null($wp_admin_bar) === false){
                    $nodes      = $wp_admin_bar->get_nodes();
                    $result     = Array();
                    foreach($nodes as $node){
                        if ($node->parent === false){
                            if (isset($node->id)){
                                $item               = new \stdClass();
                                $item->id           = $node->id;
                                $item->title        = (isset($node->title   ) ? $node->title    : NULL);
                                $item->href         = (isset($node->href    ) ? $node->href     : NULL);
                                $item->meta         = (isset($node->meta    ) ? $node->meta     : NULL);
                                $nodes[$node->id]   = NULL;
                                $this->children ($nodes,    $item);
                                array_push      ($result,   $item);
                                $item               = NULL;
                            }
                        }
                    }
                    \Pure\Components\Tools\Cache\Cache::set($cache->key, $result);
                    return $result;
                }
            }
            return $result;
        }
    }
}
?>