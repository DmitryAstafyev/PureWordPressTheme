<?php
namespace Pure\Templates\BuddyPress\GroupAdmin\Requests{
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
                'pure.buddypress.groupadmin.requests.configuration.destination',
                $Requests->url,
                false,
                true
            );
            //'user', 'group', 'waited_user', 'request_id', 'action'
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.groupadmin.requests.configuration.request.action',
                'command'.      '=templates_of_groups_request_action'.  '&'.
                'group'.        '='.$parameters->group_id.              '&'.
                'user'.         '='.$current->ID.                       '&'.
                'waited_user'.  '='.'[waited_user]'.                    '&'.
                'request_id'.   '='.'[request_id]'.                     '&'.
                'action'.       '='.'[action]',
                false,
                true
            );
            $Requests = NULL;
        }
        private function getList($group_id, $current, $instance_id){
            $innerHTML  = '';
            $GroupProviderCommon    = \Pure\Providers\Groups\Initialization::instance()->getCommon();
            $members                = $GroupProviderCommon->get_group_membership_requests($group_id);
            $GroupProviderCommon    = NULL;
            if ($members !== false){
                \Pure\Components\Tools\Dates\Initialization::instance()->attach(true);
                $DateTool       = new \Pure\Components\Tools\Dates\Dates();
                $innerHTMLRows  = '';
                foreach($members as $member){
                    $innerHTMLRows .= Initialization::instance()->html(
                        'A/row',
                        array(
                            array('member_avatar',      $member->author->avatar                             ),
                            array('member_name',        $member->author->name                               ),
                            array('member_id',          $member->author->id                                 ),
                            array('instance_id',        $instance_id                                        ),
                            array('request_id',         $member->membership_request_id                      ),
                            array('waiting_time',       __('waited ','pure'). $DateTool->fromNow($member->membership_request_date)),
                            array('deny',               __('Deny','pure')                            ),
                            array('accept',             __('Accept','pure')                          ),
                        )
                    );
                }
                if ($innerHTMLRows !== ''){
                    $innerHTML = Initialization::instance()->html(
                        'A/list',
                        array(
                            array('rows',       $innerHTMLRows                      ),
                            array('label_0',    __('Waiting users',  'pure') ),
                            array('label_1',    __('Actions',        'pure') ),
                        )
                    );
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
                                array('page_title',             __('Requests for membership','pure') ),
                                array('help',                   __('You can accept of deny request for membership','pure') ),
                                array('list',                   $this->getList($parameters->group_id, $current, $parameters->id)),
                                //Buttons
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