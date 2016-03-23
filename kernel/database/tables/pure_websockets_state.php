<?php
namespace Pure\DataBase\Tables{
    class pure_websockets_state implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->websockets_state;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'uniqid '.          'CHAR(250) '.   'NOT NULL,'.
                                                            'pulse '.           'DATETIME '.    'NOT NULL);';
            }
        }
    }
}
?>