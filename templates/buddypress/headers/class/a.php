<?php
namespace Pure\Templates\BuddyPress\Headers{
    class A{
        private function validate(&$parameters){
            $parameters             = (is_object($parameters) === true ? $parameters : new \stdClass());
            $parameters->avatar_id  = (isset($parameters->avatar_id ) === true ? (gettype($parameters->avatar_id) === 'string'  ? $parameters->avatar_id    : false ) : false);
            $parameters->title      = (isset($parameters->title     ) === true ? (gettype($parameters->title    ) === 'string'  ? $parameters->title        : false ) : false);
        }
        private function resources($current, $target_id){
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Dialogs\A\Initialization::instance()->attach(false, 'after');
            \Pure\Components\Dialogs\B\Initialization::instance()->attach(false, 'after');
            \Pure\Templates\ProgressBar\Initialization::instance()->get('B');
            //Define variables
            \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
            \Pure\Components\Attacher\Module\Initialization             ::instance()->attach();
            $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.friendship.configuration.destination',
                $Requests->url,
                false,
                true
            );
            if ($current !== false){
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.buddypress.friendship.configuration.request',
                    'command'.  '='.'templates_of_authors_set_friendship'.  '&'.
                    'initiator'.'='.$current->ID.                           '&'.
                    'friend'.   '='.'[friend]'.                             '&'.
                    'action'.   '='.'[action]',
                    false,
                    true
                );
                if ((int)$current->ID !== (int)$target_id){
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.buddypress.stream.configuration.request.destination',
                        $Requests->url,
                        false,
                        true
                    );
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.buddypress.stream.configuration.request.toggle',
                        'command'.  '='.'stream_toggle'.    '&'.
                        'owner_id'. '='.$current->ID.       '&'.
                        'target_id'.'='.'[target_id]',
                        false,
                        true
                    );
                }
            }
        }
        private function innerHTMLCreateGroup($user_id, $id){
            $CreateGroup    = \Pure\Templates\BuddyPress\CreateGroup\Initialization::instance()->get('A');
            $innerHTML      = $CreateGroup->get((object)array(  'user_id'   =>(int)$user_id,
                                                                'id'        =>$id));
            $CreateGroup    = NULL;
            return $innerHTML;
        }
        private function innerHTMLManageQuotes($user_id, $id){
            $ManageQuotes   = \Pure\Templates\BuddyPress\QuotesManage\Initialization::instance()->get('A');
            $innerHTML      = $ManageQuotes->get((object)array(     'user_id'   =>(int)$user_id,
                                                                    'id'        =>$id));
            $ManageQuotes   = NULL;
            return $innerHTML;
        }
        private function innerHTMLPersonalSettings($user_id, $id){
            $PersonalSettings   = \Pure\Templates\BuddyPress\PersonalSettings\Initialization::instance()->get('A');
            $innerHTML          = $PersonalSettings->get((object)array( 'user_id'   =>(int)$user_id,
                                                                        'id'        =>$id));
            $PersonalSettings   = NULL;
            return $innerHTML;
        }
        private function getBackground($user_id){
            \Pure\Components\BuddyPress\PersonalSettings\Initialization::instance()->attach();
            $Settings                   = new \Pure\Components\BuddyPress\PersonalSettings\User();
            $settings                   = $Settings->get((object)array('user_id'=>(int)$user_id));
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
        public function get($user_id, $parameters = false){
            $this->validate($parameters);
            $WordPress                  = new \Pure\Components\WordPress\UserData\Data();
            $UserData                   = \Pure\Providers\Members\Initialization::instance()->getCommon();
            $member                     = $UserData->get($user_id);
            $current                    = $WordPress->get_current_user();
            $UserData                   = NULL;
            $WordPress                  = NULL;
            $background_image           = $this->getBackground($user_id);
            $background_image           = ($background_image === '' ? Initialization::instance()->configuration->urls->images.'/A/default_background_image.jpg' : $background_image);
            $IDs                        =(object)array(
                'createGroup'   =>uniqid(),
                'manageQuotes'  =>uniqid(),
                'settings'      =>uniqid(),
            );
            $innerHTMLCreateGroup       = '';
            $innerHTMLManageQuotes      = '';
            $innerHTMLPersonalSettings  = '';
            if ($member->friendship->created !== false){
                if ($member->friendship->accepted === true){
                    $friendship = 'remove';//remove from friends
                }else{
                    if ($member->friendship->is_initiator !== false){
                        $friendship = 'accept';//accept request
                    }else{
                        $friendship = 'cancel';//cancel request
                    }
                }
            }else{
                $friendship = 'add';//sent request for friendship
            }
            if ($current !== false){
                if ((int)$current->ID === (int)$user_id){
                    $innerHTMLCreateGroup       = $this->innerHTMLCreateGroup       ($user_id, $IDs->createGroup    );
                    $innerHTMLManageQuotes      = $this->innerHTMLManageQuotes      ($user_id, $IDs->manageQuotes   );
                    $innerHTMLPersonalSettings  = $this->innerHTMLPersonalSettings  ($user_id, $IDs->settings       );
                    $innerHTMLControls          = Initialization::instance()->html(
                        'A/controls_owner',
                        array(
                            array('label_profile',      __('Profile', 'pure')        ),
                            array('label_quotes',       __('Manage quotes', 'pure')  ),
                            array('label_creategroup',  __('Create group', 'pure')   ),
                            array('label_settings',     __('Settings', 'pure')       ),
                            array('id_creategroup',     $IDs->createGroup                   ),
                            array('id_managequotes',    $IDs->manageQuotes                  ),
                            array('id_settings',        $IDs->settings                      ),
                            array('profile_url',        $member->author->urls->profile      ),
                        )
                    );
                }else{
                    $is_in_stream = false;
                    if ((int)$current->ID !== (int)$user_id){
                        \Pure\Components\Stream\Module\Initialization::instance()->attach();
                        $Stream         = new \Pure\Components\Stream\Module\Provider();
                        $is_in_stream   = $Stream->is_in_stream($current->ID, $user_id);
                        $Stream         = NULL;
                    }
                    $innerHTMLControls = Initialization::instance()->html(
                        'A/controls_visiter',
                        array(
                            array('label_profile',      __('Profile', 'pure')                    ),
                            array('label_stream',       __('Add / remove to stream', 'pure')     ),
                            array('label_message',      __('Send message', 'pure')               ),
                            array('recipient_id',       $member->author->id                             ),
                            array('recipient_avatar',   $member->author->avatar                         ),
                            array('recipient_name',     $member->author->name                           ),
                            array('profile_url',        $member->author->urls->profile                  ),
                            array('stream_label_0',     __('Add to your stream', 'pure')         ),
                            array('stream_label_1',     __('Remove from your stream', 'pure')    ),
                            array('target_id',          $user_id                                        ),
                            array('stream_state',       ($is_in_stream === false ? 'add' : 'remove')    ),
                        )
                    );
                }
            }else{
                $innerHTMLControls = Initialization::instance()->html(
                    'A/controls_anonim',
                    array(
                        array('label_profile',      __('Profile', 'pure')        ),
                        array('profile_url',        $member->author->urls->profile      ),
                    )
                );
            }
            \Pure\Components\WordPress\Location\Special\Initialization::instance()->attach();
            $Special    = new \Pure\Components\WordPress\Location\Special\Register();
            $stream_url = $Special->getURL('STREAM', array('user_id'=>$user_id));
            $Special    = NULL;
            $innerHTML          = Initialization::instance()->html(
                'A/header',
                array(
                    array('name',               $member->author->name                       ),
                    array('unique_id',          uniqid()                                    ),
                    array('background_image',   $background_image                           ),
                    array('avatar',             $member->author->avatar                     ),
                    array('label_life',         __('Life', 'pure')                   ),
                    array('label_content',      __('Content', 'pure')                ),
                    array('label_friends',      __('Friends', 'pure')                ),
                    array('label_groups',       __('Groups', 'pure')                 ),
                    array('label_stream',       __('Stream', 'pure')                 ),
                    array('url_life',           $member->author->urls->member               ),
                    array('url_content',        $member->author->urls->posts                ),
                    array('url_friends',        $member->author->urls->friends              ),
                    array('url_groups',         $member->author->urls->groups               ),
                    array('url_stream',         $stream_url                                 ),
                    array('controls',           $innerHTMLControls                          ),
                    array('member_id',          $user_id                                    ),
                    array('friendship_label_0', __('Request friendship', 'pure')     ),
                    array('friendship_label_1', __('Remove from friends', 'pure')    ),
                    array('friendship_label_2', __('Cancel request', 'pure')         ),
                    array('friendship_label_3', __('Accept request', 'pure')         ),
                    array('friendship_state',   $friendship              ),
                )
            );
            $innerHTML .= $innerHTMLCreateGroup;
            $innerHTML .= $innerHTMLManageQuotes;
            $innerHTML .= $innerHTMLPersonalSettings;
            $this->resources($current, $user_id);
            return $innerHTML;
        }
    }
}
?>