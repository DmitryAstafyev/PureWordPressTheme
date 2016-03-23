<?php
namespace Pure\Components\WordPress\WP_Filters{
    class Filters{
        private function getAllIdentifiers($tag){
            $identifiers        = array();
            $filters_outside    = (array_key_exists($tag, $GLOBALS['wp_filter']) === true ? $GLOBALS['wp_filter'][ $tag ] : array());
            $filters_outside    = (empty ( $filters_outside ) === true ? array() : $filters_outside);
            foreach($filters_outside as $priority => $filters){
                foreach ( $filters as $identifier => $function ){
                    array_push($identifiers, $identifier);
                }
            }
            return $identifiers;
        }
        public function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1){
            if(function_exists('add_filter') === true){
                $filters_before     = $this->getAllIdentifiers($tag);
                add_filter($tag, $function_to_add, $priority, $accepted_args);
                $filters_after      = $this->getAllIdentifiers($tag);
                $compare_results    = array_diff($filters_after, $filters_before);
                if (count($compare_results) === 1){
                    return array_pop($compare_results);
                }
            }
            return NULL;
        }
    }
}
?>