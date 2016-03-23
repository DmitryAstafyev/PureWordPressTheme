<?php
namespace Pure\Components\WordPress\Post\ViewsCounter{
    class Counter{
        private $field_name = 'pure_post_views_count';
        public function get($postID){
            $count = get_post_meta($postID, $this->field_name, true);
            if ($count === false){
                $count = 0;
                update_post_meta($postID, $this->field_name, $count);
            }
            return (int)$count;
        }
        public function set($postID) {
            update_post_meta($postID, $this->field_name, ($this->get($postID) + 1));
        }
    }
}
?>