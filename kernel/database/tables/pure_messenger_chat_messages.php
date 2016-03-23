<?php
namespace Pure\DataBase\Tables{
    class pure_messenger_chat_messages implements \Pure\DataBase\Table{
        public function create(){
            global $wpdb;
            $table_name = \Pure\DataBase\TablesNames::instance()->messenger->chat->messages;
            if($wpdb->get_var('SHOW TABLES LIKE "'.$table_name.'"') !== $table_name){
                \Pure\DataBase\TablesQuery::$query .=   'CREATE TABLE '.
                                                        $table_name.' ( '.
                                                            'id '.              'BIGINT(20) '.  'NOT NULL AUTO_INCREMENT PRIMARY KEY,'.
                                                            'number_in_thread '.'BIGINT(20) '.  'DEFAULT NULL,'.
                                                            'message '.         'LONGTEXT '.    'NOT NULL,'.
                                                            'created '.         'DATETIME '.    'NOT NULL,'.
                                                            'sender_id '.       'BIGINT(20) '.  'NOT NULL,'.
                                                            'thread_id '.       'BIGINT(20) '.  'NOT NULL,'.
                                                            'attachment_id '.   'BIGINT(20) '.  'DEFAULT NULL);';
                \Pure\DataBase\TablesQuery::$query .=   'DROP FUNCTION IF EXISTS pure_messenger_chat_update_threads;'.
                                                        'CREATE DEFINER=`'.DB_USER.'`@`'.DB_HOST.'` FUNCTION `pure_messenger_chat_update_threads`(`target_thread_id` bigint) RETURNS bigint(20) '.
                                                        'BEGIN '.
                                                            'SET @i = (SELECT COUNT(*) FROM '.$table_name.' WHERE thread_id = target_thread_id) + 1; '.
                                                                'UPDATE '.$table_name.' '.
                                                                    'SET number_in_thread = @i '.
                                                                'WHERE '.
                                                                    'thread_id = target_thread_id '.
                                                                'AND @i :=@i - 1; '.
                                                            'RETURN 0; '.
                                                        'END;';
            }
        }
    }
}
?>