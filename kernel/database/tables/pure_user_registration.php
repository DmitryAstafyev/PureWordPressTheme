<?php
namespace Pure\DataBase\Tables{
    class pure_user_registration implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->user_registration;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'login '.           'CHAR(250) '.   'NOT NULL,'.
                                                            'password '.        'CHAR(250) '.   'NOT NULL,'.
                                                            'email '.           'CHAR(250) '.   'NOT NULL,'.
                                                            'code '.            'CHAR(250) '.   'NOT NULL,'.
                                                            'created '.         'DATETIME '.    'NOT NULL);';
            }
        }
    }
}
?>