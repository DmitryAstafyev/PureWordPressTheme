<?php
namespace Pure\DataBase\Tables{
    class pure_security_tokens implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->security_tokens;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'user_id '.         'BIGINT(20) '.  'NOT NULL,'.
                                                            'token '.           'CHAR(250) '.   'NOT NULL,'.
                                                            'created '.         'DATETIME '.    'NOT NULL);';
            }
        }
    }
}
?>