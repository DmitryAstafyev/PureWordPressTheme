<?php
namespace Pure\Templates\Messenger\Mails{
    class A{
        private function innerHTML(){
            $innerHTML =    '<input data-element-type="Pure.Messenger.Mails.Tab" type="radio" id="Messenger.Mails.Inbox" name="Messenger.Mails.Tabs" data-mails-engine-element="switcher.inbox" checked/>'.
                            '<div data-element-type="Pure.Messenger.Mails.List">'.
                                '<table data-element-type="Pure.Messenger.Mails.List" border="0" data-mails-engine-element="inbox">'.
                                    '<tr>'.
                                        '<td>'.
                                            '<p data-element-type="Pure.Messenger.Mails.List.Title">'.__( 'From', 'pure' ).'</p>'.
                                        '</td>'.
                                        '<td>'.
                                            '<p data-element-type="Pure.Messenger.Mails.List.Title">'.__( 'Content', 'pure' ).'</p>'.
                                        '</td>'.
                                        '<td>'.
                                            '<p data-element-type="Pure.Messenger.Mails.List.Title">'.__( 'Date', 'pure' ).'</p>'.
                                        '</td>'.
                                    '</tr>'.
                                    '<tr data-element-type="Pure.Messenger.Mails.MessageRow" data-mails-engine-template="list.basic">'.
                                        '<td>'.
                                            '<div data-element-type="Pure.Messenger.Mails.User">'.
                                                '<div data-element-type="Pure.Messenger.Mails.User.Avatar" data-mails-engine-template-item="avatar">'.
                                                '</div>'.
                                                '<p data-element-type="Pure.Messenger.Mails.User.Name" data-mails-engine-template-item="name"></p>'.
                                            '</div>'.
                                        '</td>'.
                                        '<td>'.
                                            '<label for="data-mails-engine-random-group-id">'.
                                                '<a data-element-type="Pure.Messenger.Mails.List.Subject" data-mails-engine-template-item="subject"></a>'.
                                                '<div data-element-type="Pure.Messenger.Mails.List.Content" data-mails-engine-template-item="message">'.
                                                '</div>'.
                                            '</label>'.
                                            '<div data-element-type="Pure.Messenger.Mails.Create.Group" data-mails-engine-template="attachment.container">'.
                                                '<div data-element-type="Pure.Messenger.Mails.Create.Attachment" data-mails-engine-template="attachment.view">'.
                                                    '<p data-element-type="Pure.Messenger.Mails.Create.Attachment.FileName">[name]</p>'.
                                                    '<a data-element-type="Pure.Messenger.Mails.User.Remove" data-addition-type="attachment_download" href="[url]" target="_blank"></a>'.
                                                '</div>'.
                                            '</div>'.
                                            '<input data-element-type="Pure.Messenger.Mails.Fast.Controls.Switcher" id="data-mails-engine-random-group-id" type="checkbox"/>'.
                                            '<div data-element-type="Pure.Messenger.Mails.Fast.Controls">'.
                                                '<label for="data-mails-engine-random-group-id">'.
                                                    '<div data-element-type="Pure.Messenger.Mails.Fast.Controls.Button" data-addition-type="switcher">'.
                                                    '</div>'.
                                                    '<p data-element-type="Pure.Messenger.Mails.Fast.Controls.Nested.Count" data-mails-engine-template-item="nested.count"></p>'.
                                                '</label>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Fast.Controls.Button" data-addition-type="remove" data-mails-engine-template-item="remove">'.
                                                '</div>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Fast.Controls.Button" data-addition-type="full" data-mails-engine-template-item="full">'.
                                                '</div>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Fast.Controls.Button" data-addition-type="reply" data-mails-engine-template-item="reply">'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Messenger.Mails.Nested.Container" data-mails-engine-template-item="nested.container"></div>'.
                                            '<div data-element-type="Pure.Messenger.Mails.Nested" data-mails-engine-template="list.nested">'.
                                                '<p data-element-type="Pure.Messenger.Mails.List.Subject" data-mails-engine-template-item="subject"></p>'.
                                                '<p data-element-type="Pure.Messenger.Mails.List.Name" data-mails-engine-template-item="name"></p>'.
                                                '<p data-element-type="Pure.Messenger.Mails.List.Date" data-addition-type="nested" data-mails-engine-template-item="date"></p>'.
                                                '<div data-element-type="Pure.Messenger.Mails.List.Content" data-mails-engine-template-item="message">'.
                                                '</div>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Create.Group" data-mails-engine-template="attachment.container.nested">'.
                                                    '<div data-element-type="Pure.Messenger.Mails.Create.Attachment" data-mails-engine-template="attachment.view">'.
                                                        '<p data-element-type="Pure.Messenger.Mails.Create.Attachment.FileName">[name]</p>'.
                                                        '<a data-element-type="Pure.Messenger.Mails.User.Remove" data-addition-type="attachment_download" href="[url]" target="_blank"></a>'.
                                                    '</div>'.
                                                '</div>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Fast.Controls" data-addition-type="nested">'.
                                                    '<div data-element-type="Pure.Messenger.Mails.Fast.Controls.Button" data-addition-type="full" data-mails-engine-template-item="full">'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</td>'.
                                        '<td>'.
                                            '<p data-element-type="Pure.Messenger.Mails.List.Date" data-mails-engine-template-item="date"></p>'.
                                        '</td>'.
                                    '</tr>'.
                                    '<tr>'.
                                        '<td colspan="3">'.
                                            '<p data-element-type="Pure.Messenger.Mails.List.More"><span data-mails-engine-template-item="inbox.shown">1</span>/<span data-mails-engine-template-item="inbox.total">23</span><a data-element-type="Pure.Messenger.Mails.Button" data-mails-engine-template="inbox.more">more</a></p>'.
                                        '</td>'.
                                    '</tr>'.
                                '</table>'.
                            '</div>'.
                            '<input data-element-type="Pure.Messenger.Mails.Tab" type="radio" id="Messenger.Mails.Outbox" name="Messenger.Mails.Tabs" data-mails-engine-element="switcher.outbox"/>'.
                            '<div data-element-type="Pure.Messenger.Mails.List">'.
                                '<table data-element-type="Pure.Messenger.Mails.List" border="0" data-mails-engine-element="outbox">'.
                                    '<tr>'.
                                        '<td>'.
                                            '<p data-element-type="Pure.Messenger.Mails.List.Title">'.__( 'Recipient', 'pure' ).'</p>'.
                                        '</td>'.
                                        '<td>'.
                                            '<p data-element-type="Pure.Messenger.Mails.List.Title">'.__( 'Content', 'pure' ).'</p>'.
                                        '</td>'.
                                        '<td>'.
                                            '<p data-element-type="Pure.Messenger.Mails.List.Title">'.__( 'Date', 'pure' ).'</p>'.
                                        '</td>'.
                                    '</tr>'.
                                    '<tr data-element-type="Pure.Messenger.Mails.MessageRow" data-mails-engine-template="list.basic">'.
                                        '<td>'.
                                            '<div data-element-type="Pure.Messenger.Mails.User" data-mails-engine-template-item="recipient">'.
                                                '<div data-element-type="Pure.Messenger.Mails.User.Avatar" style="background-image:url([avatar])">'.
                                                '</div>'.
                                                '<p data-element-type="Pure.Messenger.Mails.User.Name">[name]</p>'.
                                            '</div>'.
                                        '</td>'.
                                        '<td>'.
                                            '<label for="data-mails-engine-random-group-id">'.
                                                '<a data-element-type="Pure.Messenger.Mails.List.Subject" data-mails-engine-template-item="subject"></a>'.
                                                '<div data-element-type="Pure.Messenger.Mails.List.Content" data-mails-engine-template-item="message">'.
                                                '</div>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Create.Group" data-mails-engine-template="attachment.container">'.
                                                    '<div data-element-type="Pure.Messenger.Mails.Create.Attachment" data-mails-engine-template="attachment.view">'.
                                                        '<p data-element-type="Pure.Messenger.Mails.Create.Attachment.FileName">[name]</p>'.
                                                        '<a data-element-type="Pure.Messenger.Mails.User.Remove" data-addition-type="attachment_download" href="[url]" target="_blank"></a>'.
                                                    '</div>'.
                                                '</div>'.
                                            '</label>'.
                                            '<input data-element-type="Pure.Messenger.Mails.Fast.Controls.Switcher" id="data-mails-engine-random-group-id" type="checkbox"/>'.
                                            '<div data-element-type="Pure.Messenger.Mails.Fast.Controls">'.
                                                '<label for="data-mails-engine-random-group-id">'.
                                                    '<div data-element-type="Pure.Messenger.Mails.Fast.Controls.Button" data-addition-type="switcher">'.
                                                    '</div>'.
                                                '</label>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Fast.Controls.Button" data-addition-type="remove" data-mails-engine-template-item="remove">'.
                                                '</div>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Fast.Controls.Button" data-addition-type="full" data-mails-engine-template-item="full">'.
                                                '</div>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Fast.Controls.Button" data-addition-type="write" data-mails-engine-template-item="repeat">'.
                                                '</div>'.
                                            '</div>'.
                                        '</td>'.
                                        '<td>'.
                                            '<p data-element-type="Pure.Messenger.Mails.List.Date" data-mails-engine-template-item="date"></p>'.
                                        '</td>'.
                                    '</tr>'.
                                    '<tr>'.
                                        '<td colspan="3">'.
                                            '<p data-element-type="Pure.Messenger.Mails.List.More"><span data-mails-engine-template-item="outbox.shown">1</span>/<span data-mails-engine-template-item="outbox.total">23</span><a data-element-type="Pure.Messenger.Mails.Button" data-mails-engine-template="outbox.more">more</a></p>'.
                                        '</td>'.
                                    '</tr>'.
                                '</table>'.
                            '</div>'.
                            '<input data-element-type="Pure.Messenger.Mails.Tab" type="radio" id="Messenger.Mails.Create" name="Messenger.Mails.Tabs" data-mails-engine-element="switcher.create"/>'.
                            '<div data-element-type="Pure.Messenger.Mails.List">'.
                                '<div data-element-type="Pure.Messenger.Mails.Create.Container">'.
                                    '<div data-element-type="Pure.Messenger.Mails.Create.PseudoCenter">'.
                                        '<div data-element-type="Pure.Messenger.Mails.Create.PseudoCenter.Container">'.
                                            '<div data-element-type="Pure.Messenger.Mails.Create.Group">'.
                                                '<p data-element-type="Pure.Messenger.Mails.Create.Block.Title" data-mails-engine-element="create.title.recipients">'.__( 'Recipients', 'pure' ).'</p>'.
                                                '<a data-element-type="Pure.Messenger.Mails.Button" data-addition-type="add" data-mails-engine-element="buttons.recipients">'.__( 'add recipients', 'pure' ).'</a>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Create.Block.ResetFloat"></div>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Create.Block.Container" data-mails-engine-element="create.users.container">'.
                                                    '<div data-element-type="Pure.Messenger.Mails.User" data-addition-type="recipients" data-mails-engine-template="create.user">'.
                                                        '<div data-element-type="Pure.Messenger.Mails.User.Avatar" data-mails-engine-template-item="avatar">'.
                                                        '</div>'.
                                                        '<p data-element-type="Pure.Messenger.Mails.User.Name" data-mails-engine-template-item="name"></p>'.
                                                        '<a data-element-type="Pure.Messenger.Mails.User.Remove" data-mails-engine-template-item="remove"></a>'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Messenger.Mails.Create.Group">'.
                                                '<p data-element-type="Pure.Messenger.Mails.Create.Block.Title" data-mails-engine-element="create.title.subject">'.__( 'Subject', 'pure' ).'</p>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Create.Block.ResetFloat"></div>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Create.Block.Container" data-addition-type="subject">'.
                                                    '<div data-element-type="Pure.Messenger.Mails.Create.Textarea.Container">'.
                                                        '<textarea data-element-type="Pure.Messenger.Mails.Create.Subject" data-mails-engine-element="create.subject"></textarea>'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Messenger.Mails.Create.Group">'.
                                                '<p data-element-type="Pure.Messenger.Mails.Create.Block.Title">'.__( 'Message', 'pure' ).'</p>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Create.Block.ResetFloat"></div>'.
                                                '<div data-element-type="Pure.Messenger.Mails.Create.Block.Container" data-addition-type="message">'.
                                                    '<div data-element-type="Pure.Messenger.Mails.Create.Textarea.Container" data-resize-type="message">'.
                                                        '<div data-mails-engine-element="mail.editor">'.
                                                        '</div>'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Messenger.Mails.Create.Group">'.
                                                '<div data-element-type="Pure.Messenger.Mails.Create.Attachment" data-mails-engine-template="attachment">'.
                                                    '<div data-element-type="Pure.Common.FileInput.Wrapper">'.
                                                        '<input type="file" data-element-type="Pure.Common.FileInput" data-mails-engine-element="attachment.input"/>'.
                                                    '</div>'.
                                                    '<p data-element-type="Pure.Messenger.Mails.Create.Attachment.FileName" data-mails-engine-element="attachment.name"></p>'.
                                                    '<a data-element-type="Pure.Messenger.Mails.User.Remove" data-addition-type="attachment_remove" data-mails-engine-element="attachment.remove"></a>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Messenger.Mails.Create.Group">'.
                                                '<a data-element-type="Pure.Messenger.Mails.Button" data-addition-type="send" data-mails-engine-element="buttons.cancel">'. __( 'cancel',    'pure' ).'</a>'.
                                                '<a data-element-type="Pure.Messenger.Mails.Button" data-addition-type="send" data-mails-engine-element="buttons.attach">'. __( 'attach',    'pure' ).'</a>'.
                                                '<a data-element-type="Pure.Messenger.Mails.Button" data-addition-type="send" data-mails-engine-element="buttons.send">'.   __( 'send',      'pure' ).'</a>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                '</div>'.
                            '</div>'.
                            '<div data-element-type="Pure.Messenger.Mails.Menu">'.
                                '<div data-element-type="Pure.Messenger.Menu.Mails.Content">'.
                                    '<label for="Messenger.Mails.Inbox">'.
                                        '<div data-element-type="Pure.Messenger.Menu.Mails.Item" data-addition-type="left">'.
                                            '<div data-element-type="Pure.Messenger.Menu.Mails.Item.Icon" data-addition-type="inbox">'.
                                            '</div>'.
                                            '<p data-element-type="Pure.Messenger.Menu.Mails.Item.Title">'.__( 'inbox', 'pure' ).'</p>'.
                                        '</div>'.
                                    '</label>'.
                                    '<label for="Messenger.Mails.Outbox">'.
                                        '<div data-element-type="Pure.Messenger.Menu.Mails.Item" data-addition-type="left">'.
                                            '<div data-element-type="Pure.Messenger.Menu.Mails.Item.Icon" data-addition-type="outbox">'.
                                            '</div>'.
                                            '<p data-element-type="Pure.Messenger.Menu.Mails.Item.Title">'.__( 'outbox', 'pure' ).'</p>'.
                                        '</div>'.
                                    '</label>'.
                                    '<label for="Messenger.Mails.Create">'.
                                        '<div data-element-type="Pure.Messenger.Menu.Mails.Item" data-addition-type="right" data-mails-engine-element="switcher.button.create">'.
                                            '<div data-element-type="Pure.Messenger.Menu.Mails.Item.Icon" data-addition-type="create">'.
                                            '</div>'.
                                            '<p data-element-type="Pure.Messenger.Menu.Mails.Item.Title">'.__( 'create', 'pure' ).'</p>'.
                                        '</div>'.
                                    '</label>'.
                                '</div>'.
                            '</div>';
            return $innerHTML;
        }
        private function resources(){
            \Pure\Templates\ProgressBar\Initialization::instance()->attach_resources_of('A', false, 'immediately');
            \Pure\Templates\ProgressBar\Initialization::instance()->attach_resources_of('B', false, 'immediately');
            \Pure\Components\Uploader\Module\Initialization::instance()->attach(false, 'immediately');
        }
        public function get(){
            $this->resources();
            return $this->innerHTML();
        }
    }
}
?>