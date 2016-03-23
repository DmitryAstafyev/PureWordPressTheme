<?php
namespace Pure\Requests\Questions\Additions{
    class Create{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'update':
                    \Pure\Components\WordPress\Post\Visibility\Initialization::instance()->attach(true);
                    $parameters->post_id        = (integer  )($parameters->post_id          );
                    $parameters->addition_id    = (integer  )($parameters->addition_id      );
                    $parameters->author_id      = (integer  )($parameters->author_id        );
                    $parameters->content        = (string   )($parameters->content          );
                    $parameters->content        = Decoder::decode($parameters->content);
                    return true;
                    break;
                case 'remove':
                    \Pure\Components\WordPress\Post\Visibility\Initialization::instance()->attach(true);
                    $parameters->addition_id    = (integer  )($parameters->addition_id      );
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
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->author_id) {
                    if (strlen($parameters->content) > 3 && strlen($parameters->content) < 5000){
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
                            $post_date  = date("Y-m-d H:i:s");
                            $arguments  = array(
                                'comment_status'    => false,
                                'post_author'       => $parameters->author_id,
                                'post_parent'       => $parameters->post_id,
                                'post_content'      => $content,
                                'post_excerpt'      => '',
                                'post_title'        => '',
                                'post_type'         => 'question_addition',
                                'post_status'       => 'publish'
                            );
                            if ((int)$parameters->addition_id > 0){
                                $arguments['ID']                = (int)$parameters->addition_id;
                                $arguments['post_modified']     = $post_date;
                                $arguments['post_modified_gmt'] = $post_date;
                                $post_id                        = wp_update_post($arguments);
                            }else{
                                $arguments['post_date']         = $post_date;
                                $arguments['post_date_gmt']     = $post_date;
                                $post_id                        = wp_insert_post($arguments, false);
                            }
                            if ($post_id !== false){
                                $response = (object)array(
                                    'addition_id'   =>$post_id,
                                    'content'       =>$content,
                                    'date'          =>$post_date,
                                    'post_id'       =>$parameters->post_id
                                );
                                echo json_encode($response);
                                return true;
                            }
                        }
                    }
                }
            }
            echo 'error';
            return false;
        }
        public function remove($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false){
                    $author = get_post_field( 'post_author', $parameters->addition_id);
                    if ((int)$author === (int)$current->ID){
                        \Pure\Components\PostAttachments\Module\Initialization::instance()->attach();
                        $Attachments = new \Pure\Components\PostAttachments\Module\Provider();
                        $Attachments->removeAll($parameters->addition_id, 'addition');
                        $Attachments = NULL;
                        if (wp_delete_post($parameters->addition_id, true) !== false){
                            echo 'success';
                            return true;
                        }else{
                            echo 'fail';
                            return false;
                        }
                    }
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
}
?>