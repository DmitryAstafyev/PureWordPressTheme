<?php
namespace Pure\Requests\Questions\Editor{
    class Create{
        private function parseResponse($result){
            switch($result->message){
                case 'error saving':
                    //Error: during saving
                    $this->error(__('We are sorry, but there are some error during saving.', 'pure'));
                    break;
                case 'no content':
                    //Error: no content
                    $this->error(__('Sorry, but we cannot create question, because content of it was not found.', 'pure'));
                    break;
                case 'no title':
                    //Error: no title
                    $this->error(__('Sorry, but we cannot create question, because title of it was not found.', 'pure'));
                    break;
                case 'no access':
                    //Error: no access
                    $this->error(__('Sorry, but we cannot create question, you should login.', 'pure'));
                    break;
                case 'no question':
                    //Error: post was not found
                    $this->error(__('Sorry, but we cannot find question.', 'pure'));
                    break;
                case 'publish':
                    header("Location: ".get_permalink($result->id));
                    break;
                case 'drafted':
                    header("Location: ".get_author_posts_url($result->id));
                    exit;
                    break;
                case 'removed':
                    header("Location: ".get_author_posts_url($result->id));
                    exit;
                    break;
            }
        }
        private function error($message){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->basic->properties;
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $ErrorPage  = \Pure\Templates\Pages\Error\Initialization::instance()->get($settings->error_page_template);
            $ErrorPage->message('Post manage error', $message, true);
            exit;
        }
        public function create($parameters, $update = false){
            \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
            $Questions  = new \Pure\Components\PostTypes\Questions\Module\Create();
            $result     = $Questions->create_from_POST($parameters, $update);
            $Questions  = NULL;
            $this->parseResponse($result);
        }
        public function update($parameters){
            \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
            $Questions  = new \Pure\Components\PostTypes\Questions\Module\Create();
            $result     = $Questions->update($parameters);
            $Questions  = NULL;
            $this->parseResponse($result);
        }
    }
}
?>