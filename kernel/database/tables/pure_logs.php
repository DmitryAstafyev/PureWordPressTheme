<?php
namespace Pure\DataBase\Tables{
    class pure_logs implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->logs;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'session_id '.      'CHAR(250) '.   'NOT NULL,'.
                                                            'operation '.       'CHAR(250) '.   'NOT NULL,'.
                                                            'start '.           'DATETIME '.    'NOT NULL,'.
                                                            'finish '.          'DATETIME '.    'NOT NULL,'.
                                                            'duration '.        'FLOAT(8,4) '.  'NOT NULL);';
            }
        }
    }
}
?>