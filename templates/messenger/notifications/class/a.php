<?php
namespace Pure\Templates\Messenger\Notifications{
    class A{
        private function innerHTML(){
            $innerHTML =    '<div data-element-type="Pure.Messenger.Notifications.List">'.
                            '<table data-element-type="Pure.Messenger.Notifications.List" border="0" data-notification-engine-element="notifications">'.
                                '<tr>'.
                                    '<td>'.
                                        '<p data-element-type="Pure.Messenger.Notifications.List.Title">'.__( 'Content', 'pure' ).'</p>'.
                                    '</td>'.
                                    '<td>'.
                                        '<p data-element-type="Pure.Messenger.Notifications.List.Title">'.__( 'Date', 'pure' ).'</p>'.
                                    '</td>'.
                                '</tr>'.
                                '<tr data-notification-engine-template="notification">'.
                                    '<td>'.
                                        '<p data-element-type="Pure.Messenger.Notifications.List.Subject" data-notification-engine-template-item="subject"></p>'.
                                        '<p data-element-type="Pure.Messenger.Notifications.List.Content" data-notification-engine-template-item="content"></p>'.

                                        '<div data-element-type="Pure.Messenger.Notifications.User" data-notification-engine-template-item="target">'.
                                            '<div data-element-type="Pure.Messenger.Notifications.User.Avatar" style="background-image:url([avatar]);">'.
                                            '</div>'.
                                            '<p data-element-type="Pure.Messenger.Notifications.User.Name">[name]</p>'.
                                            '<p data-element-type="Pure.Messenger.Notifications.User.Addition">[description]</p>'.
                                        '</div>'.


                                        '<div data-element-type="Pure.Messenger.Notifications.Fast.Controls" data-notification-engine-template-item="controls">'.
                                            '<a data-element-type="Pure.Messenger.Notifications.List.ActionButton" data-notification-engine-template-item="button">[title]</a>'.
                                        '</div>'.
                                    '</td>'.
                                    '<td>'.
                                        '<p data-element-type="Pure.Messenger.Notifications.List.Date" data-notification-engine-template-item="date"></p>'.
                                    '</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<td colspan="3">'.
                                        '<p data-element-type="Pure.Messenger.Notifications.List.More"><span data-notification-engine-element="more.shown"></span>/<span data-notification-engine-element="more.total"></span><a data-element-type="Pure.Messenger.Notifications.List.More" data-notification-engine-template="more.button">'.__( 'more', 'pure' ).'</a></p>'.
                                    '</td>'.
                                '</tr>'.
                            '</table>'.
                        '</div>'.
                        '<div data-element-type="Pure.Messenger.Notifications.Menu">'.
                        '</div>';
            return $innerHTML;
        }
        private function resources(){
            \Pure\Templates\ProgressBar\Initialization::instance()->attach_resources_of('B', false, 'immediately');
        }
        public function get(){
            $this->resources();
            return $this->innerHTML();
        }
    }
}
?>