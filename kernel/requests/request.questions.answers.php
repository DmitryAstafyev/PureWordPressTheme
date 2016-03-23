<?php
namespace Pure\Requests\Questions\Answers{
    class Create{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'update':
                    $parameters->post_id        = (integer  )($parameters->post_id          );
                    $parameters->comment_id     = (integer  )($parameters->comment_id       );
                    $parameters->parent_id      = (integer  )($parameters->parent_id        );
                    $parameters->author_id      = (integer  )($parameters->author_id        );
                    $parameters->content        = (string   )($parameters->content          );
                    $parameters->content        = Decoder::decode($parameters->content);
                    return true;
                    break;
                case 'remove':
                    $parameters->answer_id      = (integer  )($parameters->answer_id        );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        public function update($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user(false, false, true);
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->author_id) {
                    if (strlen($parameters->content) > 3 && strlen($parameters->content) < 10000){
                        \Pure\Components\Tools\HTMLStrings\Initialization::instance()->attach();
                        $content    = $parameters->content;
                        $HTMLParser = new \Pure\Components\Tools\HTMLStrings\HTMLParser();
                        $content    = $HTMLParser->remove_tags_from_string_without_tags(
                            $content,
                            array('a', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'strong', 'b', 'i', 'em', 'ul', 'ol', 'li'),
                            array('pre')
                        );
                        if ($content !== false && $content !== ''){
                            $content = $HTMLParser->remove_attributes_except(
                                $content,
                                array('href', 'target', 'style', 'class'),
                                true
                            );
                        }
                        $HTMLParser = NULL;
                        if ($content !== false && $content !== '') {
                            \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach();
                            $BuddyPress         = new \Pure\Components\BuddyPress\URLs\Core();
                            if ((int)$parameters->comment_id > 0){
                                $CommentProvider    = \Pure\Providers\Comments\Initialization::instance()->getCommon();
                                $is_author          = $CommentProvider->isUserCommentAuthor($parameters->comment_id, $parameters->author_id);
                                $CommentProvider    = NULL;
                                if ($is_author !== false){
                                    remove_filter( 'pre_comment_content', 'wp_rel_nofollow'     );
                                    remove_filter( 'pre_comment_content', 'wp_filter_post_kses' );
                                    remove_filter( 'pre_comment_content', 'wp_filter_kses'      );
                                    wp_update_comment(
                                        array(
                                            'comment_ID'        =>$parameters->comment_id,
                                            'comment_content'   =>$content
                                        )
                                    );
                                    add_filter( 'pre_comment_content', 'wp_rel_nofollow', 15);
                                    $comment = get_comment($parameters->comment_id);
                                    if ($comment !== false){
                                        $response = (object)array(
                                            'id'            =>(int)$parameters->comment_id,
                                            'parent'        =>(int)$parameters->parent_id,
                                            'comment'       =>$content,
                                            'post_id'       =>(int)$parameters->post_id,
                                            'date'          =>date('H:i, F j, Y', strtotime($comment->comment_date)),
                                            'author_id'     =>$current->ID,
                                            'author_name'   =>$current->name,
                                            'author_avatar' =>$current->avatar,
                                            'author_url'    =>$BuddyPress->member($current->user_login)
                                        );
                                        WebSocketServer::add(
                                            $parameters->post_id,
                                            $response
                                        );
                                        echo json_encode($response);
                                        return true;
                                    }
                                }
                            }else{
                                $CommentProvider    = \Pure\Providers\Comments\Initialization::instance()->getCommon();
                                $response           = $CommentProvider->create(
                                    $parameters->author_id,
                                    $parameters->post_id,
                                    $parameters->parent_id,
                                    $content,
                                    false
                                );
                                $CommentProvider    = NULL;
                                if ($response !== false) {
                                    $response->author_id        = $current->ID;
                                    $response->author_url       = $BuddyPress->member($current->user_login);
                                    $response->author_name      = $current->name;
                                    $response->author_avatar    = $current->avatar;
                                    $response->date             = date('H:i, F j, Y', strtotime($response->date));
                                    WebSocketServer::add(
                                        $parameters->post_id,
                                        $response
                                    );
                                    echo json_encode($response);
                                    return true;
                                    /*
                                    (object)array(
                                        'id'        =>(int)$result,
                                        'parent'    =>(int)$parent_comment_id,
                                        'comment'   =>stripcslashes($comment),
                                        'post_id'   =>(int)$post_id,
                                        'date'      =>$created
                                    );
                                    */

                                }
                            }
                        }
                    }
                }
            }
            echo 'error';
            return false;
        }
    }
    class More{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $parameters->post_id    = (integer  )($parameters->post_id      );
                    $parameters->shown      = (integer  )($parameters->shown        );
                    $parameters->all        = (string   )($parameters->all          );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        private function addSolutions($parameters, $answers){
            \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
            $Solutions  = new \Pure\Components\PostTypes\Questions\Module\Solutions();
            foreach($answers as $key=>$answer){
                $answers[$key]->is_solution = $Solutions->isAnswerSolution($parameters->post_id, $answer->comment->id);
            }
            $Solutions  = NULL;
            return $answers;
        }
        public function get($parameters) {
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->comments->properties;
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                $Comments   = \Pure\Providers\Comments\Initialization::instance()->get('last_in_posts');
                $answers    = $Comments->get(
                    array(
                        'from_date'     =>date('Y-m-d'),
                        'days'          =>9999,
                        'shown'         =>$parameters->shown,
                        'maxcount'      =>($parameters->all === 'next' ? $settings->show_on_page : 9999),
                        'targets_array' =>array($parameters->post_id),
                        'add_post_data' =>false,
                        'add_user_data' =>true,
                        'add_excerpt'   =>false,
                        'add_DB_fields' =>false,
                        'make_tree'     =>true,
                    )
                );
                $Comments = NULL;
                if ($answers !== false){
                    $answers->comments = $this->addSolutions($parameters, $answers->comments);
                    echo json_encode($answers);
                    return true;
                }
            }
            echo 'error';
            return false;
        }
    }
    class Decoder{
        static function decode($text){
            $result = preg_replace('/\s/', '+', stripcslashes($text));
            $result = base64_decode($result);
            $result = preg_replace('/\r\n/',   '', $result);
            $result = preg_replace('/\n/',     '', $result);
            $result = preg_replace('/\t/',     '', $result);
            return $result;
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
                            $WebSocketServer->add((int)$recipient->id, 'questions_answer_update', $parameters);
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