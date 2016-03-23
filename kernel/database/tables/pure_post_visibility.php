<?php
namespace Pure\DataBase\Tables{
    class pure_post_visibility implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->post_visibility;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'post_id '.         'BIGINT(20) '.  'NOT NULL,'.
                                                            'association '.     'CHAR(50) '.    'NOT NULL,'.
                                                            'object_id '.       'BIGINT(20) '.  'NOT NULL,'.
                                                            'closed '.          'TINYINT '.     'NOT NULL);';
            }
        }
    }
}
?>