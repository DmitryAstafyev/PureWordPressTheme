<?php
namespace Pure\Templates\BuddyPress\Headers{
    class AGroup{
        private function resources($group_id){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Dialogs\B\Initialization::instance()->attach(false, 'after');
            \Pure\Templates\ProgressBar\Initialization::instance()->get('A');
            //Define variables
            \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
            \Pure\Components\Attacher\Module\Initialization             ::instance()->attach();
            $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.membership.configuration.destination',
                $Requests->url,
                false,
                true
            );
            if ($current !== false){
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.buddypress.membership.configuration.request.action',
                    'command'.  '='.'templates_of_groups_set_membership'.   '&'.
                    'group'.    '='.$group_id.                              '&'.
                    'user'.     '='.$current->ID,
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.buddypress.membership.configuration.request.income_invite',
                    'command'.  '='.'templates_of_groups_income_invite_action'. '&'.
                    'group'.    '='.$group_id.                                  '&'.
                    'action'.   '='.'[action]'.                                 '&'.
                    'user'.     '='.$current->ID,
                    false,
                    true
                );
            }
        }
        private function innerHTMLGroupSettings($group_id, $id){
            $GroupSettings  = \Pure\Templates\BuddyPress\GroupAdmin\Settings\Initialization::instance()->get('A');
            $innerHTML      = $GroupSettings->get((object)array(    'group_id'      =>(int)$group_id,
                                                                    'id'            =>$id));
            $GroupSettings  = NULL;
            return $innerHTML;
        }
        private function innerHTMLGroupInvitations($group_id, $id){
            $GroupSettings  = \Pure\Templates\BuddyPress\GroupAdmin\Invitations\Initialization::instance()->get('A');
            $innerHTML      = $GroupSettings->get((object)array(    'group_id'      =>(int)$group_id,
                                                                    'id'            =>$id));
            $GroupSettings  = NULL;
            return $innerHTML;
        }
        private function innerHTMLGroupRequests($group_id, $id){
            $GroupSettings  = \Pure\Templates\BuddyPress\GroupAdmin\Requests\Initialization::instance()->get('A');
            $innerHTML      = $GroupSettings->get((object)array(    'group_id'      =>(int)$group_id,
                                                                    'id'            =>$id));
            $GroupSettings  = NULL;
            return $innerHTML;
        }
        private function innerHTMLGroupManage($group_id, $id){
            $GroupSettings  = \Pure\Templates\BuddyPress\GroupAdmin\Manage\Initialization::instance()->get('A');
            $innerHTML      = $GroupSettings->get((object)array(    'group_id'      =>(int)$group_id,
                                                                    'id'            =>$id));
            $GroupSettings  = NULL;
            return $innerHTML;
        }
        private function buttonsList($group, $permissions){
            $buttons = (object)array(
                'join'      =>false,
                'settings'  =>false,
                'invite'    =>false,
                'ban'       =>false,
                'request'   =>false,
                'roles'     =>false,
            );
            if ($permissions->has_rights === false){
                //User cannot do anything
                if ($group->status !== 'hidden'){
                    //User can only try to join
                    $buttons->join = true;
                }
            }else{
                $buttons->join      = true;
                $buttons->invite    = $permissions->invite;
                $buttons->ban       = $permissions->ban_and_admonition;
                $buttons->roles     = $permissions->roles_and_remove;
                $buttons->request   = $permissions->requests;
                $buttons->settings  = $permissions->details;
            }
            return $buttons;
        }
        private function getControls($group, $IDs){
            $innerHTML  = '';
            \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
            $WordPress          = new \Pure\Components\WordPress\UserData\Data();
            $GroupData          = new \Pure\Components\BuddyPress\Groups\Core();
            $current            = $WordPress->get_current_user();
            $innerHTMLButtons   = '';
            $innerHTMLModules   = '';
            \Pure\Components\WordPress\Location\Special\Initialization::instance()->attach();
            $Special            = new \Pure\Components\WordPress\Location\Special\Register();
            $innerHTMLButtons   .= Initialization::instance()->html(
                'AGroup/button_link',
                array(
                    array('button',     __('Content', 'pure')    ),
                    array('id',         uniqid()                        ),
                    array('property',   'groupcontent'                  ),
                    array('type',       'content'                       ),
                    array('url',        $Special->getURL('GROUPCONTENT', array('group_id'=>(int)$group->id)) ),
                )
            );
            $Special            = NULL;
            if ($current !== false){
                $group              = $GroupData->get((object)array('id'=>(int)$group->id));
                $permissions        = $GroupData->getUserPermissions(
                    (object)array(
                        'group' =>$group,
                        'user'  =>$current
                    )
                );
                $buttons            = $this->buttonsList($group, $permissions);
                if ($buttons->join !== false){
                    $membership         = $GroupData->getMembershipData((object)array(
                        'group_id'  =>(int)$group->id,
                        'user_id'   =>(int)$current->ID)
                    );
                    if ($membership !== false){
                        switch($membership->status){
                            case 'banned':
                                $membership_status = 'banned';
                                break;
                            case 'waited':
                                $membership_status = 'cancel';
                                break;
                            case 'invited':
                                $membership_status = 'request';
                                break;
                            case 'member':
                                $membership_status = 'leave';
                                break;
                        }
                    }else{
                        $membership_status = 'join';
                    }
                    $innerHTMLButtons   .= Initialization::instance()->html(
                        'AGroup/button_join',
                        array(
                            array('button',     __('Join', 'pure')                       ),
                            array('id',         $IDs->join                                      ),
                            array('state',      $membership_status                              ),
                            array('join',       __('Join to group',              'pure') ),
                            array('leave',      __('Leave group',                'pure') ),
                            array('banned',     __('You are banned',             'pure') ),
                            array('cancel',     __('Reject request',             'pure') ),
                            array('request',    __('Accept / deny invitation',   'pure') ),
                        )
                    );
                }
                if ($buttons->invite !== false){
                    $innerHTMLButtons .= Initialization::instance()->html(
                        'AGroup/button_settings',
                        array(
                            array('button',     __('Invite', 'pure')     ),
                            array('id',         $IDs->invitations               ),
                            array('property',   'groupinvitations'              ),
                            array('type',       'invite'                        ),
                        )
                    );
                    $innerHTMLModules .= $this->innerHTMLGroupInvitations((int)$group->id, $IDs->invitations );
                }
                if ($buttons->request !== false){
                    $innerHTMLButtons .= Initialization::instance()->html(
                        'AGroup/button_settings',
                        array(
                            array('button',     __('Requests', 'pure')   ),
                            array('id',         $IDs->requests                  ),
                            array('property',   'grouprequests'                 ),
                            array('type',       'requests'                      ),
                        )
                    );
                    $innerHTMLModules .= $this->innerHTMLGroupRequests((int)$group->id, $IDs->requests );
                }
                if ($buttons->roles !== false || $buttons->ban !== false){
                    $innerHTMLButtons .= Initialization::instance()->html(
                        'AGroup/button_settings',
                        array(
                            array('button',     __('Manage', 'pure')     ),
                            array('id',         $IDs->manage                    ),
                            array('property',   'groupmanage'                   ),
                            array('type',       'manage'                        ),
                        )
                    );
                    $innerHTMLModules .= $this->innerHTMLGroupManage((int)$group->id, $IDs->manage );
                }
                if ($buttons->settings !== false){
                    $innerHTMLButtons .= Initialization::instance()->html(
                        'AGroup/button_settings',
                        array(
                            array('button',     __('Settings', 'pure')   ),
                            array('id',         $IDs->settings                  ),
                            array('property',   'groupsettings'                 ),
                            array('type',       'settings'                      ),
                        )
                    );
                    $innerHTMLModules .= $this->innerHTMLGroupSettings((int)$group->id, $IDs->settings    );
                }
            }
            if ($innerHTMLButtons !== ''){
                $innerHTML = Initialization::instance()->html(
                    'AGroup/controls_container',
                    array(
                        array('buttons',    $innerHTMLButtons ),
                        array('modules',    $innerHTMLModules ),
                    )
                );
            }
            return $innerHTML;
        }
        private function getBackground($group_id){
            \Pure\Components\BuddyPress\PersonalSettings\Initialization::instance()->attach();
            $Settings                   = new \Pure\Components\BuddyPress\PersonalSettings\Group();
            $settings                   = $Settings->get((object)array('group_id'=>(int)$group_id));
            $Settings                   = NULL;
            $background_url             = '';
            if ($settings !== false){
                $background_url = '';
                if ((int)$settings['header_background']->attachment_id > 0){
                    $background_url = wp_get_attachment_image_src( (int)$settings['header_background']->attachment_id, 'full', false );
                    $background_url = (is_array($background_url) !== false ? $background_url[0] : '');
                }
                if ($background_url === '' && (string)$settings['header_background']->url !== ''){
                    $background_url = $settings['header_background']->url;
                }
            }
            return $background_url;
        }
        private function getShortInfo($group){
            \Pure\Components\Tools\Dates\Initialization::instance()->attach();
            $Dates      = new \Pure\Components\Tools\Dates\Dates();
            $innerHTML  = __('Group was created', 'pure').' '.date('jS \of F Y', strtotime($group->date_created)).', '.$Dates->fromNow($group->date_created).' '.__('ago', 'pure');
            $Dates      = NULL;
            return $innerHTML;
        }
        public function get($group){
            $background_image   = $this->getBackground($group->id);
            $background_image   = ($background_image === '' ? Initialization::instance()->configuration->urls->images.'/agroup/default_background_image.jpg' : $background_image);
            $IDs                        =(object)array(
                'settings'      =>uniqid(),
                'invitations'   =>uniqid(),
                'requests'      =>uniqid(),
                'manage'        =>uniqid(),
                'join'          =>uniqid(),
            );
            $innerHTML          = Initialization::instance()->html(
                'AGroup/header',
                array(
                    array('name',               $group->name                    ),
                    array('status',             $group->status                  ),
                    array('label_0',            __('Group', 'pure')      ),
                    array('unique_id',          uniqid()                        ),
                    array('background_image',   $background_image               ),
                    array('controls',           $this->getControls($group, $IDs )),
                    array('shortinfo',          $this->getShortInfo($group)     ),
                )
            );
            $this->resources($group->id);
            return $innerHTML;
        }
    }
}
?>