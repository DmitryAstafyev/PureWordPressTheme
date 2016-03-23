<?php
namespace Pure\DataBase\Tables{
    class pure_messenger_mails_attaches implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->messenger->mails->attaches;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'user_id '.         'BIGINT(20) '.  'NOT NULL,'.
                                                            'message_id '.      'BIGINT(20) '.  'NOT NULL,'.
                                                            'file '.            'TEXT '.        'NOT NULL,'.
                                                            'type '.            'CHAR(250) '.   'NOT NULL,'.
                                                            'original_name '.   'CHAR(250) '.   'NOT NULL,'.
                                                            'added '.           'DATETIME '.    'NOT NULL,'.
                                                            '`key` '.           'CHAR(200) '.   'NOT NULL);';
            }
        }
    }
}
?>