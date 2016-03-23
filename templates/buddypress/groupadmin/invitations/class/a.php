<?php
namespace Pure\Templates\BuddyPress\GroupAdmin\Invitations{
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
            \Pure\Components\Dialogs\A\Initialization                   ::instance()->attach();
            \Pure\Templates\ProgressBar\Initialization                  ::instance()->get('A');
            //Define variables
            \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
            \Pure\Components\Attacher\Module\Initialization             ::instance()->attach();
            $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupadmin.invitaions.configuration.destination',
                $Requests->url,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupadmin.invitaions.configuration.request.action',
                'command'.      '=templates_of_groups_invite_action'.   '&'.
                'group'.        '='.$parameters->group_id.              '&'.
                'user'.         '='.$current->ID.                       '&'.
                'members'.      '='.'[members]'.                        '&'.
                'action'.       '='.'[action]',
                false,
                true
            );
            $Requests = NULL;
        }
        private function getList($group_id, $current, $instance_id){
            $innerHTML  = '';
            $provider   = \Pure\Providers\Members\Initialization::instance()->get('friends_of_user');
            if ($provider !== false) {
                $members = $provider->get(array(
                    'shown'             => 0,
                    'only_with_avatar'  => false,
                    'maxcount'          => 1000,
                    'profile'           => '',
                    'from_date'         => date('Y-m-d'),
                    'days'              => 9999,
                    'targets_array'     => array($current->ID),
                    'addition_request'  => array(
                        'request'   => 'status_in_groups',
                        'groups'    => array($group_id)
                    )
                ));
                if ($members !== false) {
                    $innerHTMLRows = '';
                    foreach($members->members as $member){
                        $is_member      = (is_array($member->status_in_groups) === true ? true : false);
                        $is_confirmed   = (is_array($member->status_in_groups) === true ? ((int)$member->status_in_groups[0]->is_confirmed === 0 ? false : true): false);
                        $is_invited     = (is_array($member->status_in_groups) === true ? ((int)$member->status_in_groups[0]->invite_sent === 1 ? true : false): false);
                        if ($is_member === false || ($is_member === true && $is_confirmed === false)){
                            $show_invite    = ($is_confirmed    === false && $is_invited === true   ? 'hide' : 'show');
                            $show_reject    = ($show_invite     === 'show'                          ? 'hide' : 'show');
                            $innerHTMLRows .= Initialization::instance()->html(
                                'A/row',
                                array(
                                    array('member_avatar',      $member->author->avatar                 ),
                                    array('member_name',        $member->author->name                   ),
                                    array('member_id',          $member->author->id                     ),
                                    array('instance_id',        $instance_id                            ),
                                    array('label_invite',       __('Invite','pure')              ),
                                    array('label_reject',       __('Reject invitation','pure')   ),
                                    array('state_reject',       $show_reject                            ),
                                    array('state_invite',       $show_invite                            ),
                                )
                            );
                        }
                    }
                    if ($innerHTMLRows !== ''){
                        $innerHTML = Initialization::instance()->html(
                            'A/list',
                            array(
                                array('rows',       $innerHTMLRows                  ),
                                array('label_0',    __('Your friend','pure') ),
                                array('label_1',    __('Invitation','pure')  ),
                            )
                        );
                    }
                }
            }
            return $innerHTML;
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
                    if ($permissions->invite !== false){
                        $innerHTML      = Initialization::instance()->html(
                            'A/wrapper',
                            array(
                                array('instance_id',            $parameters->id                 ),
                                array('group_id',               uniqid()                        ),
                                array('page_title',             __('Invitations new members','pure') ),
                                array('help',                   __('Select people, which should get your invitation','pure') ),
                                array('list',                   $this->getList($parameters->group_id, $current, $parameters->id)),
                                //Buttons
                                array('invite',                 __('Invite','pure') ),
                                array('cancel',                 __('cancel','pure') ),
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