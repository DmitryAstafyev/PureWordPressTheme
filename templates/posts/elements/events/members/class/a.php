<?php
namespace Pure\Templates\Posts\Elements\Events\Members{
    class A{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->post_id) !== false ? true : false));
                return $result;
            }
            return false;
        }
        private function resources($user_id){
            if ($user_id !== false){
                require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->requests.'/settings/request.events.actions.php'));
                $Settings = new \Pure\Requests\Events\Actions\Settings\Initialization();
                $Settings->init((object)array('user_id'=>$user_id));
                $Settings = NULL;
                \Pure\Templates\ProgressBar\        Initialization::instance()->get('B');
                \Pure\Components\Dialogs\B\         Initialization::instance()->attach();
                \Pure\Components\Styles\Buttons\C\  Initialization::instance()->attach();
            }
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false) {
                \Pure\Components\PostTypes\Events\Module\Initialization::instance()->attach();
                $EventProvider      = new \Pure\Components\PostTypes\Events\Module\Provider();
                $members            = $EventProvider->getMembers($parameters->post_id);
                $isRegistrationOpen = $EventProvider->isRegistrationAvailable($parameters->post_id);
                $EventProvider      = NULL;
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $current            = $WordPress->get_current_user();
                $WordPress          = NULL;
                $action             = 'join';
                $this->resources(($current !== false ? $current->ID : false));
                if ($members !== false){
                    $innerHTML .=   '<!--BEGIN: Post.Members.A -->'.
                                    '<div data-post-element-type="Pure.Posts.Members.A">'.
                                        '<table data-post-element-type="Pure.Posts.Members.A">'.
                                            '<tr>'.
                                                '<td data-post-element-type="Pure.Posts.Members.A.Title">'.
                                                    '<p data-post-element-type="Pure.Posts.Members.A.Title">'.__( "Members", 'pure' ).' '.(count($members) > 0 ? '('.count($members).')' : '').'</p>'.
                                                '</td>'.
                                            '</tr>'.
                                            '<tr>'.
                                                '<td data-post-element-type="Pure.Posts.Members.A.List">';
                    if (count($members) > 0){
                        foreach($members as $member){
                            $innerHTML .=   '<div data-element-type="Pure.Posts.Members.A.User">'.
                                                '<div data-element-type="Pure.Posts.Members.A.User.Avatar" style="background-image:url('.$member->avatar.')">'.
                                                '</div>'.
                                                '<a data-element-type="Pure.Posts.Members.A.User.Name" href="'.$member->profile.'" target="_blank">'.$member->name.'</a>'.
                                            '</div>';
                            if ($current !== false){
                                if ((int)$member->id === (int)$current->ID){
                                    $action = 'refuse';
                                }
                            }
                        }
                    }else{
                        $innerHTML .=               '<p data-post-element-type="Pure.Posts.Members.A.Field">'.__( "At current time are no members.", 'pure' ).'</p>';
                    }
                    $innerHTML .=               '</td>'.
                                            '</tr>'.
                                            '<tr>'.
                                                '<td>';
                    if ($current === false){
                        //It's better show nothing in this case
                        //$innerHTML .=               '<p data-post-element-type="Pure.Posts.Members.A.Field">'.__( "You should login to join.", 'pure' ).'</p>';
                    }else{
                        if ($isRegistrationOpen !== false){
                            $innerHTML .=           '<a data-element-type="Pure.CommonStyles.Button.C" '.
                                                        'data-members-button-type="Members.A" '.
                                                        'data-event-members-engine-element="button" '.
                                                        'data-event-members-engine-action="'.$action.'" '.
                                                        'data-event-members-engine-eventID="'.$parameters->post_id.'" '.
                                                        'data-event-members-engine-userID="'.$current->ID.'" '.
                                                    '>'.($action === 'join' ? __( "JOIN", 'pure' ) : __( "REFUSE", 'pure' )).'</a>';
                        }else{
                            $innerHTML .=           '<p data-post-element-type="Pure.Posts.Members.A.Field">'.__( "Registration is closed for now.", 'pure' ).'</p>';
                        }
                    }
                    $innerHTML .=               '</td>'.
                                            '</tr>'.
                                        '</table>'.
                                    '</div>'.
                                    '<!--END: Post.Members.A -->';
                }
            }
            return $innerHTML;
        }
    }
}
?>