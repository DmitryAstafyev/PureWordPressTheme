<?php
namespace Pure\Templates\LoaderProgress{
    class A{
        private $data;
        private function getData(){
            $this->data = (object)array(
                'post'      =>false,
                'event'     =>false,
                'question'  =>false,
            );
            foreach($this->data as $post_type=>$record){
                $this->data->$post_type = get_posts( array(
                    'numberposts'     => 10, // тоже самое что posts_per_page
                    'offset'          => 0,
                    'category'        => '',
                    'orderby'         => 'post_date',
                    'order'           => 'DESC',
                    'include'         => '',
                    'exclude'         => '',
                    'meta_key'        => '',
                    'meta_value'      => '',
                    'post_type'       => $post_type,
                    'post_mime_type'  => '',
                    'post_parent'     => '',
                    'post_status'     => 'publish'
                ));
            }
            $this->data->author = get_users(array(
                'orderby'      => 'user_registered',
                'order'        => 'DESC',
                'number'       => 10,
                'count_total'  => false,
                'fields'       => 'all',
            ));
        }
        private function getColumn($title, $data, $field){
            $innerHTMLItems = '';
            foreach($data as $record){
                $innerHTMLItems .= Initialization::instance()->html(
                    'A/item',
                    array(
                        array('value', $record->$field),
                    )
                );
            }
            $innerHTML = Initialization::instance()->html(
                'A/column',
                array(
                    array('title', $title           ),
                    array('items', $innerHTMLItems  ),
                )
            );
            return $innerHTML;
        }
        public function get(){
            $this->getData();
            $innerHTMLColumns   = '';
            $innerHTMLColumns   .= $this->getColumn(__('Authors',      'pure'), $this->data->author,   'display_name'  );
            $innerHTMLColumns   .= $this->getColumn(__('Posts',        'pure'), $this->data->post,     'post_title'    );
            $innerHTMLColumns   .= $this->getColumn(__('Events',       'pure'), $this->data->event,    'post_title'    );
            $innerHTMLColumns   .= $this->getColumn(__('Questions',    'pure'), $this->data->question, 'post_title'    );
            $innerHTML          = Initialization::instance()->html(
                'A/wrapper',
                array(
                    array('columns', $innerHTMLColumns),
                )
            );
            return $innerHTML;
        }
        public function getCSSFile(){
            return Initialization::instance()->configuration->paths->css.'/AManual.css';
        }
        public function getJSFile(){
            return Initialization::instance()->configuration->paths->js.'/AManual.js';
        }
        public function getCapColor(){
            return 'rgb(245, 245, 245)';
        }
    }
}
?>