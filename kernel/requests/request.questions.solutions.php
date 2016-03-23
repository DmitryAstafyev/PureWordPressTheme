<?php
namespace Pure\Requests\Questions\Solutions{
    class Core{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'set':
                    $parameters->question_id    = (integer  )($parameters->question_id  );
                    $parameters->object_id      = (integer  )($parameters->object_id    );
                    $parameters->object_type    = (string   )($parameters->object_type  );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        public function set($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false && $parameters->question_id > 0 && $parameters->object_id > 0) {
                    $author = get_post_field( 'post_author', $parameters->question_id);
                    if ((int)$current->ID === (int)$author){
                        \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
                        $Questions  = new \Pure\Components\PostTypes\Questions\Module\Solutions();
                        $result     = $Questions->set(
                            $parameters->question_id,
                            $parameters->object_type,
                            $parameters->object_id
                        );
                        $Questions  = NULL;
                        if ($result !== false){
                            WebSocketServer::add(
                                $parameters->question_id,
                                (object)array(
                                    'question_id'   =>$parameters->question_id,
                                    'object_id'     =>$parameters->object_id,
                                    'object_type'   =>$parameters->object_type,
                                    'is_active'     =>$result->is_item_active,
                                )
                            );
                            echo json_encode($result);
                            return true;
                        }
                    }
                }
            }
            echo 'error';
            return false;
        }
    }
    class WebSocketServer{
        static function add($question_id, $parameters){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ($current !== false){
                \Pure\Components\WordPress\Location\Module\Initialization::instance()->attach(true);
                $Recorder   = new \Pure\Components\WordPress\Location\Module\Recorder();
                $recipients = $Recorder->getUsersByObject('post', $question_id);
                if ($recipients !== false){
                    \Pure\Components\webSocketServer\Events\Initialization::instance()->attach(true);
                    $WebSocketServer = new \Pure\Components\webSocketServer\Events\Events();
                    foreach($recipients as $recipient){
                        if ((int)$recipient->id !== (int)$current->ID){
                            $WebSocketServer->add((int)$recipient->id, 'questions_solution_update', $parameters);
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