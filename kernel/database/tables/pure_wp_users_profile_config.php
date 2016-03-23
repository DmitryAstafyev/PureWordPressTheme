<?php
namespace Pure\DataBase\Tables{
    class pure_wp_users_profile_config implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->profile_config;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'object_id '.       'BIGINT(20) '.  'NOT NULL,'.
                                                            'field_name '.      'CHAR(50) '.    'NOT NULL,'.
                                                            'user_control '.    'TINYINT(4) '.  'NOT NULL,'.
                                                            'visibility '.      'CHAR(50) '.    'NOT NULL);';
            }
        }
    }
}
?>