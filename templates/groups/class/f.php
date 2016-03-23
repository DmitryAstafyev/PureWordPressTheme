<?php
namespace Pure\Templates\Groups{
    class F{
        private $id_prefix;
        private $id_index;
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->show_opened        = (isset($parameters->show_opened       ) === true  ? $parameters->show_opened      : false             );
            $parameters->only_with_avatar   = (isset($parameters->only_with_avatar  ) === true  ? $parameters->only_with_avatar : true              );
            $parameters->show_life          = (isset($parameters->show_life         ) === true  ? $parameters->show_life        : false             );
            $parameters->show_content       = (isset($parameters->show_content      ) === true  ? $parameters->show_content     : false             );
            $parameters->show_admin_part    = (isset($parameters->show_admin_part   ) === true  ? $parameters->show_admin_part  : false             );
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
        }
        private function id(){
            $this->id_index ++;
            return $this->id_prefix.'_'.$this->id_index;
        }
        private function content($group){
            $wrapper        = \Pure\Templates\Positioning\Initialization::instance()->get('B');
            $posts          = new \Pure\Plugins\Thumbnails\Posts\Builder(array( 'content'           => 'group',
                                                                                'targets'	        => $group->id,
                                                                                'template'	        => 'D',
                                                                                'title'		        => '',
                                                                                'title_type'        => '',
                                                                                'maxcount'	        => 10,
                                                                                'only_with_avatar'	=> false,
                                                                                'profile'	        => '',
                                                                                'days'	            => 3650,
                                                                                'from_date'         => '',
                                                                                'hidetitle'	        => true,
                                                                                'thumbnails'	    => false,
                                                                                'slider_template'	=> 'A',
                                                                                'tab_template'	    => 'A',
                                                                                'presentation'	    => 'clear',
                                                                                'tabs_columns'	    => 1,
                                                                                'more'              => true));
            $subInnerHTML   = $posts->render();
            $subInnerHTML   = ($subInnerHTML !== '' ? $wrapper->get($subInnerHTML, (object)array('column_width'=>'28em', 'node_type'=>'article', 'space'=>'1em')) : '');
            $wrapper        = NULL;
            $posts          = NULL;
            return $subInnerHTML;
        }
        private function members_list($group_id, $user_id, $id, $IDs, $permissions){
            $id         .= '_members';
            $innerHTML  = '';
            $provider   = \Pure\Providers\Members\Initialization::instance()->get('users_of_group');
            if ($provider !== false) {
                \Pure\Components\BuddyPress\Admonitions\Initialization::instance()->attach();
                $Admonitions = new \Pure\Components\BuddyPress\Admonitions\Core();
                $members = $provider->get(array(
                    'shown'             =>0,
                    'only_with_avatar'  =>false,
                    'maxcount'          =>1000,
                    'profile'           =>'',
                    'from_date'         =>date('Y-m-d'),
                    'days'              =>9999,
                    'targets_array'     =>array($group_id),
                    'addition_request'  => array(
                        'request'   =>'status_in_groups',
                        'groups'    =>array($group_id)
                    )
                ));
                if ($members !== false){
                    $innerHTML = '<table data-element-type="Pure.Social.Groups.A.Item.MembersList.Table" border="0">'.
                                    '<tr data-element-type="Pure.Social.Groups.A.Item.MembersList.Table.Row">'.
                                        '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Name" colspan="2">'.
                                            '<p data-element-type="Pure.Social.Groups.A.Item.MembersList.Title">Member</p>'.
                                        '</td>';
                    if ($permissions->roles_and_remove === true){
                        $innerHTML .=   '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Role">'.
                                            '<p data-element-type="Pure.Social.Groups.A.Item.MembersList.Title">Role</p>'.
                                        '</td>';
                    }
                    $innerHTML .=       '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Actions">'.
                                            '<p data-element-type="Pure.Social.Groups.A.Item.MembersList.Title">Actions</p>'.
                                        '</td>'.
                                    '</tr>';
                    foreach($members->members as $member){
                        $admonitions = $Admonitions->count((object)array(
                            'group'=>(int)$group_id,
                            'user' =>(int)$member->author->id
                        ));
                        $innerHTML .=   '<tr data-element-type="Pure.Social.Groups.A.Item.MembersList.Table.Row"'.
                                            'data-engine-id="'.$id.'" '.
                                            'data-engine-element="group_members_settings_data" '.
                                            'data-engine-data-user="'.$user_id.'" '.
                                            'data-engine-data-member="'.$member->author->id.'" '.
                                            'data-engine-data-group="'.$group_id.'" '.
                                            'data-engine-data-destination="'.get_site_url().'/request/" '.
                                            'data-engine-reset-members="'.$IDs->members.'" '.
                                            'data-engine-reset-admins="'.$IDs->admins.'" '.
                                            'data-engine-reset-moderators="'.$IDs->moderators.'" '.
                                            'data-engine-data-progress="A">'.
                                            '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Avatar">'.
                                                '<div data-element-type="Pure.Social.Groups.A.Item.MembersList.Avatar" style="background-image:url('.$member->author->avatar.');"></div>'.
                                            '</td>'.
                                            '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Name">'.
                                                '<p data-element-type="Pure.Social.Groups.A.Item.MembersList.Name">'.$member->author->name.'</p>'.
                                            '</td>';
                        if ($permissions->roles_and_remove === true){
                            $innerHTML .=   '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Role">'.
                                                '<div data-engine-data-group="'.$id.$member->author->id.'" data-engine-data-name="'.esc_attr($member->author->name).'" data-element-type="Pure.Social.Groups.A.Item.MembersList.ButtonIcon" data-engine-element="members_list_button" data-engine-id="'.$id.'" data-engine-action="admin"'.((int)$member->status_in_groups[0]->is_admin === 1 ? ' data-engine-state="active" ' : '').'></div>'.
                                                '<div data-engine-data-group="'.$id.$member->author->id.'" data-engine-data-name="'.esc_attr($member->author->name).'" data-element-type="Pure.Social.Groups.A.Item.MembersList.ButtonIcon" data-engine-element="members_list_button" data-engine-id="'.$id.'" data-engine-action="mod"'.((int)$member->status_in_groups[0]->is_mod === 1 ? ' data-engine-state="active" ' : '').'></div>'.
                                            '</td>';
                        }
                        $innerHTML .=       '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Actions">'.
                                                '<div data-engine-data-group="'.$id.$member->author->id.'" data-engine-data-name="'.esc_attr($member->author->name).'" data-element-type="Pure.Social.Groups.A.Item.MembersList.ButtonIcon" data-engine-element="members_list_button" data-engine-id="'.$id.'" data-engine-action="admonition"><span>'.$admonitions.'</span></div>'.
                                                '<div data-engine-data-group="'.$id.$member->author->id.'" data-engine-data-name="'.esc_attr($member->author->name).'" data-element-type="Pure.Social.Groups.A.Item.MembersList.ButtonIcon" data-engine-element="members_list_button" data-engine-id="'.$id.'" data-engine-action="ban"'.((int)$member->status_in_groups[0]->is_banned === 1 ? ' data-engine-state="active" ' : '').'></div>';
                        if ($permissions->roles_and_remove === true){
                            $innerHTML .=       '<div data-engine-data-group="'.$id.$member->author->id.'" data-engine-data-name="'.esc_attr($member->author->name).'" data-element-type="Pure.Social.Groups.A.Item.MembersList.ButtonIcon" data-engine-element="members_list_button" data-engine-id="'.$id.'" data-engine-action="remove"></div>';
                        }
                        $innerHTML .=       '</td>'.
                                        '</tr>';
                    }
                    $innerHTML .= '</table>';
                }
            }
            $provider   = null;
            return $innerHTML;
        }
        private function requests_list($group_id, $user_id, $id){
            $id         .= '_requests';
            $innerHTML  = '';
            $GroupProviderCommon    = \Pure\Providers\Groups\Initialization::instance()->getCommon();
            $users                  = $GroupProviderCommon->get_group_membership_requests($group_id);
            $GroupProviderCommon    = NULL;
            if ($users !== false){
                \Pure\Components\Tools\Dates\Initialization::instance()->attach(true);
                $DateTool   = new \Pure\Components\Tools\Dates\Dates();
                $innerHTML  =   '<p data-element-type="Pure.Social.Groups.A.Item.Control.Info">'.__( 'You should make decision: accept user\' request or not.', 'pure' ).'</p>'.
                                '<table data-element-type="Pure.Social.Groups.A.Item.MembersList.Table" border="0">'.
                                    '<tr data-element-type="Pure.Social.Groups.A.Item.MembersList.Table.Row">'.
                                        '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Name" colspan="2">'.
                                            '<p data-element-type="Pure.Social.Groups.A.Item.MembersList.Title">Waiting users</p>'.
                                        '</td>'.
                                        '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Actions">'.
                                            '<p data-element-type="Pure.Social.Groups.A.Item.MembersList.Title">Actions</p>'.
                                        '</td>'.
                                    '</tr>';
                foreach($users as $user){
                    $innerHTML .=   '<tr data-element-type="Pure.Social.Groups.A.Item.FriendsList.Table.Row"'.
                                        'data-engine-id="'.$id.'" '.
                                        'data-engine-element="group_requests_manage_data" '.
                                        'data-engine-data-user="'.$user_id.'" '.
                                        'data-engine-data-member="'.$user->author->id.'" '.
                                        'data-engine-data-request="'.$user->membership_request_id.'" '.
                                        'data-engine-data-group="'.$group_id.'" '.
                                        'data-engine-data-destination="'.get_site_url().'/request/" '.
                                        'data-engine-data-progress="A">'.
                                        '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Avatar">'.
                                            '<div data-element-type="Pure.Social.Groups.A.Item.MembersList.Avatar" style="background-image:url('.$user->author->avatar.');"></div>'.
                                        '</td>'.
                                        '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Name">'.
                                            '<p data-element-type="Pure.Social.Groups.A.Item.MembersList.Name">'.$user->author->name.'</p>'.
                                            '<p data-element-type="Pure.Social.Groups.A.Item.MembersList.Small">'.$DateTool->fromNow($user->membership_request_date).'</p>'.
                                        '</td>'.
                                        '<td data-element-type="Pure.Social.Groups.A.Item.RequestsList.Actions">'.
                                            '<a data-element-type="Pure.Social.Groups.A.Item.FriendsList.Actions.Button" data-engine-data-request="'.$user->membership_request_id.'" data-engine-action="deny">Deny</a>'.
                                            '<a data-element-type="Pure.Social.Groups.A.Item.FriendsList.Actions.Button" data-engine-data-request="'.$user->membership_request_id.'" data-engine-action="accept">Accept</a>'.
                                        '</td>'.
                                    '</tr>';
                }
                $innerHTML .=   '</table>';
                $DateTool = NULL;
            }else{
                $innerHTML = '<p data-element-type="Pure.Social.Groups.A.Item.Control.Info">'.__( 'No requests for membership', 'pure' ).'</p>';
            }
            return $innerHTML;
        }
        private function fiends_list($group_id, $member_id, $id){
            $_id        = '_fiends';
            $innerHTML  = '';
            $provider   = \Pure\Providers\Members\Initialization::instance()->get('friends_of_user');
            if ($provider !== false) {
                $members = $provider->get(array(
                    'shown'             =>0,
                    'only_with_avatar'  =>false,
                    'maxcount'          =>1000,
                    'profile'           =>'',
                    'from_date'         =>date('Y-m-d'),
                    'days'              =>9999,
                    'targets_array'     =>array($member_id),
                    'addition_request'  => array(
                        'request'   =>'status_in_groups',
                        'groups'    =>array($group_id)
                    )
                ));
                if ($members !== false){
                    $innerHTML = '<table data-element-type="Pure.Social.Groups.A.Item.MembersList.Table" border="0">'.
                                    '<tr data-element-type="Pure.Social.Groups.A.Item.MembersList.Table.Row">'.
                                        '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Name" colspan="2">'.
                                            '<p data-element-type="Pure.Social.Groups.A.Item.MembersList.Title">Your friend</p>'.
                                        '</td>'.
                                        '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Actions">'.
                                            '<p data-element-type="Pure.Social.Groups.A.Item.MembersList.Title">Invitation</p>'.
                                        '</td>'.
                                    '</tr>';
                    foreach($members->members as $member){
                        $is_member      = (is_array($member->status_in_groups) === true ? true : false);
                        $is_confirmed   = (is_array($member->status_in_groups) === true ? ((int)$member->status_in_groups[0]->is_confirmed === 0 ? false : true): false);
                        $is_invited     = (is_array($member->status_in_groups) === true ? ((int)$member->status_in_groups[0]->invite_sent === 1 ? true : false): false);
                        //echo var_dump(array('__id__'=>$member->author->id, '__group_id__'=>$group_id, 'is_member'=>$is_member, 'is_confirmed'=>$is_confirmed, 'is_invited'=>$is_invited));
                        if ($is_member === false || ($is_member === true && $is_confirmed === false)){
                            $show_checkbox  = ($is_confirmed    === false && $is_invited === true   ? 'hide' : 'show');
                            $show_reject    = ($show_checkbox   === 'show'                          ? 'hide' : 'show');
                            $innerHTML .=   '<tr data-element-type="Pure.Social.Groups.A.Item.FriendsList.Table.Row">'.
                                                '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Avatar">'.
                                                    '<div data-element-type="Pure.Social.Groups.A.Item.MembersList.Avatar" style="background-image:url('.$member->author->avatar.');"></div>'.
                                                '</td>'.
                                                '<td data-element-type="Pure.Social.Groups.A.Item.MembersList.Name">'.
                                                    '<p data-element-type="Pure.Social.Groups.A.Item.MembersList.Name">'.$member->author->name.'</p>'.
                                                '</td>'.
                                                '<td data-element-type="Pure.Social.Groups.A.Item.FriendsList.Actions">'.
                                                    '<a data-engine-id="'.$id.$group_id.'" data-engine-action="reject" data-engine-state="'.$show_reject.'" data-element-type="Pure.Social.Groups.A.Item.FriendsList.Actions.Button" data-engine-data-member="'.$member->author->id.'">Reject invitation</a>'.
                                                    '<label data-engine-id="'.$id.$group_id.'" data-engine-action="invite" data-engine-state="'.$show_checkbox.'" data-engine-data-member="'.$member->author->id.'" data-element-type="Pure.Social.Groups.A.Item.FriendsList.Actions.Button">'.
                                                        '<input data-element-type="Pure.Social.Groups.A.Item.FriendsList.MarkerForInvite" type="checkbox"/>'.
                                                        '<a data-element-type="Pure.Social.Groups.A.Item.FriendsList.Actions.Button">Invite</a>'.
                                                    '</label>'.
                                                '</td>'.
                                            '</tr>';
                        }
                    }
                    $innerHTML .= '</table>';
                }
            }
            $provider   = null;
            return $innerHTML;
        }
        private function administration($group_id, $id, $IDs){
            \Pure\Components\GlobalSettings\MIMETypes\Initialization::instance()->attach(true);
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
            $GroupData  = new \Pure\Components\BuddyPress\Groups\Core();
            $group      = $GroupData->get((object)array('id'=>(int)$group_id));
            $current    = $WordPress->get_current_user();
            $id_original = $id;
            $id         .= '_manage';
            $innerHTML  = '';
            if ($current !== false && $group !== false){
                $permissions = $GroupData->getUserPermissions((object)array(
                    'group' =>$group,
                    'user'  =>$current));
                if ($permissions->has_rights === true){
                    $innerHTML  =   '<!--BEGIN: Details -->'.
                                    '<div data-element-type="Pure.Social.Groups.A.Item.Details" data-addition-type="Manage">'.
                                        '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Container">'.
                                            '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.SubContainer">'.
                                                '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tabs">';
                    if ($permissions->details === true){
                        $innerHTML .=               '<label for="'.$id.'tab_0">'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Details.Tab">Details</p>'.
                                                    '</label>';
                    }
                    if ($permissions->visibility === true){
                        $innerHTML .=               '<label for="'.$id.'tab_1">'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Details.Tab">Visibility</p>'.
                                                    '</label>';
                    }
                    if ($permissions->roles_and_remove === true || $permissions->ban_and_admonition === true){
                        $innerHTML .=               '<label for="'.$id.'tab_2">'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Details.Tab">Members</p>'.
                                                    '</label>';
                    }
                    if ($permissions->requests === true){
                        $innerHTML .=               '<label for="'.$id.'tab_3">'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Details.Tab">Requests</p>'.
                                                    '</label>';
                    }
                    if ($permissions->invite === true){
                        $innerHTML .=               '<label for="'.$id.'tab_4">'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Details.Tab">Invite</p>'.
                                                    '</label>';
                    }
                    $innerHTML .=               '</div>'.
                                            '</div>'.
                                        '</div>'.
                                        '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Content">';
                    if ($permissions->details === true){
                        $innerHTML .=       '<input data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Switcher" type="radio" name="'.$id.'" id="'.$id.'tab_0" hidden />'.
                                            '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab">'.
                                                '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab.Wrapper">'.
                                                    '<div data-element-type="Pure.Social.Groups.A.Item.Details.Controls.Container" data-engine-element="dialog_parent">'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Control.Title">'.__( 'Group Name (required)', 'buddypress' ).'</p>'.
                                                        '<textarea data-engine-id="'.$id.'" data-element-type="Pure.Social.Groups.A.Item.Control.TextArea" data-addition-type="name" data-engine-element="group_basic_settings_name">'.stripslashes_deep($group->name).'</textarea>'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Control.Title">'.__( 'Group Description (required)', 'buddypress' ).'</p>'.
                                                        '<textarea data-engine-id="'.$id.'" data-element-type="Pure.Social.Groups.A.Item.Control.TextArea" data-addition-type="description" data-engine-element="group_basic_settings_description">'.$group->description.'</textarea>'.
                                                        '<input data-engine-id="'.$id.'" data-element-type="Pure.Social.Groups.A.Item.Control.Checkbox" type="checkbox" id="'.$id.'group_notifications" data-engine-element="group_basic_settings_notifications"/>'.
                                                        '<label data-element-type="Pure.Social.Groups.A.Item.Control.Checkbox" for="'.$id.'group_notifications">'.__( 'Notify group members of these changes via email', 'buddypress' ).'</label>'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Control.Title">'.__( 'Choose avatar', 'pure' ).'</p>'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Control.Info">'.__( 'Upload an image to use as an avatar for this group. The image will be shown on the main group page, and in search results.', 'buddypress' ).'</p>'.
                                                        '<div data-element-type="Pure.Social.Groups.A.Item.Details.Controls.Group">'.
                                                            '<div data-element-type="Pure.Social.Groups.A.Item.Details.Controls.GroupAvatar.Container">'.
                                                                '<div data-element-type="Pure.Social.Groups.A.Item.Details.Controls.GroupAvatar.SubContainer">'.
                                                                    '<div data-element-type="Pure.Social.Groups.A.Item.Details.Controls.GroupAvatar">'.
                                                                        '<img alt="" data-element-type="Pure.Social.Groups.Item.Details.Controls.GroupAvatar" data-engine-id="'.$id.'" src="'.($group->avatar !== false ? $group->avatar : \Pure\Templates\Groups\Initialization::instance()->configuration->urls->images.'/F.group_icon.png').'"/>'.
                                                                    '</div>'.
                                                                '</div>'.
                                                            '</div>'.
                                                            '<div data-element-type="Pure.Common.FileInput.Wrapper">'.
                                                                '<input type="file" data-element-type="Pure.Common.FileInput" data-engine-id="'.$id.'" accept="'.\Pure\Components\GlobalSettings\MIMETypes\Types::$images.'"/>'.
                                                            '</div>'.
                                                            '<a data-element-type="Pure.Social.Groups.A.Item.Control.Button" '.
                                                                'data-engine-id="'.$id.'" '.
                                                                'data-engine-element="group_avatar_file_chooser" '.
                                                                'data-engine-data-user="'.$current->ID.'" '.
                                                                'data-engine-data-group="'.$group_id.'" '.
                                                                'data-engine-data-destination="'.get_site_url().'/request/" '.
                                                                'data-engine-data-progress="D">Update group avatar</a>'.
                                                        '</div>'.
                                                        '<div data-element-type="Pure.Social.Groups.A.Item.Details.Controls.ResetFloat"></div>'.
                                                        '<a data-element-type="Pure.Social.Groups.A.Item.Control.Button" '.
                                                            'data-engine-id="'.$id.'" '.
                                                            'data-engine-element="group_basic_settings_save" '.
                                                            'data-engine-data-user="'.$current->ID.'" '.
                                                            'data-engine-data-group="'.$group_id.'" '.
                                                            'data-engine-data-destination="'.get_site_url().'/request/" '.
                                                            'data-engine-data-progress="D">Save settings</a>'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>';
                    }
                    if ($permissions->visibility === true){
                        $innerHTML .=       '<input data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Switcher" type="radio" name="'.$id.'" id="'.$id.'tab_1" hidden />'.
                                            '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab">'.
                                                '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab.Wrapper">'.
                                                    '<div data-element-type="Pure.Social.Groups.A.Item.Details.Controls.Container" data-engine-element="dialog_parent" data-type-addition="visibility">'.
                                                        '<label>'.
                                                            '<input data-element-type="Pure.Social.Groups.A.Item.Details.Controls.Viability" type="radio" name="'.$id.'group-status" value="public"'.($group->status === 'public' ? ' checked ' : '').' />'.
                                                            '<strong>'.__( 'This is a public group', 'buddypress' ).'</strong>'.
                                                            '<ul>'.
                                                                '<li>'.__( 'Any site member can join this group.', 'buddypress' ).'</li>'.
                                                                '<li>'.__( 'This group will be listed in the groups directory and in search results.', 'buddypress' ).'</li>'.
                                                                '<li>'.__( 'Group content and activity will be visible to any site member.', 'buddypress' ).'</li>'.
                                                            '</ul>'.
                                                        '</label>'.
                                                        '<label>'.
                                                            '<input data-element-type="Pure.Social.Groups.A.Item.Details.Controls.Viability" type="radio" name="'.$id.'group-status" value="private"'.($group->status === 'private' ? ' checked ' : '').'/>'.
                                                            '<strong>'.__( 'This is a private group', 'buddypress' ).'</strong>'.
                                                            '<ul>'.
                                                                '<li>'.__( 'Only users who request membership and are accepted can join the group.', 'buddypress' ).'</li>'.
                                                                '<li>'.__( 'This group will be listed in the groups directory and in search results.', 'buddypress' ).'</li>'.
                                                                '<li>'.__( 'Group content and activity will only be visible to members of the group.', 'buddypress' ).'</li>'.
                                                            '</ul>'.
                                                        '</label>'.
                                                        '<label>'.
                                                            '<input data-element-type="Pure.Social.Groups.A.Item.Details.Controls.Viability" type="radio" name="'.$id.'group-status" value="hidden"'.($group->status === 'hidden' ? ' checked ' : '').'/>'.
                                                            '<strong>'.__( 'This is a hidden group', 'buddypress' ).'</strong>'.
                                                            '<ul>'.
                                                                '<li>'.__( 'Only users who are invited can join the group.', 'buddypress' ).'</li>'.
                                                                '<li>'.__( 'This group will not be listed in the groups directory or search results.', 'buddypress' ).'</li>'.
                                                                '<li>'.__( 'Group content and activity will only be visible to members of the group.', 'buddypress' ).'</li>'.
                                                            '</ul>'.
                                                        '</label>'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Control.Title">'.__( 'Group Invitations', 'buddypress' ).'</p>'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Control.Info">'.__( 'Which members of this group are allowed to invite others?', 'buddypress' ).'</p>'.
                                                        '<div data-element-type="Pure.Social.Groups.A.Item.Control.Invitations">'.
                                                            '<label>'.
                                                                '<input data-element-type="Pure.Social.Groups.A.Item.Details.Controls.Viability" type="radio" name="'.$id.'group-invite-status" value="members"'.($group->invite_status === 'members' ? ' checked ' : '').'/>'.
                                                                '<span>'.__( 'All group members', 'buddypress' ).'</span>'.
                                                            '</label>'.
                                                            '<label>'.
                                                                '<input data-element-type="Pure.Social.Groups.A.Item.Details.Controls.Viability" type="radio" name="'.$id.'group-invite-status" value="mods"'.($group->invite_status === 'mods' ? ' checked ' : '').'/>'.
                                                                '<span>'.__( 'Group admins and mods only', 'buddypress' ).'</span>'.
                                                            '</label>'.
                                                            '<label>'.
                                                                '<input data-element-type="Pure.Social.Groups.A.Item.Details.Controls.Viability" type="radio" name="'.$id.'group-invite-status" value="admins"'.($group->invite_status === 'admins' ? ' checked ' : '').'/>'.
                                                                '<span>'.__( 'Group admins only', 'buddypress' ).'</span>'.
                                                            '</label>'.
                                                        '</div>'.
                                                        '<a data-element-type="Pure.Social.Groups.A.Item.Control.Button" '.
                                                            'data-engine-id="'.$id.'" '.
                                                            'data-engine-element="group_visibility_settings_save" '.
                                                            'data-engine-data-user="'.$current->ID.'" '.
                                                            'data-engine-data-group="'.$group_id.'" '.
                                                            'data-engine-data-destination="'.get_site_url().'/request/" '.
                                                            'data-engine-data-progress="D">Save settings</a>'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>';
                    }
                    if ($permissions->roles_and_remove === true || $permissions->ban_and_admonition === true){
                        $innerHTML .=       '<input data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Switcher" type="radio" name="'.$id.'" id="'.$id.'tab_2" hidden />'.
                                            '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab">'.
                                                '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab.Wrapper">'.
                                                    '<div data-element-type="Pure.Social.Groups.A.Item.Details.Controls.Container" data-engine-element="dialog_parent" data-type-addition="members">'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Control.Title">'.__( 'Manage members of group', 'pure' ).'</p>'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Control.Info">'.__( 'Here you can manage members of group. You can define administrators and moderators. You can ban some member or remove ban. Also you can remove member from group, but be careful with such operation.', 'pure' ).'</p>'.
                                                        $this->members_list($group_id, $current->ID, $id_original, $IDs, $permissions).
                                                    '</div>'.
                                                '</div>'.
                                            '</div>';
                    }
                    if ($permissions->requests === true){
                        $innerHTML .=       '<input data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Switcher" type="radio" name="'.$id.'" id="'.$id.'tab_3" hidden />'.
                                            '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab">'.
                                                '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab.Wrapper">'.
                                                    '<div data-element-type="Pure.Social.Groups.A.Item.Details.Controls.Container" data-engine-element="dialog_parent" data-type-addition="members">'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Control.Title">'.__( 'Requests for membership', 'pure' ).'</p>'.
                                                        $this->requests_list($group_id, $current->ID, $id_original).
                                                    '</div>'.
                                                '</div>'.
                                            '</div>';
                    }
                    if ($permissions->invite === true){
                        $innerHTML .=       '<input data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Switcher" type="radio" name="'.$id.'" id="'.$id.'tab_4" hidden />'.
                                            '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab">'.
                                                '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab.Wrapper">'.
                                                    '<div data-element-type="Pure.Social.Groups.A.Item.Details.Controls.Container" data-engine-element="dialog_parent" data-type-addition="invites">'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Control.Title">'.__( 'Invitations to group', 'pure' ).'</p>'.
                                                        '<p data-element-type="Pure.Social.Groups.A.Item.Control.Info">'.__( 'Select users from your friends list, which should get invitation and press "Send intitation"', 'pure' ).'</p>'.
                                                        $this->fiends_list($group_id, $current->ID, $id_original).
                                                        '<a data-element-type="Pure.Social.Groups.A.Item.Control.Button" '.
                                                            'data-engine-id="'.$id_original.'" '.
                                                            'data-engine-element="group_send_invitation" '.
                                                            'data-engine-data-user="'.$current->ID.'" '.
                                                            'data-engine-data-group="'.$group_id.'" '.
                                                            'data-engine-data-destination="'.get_site_url().'/request/" '.
                                                            'data-engine-data-progress="D">Send invitation</a>'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>';
                    }
                    $innerHTML .=       '</div>'.
                                    '</div>'.
                                    '<!--END: Details -->';
                }
            }
            \Pure\Resources\Compressor::instance()->JS(Initialization::instance()->configuration->paths->js.'/'.'F.admin.js');
            //Attach progress bars
            \Pure\Templates\ProgressBar\Initialization::instance()->get("A");
            \Pure\Templates\ProgressBar\Initialization::instance()->get("D");
            //Attach uploader
            \Pure\Components\Uploader\Module\Initialization::instance()->attach();
            //Attach crop library
            \Pure\Components\Crop\Module\Initialization::instance()->attach();
            //Addition dialog
            \Pure\Components\Dialogs\B\Initialization::instance()->attach();
            return $innerHTML;
        }
        public function top($group, $parameters = NULL){
            return $this->simple($group, $parameters);
        }
        public function simple($group, $parameters = NULL){
            $this->validate($parameters);
            $attribute_str      = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $id                 = $this->id();
            $Data               = new \Pure\Components\WordPress\UserData\Data();
            \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
            $GroupData          = new \Pure\Components\BuddyPress\Groups\Core();
            \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach();
            $URLs               = new \Pure\Components\BuddyPress\URLs\Core();
            $user               = $Data->get_current_user(true, false);
            $membership_status  = false;
            if ($user !== false){
                $membership         = $GroupData->getMembershipData((object)array(
                    'group_id'  =>(int)$group->id,
                    'user_id'   =>(int)$user->ID));
                $membership_status  = ($membership_status === false ? ($membership === false ? 'not member' : false) : false);
                $membership_status  = ($membership_status === false ? $membership->status : $membership_status);
            }
            $IDs            = (object)array(
                'content'       =>uniqid('group_content_'      ),
                'activities'    =>uniqid('group_activities_'   ),
                'members'       =>uniqid('group_members_'      ),
                'admins'        =>uniqid('group_admins_'       ),
                'moderators'    =>uniqid('group_moderators_'   ),
                'manage'        =>uniqid('group_manage_'       )
            );
            $members        = new \Pure\Plugins\Thumbnails\Authors\Builder(array(   'content'           => 'users_of_group',
                                                                                    'targets'	        => $group->id,
                                                                                    'template'	        => 'F',
                                                                                    'title'		        => '',
                                                                                    'title_type'        => '',
                                                                                    'maxcount'	        => 10,
                                                                                    'only_with_avatar'	=> false,
                                                                                    'top'	            => false,
                                                                                    'profile'	        => '',
                                                                                    'days'	            => 3650,
                                                                                    'from_date'         => '',
                                                                                    'more'              => true,
                                                                                    'group'             =>$IDs->members));
            $admins        = new \Pure\Plugins\Thumbnails\Authors\Builder(array(    'content'           => 'admins_of_group',
                                                                                    'targets'	        => $group->id,
                                                                                    'template'	        => 'F',
                                                                                    'title'		        => '',
                                                                                    'title_type'        => '',
                                                                                    'maxcount'	        => 10,
                                                                                    'only_with_avatar'	=> false,
                                                                                    'top'	            => false,
                                                                                    'profile'	        => '',
                                                                                    'days'	            => 3650,
                                                                                    'from_date'         => '',
                                                                                    'more'              => true,
                                                                                    'group'             =>$IDs->admins));
            $moderators     = new \Pure\Plugins\Thumbnails\Authors\Builder(array(   'content'           => 'moderators_of_group',
                                                                                    'targets'	        => $group->id,
                                                                                    'template'	        => 'F',
                                                                                    'title'		        => '',
                                                                                    'title_type'        => '',
                                                                                    'maxcount'	        => 10,
                                                                                    'only_with_avatar'	=> false,
                                                                                    'top'	            => false,
                                                                                    'profile'	        => '',
                                                                                    'days'	            => 3650,
                                                                                    'from_date'         => '',
                                                                                    'more'              => true,
                                                                                    'group'             =>$IDs->moderators));
            if ($parameters->show_life !== false){
                $Activities     = \Pure\Templates\BuddyPress\Activities\Initialization::instance()->get('A');
                $activities     = $Activities->innerHTML(
                    (object)array(
                        'group_id'=>$group->id
                    )
                );
                $Activities     = NULL;
            }
            $members    = $members      ->render();
            $admins     = $admins       ->render();
            $moderators = $moderators   ->render();
            $manage     = ($parameters->show_admin_part === true ? $this->administration($group->id, $id, $IDs): '');
            $innerHTML  =   '<!--BEGIN: Group item -->'.
                            '<div data-element-type="Pure.Social.Groups.A.Item" '.
                                'data-engine-id="'.$id.'" '.
                                'data-engine-element="group_container" '.
                                'data-engine-data-IDs="'.implode(',', (array)$IDs).'" '.
                                'data-engine-data-IDsKeys="'.implode(',', array_keys((array)$IDs)).'" '.
                                $attribute_str.
                                ' data-engine-element="dialog_parent">'.
                                '<input data-element-type="Pure.Social.Groups.A.Item.Details.Switcher" type="checkbox" data-engine-type="GroupPostsSwitcher" id="'.$id.'_switcher" hidden '.($parameters->show_opened !== false ? 'checked' : '').'/>'.
                                '<!--BEGIN: Basic information -->'.
                                '<div data-element-type="Pure.Social.Groups.A.Item.Icon.Container">'.
                                    '<div data-element-type="Pure.Social.Groups.A.Item.Icon.SubContainer">'.
                                        '<div data-element-type="Pure.Social.Groups.A.Item.Icon.Wrapper">'.
                                            '<p data-element-type="Pure.Social.Groups.A.Item.Icon.Date">'.date_format(date_create($group->date_created), 'Y-m-d').'</p>';
            switch($membership_status){
                case 'not member':
                    $innerHTML .=           '<a data-element-type="Pure.Social.Groups.A.Item.Icon.Button" data-engine-groupID="'.$group->id.'">Join</a>';
                    break;
                case 'banned':
                    $innerHTML .=           '<a data-element-type="Pure.Social.Groups.A.Item.Icon.Button" data-addition-type="Blocked">You are banned</a>';
                    break;
                case 'waited':
                    $innerHTML .=           '<a data-element-type="Pure.Social.Groups.A.Item.Icon.Button" data-engine-groupID="'.$group->id.'">Reject request</a>';
                    break;
                case 'invited':
                    $innerHTML .=           '<a data-element-type="Pure.Social.Groups.A.Item.Icon.Button" data-engine-groupID="'.$group->id.'" data-engine-membership-action="invited">You are invited</a>';
                    break;
                case 'member':
                    if ($membership->is_admin == 1){
                        $innerHTML .=           '<a data-element-type="Pure.Social.Groups.A.Item.Icon.Button" href="'.$URLs->group((int)$group->id).'">Manage</a>';
                    }else{
                        $innerHTML .=           '<a data-element-type="Pure.Social.Groups.A.Item.Icon.Button" data-engine-groupID="'.$group->id.'" data-engine-membership-action="leave">Leave</a>';
                    }
                    break;
            }
            $innerHTML .=                   '<div data-element-type="Pure.Social.Groups.A.Item.Icon" data-engine-group_avatar="'.$id.'_manage'.'" style="background-image:url('.($group->avatar !== false ? $group->avatar : \Pure\Templates\Groups\Initialization::instance()->configuration->urls->images.'/F.group_icon.png').');">'.
                                            '</div>'.
                                            '<p data-element-type="Pure.Social.Groups.A.Item.Icon.Label">'.$group->count.'</p>'.
                                        '</div>'.
                                    '</div>'.
                                '</div>'.
                                '<div data-element-type="Pure.Social.Groups.A.Item.Caption">'.
                                    '<a data-element-type="Pure.Social.Groups.A.Item.Name" href="'.$URLs->group((int)$group->id).'" data-engine-group_name="'.$id.'_manage'.'">'.stripslashes_deep($group->name).'</a>'.
                                    '<p data-element-type="Pure.Social.Groups.A.Item.Description" data-engine-group_description="'.$id.'_manage'.'">'.$group->description.'</p>'.
                                '</div>'.
                                '<!--END: Basic information -->'.
                                '<!--BEGIN: Details -->'.
                                '<div data-element-type="Pure.Social.Groups.A.Item.Details">'.
                                    '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Container">'.
                                        '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.SubContainer">'.
                                            '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tabs">';
            if ($parameters->show_content === true){
                $innerHTML .=                   '<label for="'.$IDs->content.'">'.
                                                    '<p data-element-type="Pure.Social.Groups.A.Item.Details.Tab">Content</p>'.
                                                '</label>';
            }
            if ($parameters->show_life !== false){
                $innerHTML .=                   '<label for="'.$IDs->activities.'">'.
                                                    '<p data-element-type="Pure.Social.Groups.A.Item.Details.Tab">Life</p>'.
                                                '</label>';
            }
            $innerHTML .=                       '<label for="'.$IDs->members.'">'.
                                                    '<p data-element-type="Pure.Social.Groups.A.Item.Details.Tab">Members</p>'.
                                                '</label>'.
                                                '<label for="'.$IDs->admins.'">'.
                                                    '<p data-element-type="Pure.Social.Groups.A.Item.Details.Tab">Administrators</p>'.
                                                '</label>'.
                                                '<label for="'.$IDs->moderators.'">'.
                                                    '<p data-element-type="Pure.Social.Groups.A.Item.Details.Tab">Moderators</p>'.
                                                '</label>';
            if ($manage !== ''){
                $innerHTML .=                   '<label for="'.$IDs->manage.'">'.
                                                    '<p data-element-type="Pure.Social.Groups.A.Item.Details.Tab">Manage</p>'.
                                                '</label>';
            }
             $innerHTML .=                  '</div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Content">';
            if ($parameters->show_content === true){
                $content    = $this->content($group);
                $innerHTML .=           '<input data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Switcher" data-engine-type="GroupPostsSwitcher" type="radio" name="'.$id.'" id="'.$IDs->content.'" checked hidden/>'.
                                        '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab" data-engine-content-container-id="'.$IDs->content.'">'.
                                            ($content !== '' ? $content : '<p data-element-type="Pure.Social.Groups.A.Item.Description">No posts in this group.</p>').
                                        '</div>';
            }
            if ($parameters->show_life !== false){
                $innerHTML .=           '<input data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Switcher" type="radio" name="'.$id.'" id="'.$IDs->activities.'" checked hidden/>'.
                                        '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab">'.
                                            ($activities !== '' ? $activities : '<p data-element-type="Pure.Social.Groups.A.Item.Description">No activities in this group.</p>').
                                        '</div>';
            }
            $innerHTML .=               '<input data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Switcher" type="radio" name="'.$id.'" id="'.$IDs->members.'" hidden />'.
                                        '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab">'.
                                            '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab.Wrapper">'.
                                                '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab.Center">'.
                                                    ($members !== '' ? $members : '<p data-element-type="Pure.Social.Groups.A.Item.Description">No members in this group.</p>').
                                                '</div>'.
                                            '</div>'.
                                        '</div>'.
                                        '<input data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Switcher" type="radio" name="'.$id.'" id="'.$IDs->admins.'" hidden />'.
                                        '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab">'.
                                            '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab.Wrapper">'.
                                                '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab.Center">'.
                                                    ($admins !== '' ? $admins : '<p data-element-type="Pure.Social.Groups.A.Item.Description">No administrators in this group.</p>').
                                                '</div>'.
                                            '</div>'.
                                        '</div>'.
                                        '<input data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Switcher" type="radio" name="'.$id.'" id="'.$IDs->moderators.'" hidden />'.
                                        '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab">'.
                                            '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab.Wrapper">'.
                                                '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab.Center">'.
                                                    ($moderators !== '' ? $moderators : '<p data-element-type="Pure.Social.Groups.A.Item.Description">No moderators in this group.</p>').
                                                '</div>'.
                                            '</div>'.
                                        '</div>';
            if ($manage !== ''){
                $innerHTML .=           '<input data-element-type="Pure.Social.Groups.A.Item.Details.Tabs.Switcher" type="radio" name="'.$id.'" id="'.$IDs->manage.'" hidden />'.
                                        '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab">'.
                                            '<div data-element-type="Pure.Social.Groups.A.Item.Details.Tab.Wrapper">'.
                                                $manage.
                                            '</div>'.
                                        '</div>';
            }
            $innerHTML .=           '</div>'.
                                '</div>'.
                                '<!--END: Details -->'.
                                '<!--BEGIN: Switcher of details -->'.
                                '<div data-element-type="Pure.Social.Groups.A.Item.More.Button.Line">'.
                                '</div>'.
                                '<label data-element-type="Pure.Social.Groups.A.Item.Details.Switcher" for="'.$id.'_switcher">'.
                                    '<div data-element-type="Pure.Social.Groups.A.Item.More.Button">'.
                                    '</div>'.
                                '</label>'.
                                '<!--END: Switcher of details -->'.
                            '</div>'.
                            '<!--END: Group item -->';
            \Pure\Components\Dialogs\A\Initialization::instance()->attach();
            $members    = NULL;
            $Data       = NULL;
            $GroupData  = NULL;
            $URLs       = NULL;
            return $innerHTML;
        }
        private function resources_more(){
            \Pure\Resources\Compressor::instance()->CSS(
                \Pure\Templates\Groups\Initialization::instance()->configuration->paths->css.'/'.'F.more.css'
            );
            \Pure\Templates\ProgressBar\Initialization::instance()->get('D');
        }
        public function more($parameters){
            $this->resources_more($parameters);
            $innerHTML =    '<div data-element-type="Groups.Thumbnail.F.More" '.
                                    'data-type-more-group="'.   $parameters['group'].'" '.
                                    'data-type-more-max="'.     $parameters['maxcount'].'" '.
                                    'data-type-more-template="'.$parameters['template'].'" '.
                                    'data-type-more-progress="D" '.
                                    'data-type-more-settings="'.$parameters['more_settings'].'" '.
                                    'data-type-use="Pure.Components.More">'.
                            '</div>'.
                            '<p data-element-type="Groups.Thumbnail.F.More.Info">'.
                                '<span data-element-type="Groups.Thumbnail.F.More.Info" data-type-use="Pure.Components.More.Shown" data-type-more-group="'.$parameters['group'].'">'.$parameters['shown'].'</span> / '.
                                '<span data-element-type="Groups.Thumbnail.F.More.Info">'.$parameters['total'].'</span>'.
                            '</p>'.
                            '<div data-element-type="Groups.Thumbnail.F.Reset"></div>';
            return $innerHTML;
        }
        function __construct(){
            $this->id_prefix    = 'id_'.rand(100000, 999999).'_'.rand(100000, 999999);
            $this->id_index     = 0;
        }
    }
}
?>