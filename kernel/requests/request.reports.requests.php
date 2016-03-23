<?php
namespace Pure\Requests\Reports\Requests{
    class Provider{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'vote':
                    $parameters->post_id    = (integer)($parameters->post_id    );
                    $parameters->user_id    = (integer)($parameters->user_id    );
                    $parameters->index      = (integer)($parameters->index      );
                    $parameters->value      = (integer)($parameters->value      );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        public function vote($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false){
                    if ((int)$current->ID === (int)$parameters->user_id){
                        \Pure\Components\PostTypes\Reports\Module\Initialization::instance()->attach();
                        $Provider = new \Pure\Components\PostTypes\Reports\Module\Provider();
                        if ($Provider->isUserVoted($parameters->post_id, $parameters->index, $parameters->user_id) === false){
                            $vote = $Provider->addVote(
                                $parameters->post_id,
                                $parameters->index,
                                $parameters->value,
                                $parameters->user_id
                            );
                            if (is_numeric($vote) !== false){
                                WebSocketServer::add(
                                    (int)$parameters->post_id,
                                    'new_index_value',
                                    (object)array(
                                        'object_id' =>$parameters->post_id,
                                        'index'     =>$parameters->index,
                                        'vote'      =>$vote
                                    )
                                );
                                echo $vote;
                                return true;
                            }else{
                                echo 'error';
                                return false;
                            }
                        }else{
                            echo 'voted';
                            return false;
                        }
                    }
                }
            }
            echo 'access_error';
            return false;
        }
    }
    class WebSocketServer{
        static function add($post_id, $event, $parameters){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ($current !== false){
                \Pure\Components\WordPress\Location\Module\Initialization::instance()->attach(true);
                $Recorder   = new \Pure\Components\WordPress\Location\Module\Recorder();
                $recipients = $Recorder->getUsersByObject('post', $post_id);
                if ($recipients !== false){
                    \Pure\Components\webSocketServer\Events\Initialization::instance()->attach(true);
                    $WebSocketServer = new \Pure\Components\webSocketServer\Events\Events();
                    foreach($recipients as $recipient){
                        if ((int)$recipient->id !== (int)$current->ID){
                            $WebSocketServer->add((int)$recipient->id, $event, $parameters);
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