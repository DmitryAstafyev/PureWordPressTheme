<?php
namespace Pure\DataBase\Tables{
    class pure_mana_value implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->mana->values;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'user_id '.         'BIGINT(20) '.  'NOT NULL,'.
                                                            'object_type '.     'CHAR(250) '.   'NOT NULL,'.
                                                            'object_id '.       'BIGINT(20) '.  'NOT NULL,'.
                                                            'minus '.           'BIGINT(20) '.  'NOT NULL,'.
                                                            'plus '.            'BIGINT(20) '.  'NOT NULL,'.
                                                            'used '.            'TINYINT '.     'NOT NULL);';
            }
        }
    }
}
?>