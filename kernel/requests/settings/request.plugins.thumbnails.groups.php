<?php
namespace Pure\Requests\Plugins\Thumbnails\Groups\Settings{
    class Initialization{
        private function basic(){
            if (\Pure\Configuration::instance()->globals->requests->AJAX === false){
                if (isset(\Pure\Configuration::instance()->globals->PureRequestsPluginsThumbnailsGroupsSettingsBasic) !== true){
                    $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                    $current_user   = $WordPress->get_current_user();
                    $WordPress      = NULL;
                    \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.settings.plugins.thumbnails.groups.more.url',
                        get_site_url().'/request/',
                        false,
                        true
                    );
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.settings.plugins.thumbnails.groups.basic.url',
                        get_site_url().'/request/',
                        false,
                        true
                    );
                    if ($current_user !== false){
                        \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                            'pure.settings.plugins.thumbnails.groups.membership.params',
                            'command'.  '='.'templates_of_groups_set_membership'.   '&'.
                            'user'.     '='.$current_user->ID.                      '&'.
                            'group'.    '='.'[group]',
                            false,
                            true
                        );
                        \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                            'pure.settings.plugins.thumbnails.groups.incomeInvites.params',
                            'command'.  '='.'templates_of_groups_income_invite_action'. '&'.
                            'user'.     '='.$current_user->ID.                          '&'.
                            'group'.    '='.'[group]'.                                  '&'.
                            'action'.   '='.'[action]',
                            false,
                            true
                        );
                    }
                    \Pure\Configuration::instance()->globals->PureRequestsPluginsThumbnailsGroupsSettingsBasic = true;
                }
            }
        }
        private function define($parameters){
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.settings.plugins.thumbnails.groups.more.params.'.$parameters['group'],
                    'command'.          '='.'templates_get_more_of_groups'.                                                     '&'.
                    'count'.            '='.'[count]'.                                                                          '&'.
                    'maximum'.          '='.(!isset($parameters['maxcount']         ) ? false : $parameters['maxcount']).       '&'.
                    'template'.         '='.(!isset($parameters['template']         ) ? false : $parameters['template']).       '&'.
                    'group'.            '='.(!isset($parameters['group']            ) ? false : $parameters['group']).          '&'.
                    'content'.          '='.(!isset($parameters['content']          ) ? false : $parameters['content']).        '&'.
                    'targets'.          '='.(!isset($parameters['targets']          ) ? false : $parameters['targets']).        '&'.
                    'show_content'.     '='.(!isset($parameters['show_content']     ) ? false : $parameters['show_content']).   '&'.
                    'show_admin_part'.  '='.(!isset($parameters['show_admin_part']  ) ? false : $parameters['show_admin_part']).'&'.
                    'show_life'.        '='.(!isset($parameters['show_life']        ) ? false : $parameters['show_life']).      '&'.
                    'from_date'.        '='.(!isset($parameters['from_date']        ) ? false : $parameters['from_date']).      '&'.
                    'days'.             '='.(!isset($parameters['days']             ) ? false : $parameters['days']),
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