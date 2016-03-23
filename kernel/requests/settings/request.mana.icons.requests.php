<?php
namespace Pure\Requests\Mana\Icons\Requests\Settings{
    class Initialization{
        private function define($parameters){
            \Pure\Components\WordPress\Location\Requests\   Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\               Initialization::instance()->attach();
            $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.mana.icons.configuration.requestURL',
                $Requests->url,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.mana.icons.configuration.requests.get',
                'command'.      '=templates_of_mana_icons_get'. '&'.
                'object'.       '='.'[object]'.                 '&'.
                'user_ids'.     '='.'[user_ids]'.               '&'.
                'object_ids'.   '='.'[object_ids]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.mana.icons.configuration.requests.set',
                'command'.      '=templates_of_mana_icons_set'. '&'.
                'value'.        '='.'[value]'.                  '&'.
                'object'.       '='.'[object]'.                 '&'.
                'field'.        '='.'[field]'.                  '&'.
                'object_id'.    '='.'[object_id]',
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