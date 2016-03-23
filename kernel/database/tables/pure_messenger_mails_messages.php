<?php
namespace Pure\DataBase\Tables{
    class pure_messenger_mails_messages implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->messenger->mails->messages;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'message '.         'LONGTEXT '.    'NOT NULL,'.
                                                            'subject '.         'CHAR(200) '.   'NOT NULL,'.
                                                            'created '.         'DATETIME '.    'NOT NULL,'.
                                                            'sender_id '.       'BIGINT(20) '.  'NOT NULL,'.
                                                            'thread_id '.       'BIGINT(20) '.  'NOT NULL);';
            }
        }
    }
}
?>