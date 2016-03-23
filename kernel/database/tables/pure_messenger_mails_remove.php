<?php
namespace Pure\DataBase\Tables{
    class pure_messenger_mails_remove implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->messenger->mails->remove;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.          'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'message_id '.  'BIGINT(20) '.  'NOT NULL,'.
                                                            'user_id '.     'BIGINT(20) '.  'NOT NULL,'.
                                                            'is_removed '.  'TINYINT '.     'NOT NULL);';
            }
        }
    }
}
?>