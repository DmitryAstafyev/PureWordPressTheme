<?php
namespace Pure\DataBase\Tables{
    class pure_db_cache implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->db_cache;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'cache_group '.     'CHAR(255) '.   'NOT NULL,'.
                                                            'cache_key '.       'BIGINT(20) '.  'NOT NULL,'.
                                                            'targets '.         'LONGTEXT '.    'NOT NULL,'.
                                                            'cache_value '.     'LONGTEXT '.    'NOT NULL);';
            }
        }
    }
}
?>