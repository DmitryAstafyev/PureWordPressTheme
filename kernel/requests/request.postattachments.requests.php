<?php
namespace Pure\Requests\PostAttachments{
    class Core{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'add':
                    $parameters->object_id      = (integer  )($parameters->object_id    );
                    $parameters->object_type    = (string   )($parameters->object_type  );
                    return true;
                case 'remove':
                    $parameters->user_id        = (integer  )($parameters->user_id      );
                    $parameters->object_id      = (integer  )($parameters->object_id    );
                    $parameters->object_type    = (string   )($parameters->object_type  );
                    $parameters->url            = (string   )($parameters->url          );
                    return true;
                case 'request':
                    $parameters->object_ids     = (string   )($parameters->object_ids   );
                    $parameters->object_types   = (string   )($parameters->object_types );
                    $parameters->object_ids     = explode(',', $parameters->object_ids  );
                    $parameters->object_types   = explode(',', $parameters->object_types);
                    $result = true;
                    $result = ($result === false ? false : is_array($parameters->object_ids     ));
                    $result = ($result === false ? false : is_array($parameters->object_types   ));
                    if ($result !== false){
                        if (count($parameters->object_types) === count($parameters->object_ids)){
                            return $result;
                        }
                    }
                    return false;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'add':
                    $parameters->object_type = esc_sql($parameters->object_type);
                    return true;
                case 'remove':
                    $parameters->object_type = esc_sql($parameters->object_type);
                    return true;
            }
        }
        public function add($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                $this->sanitize($parameters, __METHOD__);
                $response = (object)array(
                    'url'       => '',
                    'message'   => ''
                );
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $current            = $WordPress->get_current_user();
                $WordPress          = NULL;
                if ($current !== false) {
                    \Pure\Components\PostAttachments\Module\Initialization::instance()->attach();
                    $Permissions        = new \Pure\Components\PostAttachments\Module\Permissions();
                    $permissions        = $Permissions->isAllow($parameters->object_id, $parameters->object_type);
                    $Permissions        = NULL;
                    if ($permissions !== false){
                        $Attachments        = new \Pure\Components\PostAttachments\Module\Provider();
                        $allow              = $Attachments->isAllow(
                            $parameters->object_id,
                            $parameters->object_type,
                            $current->ID
                        );
                        if ($allow === true){
                            if (isset($_FILES['file']) === true) {
                                \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                                $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->settings->attachments->properties;
                                $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                                if ((int)$_FILES['file']['size'] < (int)$settings->max_size_attachment) {
                                    if ( !function_exists( 'wp_handle_upload' ) ) { require_once( \Pure\Configuration::instance()->dir(ABSPATH . 'wp-admin/includes/file.php') ); }
                                    $file = wp_handle_upload($_FILES['file'], array( 'test_form' => false ));
                                    if ($file !== false){
                                        if (isset($file['file']) !== false){
                                            $filename           = pathinfo($file['file'])['filename'].'.'.pathinfo($file['file'])['extension'];
                                            $attachment_id      = $Attachments->add(
                                                $parameters->object_id,
                                                $parameters->object_type,
                                                $file['url'],
                                                $filename,
                                                $file['file']
                                            );
                                            if ((int)$attachment_id > 0){
                                                $attachment             = (object)array(
                                                    'action'    =>'new',
                                                    'attachment'=>(object)array(
                                                        'id'            =>(int)$attachment_id,
                                                        'object_id'     =>(int)$parameters->object_id,
                                                        'object_type'   =>$parameters->object_type,
                                                        'url'           =>$file['url'],
                                                        'file_name'     =>$filename
                                                    )
                                                );
                                                WebSocketServer::add(
                                                    $attachment
                                                );
                                                $Attachments            = NULL;
                                                $response->id           = (int)$attachment_id;
                                                $response->user_id      = (int)$current->ID;
                                                $response->url          = $file['url'];
                                                $response->file_name    = $filename;
                                                $response->message      = 'success';
                                                echo json_encode($response);
                                                return true;
                                            }
                                        }
                                    }
                                }else{
                                    echo 'too_big';
                                    return false;
                                }
                            }
                        }else{
                            $Attachments = NULL;
                            if ($allow !== false){
                                echo $allow->reason;
                                return false;
                            }
                        }
                    }
                }
            }
            echo 'error';
            return false;
        }
        public function remove($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $current            = $WordPress->get_current_user();
                $WordPress          = NULL;
                if ($current !== false) {
                    if ((int)$current->ID === (int)$parameters->user_id){
                        \Pure\Components\PostAttachments\Module\Initialization::instance()->attach();
                        $Attachments        = new \Pure\Components\PostAttachments\Module\Provider();
                        $attachment         = $Attachments->getAttachment(
                            $parameters->object_id,
                            $parameters->object_type,
                            $parameters->url,
                            $parameters->user_id
                        );
                        if ($attachment !== false){
                            $attachment         = (object)array(
                                'action'    =>'remove',
                                'attachment'=>(object)array(
                                    'id'            =>(int)$attachment->id,
                                    'object_id'     =>$parameters->object_id,
                                    'object_type'   =>$parameters->object_type
                                )
                            );
                        }
                        if ($Attachments->remove(
                                $parameters->object_id,
                                $parameters->object_type,
                                $parameters->url,
                                $parameters->user_id) !== false){
                            $Attachments = NULL;
                            WebSocketServer::add(
                                $attachment
                            );
                            echo 'success';
                            return true;
                        }
                        $Attachments = NULL;
                    }
                }
            }
            echo 'error';
            return false;
        }
        public function request($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                $this->sanitize($parameters, __METHOD__);
                \Pure\Components\PostAttachments\Module\Initialization::instance()->attach();
                $Attachments = new \Pure\Components\PostAttachments\Module\Provider();
                $attachments = array();
                foreach($parameters->object_ids as $index=>$object_id){
                    if ((int)$parameters->object_ids[$index] > 0){
                        $attachment = $Attachments->get((int)$parameters->object_ids[$index], esc_sql($parameters->object_types[$index]));
                        if ($attachment !== false){
                            if (count($attachment) > 0){
                                $attachments[] = $attachment;
                            }
                        }
                    }
                }
                $Attachments = NULL;
                if (count($attachments) > 0){
                    echo json_encode($attachments);
                    return true;
                }
            }
            echo 'error';
            return false;
        }
    }
    class WebSocketServer{
        static function add($attachment){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ($current !== false){
                $recipients = false;
                switch($attachment->attachment->object_type){
                    case 'comment':
                        $comment = get_comment($attachment->attachment->object_id);
                        if ($comment !== false){
                            \Pure\Components\WordPress\Location\Module\Initialization::instance()->attach(true);
                            $Recorder   = new \Pure\Components\WordPress\Location\Module\Recorder();
                            $recipients = $Recorder->getUsersByObject('post', $comment->comment_post_ID);
                        }
                        break;
                }
                if ($recipients !== false){
                    \Pure\Components\webSocketServer\Events\Initialization::instance()->attach(true);
                    $WebSocketServer = new \Pure\Components\webSocketServer\Events\Events();
                    foreach($recipients as $recipient){
                        if ((int)$recipient->id !== (int)$current->ID){
                            $WebSocketServer->add((int)$recipient->id, 'post_attachments_update', $attachment);
                        }
                    }
                    $WebSocketServer = NULL;
                }
                $Recorder   = NULL;
            }
        }
    }
}
?>