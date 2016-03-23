<?php
namespace Pure\Requests\Events\Editor{
    class Create{
        private function error($message){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->basic->properties;
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $ErrorPage  = \Pure\Templates\Pages\Error\Initialization::instance()->get($settings->error_page_template);
            $ErrorPage->message('Post manage error', $message, true);
            exit;
        }
        private function parseResponse($result){
            switch($result->message){
                case 'thumbnail error':
                    //Error: with setting attachment
                    $this->error(__('We are created your event, but we did not attach thumbnail (miniature).', 'pure'));
                    break;
                case 'error during saving':
                    //Error: during saving
                    $this->error(__('We are sorry, but there are some error during saving.', 'pure'));
                    break;
                case 'bad date':
                    //Error: during saving
                    $this->error(__('Sorry, but some date of event (start, finish or dates of registration) has error.', 'pure'));
                    break;
                case 'no content':
                    //Error: no content
                    $this->error(__('Sorry, but we cannot create event, because content of it was not found.', 'pure'));
                    break;
                case 'no title':
                    //Error: no title
                    $this->error(__('Sorry, but we cannot create event, because title of it was not found.', 'pure'));
                    break;
                case 'no access':
                    //Error: no access
                    $this->error(__('Sorry, but we cannot create event, you should login.', 'pure'));
                    break;
                case 'no event':
                    //Error: post was not found
                    $this->error(__('Sorry, but we cannot find event.', 'pure'));
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
        public function create($parameters, $update = false){
            \Pure\Components\PostTypes\Events\Module\Initialization::instance()->attach();
            $Events = new \Pure\Components\PostTypes\Events\Module\Create();
            $result = $Events->create_from_POST($parameters, $update);
            $Events = NULL;
            $this->parseResponse($result);
        }
        public function update($parameters){
            \Pure\Components\PostTypes\Events\Module\Initialization::instance()->attach();
            $Events = new \Pure\Components\PostTypes\Events\Module\Create();
            $result = $Events->update($parameters);
            $Events = NULL;
            $this->parseResponse($result);
        }
    }
}
?>