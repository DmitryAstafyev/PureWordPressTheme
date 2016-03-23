<?php
namespace Pure\Components\WordPress\LastLogin{
    class Provider{
        private $field_current  = 'pure_current_user_login';
        private $field_previous = 'pure_previous_user_login';
        public function update(){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ($current !== false){
                $previous = get_user_meta( $current->ID, $this->field_current, true );
                $previous = ($previous === '' ? date("Y-m-d H:i:s") : $previous);
                update_user_meta( $current->ID, $this->field_previous,  $previous           );
                update_user_meta( $current->ID, $this->field_current,   date("Y-m-d H:i:s") );
            }
        }
        public function get($user_id){
            $result = get_user_meta( $user_id, $this->field_previous, true );
            return ($result === '' ? date("Y-m-d H:i:s") : $result);
        }
    }
}
?>