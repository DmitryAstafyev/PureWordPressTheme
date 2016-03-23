<?php
namespace Pure\Templates\BuddyPress\GroupAdmin\Manage{
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
                'pure.buddypress.groupadmin.manage.configuration.destination',
                $Requests->url,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupadmin.manage.configuration.request.action',
                'command'.      '=templates_of_groups_member_action'.   '&'.
                'group'.        '='.$parameters->group_id.              '&'.
                'user'.         '='.$current->ID.                       '&'.
                'target_user'.  '='.'[target_user]'.                    '&'.
                'comment'.      '='.'[comment]'.                        '&'.
                'action'.       '='.'[action]',
                false,
                true
            );
            $Requests = NULL;
        }
        private function getList($group_id, $current, $instance_id, $permissions){
            $innerHTML  = '';
            $provider   = \Pure\Providers\Members\Initialization::instance()->get('users_of_group');
            if ($provider !== false) {
                $members = $provider->get(array(
                    'shown'             => 0,
                    'only_with_avatar'  => false,
                    'maxcount'          => 1000,
                    'profile'           => '',
                    'from_date'         => date('Y-m-d'),
                    'days'              => 9999,
                    'targets_array'     => array($group_id),
                    'addition_request'  => array(
                        'request'   => 'status_in_groups',
                        'groups'    => array($group_id)
                    )
                ));
                if ($members !== false) {
                    \Pure\Components\BuddyPress\Admonitions\Initialization::instance()->attach();
                    $innerHTMLRows  = '';
                    $Admonitions    = new \Pure\Components\BuddyPress\Admonitions\Core();
                    foreach($members->members as $member){
                        $admonitions = $Admonitions->count((object)array(
                            'group'=>(int)$group_id,
                            'user' =>(int)$member->author->id
                        ));
                        if ($permissions->roles_and_remove === true){
                            $innerHTMLRemoveSegment = Initialization::instance()->html(
                                'A/row_remove_segment',
                                array(
                                    array('member_name',        $member->author->name                                                   ),
                                    array('member_id',          $member->author->id                                                     ),
                                    array('instance_id',        $instance_id                                                            ),
                                    array('active_admin',       ((int)$member->status_in_groups[0]->is_admin    === 1 ? 'active' : '')  ),
                                    array('active_mod',         ((int)$member->status_in_groups[0]->is_mod      === 1 ? 'active' : '')  ),
                                    array('help_moderator',     __('Moderator role','pure')   ),
                                    array('help_administrator', __('Administrator role','pure')   ),
                                )
                            );
                            $innerHTMLRemoveButton = Initialization::instance()->html(
                                'A/row_remove_button',
                                array(
                                    array('member_name',    $member->author->name               ),
                                    array('member_id',      $member->author->id                 ),
                                    array('instance_id',    $instance_id                        ),
                                    array('help_remove',    __('Remove member','pure')   ),
                                )
                            );
                        }else{
                            $innerHTMLRemoveSegment = '';
                            $innerHTMLRemoveButton  = '';
                        }
                        $innerHTMLRows .= Initialization::instance()->html(
                            'A/row',
                            array(
                                array('member_avatar',      $member->author->avatar                 ),
                                array('member_name',        $member->author->name                   ),
                                array('member_id',          $member->author->id                     ),
                                array('instance_id',        $instance_id                            ),
                                array('remove_segment',     $innerHTMLRemoveSegment                 ),
                                array('remove_button',      $innerHTMLRemoveButton                  ),
                                array('admonition_count',   $admonitions                            ),
                                array('active_ban',         ((int)$member->status_in_groups[0]->is_banned === 1 ? 'active' : '')                            ),
                                array('help_admonition',    __('Admonition','pure')          ),
                                array('help_ban',           __('Ban member','pure')          ),
                            )
                        );

                    }
                    if ($innerHTMLRows !== ''){
                        if ($permissions->roles_and_remove === true){
                            $innerHTMLListRemoveSegment = Initialization::instance()->html(
                                'A/list_remove_segment',
                                array(
                                    array('label',    __('Role','pure') ),
                                )
                            );
                        }else{
                            $innerHTMLListRemoveSegment = '';
                        }
                        $innerHTML = Initialization::instance()->html(
                            'A/list',
                            array(
                                array('rows',           $innerHTMLRows              ),
                                array('remove_segment', $innerHTMLListRemoveSegment ),
                                array('label_0',        __('Member','pure')  ),
                                array('label_1',        __('Actions','pure') ),
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
                    if ($permissions->roles_and_remove === true || $permissions->ban_and_admonition === true){
                        $innerHTML      = Initialization::instance()->html(
                            'A/wrapper',
                            array(
                                array('instance_id',            $parameters->id                 ),
                                array('group_id',               uniqid()                        ),
                                array('page_title',             __('Administration of group','pure') ),
                                array('help',                   __('Here you can manage members of this group','pure') ),
                                array('list',                   $this->getList($parameters->group_id, $current, $parameters->id, $permissions)),
                                //Titles
                                array('member',                 __('Member','pure') ),
                                array('admonition_0',           __('Admonition','pure') ),
                                array('admonition_1',           __('Please, write below the reason of admonition.','pure') ),
                                //Buttons
                                array('cancel',                 __('cancel','pure') ),
                                array('back',                   __('back to list','pure') ),
                                array('send',                   __('apply admonition','pure') ),
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