<?php
namespace Pure\DataBase\Tables{
    class pure_bp_users_admonitions implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->admonitions;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'group_id '.        'BIGINT(20) '.  'NOT NULL,'.
                                                            'user_id '.         'BIGINT(20) '.  'NOT NULL,'.
                                                            'date_sent '.       'DATETIME '.    'NOT NULL,'.
                                                            'sender_comment '.  'LONGTEXT '.    'NOT NULL);';
            }
        }
    }
}
?>