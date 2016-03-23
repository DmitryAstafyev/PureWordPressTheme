<?php
namespace Pure\Templates\BuddyPress\CreateGroup{
    class A{
        private function validate(&$parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $result = ($result === false ? $result : (isset($parameters->user_id) === true ? (gettype($parameters->user_id  ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->id     ) === true ? (gettype($parameters->id       ) == 'string'   ? true : false) : false));
                    break;
            }
            return $result;
        }
        private function innerHTML($parameters){
            $innerHTML = Initialization::instance()->html(
                'A/wrapper',
                array(
                    array('instance_id',            $parameters->id                 ),
                    array('group_id',               uniqid()                        ),
                    array('id',                     uniqid()                        ),
                    array('description',            __('Description','pure') ),
                    array('settings',               __('Settings','pure')    ),
                    array('summary',                __('Summary','pure')     ),
                    array('next',                   __('next','pure')        ),
                    array('previous',               __('previous','pure')    ),
                    array('create',                 __('create','pure')      ),
                    array('cancel',                 __('cancel','pure')      ),
                    //Description tab
                    array('name_title',             __('Name', 'pure')),
                    array('name_title_add',         __('Define here name of your group. Not more than 255 symbols.', 'pure')),
                    array('description_title',      __('Description', 'pure')),
                    array('description_title_add',  __('Here you should add some short description of your group. Not more than 500 symbols.', 'pure')),
                    //Settings tab
                    array('settings_title',         __('Type of group', 'pure')),
                    array('settings_title_add',     __('Choose which type of group you want to create', 'pure')),
                    array('group_0',                __('This is a public group', 'pure')),
                    array('group_0_1',              __('Any site member can join this group', 'pure')),
                    array('group_0_2',              __('This group will be listed in the groups directory and in search results', 'pure')),
                    array('group_0_3',              __('Group content and activity will be visible to any site member', 'pure')),
                    array('group_1',                __('This is a private group', 'pure')),
                    array('group_1_1',              __('Only users who request membership and are accepted can join the group', 'pure')),
                    array('group_1_2',              __('This group will be listed in the groups directory and in search results', 'pure')),
                    array('group_1_3',              __('Group content and activity will only be visible to members of the group', 'pure')),
                    array('group_2',                __('This is a hidden group', 'pure')),
                    array('group_2_1',              __('Only users who are invited can join the group', 'pure')),
                    array('group_2_2',              __('This group will not be listed in the groups directory or search results', 'pure')),
                    array('group_2_3',              __('Group content and activity will only be visible to members of the group', 'pure')),
                    array('group_3',                __('Group Invitations', 'pure')),
                    array('group_3_1',              __('Which members of this group are allowed to invite others?', 'pure')),
                    array('group_invite_0',         __('All group members', 'pure')),
                    array('group_invite_1',         __('Group admins and mods only', 'pure')),
                    array('group_invite_2',         __('Group admins only', 'pure')),
                    array('_group_invite_0',        __('All group members can invite to group', 'pure')),
                    array('_group_invite_1',        __('Group admins and mods only can invite to group', 'pure')),
                    array('_group_invite_2',        __('Group admins only can invite to group', 'pure')),
                    //Summary tab
                    array('summary_0',              __('Perfect', 'pure')),
                    array('summary_1',              __('Now, please, check summary information and create your new group.', 'pure')),
                    array('incorrect_name',         __('Incorrect name of group. Name of group should be not less 3 symbols and not more 255 symbols.', 'pure')),
                    array('incorrect_description',  __('Incorrect description of group. Description of group should be not less 12 symbols and not more 500 symbols.', 'pure')),
                )
            );
            return $innerHTML;
        }
        private function resources($parameters){
            //Attach styles
            \Pure\Components\Styles\CheckBoxes\B\Initialization ::instance()->attach();
            \Pure\Components\Styles\RadioBoxes\A\Initialization ::instance()->attach();
            \Pure\Components\Dialogs\A\Initialization           ::instance()->attach();
            \Pure\Templates\ProgressBar\Initialization::instance()->get('A');
            //Define variables
            \Pure\Components\WordPress\Location\Requests\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.creategroup.configuration.destination',
                $Requests->url,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.creategroup.configuration.request',
                'command'.      '=templates_of_create_new_group'.   '&'.
                'user_id'.      '='.$parameters->user_id.           '&'.
                'name'.         '='.'[name]'.                       '&'.
                'description'.  '='.'[description]'.                '&'.
                'visibility'.   '='.'[visibility]'.                 '&'.
                'invitations'.  '='.'[invitations]',
                false,
                true
            );
            $Requests = NULL;
        }
        public function get($parameters){
            $innerHTML  = '';
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                $current        = $WordPress->get_current_user();
                $WordPress      = NULL;
                if ($current !== false){
                    if ((int)$current->ID === (int)$parameters->user_id){
                        $innerHTML = $this->innerHTML($parameters);
                        $this->resources($parameters);
                    }
                }
            }
            return $innerHTML;
        }
    }
}
?>