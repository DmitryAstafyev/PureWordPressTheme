<?php
namespace Pure\Requests\Comments\Requests\Settings{
    class Initialization{
        private function define($parameters){
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\WordPress\Location\Requests\Initialization::instance()->attach(true);
            $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.comments.posts.configuration.user_id',
                $parameters->user_id,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.comments.posts.configuration.requestURL',
                $Requests->url,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.comments.posts.configuration.requests.send',
                'command'.      '=templates_of_comments_create_new'.    '&'.
                'user_id'.      '='.$parameters->user_id.               '&'.
                'post_id'.      '='.$parameters->post_id.               '&'.
                'comment'.      '='.'[comment]'.                        '&'.
                'attachment_id'.'='.'[attachment_id]'.                  '&'.
                'comment_id'.   '='.'[comment_id]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.comments.posts.configuration.requests.more',
                'command'.      '=templates_of_comments_get_more'.      '&'.
                'post_id'.      '='.'[post_id]'.                        '&'.
                'all'.          '='.'[all]'.                            '&'.
                'shown'.        '='.'[shown]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.comments.posts.configuration.requests.update',
                'command'.      '=templates_of_comments_get_update'.    '&'.
                'user_id'.      '='.$parameters->user_id.               '&'.
                'post_id'.      '='.'[post_id]'.                        '&'.
                'after_date'.   '='.'[after_date]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.comments.posts.configuration.requests.getMemes',
                'command'.      '=templates_of_comments_get_memes'. '&'.
                'user_id'.      '='.$parameters->user_id,
                false,
                true
            );
            $Requests = NULL;
        }
        public function init($parameters){
            $this->define($parameters);
        }
    }
}
?>