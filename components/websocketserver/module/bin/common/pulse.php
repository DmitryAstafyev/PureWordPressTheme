<?php
namespace Pure\Components\webSocketServer\Common {
    class Pulse{
        public  $table;
        function __construct($uniqid){
            $this->resources($uniqid);
            $this->table = \Pure\DataBase\TablesNames::instance()->websockets_state;
            /*
            date_default_timezone_set( 'UTC' );
            require_wp_db();
            wp_set_wpdb_vars();
            */
        }
        private function resources($uniqid){
            $Resources = new \Pure\Components\webSocketServer\Module\Resources((object)array(
                'uniqid'    =>$uniqid,
                'caller'    =>'PULSE',
                'classes'   =>array(
                    'WordPress' =>true
                )
            ));
            $Resources->attach();
            $Resources = NULL;
        }
        public function clear(){
            global $wpdb;
            $wpdb->query('DELETE FROM '.$this->table);
        }
        public function register($uniqid){
            global $wpdb;
            try{
                $wpdb->insert(
                    $this->table,
                    array( 'uniqid' => $uniqid, 'pulse' => date("Y-m-d H:i:s") ),
                    array( '%s', '%s' )
                );
                return true;
            }catch (\Exception $e){
                return false;
            }
        }
        public function refresh($uniqid){
            global $wpdb;
            try{
                $updated = $wpdb->update(
                    $this->table,
                    array( 'pulse'  => date("Y-m-d H:i:s") ),
                    array( 'uniqid' => $uniqid ),
                    array( '%s' ),
                    array( '%s' )
                );
                return ($updated === 1 ? true : false);
            }catch (\Exception $e){
                return false;
            }
            return false;
        }
        public function get(){
            global $wpdb;
            try{
                $instances  = $wpdb->get_results('SELECT * FROM '.$this->table);
                return (is_array($instances) !== false ? $instances : false);
            }catch (\Exception $e){
                return false;
            }
            return false;
        }
    }
}
?>