<?php
namespace Pure\DataBase\Tables{
    class pure_attachments implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->attachments;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'object_id '.       'BIGINT(20) '.  'NOT NULL,'.
                                                            'object_type '.     'CHAR(50) '.    'NOT NULL,'.
                                                            'user_id '.         'BIGINT(20) '.  'NOT NULL,'.
                                                            'file_name '.       'CHAR(255) '.   'NOT NULL,'.
                                                            'file '.            'TEXT '.        'NOT NULL,'.
                                                            'url '.             'TEXT '.        'NOT NULL,'.
                                                            'added '.           'DATETIME '.    'NOT NULL);';
            }
        }
    }
}
?>