<?php
namespace Pure\Requests\Resources\Messenger\Mails{
    ///request/?command=resources_messenger_mails_attachment&mail_attachment_id=XXX
    class Attachments{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $parameters->mail_attachment_id = (integer)($parameters->mail_attachment_id);
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
                \Pure\Components\Messenger\Mails\Initialization::instance()->attach(true);
                $Attachments    = new \Pure\Components\Messenger\Mails\Attachments();
                $attachment     = $Attachments->get(false, $parameters->mail_attachment_id, true);
                $Attachments    = NULL;
                //echo var_dump($parameters);
                if ($attachment !== false){
                    $file       = stripcslashes($attachment->file);
                    if (file_exists(\Pure\Configuration::instance()->dir($file)) !== false){
                        $file_name  = \Pure\Configuration::instance()->dir($file);
                        $file_name  = explode(\Pure\Configuration::instance()->LPS, $file_name);
                        $file_name  = $file_name[count($file_name) - 1];
                        header("Expires: 0");
                        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                        header("Cache-Control: private", false);
                        header('Content-disposition: attachment; filename='.$file_name);
                        header('Content-Type: '.$attachment->type );
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