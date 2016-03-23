<?php
namespace Pure\Templates\BuddyPress\GroupAdmin\Settings{
    class A{
        private function validate(&$parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $result = ($result === false ? $result : (isset($parameters->group_id   ) === true ? (gettype($parameters->group_id     ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->id         ) === true ? (gettype($parameters->id           ) == 'string'   ? true : false) : false));
                    break;
            }
            return $result;
        }
        private function resources($parameters, $current){
            //Attach styles
            \Pure\Components\Styles\CheckBoxes\B\Initialization         ::instance()->attach();
            \Pure\Components\Styles\RadioBoxes\A\Initialization         ::instance()->attach();
            \Pure\Components\Dialogs\A\Initialization                   ::instance()->attach();
            \Pure\Templates\ProgressBar\Initialization                  ::instance()->get('A');
            \Pure\Templates\ProgressBar\Initialization                  ::instance()->get('B');
            //Attach tools
            \Pure\Components\Crop\Module\Initialization                 ::instance()->attach();
            \Pure\Components\Uploader\Module\Initialization             ::instance()->attach();
            \Pure\Components\WordPress\Media\Resources\Initialization   ::instance()->attach();
            //Define variables
            \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
            \Pure\Components\Attacher\Module\Initialization             ::instance()->attach();
            $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupsettings.configuration.group_id',
                $parameters->group_id,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupsettings.configuration.user_id',
                $current->ID,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupsettings.configuration.destination',
                $Requests->url,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupsettings.configuration.commands.avatar',
                'templates_of_groups_set_group_avatar',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupsettings.configuration.request.avatar',
                'command'.      '=templates_of_groups_set_group_avatar'.    '&'.
                'group'.        '='.$parameters->group_id.                  '&'.
                'user'.         '='.$current->ID.                           '&'.
                'path'.         '='.'[path]'.                               '&'.
                'x'.            '='.'[x]'.                                  '&'.
                'y'.            '='.'[y]'.                                  '&'.
                'height'.       '='.'[height]'.                             '&'.
                'width'.        '='.'[width]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupsettings.configuration.request.avatar.remove',
                'command'.      '=templates_of_groups_del_group_avatar'.    '&'.
                'group'.        '='.$parameters->group_id.                  '&'.
                'user'.         '='.$current->ID,
                false,
                true
            );





            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupsettings.configuration.commands.title',
                'templates_of_groupsettings_set_title_image',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupsettings.configuration.request.title',
                'command'.      '=templates_of_groupsettings_set_title_image'.  '&'.
                'group'.        '='.$parameters->group_id.                      '&'.
                'user'.         '='.$current->ID.                               '&'.
                'path'.         '='.'[path]'.                                   '&'.
                'x'.            '='.'[x]'.                                      '&'.
                'y'.            '='.'[y]'.                                      '&'.
                'height'.       '='.'[height]'.                                 '&'.
                'width'.        '='.'[width]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupsettings.configuration.request.title.remove',
                'command'.      '=templates_of_groupsettings_del_title_image'.  '&'.
                'group'.        '='.$parameters->group_id.                      '&'.
                'user'.         '='.$current->ID,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupsettings.configuration.noImageIcon',
                Initialization::instance()->configuration->urls->images.'/A/no_image.png',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupsettings.configuration.request.settings',
                'command'.      '=templates_of_groupsettings_update'.   '&'.
                'group'.        '='.$parameters->group_id.              '&'.
                'user'.         '='.$current->ID.                       '&'.
                'settings'.     '='.'[settings]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupsettings.configuration.request.basic',
                'command'.      '=templates_of_groups_set_basic_settings'.  '&'.
                'group'.        '='.$parameters->group_id.                  '&'.
                'user'.         '='.$current->ID.                           '&'.
                'name'.         '='.'[name]'.                               '&'.
                'description'.  '='.'[description]'.                        '&'.
                'notifications'.'='.'[notifications]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupsettings.configuration.request.privacy',
                'command'.      '=templates_of_groups_set_visibility_settings'. '&'.
                'group'.        '='.$parameters->group_id.                      '&'.
                'user'.         '='.$current->ID.                               '&'.
                'status'.       '='.'[status]'.                                 '&'.
                'invite_status'.'='.'[invite_status]',
                false,
                true
            );
            $Requests = NULL;
        }
        public function get($parameters){
            $innerHTML  = '';
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user(false, false, true);
                $WordPress  = NULL;
                if ($current !== false){
                    \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                    $GroupData      = new \Pure\Components\BuddyPress\Groups\Core();
                    $permissions    = $GroupData->getUserPermissions(
                        (object)array(
                            'group' =>$parameters->group_id,
                            'user'  =>$current
                        )
                    );
                    if ($permissions->details !== false && $permissions->visibility !== false){
                        $group          = $GroupData->get((object)array('id' =>$parameters->group_id));
                        \Pure\Components\GlobalSettings\MIMETypes\Initialization::instance()->attach();
                        $Settings       = new \Pure\Components\BuddyPress\PersonalSettings\Group();
                        $settings       = $Settings->get((object)array('group_id'=>(int)$parameters->group_id));
                        $Settings       = NULL;
                        $no_image       = Initialization::instance()->configuration->urls->images.'/A/no_image.png';
                        $images         = (object)array(
                            'header'        =>'',
                            'background'    =>($settings["background"       ]->attachment_id !== false ? wp_get_attachment_url($settings["background"       ]->attachment_id) : $no_image),
                        );
                        if ((int)$settings['header_background']->attachment_id > 0){
                            $images->header = wp_get_attachment_image_src( (int)$settings['header_background']->attachment_id, 'full', false );
                            $images->header = (is_array($images->header) !== false ? $images->header[0] : '');
                        }
                        if ($images->header === '' && (string)$settings['header_background']->url !== ''){
                            $images->header = $settings['header_background']->url;
                        }else{
                            $images->header = false;
                        }
                        $images->header     = ($images->header      !== false ? $images->header     : $no_image);
                        $images->background = ($images->background  !== false ? $images->background : $no_image);
                        $innerHTML      = Initialization::instance()->html(
                            'A/wrapper',
                            array(
                                array('instance_id',            $parameters->id                 ),
                                array('group_id',               uniqid()                        ),
                                array('id',                     uniqid()                        ),
                                array('no_image',               $no_image                       ),
                                array('image_avatar',           $group->avatar                  ),
                                array('image_title',            $images->header                 ),
                                array('image_background',       $images->background             ),
                                array('image_types',            \Pure\Components\GlobalSettings\MIMETypes\Types::$images),
                                //Tabs
                                array('tab_0',           __('Avatar & view','pure') ),
                                array('tab_1',           __('Privacy','pure') ),
                                array('tab_2',           __('Name & description','pure') ),
                                //Avatar
                                array('avatar_0',           __('Group avatar','pure') ),
                                array('avatar_1',           __('Define here group avatar','pure') ),
                                array('header_0',           __('Title image','pure') ),
                                array('header_1',           __('This image will be shown on header of group page.','pure') ),
                                array('background_0',       __('Background','pure') ),
                                array('background_1',       __('Background image for group page','pure') ),
                                array('color_0',            __('Color scheme','pure') ),
                                array('color_1',            __('You can choose the most suitable color scheme','pure') ),
                                //Privacy
                                array('privacy_0',          __('Access to group','pure') ),
                                array('privacy_1',          __('Here you can define who can visit this group. Please pay your attention, this setting manages access not only to group page. Also if group has some post, such post will be hidden (for hidden group).','pure') ),
                                array('privacy_group_0',    __( 'This is a public group', 'buddypress' )),
                                array('privacy_group_0_1',  __( 'Any site member can join this group.', 'buddypress' )       ),
                                array('privacy_group_0_2',  __( 'This group will be listed in the groups directory and in search results.', 'buddypress' )       ),
                                array('privacy_group_0_3',  __( 'Group content and activity will be visible to any site member.', 'buddypress' )       ),
                                array('privacy_group_1',    __( 'This is a private group', 'buddypress' )      ),
                                array('privacy_group_1_1',  __( 'Only users who request membership and are accepted can join the group.', 'buddypress' )       ),
                                array('privacy_group_1_2',  __( 'This group will be listed in the groups directory and in search results.', 'buddypress' )      ),
                                array('privacy_group_1_3',  __( 'Group content and activity will only be visible to members of the group.', 'buddypress' )       ),
                                array('privacy_group_2',    __( 'This is a hidden group', 'buddypress' )     ),
                                array('privacy_group_2_1',  __( 'Only users who are invited can join the group.', 'buddypress' )       ),
                                array('privacy_group_2_2',  __( 'This group will not be listed in the groups directory or search results.', 'buddypress' )      ),
                                array('privacy_group_2_3',  __( 'Group content and activity will only be visible to members of the group.', 'buddypress' )      ),
                                array('invite_0',           __( 'Group Invitations', 'buddypress' ) ),
                                array('invite_1',           __( 'Which members of this group are allowed to invite others?', 'buddypress' ) ),
                                array('invite_group_0',     __( 'All group members', 'buddypress' )     ),
                                array('invite_group_1',     __( 'Group admins and mods only', 'buddypress' )    ),
                                array('invite_group_2',     __( 'Group admins only', 'buddypress' )      ),
                                array('privacy_checked_public', ($group->status === 'public'    ? 'checked' : '')),
                                array('privacy_checked_private',($group->status === 'private'   ? 'checked' : '')),
                                array('privacy_checked_hidden', ($group->status === 'hidden'    ? 'checked' : '')),

                                array('invite_checked_members', ($group->invite_status === 'members'    ? 'checked' : '')),
                                array('invite_checked_mods',    ($group->invite_status === 'mods'       ? 'checked' : '')),
                                array('invite_checked_admins',  ($group->invite_status === 'admins'     ? 'checked' : '')),
                                //Email & password
                                array('description_0',      __('Name','pure') ),
                                array('description_1',      __('Name of group. Not more 255 symbols; not less - 3 symbols.','pure') ),
                                array('description_2',      __('Description','pure') ),
                                array('description_3',      __('Some short description of group. Not more 500 symbols; not less - 12 symbols.','pure') ),
                                array('value_name',         stripslashes_deep($group->name)),
                                array('value_description',  stripslashes_deep($group->description)),
                                //Buttons
                                array('save_image',     __('Save image','pure') ),
                                array('load_image',     __('Load image','pure') ),
                                array('remove',         __('Remove','pure') ),
                                array('select_image',   __('Select image','pure') ),
                                array('save',           __('Save','pure') ),
                                array('cancel',         __('close','pure') ),
                                //array('',            __('','pure') ),
                            )
                        );
                        $this->resources($parameters, $current);
                    }
                    $GroupData      = NULL;
                }
            }
            return $innerHTML;
        }
    }
}
?>