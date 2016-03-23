<?php
namespace Pure\Components\webSocketServer\Events{
    class Events{
        public function add($_recipient, $_event, $_parameters = false){
            $recipient  = (int)$_recipient;
            $event      = esc_sql((string)$_event);
            $parameters = serialize($_parameters);
            if ($recipient > 0 && strlen($event) > 0){
                global $wpdb;
                $result = $wpdb->insert(
                    'wp_pure_websockets_events',
                    array(
                        'recipient'     =>$recipient,
                        'event'         =>$event,
                        'created'       =>date("Y-m-d H:i:s"),
                        'parameters'    =>$parameters
                    ),
                    array('%d', '%s', '%s', '%s')
                );
                return ($result !== false ? (int)$wpdb->insert_id : false);
            }
            return false;
        }
        public function get(){
            global $wpdb;
            $selector   =   'SELECT '.
                                '* '.
                            'FROM '.
                                'wp_pure_websockets_events';
            $events     = $wpdb->get_results($selector);
            return (is_array($events) !== false ? $events : false);
        }
        public function remove($_IDs){
            if (is_array($_IDs) !== false){
                $IDs = array();
                foreach($_IDs as $id){
                    if ((int)$id > 0){
                        $IDs[] = (int)$id;
                    }
                }
                if (count($IDs) > 0){
                    global $wpdb;
                    $selector   =   'DELETE '.
                                    'FROM '.
                                        'wp_pure_websockets_events '.
                                    'WHERE '.
                                        'id IN ('.implode(',', $IDs).');';
                    $result     = $wpdb->query($selector);
                    if ($result !== false){
                        return ((int)$result > 0 ? true : false);
                    }
                }
            }
            return false;
        }
    }
}
?>