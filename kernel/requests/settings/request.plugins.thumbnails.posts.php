<?php
namespace Pure\Requests\Plugins\Thumbnails\Posts\Settings{
    class Initialization{
        private function basic(){
            if (isset(\Pure\Configuration::instance()->globals->PureRequestsPluginsThumbnailsPostsSettingsBasic) !== true){
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.settings.plugins.thumbnails.posts.more.url',
                    get_site_url().'/request/',
                    false,
                    true
                );
                \Pure\Configuration::instance()->globals->PureRequestsPluginsThumbnailsPostsSettingsBasic = true;
            }
        }
        private function define($parameters){
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.settings.plugins.thumbnails.posts.more.params.'.$parameters['group'],
                    'command'.          '='.'templates_get_more_of_posts'.'&'.
                    'count'.            '='.'[count]'.'&'.
                    'post_type'.        '='.'[post_type]'.'&'.
                    'maximum'.          '='.(!isset($parameters['maxcount']         ) ? false : $parameters['maxcount']         ).'&'.
                    'template'.         '='.(!isset($parameters['template']         ) ? false : $parameters['template']         ).'&'.
                    'group'.            '='.(!isset($parameters['group']            ) ? false : $parameters['group']            ).'&'.
                    'content'.          '='.(!isset($parameters['content']          ) ? false : $parameters['content']          ).'&'.
                    'targets'.          '='.(!isset($parameters['targets']          ) ? false : $parameters['targets']          ).'&'.
                    'profile'.          '='.(!isset($parameters['profile']          ) ? false : $parameters['profile']          ).'&'.
                    'from_date'.        '='.(!isset($parameters['from_date']        ) ? false : $parameters['from_date']        ).'&'.
                    'days'.             '='.(!isset($parameters['days']             ) ? false : $parameters['days']             ).'&'.
                    'only_with_avatar'. '='.(!isset($parameters['only_with_avatar'] ) ? false : $parameters['only_with_avatar'] ).'&'.
                    'thumbnails'.       '='.(!isset($parameters['thumbnails']       ) ? false : $parameters['thumbnails']       ).'&'.
                    'slider_template'.  '='.(!isset($parameters['slider_template']  ) ? false : $parameters['slider_template']  ).'&'.
                    'tab_template'.     '='.(!isset($parameters['tab_template']     ) ? false : $parameters['tab_template']     ).'&'.
                    'presentation'.     '='.(!isset($parameters['presentation']     ) ? false : $parameters['presentation']     ).'&'.
                    'sandbox'.          '='.(!isset($parameters['sandbox']          ) ? false : $parameters['sandbox']          ).'&'.
                    'tabs_columns'.     '='.(!isset($parameters['tabs_columns']     ) ? false : $parameters['tabs_columns']     ),
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