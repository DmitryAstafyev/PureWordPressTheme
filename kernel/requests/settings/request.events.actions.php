<?php
namespace Pure\Requests\Events\Actions\Settings{
    class Initialization{
        private function define($parameters){
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\WordPress\Location\Requests\Initialization::instance()->attach();
            $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.events.actions.configuration.requestURL',
                $Requests->url,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.events.actions.configuration.requests.action',
                'command'.      '=templates_of_events_action_do'.   '&'.
                'user_id'.      '='.$parameters->user_id.           '&'.
                'event_id'.     '='.'[event_id]'.                   '&'.
                'action'.       '='.'[action]',
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