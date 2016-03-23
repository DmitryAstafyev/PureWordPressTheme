<?php
namespace Pure\Requests\Reports\Editor{
    class Create{
        private function parseResponse($result){
            switch($result->message){
                case 'thumbnail error':
                    //Error: with setting attachment
                    $this->error(__('We are created your report, but we did not attach thumbnail (miniature).', 'pure'));
                    break;
                case 'bad data':
                    //Error: during saving
                    $this->error(__('We are sorry, but there are some error during saving.', 'pure'));
                    break;
                case 'bad indexes':
                    //Error: with indexes
                    $this->error(__('We are sorry, but there are some error during generation indexes for report.', 'pure'));
                    break;
                case 'no content':
                    //Error: no content
                    $this->error(__('Sorry, but we cannot create report, because content of it was not found.', 'pure'));
                    break;
                case 'no title':
                    //Error: no title
                    $this->error(__('Sorry, but we cannot create report, because title of it was not found.', 'pure'));
                    break;
                case 'no access':
                    //Error: no access
                    $this->error(__('Sorry, but we cannot create report, you should login.', 'pure'));
                    break;
                case 'no report':
                    //Error: post was not found
                    $this->error(__('Sorry, but we cannot find report.', 'pure'));
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
            \Pure\Components\PostTypes\Reports\Module\Initialization::instance()->attach();
            $Reports    = new \Pure\Components\PostTypes\Reports\Module\Create();
            $result     = $Reports->create_from_POST($parameters, $update);
            $Reports    = NULL;
            $this->parseResponse($result);
        }
        public function update($parameters){
            \Pure\Components\PostTypes\Reports\Module\Initialization::instance()->attach();
            $Reports    = new \Pure\Components\PostTypes\Reports\Module\Create();
            $result     = $Reports->update($parameters);
            $Reports    = NULL;
            $this->parseResponse($result);
        }
    }
}
?>