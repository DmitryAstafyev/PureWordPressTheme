<?php
namespace Pure\Templates\BuddyPress\Home{
    class A{
        private $IDPrefixCreateGroup = 'Create_New_Group';
        private function validate(&$parameters){
            $parameters             = (is_object($parameters) === true ? $parameters : new \stdClass());
            $parameters->avatar_id  = (isset($parameters->avatar_id ) === true ? (gettype($parameters->avatar_id) === 'string'  ? $parameters->avatar_id    : false ) : false);
        }
        private function innerHTMLLine($id, $user_id, $current){
            $innerHTML =    '<!--BEGIN: Social.Header:: Bottom line area-->'.
                            '<div data-element-type="Pure.Social.Header.A.BottomLine">'.
                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                    '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                        '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'Home', 'pure' ).'</p>'.
                                    '</div>'.
                                '</div>'.
                                '<div data-element-type="Pure.Social.Header.A.LineItems.Container">'.
                                    '<div data-element-type="Pure.Social.Header.A.LineItems.SubContainer">'.
                                        '<div data-element-type="Pure.Social.Header.A.LineItems">'.
                                            '<label for="'.$id.'_Life">'.
                                                '<a data-element-type="Pure.Social.Header.A.LineItems.Item">'.__( 'Life', 'pure' ).'</a>'.
                                            '</label>'.
                                            '<label for="'.$id.'_Profile">'.
                                                '<a data-element-type="Pure.Social.Header.A.LineItems.Item">'.__( 'Profile', 'pure' ).'</a>'.
                                            '</label>';
            if ($current !== false){
                if ((int)$user_id === (int)$current->ID){
                    $innerHTML .=           '<label for="'.$id.'_Settings">'.
                                                '<a data-element-type="Pure.Social.Header.A.LineItems.Item">'.__( 'Settings', 'pure' ).'</a>'.
                                            '</label>';
                }
            }
            $innerHTML .=                   '<label for="'.$id.'_History">'.
                                                '<a data-element-type="Pure.Social.Header.A.LineItems.Item">'.__( 'History', 'pure' ).'</a>'.
                                            '</label>'.
                                            '<label for="'.$id.'_Friends">'.
                                                '<a data-element-type="Pure.Social.Header.A.LineItems.Item">'.__( 'Friends', 'pure' ).'</a>'.
                                            '</label>'.
                                            '<label for="'.$id.'_Groups">'.
                                                '<a data-element-type="Pure.Social.Header.A.LineItems.Item">'.__( 'Groups', 'pure' ).'</a>'.
                                            '</label>'.
                                        '</div>'.
                                    '</div>'.
                                '</div>'.
                                '<div data-element-type="Pure.Social.Header.A.LineItems.Container">'.
                                    '<div data-element-type="Pure.Social.Header.A.LineItems.SubContainer">'.
                                        '<div data-element-type="Pure.Social.Header.A.LineItems">';
            if ($current !== false) {
                if ((int)$user_id === (int)$current->ID) {
                    $innerHTML .=           '<label for="'.$id.'_CreateGroups">'.
                                                '<div data-element-type="Pure.Social.Header.A.LineItems.IconItem" data-button-type="group" data-engine-creategroup-open-caller="'.$id.$this->IDPrefixCreateGroup.'">'.__( 'Create group', 'pure' ).'</div>'.
                                            '</label>';
                    $innerHTML .=           '<label for="'.$id.'_ManageQuote">'.
                                                '<div data-element-type="Pure.Social.Header.A.LineItems.IconItem" data-button-type="quote">'.__( 'Manage quotes', 'pure' ).'</div>'.
                                            '</label>';
                }
            }
            $innerHTML .=               '</div>'.
                                    '</div>'.
                                '</div>'.
                            '</div>'.
                            '<!--END: Social.Header:: Bottom line area-->';
            return $innerHTML;
        }
        private function innerHTMLActivities($user_id){
            $Activities = \Pure\Templates\BuddyPress\Activities\Initialization::instance()->get('A');
            $innerHTML  = $Activities->innerHTML(
                (object)array(
                    'user_id'=>$user_id
                )
            );
            $Activities = NULL;
            return $innerHTML;
        }
        private function innerHTMLProfile($user_id, $parameters){
            $innerHTML  = '';
            $Profile    = \Pure\Templates\BuddyPress\Profile\Initialization::instance()->get('A');
            $profile    = $Profile->get((int)$user_id, (object)array(
                'manage'    =>true,
                'avatar_id' =>$parameters->avatar_id
            ));
            $Profile    = NULL;
            if ($profile !== ''){
                $innerHTML =   $profile;
            }
            return $innerHTML;
        }
        private function innerHTMLSettings($id, $user_id, $parameters){
            $Settings   = \Pure\Templates\BuddyPress\PersonalSettings\Initialization::instance()->get('A');
            $innerHTML  = $Settings->get((object)array('user_id'=>(int)$user_id));
            $Settings   = NULL;
            return $innerHTML;
        }
        private function innerHTMLHistory($member, $style = ''){
            return  '<p data-element-type="Pure.Social.Home.A.Message.Normal" '.$style.'>'.__( 'With us from', 'pure' ).' '.$member->author->date.' ('.$member->author->how_long.')</p>'.
                    ($member->friendship->created !== false ? '<p data-element-type="Pure.Social.Home.A.Message.Normal" '.$style.'>'.($member->friendship->accepted === true ? __( 'Friendship', 'pure' ) : __( 'Request', 'pure' )).' '.__( 'from', 'pure' ).' '.$member->friendship->created.' ('.$member->friendship->how_long.')</p>' : '').
                    '<p data-element-type="Pure.Social.Home.A.Message.Normal" '.$style.'>'.__( 'Created', 'pure' ).': '.$member->posts->count.' '.__( 'posts', 'pure' ).' '.__( 'and', 'pure' ).' '.$member->comments->count.' '.__( 'comments', 'pure' ).'</p>'.
                    '<p data-element-type="Pure.Social.Home.A.Message.Normal" '.$style.'>'.__( 'Has', 'pure' ).' '.$member->author->friends.' '.__( 'friends and member of', 'pure' ).' '.$member->author->groups.' '.__( 'groups', 'pure' ).'</p>';
        }
        private function innerHTMLFriends($user_id){
            $friends    = new \Pure\Plugins\Thumbnails\Authors\Builder(array(
                'content'           => 'friends_of_user',
                'targets'	        => $user_id,
                'template'	        => 'F',
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => 10,
                'only_with_avatar'	=> false,
                'top'	            => false,
                'profile'	        => '',
                'days'	            => 3650,
                'from_date'         => '',
                'more'              => true));
            $innerHTML  = $friends->render();
            $innerHTML  = ($innerHTML !== '' ? $innerHTML : '<p data-element-type="Pure.Social.Home.A.Message">No friends</p>');
            $friends    = NULL;
            return $innerHTML;
        }
        private function innerHTMLGroups($user_id, $id){
            $groups     = new \Pure\Plugins\Thumbnails\Groups\Builder(array(
                'content'           => 'users',
                'targets'	        => $user_id,
                'template'	        => 'H',
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => 10,
                'only_with_avatar'	=> false,
                'top'	            => false,
                'profile'	        => '',
                'days'	            => 3650,
                'from_date'         => '',
                'show_content'      => false,
                'show_admin_part'   => false,
                'show_life'         => false,
                'more'              => true,
                'group'             => $id.$this->IDPrefixCreateGroup
            ));
            $innerHTML  = $groups->render();
            $innerHTML  = ($innerHTML !== '' ? $innerHTML : '<p data-element-type="Pure.Social.Home.A.Message">No groups</p>');
            $groups     = NULL;
            return $innerHTML;
        }
        private function innerHTMLCreateGroup($user_id, $id){
            $CreateGroup    = \Pure\Templates\BuddyPress\CreateGroup\Initialization::instance()->get('A');
            $innerHTML      = $CreateGroup->get((object)array(  'user_id'   =>(int)$user_id,
                                                                'id'        =>$id.$this->IDPrefixCreateGroup));
            $CreateGroup    = NULL;
            return $innerHTML;
        }
        private function innerHTMLManageQuotes($user_id, $id){
            $Quotes    = \Pure\Templates\BuddyPress\QuotesManage\Initialization::instance()->get('A');
            $innerHTML = $Quotes->get((object)array(
                'user_id'   =>(int)$user_id,
                'id'        =>$id.$this->IDPrefixCreateGroup));
            $Quotes    = NULL;
            return $innerHTML;
        }
        public function get($user_id, $parameters = false){
            $innerHTML = '';
            if (gettype($user_id) === 'integer'){
                $this->validate($parameters);
                $UserData       = \Pure\Providers\Members\Initialization::instance()->getCommon();
                $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                $current        = $WordPress->get_current_user();
                $member         = $UserData->get($user_id);
                $UserData       = NULL;
                $WordPress      = NULL;
                $id             = uniqid('Personal_Home_Page');
                $innerHTML      = $this->innerHTMLLine($id, $user_id, $current);
                $innerHTML      .=  '<div data-element-type="Pure.Social.Home.A.TabsContent.Container">';
                $innerHTML      .=      '<input data-element-type="Pure.Social.Home.A.Tabs.Switchers.Inputs" id="'.$id.'_Life" name="'.$id.'TabsCollection" type="radio" checked />'.
                                        '<div data-element-type="Pure.Social.Home.A.TabContent" data-tab-type="Life">'.
                                            '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                                    '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'Life', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Home.A.TabContent.Tab">'.
                                                $this->innerHTMLActivities($user_id).
                                            '</div>'.
                                        '</div>'.
                                        '<input data-element-type="Pure.Social.Home.A.Tabs.Switchers.Inputs" id="'.$id.'_Profile" name="'.$id.'TabsCollection" type="radio" />'.
                                        '<div data-element-type="Pure.Social.Home.A.TabContent" data-tab-type="Profile">'.
                                            '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                                    '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'Profile', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Home.A.TabContent.Tab">'.
                                                $this->innerHTMLProfile($user_id, $parameters).
                                            '</div>'.
                                        '</div>';
                if ($current !== false) {
                    if ((int)$user_id === (int)$current->ID) {
                        $innerHTML .=   '<input data-element-type="Pure.Social.Home.A.Tabs.Switchers.Inputs" id="' . $id . '_Settings" name="' . $id . 'TabsCollection" type="radio" />' .
                                        '<div data-element-type="Pure.Social.Home.A.TabContent" data-tab-type="Settings">'.
                                            '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                                    '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'Settings', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Home.A.TabContent.Tab">'.
                                                $this->innerHTMLSettings($id, $user_id, $parameters).
                                            '</div>'.
                                        '</div>';
                    }
                }
                $innerHTML      .=      '<input data-element-type="Pure.Social.Home.A.Tabs.Switchers.Inputs" id="'.$id.'_History" name="'.$id.'TabsCollection" type="radio" />'.
                                        '<div data-element-type="Pure.Social.Home.A.TabContent" data-tab-type="History">'.
                                            '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                                    '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'History', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Home.A.TabContent.Tab">'.
                                                $this->innerHTMLHistory($member).
                                            '</div>'.
                                        '</div>'.
                                        '<input data-element-type="Pure.Social.Home.A.Tabs.Switchers.Inputs" id="'.$id.'_Friends" name="'.$id.'TabsCollection" type="radio" />'.
                                        '<div data-element-type="Pure.Social.Home.A.TabContent" data-tab-type="Friends">'.
                                            '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                                    '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'Friends', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Home.A.TabContent.Tab">'.
                                                '<div data-element-type="Pure.Social.Home.A.TabContent.Tab.Wrapper">'.
                                                    '<div data-element-type="Pure.Social.Home.A.TabContent.Tab.Center">'.
                                                        $this->innerHTMLFriends($user_id).
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>'.
                                        '<input data-element-type="Pure.Social.Home.A.Tabs.Switchers.Inputs" id="'.$id.'_Groups" name="'.$id.'TabsCollection" type="radio" data-engine-id="'.$id.$this->IDPrefixCreateGroup.'" data-engine-element="groups_list_container_switcher"/>'.
                                        '<div data-element-type="Pure.Social.Home.A.TabContent" data-tab-type="Groups">'.
                                            '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                                    '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'Groups', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Home.A.TabContent.Tab">'.
                                                '<div data-element-type="Pure.Social.Home.A.TabContent.Tab.Wrapper">'.
                                                    '<div data-element-type="Pure.Social.Home.A.TabContent.Tab.Center">'.
                                                        $this->innerHTMLGroups($user_id, $id).
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>'.
                                        '<input data-element-type="Pure.Social.Home.A.Tabs.Switchers.Inputs" id="'.$id.'_CreateGroups" name="'.$id.'TabsCollection" type="radio" data-engine-id="'.$id.$this->IDPrefixCreateGroup.'" data-engine-element="create_new_group_switcher"/>'.
                                        '<div data-element-type="Pure.Social.Home.A.TabContent" data-tab-type="CreateGroups">'.
                                            '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                                    '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'Create group', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Home.A.TabContent.Tab">'.
                                                '<div data-element-type="Pure.Social.Home.A.TabContent.Tab.Wrapper">'.
                                                    '<div data-element-type="Pure.Social.Home.A.TabContent.Tab.Center">'.
                                                        $this->innerHTMLCreateGroup($user_id, $id).
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>'.
                                        '<input data-element-type="Pure.Social.Home.A.Tabs.Switchers.Inputs" id="'.$id.'_ManageQuote" name="'.$id.'TabsCollection" type="radio" />'.
                                        '<div data-element-type="Pure.Social.Home.A.TabContent" data-tab-type="CreateGroups">'.
                                            '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                                    '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'Manage quotes', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Home.A.TabContent.Tab">'.
                                                '<div data-element-type="Pure.Social.Home.A.TabContent.Tab.Wrapper">'.
                                                    '<div data-element-type="Pure.Social.Home.A.TabContent.Tab.Center">'.
                                                        $this->innerHTMLManageQuotes($user_id, $id).
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>';
                $innerHTML      .=  '</div>';
            }
            return $innerHTML;
        }
    }
}
?>