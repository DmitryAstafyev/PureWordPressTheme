<?php
namespace Pure\DataBase\Tables{
    class pure_mana_history implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->mana->history;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'mana_id '.         'BIGINT(20) '.  'NOT NULL,'.
                                                            'user_id '.         'BIGINT(20) '.  'NOT NULL,'.
                                                            'happened '.        'DATETIME '.    'NOT NULL);';
            }
        }
    }
}
?>