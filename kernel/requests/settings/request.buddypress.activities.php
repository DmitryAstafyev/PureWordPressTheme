<?php
namespace Pure\Requests\BuddyPress\Activities\Settings{
    class Initialization{
        private function define($parameters){
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\WordPress\Location\Requests\Initialization::instance()->attach(true);
            $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.user_id',
                $parameters->user_id,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.requestURL',
                $Requests->url,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.requests.more',
                'command'.      '=templates_of_activities_get_more'.    '&'.
                'object_type'.  '='.'[object_type]'.                    '&'.
                'object_id'.    '='.'[object_id]'.                      '&'.
                'all'.          '='.'[all]'.                            '&'.
                'shown'.        '='.'[shown]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.requests.sendComment',
                'command'.      '=templates_of_activities_send_comment'.    '&'.
                'user_id'.      '='.$parameters->user_id.                   '&'.
                'root_id'.      '='.'[root_id]'.                            '&'.
                'activity_id'.  '='.'[activity_id]'.                        '&'.
                'comment'.      '='.'[comment]'.                            '&'.
                'attachment_id'.'='.'[attachment_id]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.requests.getMemes',
                'command'.      '=templates_of_activities_get_memes'. '&'.
                'user_id'.      '='.$parameters->user_id,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.requests.sendPost',
                'command'.      '=templates_of_activities_send_post'.   '&'.
                'user_id'.      '='.$parameters->user_id.               '&'.
                'object_id'.    '='.'[object_id]'.                      '&'.
                'object_type'.  '='.'[object_type]'.                    '&'.
                'post'.         '='.'[post]'.                           '&'.
                'attachment_id'.'='.'[attachment_id]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.requests.remove',
                'command'.      '=templates_of_activities_remove'.  '&'.
                'user_id'.      '='.$parameters->user_id.           '&'.
                'activity_id'.  '='.'[activity_id]',
                false,
                true
            );
            /*
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.requests.update',
                'command'.      '=templates_of_comments_get_update'.    '&'.
                'user_id'.      '='.$parameters->user_id.               '&'.
                'post_id'.      '='.'[post_id]'.                        '&'.
                'after_date'.   '='.'[after_date]',
                false,
                true
            );
            */
            $Requests = NULL;
        }
        public function init($parameters){
            $this->define($parameters);
        }
    }
}
?>