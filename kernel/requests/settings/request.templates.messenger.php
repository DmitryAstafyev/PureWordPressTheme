<?php
namespace Pure\Requests\Templates\Messenger\Settings{
    class Initialization{
        private function define($parameters){
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            //COMMON
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.template',
                $parameters->template,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.user_id',
                $parameters->user_id,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.user_avatar',
                $parameters->user_avatar,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.user_name',
                $parameters->user_name,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requestURL',
                get_site_url().'/request/',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.body',
                'command'.  '=templates_of_messenger_get_body'. '&'.
                'user_id'.  '='.$parameters->user_id.           '&'.
                'template'. '='.$parameters->template,
                false,
                true
            );
            //MAILS
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.mails.maxCount',
                $parameters->mails_max_count,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.mails.maxSize',
                $parameters->mail_max_size,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.mails.maxSubjectSize',
                $parameters->mail_subject_max_size,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.mails.allowAttachment',
                $parameters->allow_attachment_in_mail,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.mails.attachmentMaxSize',
                $parameters->attachment_max_size,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.mails.attachmentsMaxCount',
                $parameters->attachment_max_count,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.mails.inbox',
                'command'.  '=templates_of_messenger_get_inbox_mails'.  '&'.
                'user_id'.  '='.$parameters->user_id.                   '&'.
                'shown'.    '=[shown]'.                                 '&'.
                'maxcount'. '=[maxcount]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.mails.inboxByThreadAfter',
                'command'.  '=templates_of_messenger_get_inbox_mails_by_thread_after'.  '&'.
                'user_id'.  '='.$parameters->user_id.                                   '&'.
                'thread_id'.'=[thread_id]'.                                             '&'.
                'date'.     '=[date]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.mails.messagesOfThread',
                'command'.  '=templates_of_messenger_get_inbox_mails_message_of_thread'.    '&'.
                'user_id'.  '='.$parameters->user_id.                                       '&'.
                'thread_id'.'=[thread_id]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.mails.outbox',
                'command'.  '=templates_of_messenger_get_outbox_mails'. '&'.
                'user_id'.  '='.$parameters->user_id.                   '&'.
                'shown'.    '=[shown]'.                                 '&'.
                'maxcount'. '=[maxcount]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.mails.send',
                'command'.          '=templates_of_messenger_mails_send_message'.   '&'.
                'user_id'.          '='.$parameters->user_id.                       '&'.
                'message_id'.       '=[message_id]'.                                '&'.
                'message'.          '=[message]'.                                   '&'.
                'subject'.          '=[subject]'.                                   '&'.
                'recipients'.       '=[recipients]'.                                '&'.
                'attachments_key'.  '=[attachments_key]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.mails.attachment.preload',
                'templates_of_messenger_mails_attachment_preload',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.mails.attachment.remove',
                'command'.          '=templates_of_messenger_mails_attachment_remove'.  '&'.
                'user_id'.          '='.$parameters->user_id.                           '&'.
                'attachment_id'.    '=[attachment_id]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.mails.updateReadUnread',
                'command'.      '=templates_of_messenger_mails_update_unread'.  '&'.
                'user_id'.      '='.$parameters->user_id.                       '&'.
                'message_id'.   '=[message_id]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.mails.getUnreadCount',
                'command'.      '=templates_of_messenger_mails_count_unread'.  '&'.
                'user_id'.      '='.$parameters->user_id,
                false,
                true
            );
            //CHAT
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.chat.messagesMaxCount',
                $parameters->chats_max_count_messages,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.chat.messagesMaxSize',
                $parameters->chat_message_max_size,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.chat.allowMemes',
                $parameters->chat_allow_memes,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.chat.allowedAttachmentSize',
                $parameters->chat_attachment_max_size,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.chat.messages',
                'command'.  '=templates_of_messenger_get_chat_messages'.    '&'.
                'user_id'.  '='.$parameters->user_id.                       '&'.
                'maxcount'. '=[maxcount]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.chat.messagesByThread',
                'command'.  '=templates_of_messenger_get_chat_messages_by_thread'.  '&'.
                'user_id'.  '='.$parameters->user_id.                               '&'.
                'thread_id'.'=[thread_id]'.                                         '&'.
                'shown'.    '=[shown]'.                                             '&'.
                'maxcount'. '=[maxcount]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.chat.messagesByThreadAfter',
                'command'.  '=templates_of_messenger_get_chat_messages_by_thread_after'.    '&'.
                'user_id'.  '='.$parameters->user_id.                                       '&'.
                'thread_id'.'=[thread_id]'.                                                 '&'.
                'date'.     '=[date]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.chat.send',
                'command'.      '=templates_of_messenger_get_chat_message_send'.    '&'.
                'user_id'.      '='.$parameters->user_id.                           '&'.
                'message'.      '='.'[message]'.                                    '&'.
                'thread_id'.    '='.'[thread_id]'.                                  '&'.
                'recipients'.   '='.'[recipients]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.chat.getMemes',
                'command'.      '=templates_of_messenger_get_chat_memes'.    '&'.
                'user_id'.      '='.$parameters->user_id,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.chat.getUnreadCount',
                'command'.      '=templates_of_messenger_get_count_unread_chat_messages'.   '&'.
                'user_id'.      '='.$parameters->user_id,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.chat.attachment.command',
                'templates_of_messenger_send_chat_attachment',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.chat.attachment.url',
                'command'.              '=resources_messenger_chat_attachment'.    '&'.
                'chat_attachment_id'.   '=[attachment_id]',
                false,
                true
            );
            //NOTIFICATIONS
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.notifications.get',
                'command'.  '=templates_of_messenger_notifications_get'.    '&'.
                'user_id'.  '='.$parameters->user_id.                       '&'.
                'shown'.    '=[shown]'.                                     '&'.
                'maxcount'. '=[maxcount]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.notifications.notificationsMaxCount',
                $parameters->notifications_max_count,
                false,
                true
            );
            //USERS
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.users.friends',
                'command'.  '=templates_of_messenger_get_friends_list'. '&'.
                'user_id'.  '='.$parameters->user_id.                   '&'.
                'shown'.    '=[shown]'.                                 '&'.
                'maxcount'. '=[maxcount]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.users.groups',
                'command'.  '=templates_of_messenger_get_groups_list'. '&'.
                'user_id'.  '='.$parameters->user_id.                   '&'.
                'shown'.    '=[shown]'.                                 '&'.
                'maxcount'. '=[maxcount]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.users.recipients',
                'command'.  '=templates_of_messenger_get_recipients_list'.  '&'.
                'user_id'.  '='.$parameters->user_id.                       '&'.
                'shown'.    '=[shown]'.                                     '&'.
                'maxcount'. '=[maxcount]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.components.messenger.configuration.requests.users.talks',
                'command'.  '=templates_of_messenger_get_talks_list'.   '&'.
                'user_id'.  '='.$parameters->user_id.                   '&'.
                'shown'.    '=[shown]'.                                 '&'.
                'maxcount'. '=[maxcount]',
                false,
                true
            );
        }
        public function init($parameters){
            $this->define($parameters);
        }
    }
}
?>