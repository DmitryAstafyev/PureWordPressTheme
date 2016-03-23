<?php
namespace Pure\Templates\Messenger\Users{
    class A{
        private function innerHTML(){
            $innerHTML =    '<!--BEGIN: SELECT USERS -->'.
                            '<div data-element-type="Pure.Messenger.UserSelector.Container" data-messenger-users-engine-element="window">'.
                            '<div data-element-type="Pure.Messenger.UserSelector">'.
                                '<input data-element-type="Pure.Messenger.UserSelector.Tab" id="UserSelector.last" name="MessengerUserSelectorTabs" data-messenger-users-engine-element="tab.switcher.last" type="radio" checked/>'.
                                '<input data-element-type="Pure.Messenger.UserSelector.Tab" id="UserSelector.friends" name="MessengerUserSelectorTabs" data-messenger-users-engine-element="tab.switcher.friends" type="radio" />'.
                                '<input data-element-type="Pure.Messenger.UserSelector.Tab" id="UserSelector.groups" name="MessengerUserSelectorTabs" data-messenger-users-engine-element="tab.switcher.groups" type="radio"/>'.
                                '<div data-element-type="Pure.Messenger.UserSelector.Tabs.Labels">'.
                                    '<p data-element-type="Pure.Messenger.UserSelector.Tab.Label" data-addition-type="last">'.__( 'Last recipients', 'pure' ).'</p>'.
                                    '<p data-element-type="Pure.Messenger.UserSelector.Tab.Label" data-addition-type="friends">'.__( 'Your friends', 'pure' ).'</p>'.
                                    '<p data-element-type="Pure.Messenger.UserSelector.Tab.Label" data-addition-type="groups">'.__( 'Peaple from your groups', 'pure' ).'</p>'.
                                '</div>'.
                                '<div data-element-type="Pure.Messenger.UserSelector.Tabs">'.
                                    '<label for="UserSelector.last">'.
                                        '<div data-element-type="Pure.Messenger.UserSelector.Tab" data-addition-type="last">'.
                                        '</div>'.
                                    '</label>'.
                                    '<label for="UserSelector.friends">'.
                                        '<div data-element-type="Pure.Messenger.UserSelector.Tab" data-addition-type="friends">'.
                                        '</div>'.
                                    '</label>'.
                                    '<label for="UserSelector.groups">'.
                                        '<div data-element-type="Pure.Messenger.UserSelector.Tab" data-addition-type="groups">'.
                                        '</div>'.
                                    '</label>'.
                                '</div>'.
                                '<div data-element-type="Pure.Messenger.UserSelector.Content">'.
                                    '<div data-element-type="Pure.Messenger.UserSelector.Tab.Content" data-addition-type="last" data-messenger-users-engine-element="tab.last">'.
                                        '<label data-messenger-users-engine-element="user.template">'.
                                            '<input data-element-type="Pure.Messenger.UserSelector.User" type="checkbox" data-messenger-users-engine-element="user.template.switcher"/>'.
                                            '<div data-element-type="Pure.Messenger.UserSelector.User">'.
                                                '<div data-element-type="Pure.Messenger.UserSelector.User.Avatar" data-messenger-users-engine-element="user.template.avatar">'.
                                                '</div>'.
                                                '<p data-element-type="Pure.Messenger.UserSelector.User.Name" data-messenger-users-engine-element="user.template.name"></p>'.
                                            '</div>'.
                                        '</label>'.
                                    '</div>'.
                                    '<div data-element-type="Pure.Messenger.UserSelector.Tab.Content" data-addition-type="friends" data-messenger-users-engine-element="tab.friends">'.
                                    '</div>'.
                                    '<div data-element-type="Pure.Messenger.UserSelector.Tab.Content" data-addition-type="groups" data-messenger-users-engine-element="tab.groups">'.
                                    '</div>'.
                                '</div>'.
                                '<div data-element-type="Pure.Messenger.UserSelector.Controls">'.
                                    '<input data-element-type="Pure.Messenger.UserSelector.SearchUser.Switcher" id="SearchUser.Switcher" type="checkbox" />'.
                                    '<label for="SearchUser.Switcher">'.
                                        '<a data-element-type="Pure.Messenger.UserSelector.SearchUser.Icon"></a>'.
                                    '</label>'.
                                    '<div data-element-type="Pure.Messenger.UserSelector.FilterUser">'.
                                        '<div data-element-type="Pure.Messenger.UserSelector.SearchUser">'.
                                            '<input data-element-type="Pure.Messenger.UserSelector.SearchUser" type="text" data-messenger-users-engine-element="filter"/>'.
                                            '<label for="SearchUser.Switcher">'.
                                                '<a data-element-type="Pure.Messenger.UserSelector.SearchUser"></a>'.
                                            '</label>'.
                                        '</div>'.
                                    '</div>'.
                                    '<div data-element-type="Pure.Messenger.UserSelector.Buttons.Group">'.
                                        '<a data-element-type="Pure.Messenger.UserSelector.Button" data-addition-type="send" data-messenger-users-engine-element="cancel">'.__( 'cancel', 'pure' ).'</a>'.
                                        '<a data-element-type="Pure.Messenger.UserSelector.Button" data-addition-type="send" data-messenger-users-engine-element="select">'.__( 'select', 'pure' ).'</a>'.
                                    '</div>'.
                                '</div>'.
                            '</div>'.
                        '</div>'.
                        '<!--END: SELECT USERS -->';
            return $innerHTML;
        }
        public function get(){
            return $this->innerHTML();
        }
    }
}
?>