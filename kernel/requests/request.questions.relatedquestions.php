<?php
namespace Pure\Requests\Questions\RelatedQuestions{
    class Core{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'add':
                    $parameters->question_id    = (integer  )($parameters->question_id  );
                    $parameters->post_url       = (string   )($parameters->post_url     );
                    return true;
                    break;
                case 'remove':
                    $parameters->question_id    = (integer  )($parameters->question_id  );
                    $parameters->post_id        = (integer  )($parameters->post_id      );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        public function add($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false && $parameters->question_id > 0) {
                    $post_id = url_to_postid($parameters->post_url);
                    if ((int)$post_id > 0 && (int)$post_id !== (int)$parameters->question_id){
                        if (get_post_field('post_type', $post_id) === 'question'){
                            \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
                            $Questions  = new \Pure\Components\PostTypes\Questions\Module\Provider();
                            $result     = $Questions->addRelatedQuestion($parameters->question_id, $post_id, $current->ID);
                            if ($result !== false){
                                $question       = $Questions->get($post_id);
                                $Posts          = \Pure\Providers\Posts\Initialization::instance()->getCommon();
                                $post           = $Posts->get($post_id);
                                $Posts          = NULL;
                                if ($post !== false){
                                    echo json_encode(
                                        (object)array(
                                            'question_id'           =>$parameters->question_id,
                                            'post_id'               =>$post_id,
                                            'post_title'            =>base64_encode($post->post->title),
                                            'post_created'          =>date('F j, Y', strtotime($post->post->date)),
                                            'post_author'           =>$post->author->name,
                                            'post_attached_by'      =>$current->name,
                                            'post_attached_by_id'   =>$current->ID,
                                            'has_answer'            =>$question->has_answer,
                                            'post_url'              =>$post->post->url,
                                        )
                                    );
                                    $Questions  = NULL;
                                    return false;
                                }
                                $Questions  = NULL;
                            }else{
                                $Questions  = NULL;
                                echo 'fail';
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
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false && $parameters->question_id > 0 && $parameters->post_id > 0) {
                    \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
                    $Questions  = new \Pure\Components\PostTypes\Questions\Module\Provider();
                    $result     = $Questions->removeRelatedQuestion($parameters->question_id, $parameters->post_id, $current->ID);
                    $Questions  = NULL;
                    if ($result !== false){
                        echo $result;
                        return true;
                    }
                }
            }
            echo 'error';
            return false;
        }
    }
}
?>