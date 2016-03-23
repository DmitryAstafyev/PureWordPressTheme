<?php
namespace Pure\Components\WordPress\Media\Attachments{
    class Core{
        public function getPostRecordByAttachmentURL($attachment_url){
            global $wpdb;
            $selector   =   'SELECT '.
                                '* '.
                            'FROM '.
                                'wp_posts '.
                            'WHERE '.
                                'guid = "'.esc_sql($attachment_url).'" ';
            $record     = $wpdb->get_results($selector);
            if (is_array($record) !== false){
                if (count($record) === 1){
                    return $record[0];
                }
            }
        }
    }
}
?>