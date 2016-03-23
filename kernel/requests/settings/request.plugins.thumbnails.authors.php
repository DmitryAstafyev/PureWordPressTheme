<?php
namespace Pure\Requests\Plugins\Thumbnails\Authors\Settings{
    class Initialization{
        private function basic(){
            if (\Pure\Configuration::instance()->globals->requests->AJAX === false){
                if (isset(\Pure\Configuration::instance()->globals->PureRequestsPluginsThumbnailsAuthorsSettingsBasic) !== true){
                    $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                    $current_user       = $WordPress->get_current_user();
                    $WordPress          = NULL;
                    \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.settings.plugins.thumbnails.authors.more.url',
                        get_site_url().'/request/',
                        false,
                        true
                    );
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.settings.plugins.thumbnails.authors.basic.url',
                        get_site_url().'/request/',
                        false,
                        true
                    );
                    if ($current_user !== false){
                        \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                            'pure.settings.plugins.thumbnails.authors.friendship.params',
                            'command'.  '='.'templates_of_authors_set_friendship'.  '&'.
                            'initiator'.'='.$current_user->ID.                      '&'.
                            'friend'.   '='.'[friend]'.                             '&'.
                            'action'.   '='.'[action]',
                            false,
                            true
                        );
                    }
                    \Pure\Configuration::instance()->globals->PureRequestsPluginsThumbnailsAuthorsSettingsBasic = true;
                }
            }
        }
        private function define($parameters){
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.settings.plugins.thumbnails.authors.more.params.'.$parameters['group'],
                    'command'.  '='.'templates_get_more_of_authors'.                                      '&'.
                    'count'.    '='.'[count]'.                                                            '&'.
                    'maximum'.  '='.(!isset($parameters['maxcount'] ) ? false : $parameters['maxcount'] ).'&'.
                    'template'. '='.(!isset($parameters['template'] ) ? false : $parameters['template'] ).'&'.
                    'group'.    '='.(!isset($parameters['group']    ) ? false : $parameters['group']    ).'&'.
                    'content'.  '='.(!isset($parameters['content']  ) ? false : $parameters['content']  ).'&'.
                    'targets'.  '='.(!isset($parameters['targets']  ) ? false : $parameters['targets']  ).'&'.
                    'profile'.  '='.(!isset($parameters['profile']  ) ? false : $parameters['profile']  ).'&'.
                    'from_date'.'='.(!isset($parameters['from_date']) ? false : $parameters['from_date']).'&'.
                    'days'.     '='.(!isset($parameters['days']     ) ? false : $parameters['days']     ),
                false,
                true
            );
        }
        public function init($parameters){
            $this->basic();
            $this->define($parameters);
        }
    }
}
?>