<?php
namespace Pure\Templates\Messenger\Chat{
    class A{
        private function innerHTML(){
            \Pure\Components\GlobalSettings\MIMETypes\Initialization::instance()->attach(true);
            $WordPress          = new \Pure\Components\WordPress\UserData\Data();
            $current            = $WordPress->get_current_user();
            $current->avatar    = $WordPress->user_avatar_url($current->ID);
            $WordPress          = NULL;
            $innerHTML  =   '<div data-element-type="Pure.Messenger.Columns" data-messenger-engine-element="chat">'.
                                '<div data-element-type="Pure.Messenger.Left">'.
                                    '<div data-element-type="Pure.Messenger.Column">'.
                                        '<div data-element-type="Pure.Messenger.Row" data-addition-type="header">'.
                                            '<div data-element-type="Pure.Messenger.Thread.Current">'.
                                                '<div data-element-type="Pure.Messenger.Thread.Avatar" data-chat-engine-element="avatar" style="background-image:url('.$current->avatar.');">'.
                                                '</div>'.
                                                '<p data-element-type="Pure.Messenger.Thread.Name" data-chat-engine-element="name">'.$current->name.'</p>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Messenger.Tabs">'.
                                                '<label for="talks">'.
                                                    '<div data-element-type="Pure.Messenger.Tab" data-addition-type="talks">'.
                                                    '</div>'.
                                                '</label>'.
                                                '<label for="friends">'.
                                                    '<div data-element-type="Pure.Messenger.Tab" data-addition-type="friends">'.
                                                    '</div>'.
                                                '</label>'.
                                                '<label for="groups">'.
                                                    '<div data-element-type="Pure.Messenger.Tab" data-addition-type="groups">'.
                                                    '</div>'.
                                                '</label>'.
                                            '</div>'.
                                        '</div>'.
                                        '<div data-element-type="Pure.Messenger.Row" data-addition-type="tabs">'.
                                            '<input data-element-type="Pure.Messenger.Tab.Content" id="talks" name="MessengerTabs" type="radio" data-messenger-engine-switcher="talks" checked/>'.
                                            '<div data-element-type="Pure.Messenger.Tab.Content" data-chat-engine-element="list.talks">'.
                                                '<div data-element-type="Pure.Messenger.User" data-chat-engine-template="member">'.
                                                    '<div data-element-type="Pure.Messenger.User.Group" data-chat-engine-template="member.group">'.
                                                        '<div data-element-type="Pure.Messenger.User.Avatar.Group" style="background-image:url([avatar]);">'.
                                                        '</div>'.
                                                        '<p data-element-type="Pure.Messenger.User.Name.Group">[name]</p>'.
                                                    '</div>'.
                                                    '<div data-element-type="Pure.Messenger.User.Multi" data-chat-engine-template="member.multi">'.
                                                        '<div data-element-type="Pure.Messenger.User.Avatar.Multi" style="background-image:url([avatar]);">'.
                                                        '</div>'.
                                                        '<p data-element-type="Pure.Messenger.User.Name.Multi">[name]</p>'.
                                                    '</div>'.
                                                    '<div data-element-type="Pure.Messenger.User.Avatar" data-chat-engine-template-item="avatar">'.
                                                    '</div>'.
                                                    '<p data-element-type="Pure.Messenger.User.Name" data-chat-engine-template-item="name"></p>'.
                                                    '<p data-element-type="Pure.Messenger.User.Info" data-chat-engine-template-item="last">'.__('last talk', 'pure').' <span></span></p>'.
                                                    '<a data-element-type="Pure.Messenger.User.Expand" data-chat-engine-template-item="expand"></a>'.
                                                '</div>'.
                                            '</div>'.
                                            '<input data-element-type="Pure.Messenger.Tab.Content" id="friends" name="MessengerTabs" type="radio" data-messenger-engine-switcher="friends"/>'.
                                            '<div data-element-type="Pure.Messenger.Tab.Content" data-chat-engine-element="list.friends">'.
                                                '<label data-chat-engine-template="member">'.
                                                    '<input data-element-type="Pure.Messenger.User" type="checkbox" data-chat-engine-element="list.item.selector"/>'.
                                                    '<div data-element-type="Pure.Messenger.User">'.
                                                        '<div data-element-type="Pure.Messenger.User.Avatar" data-chat-engine-template-item="avatar">'.
                                                        '</div>'.
                                                        '<p data-element-type="Pure.Messenger.User.Name" data-chat-engine-template-item="name"></p>'.
                                                    '</div>'.
                                                '</label>'.
                                            '</div>'.
                                            '<input data-element-type="Pure.Messenger.Tab.Content" id="groups" name="MessengerTabs" type="radio" data-messenger-engine-switcher="groups"/>'.
                                            '<div data-element-type="Pure.Messenger.Tab.Content">'.
                                                '<div data-element-type="Pure.Messenger.Groups.Selectors">'.
                                                    '<label for="gmembers" data-element-type="Pure.Messenger.Groups.Selector">'.
                                                        '<a data-element-type="Pure.Messenger.Groups.Selector">'.__( 'members', 'pure' ).'</a>'.
                                                    '</label>'.
                                                    '<label for="ggroups" data-element-type="Pure.Messenger.Groups.Selector">'.
                                                        '<a data-element-type="Pure.Messenger.Groups.Selector">'.__( 'groups', 'pure' ).'</a>'.
                                                    '</label>'.
                                                '</div>'.
                                                '<input data-element-type="Pure.Messenger.Tab.Content" id="gmembers" name="MessengerGroupTabs" type="radio" data-messenger-engine-switcher="gmembers" checked/>'.
                                                '<div data-element-type="Pure.Messenger.Tab.Content.Members" data-chat-engine-element="list.gmembers">'.
                                                    '<label data-chat-engine-template="member">'.
                                                        '<input data-element-type="Pure.Messenger.User" type="checkbox" data-chat-engine-element="list.item.selector"/>'.
                                                        '<div data-element-type="Pure.Messenger.User" data-chat-engine-template="member">'.
                                                            '<div data-element-type="Pure.Messenger.User.Avatar" data-chat-engine-template-item="avatar">'.
                                                            '</div>'.
                                                            '<p data-element-type="Pure.Messenger.User.Name" data-chat-engine-template-item="name"></p>'.
                                                        '</div>'.
                                                    '</label>'.
                                                '</div>'.
                                                '<input data-element-type="Pure.Messenger.Tab.Content" id="ggroups" name="MessengerGroupTabs" type="radio" data-messenger-engine-switcher="ggroups"/>'.
                                                '<div data-element-type="Pure.Messenger.Tab.Content.Members" data-chat-engine-element="list.ggroups">'.
                                                    '<label data-chat-engine-template="member">'.
                                                        '<input data-element-type="Pure.Messenger.User" type="radio" name="list.ggroups" data-chat-engine-element="list.item.selector"/>'.
                                                        '<div data-element-type="Pure.Messenger.Group" data-chat-engine-template="member">'.
                                                            '<div data-element-type="Pure.Messenger.Group.Avatar" data-chat-engine-template-item="avatar">'.
                                                            '</div>'.
                                                            '<p data-element-type="Pure.Messenger.Group.Name" data-chat-engine-template-item="name"></p>'.
                                                        '</div>'.
                                                    '</label>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>'.
                                        /*'<div data-element-type="Pure.Messenger.Row" data-addition-type="search">'.
                                            '<div data-element-type="Pure.Messenger.SearchUser">'.
                                                '<input data-element-type="Pure.Messenger.SearchUser" type="text" />'.
                                                '<a data-element-type="Pure.Messenger.SearchUser"></a>'.
                                            '</div>'.
                                        '</div>'.*/
                                    '</div>'.
                                '</div>'.
                                '<div data-element-type="Pure.Messenger.Right">'.
                                    '<input data-element-type="Pure.Messenger.Chat.Content" id="content.info" name="MessengerChatContnentTabs" type="radio" data-messenger-engine-switcher="content.begin" checked/>'.
                                    '<div data-element-type="Pure.Messenger.Column" data-chat-engine-element="aria.info">'.
                                        '<div data-element-type="Pure.Messenger.Chat.Info.Container">'.
                                            '<div data-element-type="Pure.Messenger.Chat.Info.SubContainer">'.
                                                '<p data-element-type="Pure.Messenger.Chat.Info">'.__( 'Select user(s) to open chat.', 'pure' ).'</p>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<input data-element-type="Pure.Messenger.Chat.Content" id="content.chat" name="MessengerChatContnentTabs" type="radio" data-messenger-engine-switcher="content.chat" />'.
                                    '<div data-element-type="Pure.Messenger.Column" data-chat-engine-element="aria.chat">'.
                                        '<div data-element-type="Pure.Messenger.Row" data-addition-type="messages" data-chat-engine-element="aria.messages">'.
                                            '<a data-element-type="Pure.Messenger.Message.More" data-chat-engine-template="chat.message.more">'.__( 'more', 'pure' ).'</a>'.
                                            '<p data-element-type="Pure.Messenger.Message.Date" data-chat-engine-template="chat.message.date">12.12.2015</p>'.
                                            '<!--BEGIN: Image body -->'.
                                            '<div data-element-type="Pure.Messenger.Message.Image" data-chat-engine-template="chat.message.image">'.
                                                '<img alt="" data-element-type="Pure.Messenger.Message.Image" data-chat-engine-template-item="image"/>'.
                                                '<p data-element-type="Pure.Messenger.Message.Time" data-chat-engine-template-item="created"></p>'.
                                                '<p data-element-type="Pure.Messenger.Message.Name" data-chat-engine-template-item="name"></p>'.
                                            '</div>'.
                                            '<!--END: Image body -->'.
                                            '<!--BEGIN: Meme body -->'.
                                            '<div data-element-type="Pure.Messenger.Message.Meme" data-chat-engine-template="chat.message.meme">'.
                                                '<img alt="" data-element-type="Pure.Messenger.Message.Meme" data-chat-engine-template-item="meme"/>'.
                                                '<p data-element-type="Pure.Messenger.Message.Time" data-chat-engine-template-item="created"></p>'.
                                                '<p data-element-type="Pure.Messenger.Message.Name" data-chat-engine-template-item="name"></p>'.
                                            '</div>'.
                                            '<!--END: Meme body -->'.
                                            '<!--BEGIN: Message body -->'.
                                            '<div data-element-type="Pure.Messenger.Message" data-addition-type="right" data-chat-engine-template="chat.message.in">'.
                                                '<div data-element-type="Pure.Messenger.Message.Info">'.
                                                    '<div data-element-type="Pure.Messenger.Message.Avatar" data-chat-engine-template-item="avatar">'.
                                                    '</div>'.
                                                    '<p data-element-type="Pure.Messenger.Message.Time" data-chat-engine-template-item="created"></p>'.
                                                '</div>'.
                                                '<p data-element-type="Pure.Messenger.Message" data-chat-engine-template-item="message"></p>'.
                                                '<p data-element-type="Pure.Messenger.Message.Name" data-chat-engine-template-item="name"></p>'.
                                            '</div>'.
                                            '<!--END: Message body -->'.
                                            '<!--BEGIN: Message body -->'.
                                            '<div data-element-type="Pure.Messenger.Message" data-addition-type="left" data-chat-engine-template="chat.message.out">'.
                                                '<div data-element-type="Pure.Messenger.Message.Info">'.
                                                    '<div data-element-type="Pure.Messenger.Message.Avatar" data-chat-engine-template-item="avatar">'.
                                                    '</div>'.
                                                    '<p data-element-type="Pure.Messenger.Message.Time" data-chat-engine-template-item="created"></p>'.
                                                '</div>'.
                                                '<p data-element-type="Pure.Messenger.Message" data-chat-engine-template-item="message"></p>'.
                                            '</div>'.
                                            '<!--END: Message body -->'.
                                        '</div>'.
                                        '<div data-element-type="Pure.Messenger.Row" data-addition-type="editor">'.
                                            '<div data-element-type="Pure.Messenger.Editor">'.
                                                '<div data-element-type="Pure.Messenger.Editor.Container">'.
                                                    '<textarea data-element-type="Pure.Messenger.Editor" data-chat-engine-element="controls.editor"></textarea>'.
                                                '</div>'.
                                                '<div data-element-type="Pure.Messenger.Controls">'.
                                                    '<div data-element-type="Pure.Common.FileInput.Wrapper">'.
                                                        '<input type="file" data-element-type="Pure.Common.FileInput" accept="'.\Pure\Components\GlobalSettings\MIMETypes\Types::$images.'" data-chat-engine-element="controls.button.attach.input"/>'.
                                                    '</div>'.
                                                    '<div data-element-type="Pure.Messenger.Controls.Send" data-chat-engine-element="controls.button.send">'.
                                                    '</div>'.
                                                    '<div data-element-type="Pure.Messenger.Controls.Small" data-addition-type="attachment" data-chat-engine-element="controls.button.attach">'.
                                                    '</div>'.
                                                    '<div data-element-type="Pure.Messenger.Controls.Small" data-addition-type="meme" data-chat-engine-element="controls.button.meme">'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<input data-element-type="Pure.Messenger.Chat.Content" id="content.info" name="MessengerChatContnentTabs" type="radio" data-messenger-engine-switcher="content.info"/>'.
                                    '<div data-element-type="Pure.Messenger.Column" data-chat-engine-element="aria.info">'.
                                        '<div data-element-type="Pure.Messenger.Chat.Info.Container">'.
                                            '<div data-element-type="Pure.Messenger.Chat.Info.SubContainer">'.
                                                '<p data-element-type="Pure.Messenger.Chat.Info">'.__( 'Select user(s) and press "talk" to create new chat. <br/>Or just select a group to start chat with all members of group.', 'pure' ).'</p>'.
                                                '<a data-element-type="Pure.Messenger.Groups.Selector" data-addition-type="talk" data-chat-engine-element="chat.createnewthread">'.__( 'talk', 'pure' ).'</a>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                '</div>'.
                            '</div>';
            return $innerHTML;

        }
        private function resources(){
            \Pure\Templates\ProgressBar\Initialization::instance()->attach_resources_of('A', false, 'immediately');
            \Pure\Templates\ProgressBar\Initialization::instance()->attach_resources_of('D', false, 'immediately');
        }
        public function get(){
            $this->resources();
            return $this->innerHTML();
        }
    }
}
?>