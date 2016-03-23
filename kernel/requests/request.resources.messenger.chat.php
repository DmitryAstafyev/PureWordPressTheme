<?php
namespace Pure\Requests\Resources\Messenger\Chat{
    ///request/?command=resources_messenger_chat_attachment&chat_attachment_id=XXX
    class Attachments{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $parameters->chat_attachment_id = (integer)($parameters->chat_attachment_id);
                    return true;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        public function get($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                \Pure\Components\Messenger\Chat\Initialization::instance()->attach(true);
                $Provider       = new \Pure\Components\Messenger\Chat\Provider();
                $attachment     = $Provider->getAttachment(false, $parameters->chat_attachment_id, true);
                $Provider       = NULL;
                if ($attachment !== false){
                    $file       = stripcslashes($attachment->file);
                    $type       = stripcslashes($attachment->type);
                    if (file_exists(\Pure\Configuration::instance()->dir($file)) !== false){
                        $file_name  = \Pure\Configuration::instance()->dir($file);
                        $file_name  = explode(\Pure\Configuration::instance()->LPS, $file_name);
                        $file_name  = $file_name[count($file_name) - 1];
                        header("Expires: 0");
                        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                        header("Cache-Control: private", false);
                        header('Content-disposition: attachment; filename='.$file_name);
                        header('Content-Type: '.$type );
                        header("Content-Transfer-Encoding: binary");
                        header("Content-Length: ". filesize($file));
                        readfile($file);
                        exit();
                    }
                    header("HTTP/1.0 404 Not Found");
                    exit();
                }
            }else{
                header("HTTP/1.0 404 Not Found");
                exit();
            }
            return false;
        }
    }
}
?>