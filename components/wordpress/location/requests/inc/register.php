<?php
namespace Pure\Components\WordPress\Location\Requests {
    class Register{
        public $root        = 'request';
        public $url         = NULL;
        public $commands    = array(
            //Resources:: COMPRESSOR
            'compressor_get_resources'                  => array(
                'parameters'    => array('crc32'),
                'module'        => 'request.compressor.php',
                'class'         => 'Pure\Requests\Compressor\Core',
                'method'        => 'get'
            ),
            //Resources:: MAILS
            'resources_messenger_mails_attachment'      => array(
                'parameters'    => array('mail_attachment_id'),
                'module'        => 'request.resources.messenger.mails.php',
                'class'         => 'Pure\Requests\Resources\Messenger\Mails\Attachments',
                'method'        => 'get'
            ),
            //Resources:: CHAT
            'resources_messenger_chat_attachment'       => array(
                'parameters'    => array('chat_attachment_id'),
                'module'        => 'request.resources.messenger.chat.php',
                'class'         => 'Pure\Requests\Resources\Messenger\Chat\Attachments',
                'method'        => 'get'
            ),
            //BuddyPress: ACTIVITIES GET
            'templates_of_activities_get_more'          => array(
                'parameters'    => array('object_type', 'object_id', 'all', 'shown'),
                'module'        => 'request.buddypress.activities.php',
                'class'         => '\Pure\Requests\BuddyPress\Activities\Core',
                'method'        => 'get'
            ),
            //BuddyPress: ACTIVITIES ADD COMMENT
            'templates_of_activities_send_comment'      => array(
                'parameters'        => array('user_id', 'root_id', 'activity_id', 'comment', 'attachment_id'),
                'module'            => 'request.buddypress.activities.php',
                'class'             => '\Pure\Requests\BuddyPress\Activities\Core',
                'method'            => 'sendComment',
                'exclusion_esc_sql' => array('comment')
            ),
            //BuddyPress: ACTIVITIES ADD POST
            'templates_of_activities_send_post'     => array(
                'parameters'        => array('user_id', 'object_id', 'object_type', 'post', 'attachment_id'),
                'module'            => 'request.buddypress.activities.php',
                'class'             => '\Pure\Requests\BuddyPress\Activities\Core',
                'method'            => 'sendPost',
                'exclusion_esc_sql' => array('comment')
            ),
            //BuddyPress: ACTIVITIES GET MEMES
            'templates_of_activities_get_memes'     => array(
                'parameters'    => array('user_id'),
                'module'        => 'request.buddypress.activities.php',
                'class'         => '\Pure\Requests\BuddyPress\Activities\Core',
                'method'        => 'getMemes'
            ),
            //BuddyPress: ACTIVITIES REMOVE ACTIVITY
            'templates_of_activities_remove'     => array(
                'parameters'    => array('user_id', 'activity_id'),
                'module'        => 'request.buddypress.activities.php',
                'class'         => '\Pure\Requests\BuddyPress\Activities\Core',
                'method'        => 'remove'
            ),
            //Plugin: Authors
            'templates_get_more_of_authors'   => array(
                'parameters'    => array('count', 'maximum', 'template', 'group', 'content', 'targets', 'profile','days','from_date' ),
                'module'        => 'request.plugins.thumbnails.authors.php',
                'class'         => 'Pure\Requests\Plugins\Thumbnails\Authors\More',
                'method'        => 'get'
            ),
            'templates_of_authors_set_friendship'   => array(
                'parameters'    => array('initiator', 'friend', 'action'),
                'module'        => 'request.plugins.thumbnails.authors.php',
                'class'         => 'Pure\Requests\Plugins\Thumbnails\Authors\Friendship',
                'method'        => 'set'
            ),
            //Plugin: Groups
            'templates_get_more_of_groups'   => array(
                'parameters'    => array('count', 'maximum', 'template', 'group', 'content', 'targets','days','from_date', 'show_content', 'show_admin_part', 'show_life' ),
                'module'        => 'request.plugins.thumbnails.groups.php',
                'class'         => 'Pure\Requests\Plugins\Thumbnails\Groups\More',
                'method'        => 'get'
            ),
            'templates_of_groups_set_membership'   => array(
                'parameters'    => array('user', 'group' ),
                'module'        => 'request.plugins.thumbnails.groups.php',
                'class'         => 'Pure\Requests\Plugins\Thumbnails\Groups\Membership',
                'method'        => 'set'
            ),
            'templates_of_groups_set_group_avatar'   => array(
                'parameters'    => array('user', 'group', 'path', 'x', 'y', 'height', 'width'),
                'module'        => 'request.plugins.thumbnails.groups.php',
                'class'         => 'Pure\Requests\Plugins\Thumbnails\Groups\Settings',
                'method'        => 'avatar'
            ),
            'templates_of_groups_del_group_avatar'   => array(
                'parameters'    => array('user', 'group'),
                'module'        => 'request.plugins.thumbnails.groups.php',
                'class'         => 'Pure\Requests\Plugins\Thumbnails\Groups\Settings',
                'method'        => 'removeAvatar'
            ),
            'templates_of_groups_set_basic_settings'   => array(
                'parameters'    => array('user', 'group', 'name', 'description', 'notifications'),
                'module'        => 'request.plugins.thumbnails.groups.php',
                'class'         => 'Pure\Requests\Plugins\Thumbnails\Groups\Settings',
                'method'        => 'basic'
            ),
            'templates_of_groups_set_visibility_settings'   => array(
                'parameters'    => array('user', 'group', 'status', 'invite_status'),
                'module'        => 'request.plugins.thumbnails.groups.php',
                'class'         => 'Pure\Requests\Plugins\Thumbnails\Groups\Settings',
                'method'        => 'visibility'
            ),
            'templates_of_groups_member_action'   => array(
                'parameters'    => array('user', 'group', 'target_user', 'action', 'comment'),
                'module'        => 'request.plugins.thumbnails.groups.php',
                'class'         => 'Pure\Requests\Plugins\Thumbnails\Groups\Actions',
                'method'        => 'doAction'
            ),
            'templates_of_groups_request_action'   => array(
                'parameters'    => array('user', 'group', 'waited_user', 'request_id', 'action'),
                'module'        => 'request.plugins.thumbnails.groups.php',
                'class'         => 'Pure\Requests\Plugins\Thumbnails\Groups\Requests',
                'method'        => 'doAction'
            ),
            'templates_of_groups_invite_action'   => array(
                'parameters'    => array('user', 'group', 'members', 'action'),
                'module'        => 'request.plugins.thumbnails.groups.php',
                'class'         => 'Pure\Requests\Plugins\Thumbnails\Groups\Invites',
                'method'        => 'doAction'
            ),
            'templates_of_groups_income_invite_action'   => array(
                'parameters'    => array('user', 'group', 'action'),
                'module'        => 'request.plugins.thumbnails.groups.php',
                'class'         => 'Pure\Requests\Plugins\Thumbnails\Groups\IncomeInvites',
                'method'        => 'doAction'
            ),
            //Plugin: Posts
            'templates_get_more_of_posts'   => array(
                'parameters'    => array('count', 'maximum', 'template', 'content', 'targets', 'profile', 'days', 'from_date', 'only_with_avatar', 'thumbnails', 'slider_template', 'tab_template', 'presentation', 'tabs_columns', 'group', 'post_type', 'sandbox' ),
                'module'        => 'request.plugins.thumbnails.posts.php',
                'class'         => 'Pure\Requests\Plugins\Thumbnails\Posts\More',
                'method'        => 'get'
            ),
            //Template: Profile
            'templates_of_profile_set_user_avatar'   => array(
                'parameters'    => array('user', 'path', 'x', 'y', 'height', 'width'),
                'module'        => 'request.templates.profile.php',
                'class'         => 'Pure\Requests\Templates\Profile\Profile',
                'method'        => 'avatar'
            ),
            'templates_of_profile_del_user_avatar'   => array(
                'parameters'    => array('user'),
                'module'        => 'request.templates.profile.php',
                'class'         => 'Pure\Requests\Templates\Profile\Profile',
                'method'        => 'deleteAvatar'
            ),
            'templates_of_profile_update'   => array(
                'parameters'    => array('user', 'fields'),
                'module'        => 'request.templates.profile.php',
                'class'         => 'Pure\Requests\Templates\Profile\Profile',
                'method'        => 'update'
            ),
            'templates_of_email_update'   => array(
                'parameters'    => array('user', 'email'),
                'module'        => 'request.templates.profile.php',
                'class'         => 'Pure\Requests\Templates\Profile\Profile',
                'method'        => 'email'
            ),
            'templates_of_password_update'   => array(
                'parameters'        => array('user', 'old', 'new'),
                'module'            => 'request.templates.profile.php',
                'class'             => 'Pure\Requests\Templates\Profile\Profile',
                'method'            => 'password',
                'exclusion_esc_sql' => array('old', 'new'),
            ),
            //Template: Personal settings
            'templates_of_personalsettings_update'   => array(
                'parameters'    => array('user', 'settings'),
                'module'        => 'request.templates.personalsettings.php',
                'class'         => 'Pure\Requests\Templates\PersonalSettings\Settings',
                'method'        => 'update'
            ),
            'templates_of_personalsettings_set_title_image'   => array(
                'parameters'    => array('user', 'path', 'x', 'y', 'height', 'width'),
                'module'        => 'request.templates.personalsettings.php',
                'class'         => 'Pure\Requests\Templates\PersonalSettings\Settings',
                'method'        => 'setTitleImage'
            ),
            'templates_of_personalsettings_del_title_image'   => array(
                'parameters'    => array('user'),
                'module'        => 'request.templates.personalsettings.php',
                'class'         => 'Pure\Requests\Templates\PersonalSettings\Settings',
                'method'        => 'delTitleImage'
            ),
            //Template: Group settings
            'templates_of_groupsettings_update'   => array(
                'parameters'    => array('user', 'group', 'settings'),
                'module'        => 'request.templates.groupsettings.php',
                'class'         => 'Pure\Requests\Templates\GroupSettings\Settings',
                'method'        => 'update'
            ),
            'templates_of_groupsettings_set_title_image'   => array(
                'parameters'    => array('user', 'group', 'path', 'x', 'y', 'height', 'width'),
                'module'        => 'request.templates.groupsettings.php',
                'class'         => 'Pure\Requests\Templates\GroupSettings\Settings',
                'method'        => 'setTitleImage'
            ),
            'templates_of_groupsettings_del_title_image'   => array(
                'parameters'    => array('user', 'group'),
                'module'        => 'request.templates.groupsettings.php',
                'class'         => 'Pure\Requests\Templates\GroupSettings\Settings',
                'method'        => 'delTitleImage'
            ),
            //Template: Create group
            'templates_of_create_new_group'   => array(
                'parameters'    => array('user_id', 'name', 'description', 'visibility', 'invitations'),
                'module'        => 'request.templates.creategroup.php',
                'class'         => 'Pure\Requests\Templates\CreateGroup\CreateGroup',
                'method'        => 'create'
            ),
            //Template: Manage quotes
            'templates_of_manage_quotes_remove'   => array(
                'parameters'    => array('user_id', 'quote_id'),
                'module'        => 'request.templates.quotes.php',
                'class'         => 'Pure\Requests\Templates\Quotes\Quotes',
                'method'        => 'remove'
            ),
            'templates_of_manage_quotes_state'   => array(
                'parameters'    => array('user_id', 'quote_id'),
                'module'        => 'request.templates.quotes.php',
                'class'         => 'Pure\Requests\Templates\Quotes\Quotes',
                'method'        => 'state'
            ),
            'templates_of_manage_quotes_add_new'   => array(
                'parameters'        => array('user_id', 'quote'),
                'module'            => 'request.templates.quotes.php',
                'class'             => 'Pure\Requests\Templates\Quotes\Quotes',
                'method'            => 'add',
                'exclusion_esc_sql' => array('quote'),
            ),
            'templates_of_manage_quotes_import'   => array(
                'parameters'        => array('user_id', 'quote_id'),
                'module'            => 'request.templates.quotes.php',
                'class'             => 'Pure\Requests\Templates\Quotes\Quotes',
                'method'            => 'import'
            ),
            //Messenger: MAILS
            'templates_of_messenger_get_body'   => array(
                'parameters'    => array('user_id', 'template'),
                'module'        => 'request.templates.messenger.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Messenger',
                'method'        => 'getBody'
            ),
            'templates_of_messenger_get_inbox_mails'   => array(
                'parameters'    => array('user_id', 'shown', 'maxcount'),
                'module'        => 'request.templates.messenger.mails.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Mails',
                'method'        => 'getInboxMessages'
            ),
            'templates_of_messenger_get_inbox_mails_by_thread_after'   => array(
                'parameters'    => array('user_id', 'thread_id', 'date'),
                'module'        => 'request.templates.messenger.mails.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Mails',
                'method'        => 'getInboxByThreadAfterDate'
            ),
            'templates_of_messenger_get_inbox_mails_message_of_thread'   => array(
                'parameters'    => array('user_id', 'thread_id'),
                'module'        => 'request.templates.messenger.mails.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Mails',
                'method'        => 'getMessagesOfThread'
            ),
            'templates_of_messenger_get_outbox_mails'   => array(
                'parameters'    => array('user_id', 'shown', 'maxcount'),
                'module'        => 'request.templates.messenger.mails.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Mails',
                'method'        => 'getOutboxMessages'
            ),
            'templates_of_messenger_mails_send_message'   => array(
                'parameters'    => array('user_id', 'message_id', 'message', 'subject', 'recipients', 'attachments_key'),
                'module'        => 'request.templates.messenger.mails.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Mails',
                'method'        => 'sendMessage'
            ),
            'templates_of_messenger_mails_attachment_preload'   => array(
                'parameters'    => array('user_id', 'key'),
                'module'        => 'request.templates.messenger.mails.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Attachments',
                'method'        => 'preload'
            ),
            'templates_of_messenger_mails_attachment_remove'   => array(
                'parameters'    => array('user_id', 'attachment_id'),
                'module'        => 'request.templates.messenger.mails.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Attachments',
                'method'        => 'remove'
            ),
            'templates_of_messenger_mails_update_unread'   => array(
                'parameters'    => array('user_id', 'message_id'),
                'module'        => 'request.templates.messenger.mails.php',
                'class'         => 'Pure\Requests\Templates\Messenger\StatusRead',
                'method'        => 'update'
            ),
            'templates_of_messenger_mails_count_unread'   => array(
                'parameters'    => array('user_id'),
                'module'        => 'request.templates.messenger.mails.php',
                'class'         => 'Pure\Requests\Templates\Messenger\StatusRead',
                'method'        => 'getCount'
            ),
            //Messenger: CHAT
            'templates_of_messenger_get_chat_messages'   => array(
                'parameters'    => array('user_id', 'maxcount'),
                'module'        => 'request.templates.messenger.chat.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Chat\Chat',
                'method'        => 'getMessages'
            ),
            'templates_of_messenger_get_chat_messages_by_thread'   => array(
                'parameters'    => array('user_id', 'thread_id', 'shown', 'maxcount'),
                'module'        => 'request.templates.messenger.chat.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Chat\Chat',
                'method'        => 'getMessagesByThread'
            ),
            'templates_of_messenger_get_chat_messages_by_thread_after'   => array(
                'parameters'    => array('user_id', 'thread_id', 'date'),
                'module'        => 'request.templates.messenger.chat.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Chat\Chat',
                'method'        => 'getMessagesByThreadAfterDate'
            ),
            'templates_of_messenger_get_chat_message_send'   => array(
                'parameters'    => array('user_id', 'message', 'thread_id', 'recipients'),
                'module'        => 'request.templates.messenger.chat.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Chat\Chat',
                'method'        => 'sendMessage'
            ),
            'templates_of_messenger_get_chat_memes'   => array(
                'parameters'    => array('user_id'),
                'module'        => 'request.templates.messenger.chat.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Chat\Chat',
                'method'        => 'getMemes'
            ),
            'templates_of_messenger_send_chat_attachment'   => array(
                'parameters'    => array('user_id', 'thread_id'),
                'module'        => 'request.templates.messenger.chat.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Chat\Attachments',
                'method'        => 'sendAttachment'
            ),
            'templates_of_messenger_get_count_unread_chat_messages'   => array(
                'parameters'    => array('user_id'),
                'module'        => 'request.templates.messenger.chat.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Chat\Chat',
                'method'        => 'getUnreadMessagesCount'
            ),
            //Messenger: NOTIFICATIONS
            'templates_of_messenger_notifications_get'   => array(
                'parameters'    => array('user_id', 'shown', 'maxcount'),
                'module'        => 'request.templates.messenger.notifications.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Notifications\Notifications',
                'method'        => 'get'
            ),
            'templates_of_messenger_notifications_set_read'   => array(
                'parameters'    => array('user_id', 'notification_id'),
                'module'        => 'request.templates.messenger.notifications.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Notifications\Notifications',
                'method'        => 'setAsRead'
            ),
            //Messenger: USERS
            'templates_of_messenger_get_friends_list'   => array(
                'parameters'    => array('user_id', 'shown', 'maxcount'),
                'module'        => 'request.templates.messenger.users.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Users',
                'method'        => 'getFriends'
            ),
            'templates_of_messenger_get_groups_list'   => array(
                'parameters'    => array('user_id', 'shown', 'maxcount'),
                'module'        => 'request.templates.messenger.users.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Users',
                'method'        => 'getGroups'
            ),
            'templates_of_messenger_get_recipients_list'   => array(
                'parameters'    => array('user_id', 'shown', 'maxcount'),
                'module'        => 'request.templates.messenger.users.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Users',
                'method'        => 'getRecipients'
            ),
            'templates_of_messenger_get_talks_list'   => array(
                'parameters'    => array('user_id', 'shown', 'maxcount'),
                'module'        => 'request.templates.messenger.users.php',
                'class'         => 'Pure\Requests\Templates\Messenger\Users',
                'method'        => 'getTalks'
            ),
            //Posts: CREATE POST
            'templates_of_posts_create_post'   => array(
                'parameters'        => array(
                    'author_id',        'action',           'post_title',           'post_content',
                    'post_excerpt',     'post_visibility',  'post_association',     'post_association_object',
                    'post_category',    'post_sandbox',     'post_allow_comments',  'post_miniature',
                    'post_id',          'post_tags',        'post_warnings'
                ),
                'exclusion_esc_sql' => array('post_content'),
                'exclusion_exist'   => array('post_miniature', 'post_sandbox', 'post_association_object', 'post_warnings'),
                'module'            => 'request.posts.create.php',
                'class'             => 'Pure\Requests\Post\Editor\Create',
                'method'            => 'create'
            ),
            //Posts: UPDATE POST
            'templates_of_posts_update_post'   => array(
                'parameters'        => array(
                    'author_id',        'action',           'post_title',           'post_content',
                    'post_excerpt',     'post_visibility',  'post_association',     'post_association_object',
                    'post_category',    'post_sandbox',     'post_allow_comments',  'post_id',
                    'post_miniature',   'post_tags',        'post_warnings'
                ),
                'exclusion_esc_sql' => array('post_content'),
                'exclusion_exist'   => array('post_miniature', 'post_sandbox', 'post_association_object', 'post_warnings'),
                'module'            => 'request.posts.create.php',
                'class'             => 'Pure\Requests\Post\Editor\Create',
                'method'            => 'update'
            ),
            //Comments: CREATE
            'templates_of_comments_create_new'   => array(
                'parameters'        => array('user_id', 'post_id', 'comment_id', 'attachment_id', 'comment'),
                'module'            => 'request.comments.requests.php',
                'class'             => 'Pure\Requests\Comments\Requests\Create',
                'method'            => 'create',
                'exclusion_esc_sql' => array('comment'),
            ),
            //Comments: GET MORE
            'templates_of_comments_get_more'   => array(
                'parameters'    => array('post_id', 'shown', 'all'),
                'module'        => 'request.comments.requests.php',
                'class'         => 'Pure\Requests\Comments\Requests\More',
                'method'        => 'get'
            ),
            //Comments: GET UPDATE AFTER DATETIME
            'templates_of_comments_get_update'   => array(
                'parameters'    => array('user_id', 'post_id', 'after_date'),
                'module'        => 'request.comments.requests.php',
                'class'         => 'Pure\Requests\Comments\Requests\More',
                'method'        => 'getFromDateTime'
            ),
            //Comments: GET MEMES
            'templates_of_comments_get_memes'   => array(
                'parameters'    => array('user_id'),
                'module'        => 'request.comments.requests.php',
                'class'         => 'Pure\Requests\Comments\Requests\Memes',
                'method'        => 'get'
            ),
            //Mana icons: GET DATA OF OBJECTS
            'templates_of_mana_icons_get'   => array(
                'parameters'    => array('object', 'user_ids', 'object_ids'),
                'module'        => 'request.mana.icons.requests.php',
                'class'         => 'Pure\Requests\Mana\Icons\Requests\Provider',
                'method'        => 'get'
            ),
            //Mana icons: SET DATA FOR OBJECT
            'templates_of_mana_icons_set'   => array(
                'parameters'        => array('object', 'object_id', 'value', 'field'),
                'module'            => 'request.mana.icons.requests.php',
                'class'             => 'Pure\Requests\Mana\Icons\Requests\Provider',
                'method'            => 'set',
                'exclusion_exist'   => array('field'),
            ),
            //Mana summary: MAKE GIFT
            'templates_of_mana_summary_give'   => array(
                'parameters'        => array('source', 'target', 'value'),
                'module'            => 'request.mana.icons.requests.php',
                'class'             => 'Pure\Requests\Mana\Icons\Requests\Provider',
                'method'            => 'give',
            ),
            //Events: CREATE EVENT
            'templates_of_events_create_event'   => array(
                'parameters'        => array(
                    'author_id',            'action',               'post_title',                   'post_content',
                    'post_excerpt',         'post_visibility',      'post_association',             'post_association_object',
                    'post_category',        'post_sandbox',         'post_allow_comments',          'post_miniature',
                    'event_start_day',      'event_start_month',    'event_start_year',             'event_start_hour',
                    'event_start_minute',   'event_finish_day',     'event_finish_month',           'event_finish_year',
                    'event_finish_hour',    'event_finish_minute',  'event_registration_start_day', 'event_registration_start_month',
                    'event_registration_start_year',    'event_registration_start_hour',    'event_registration_start_minute',  'event_registration_finish_day',
                    'event_registration_finish_month',  'event_registration_finish_year',   'event_registration_finish_hour',   'event_registration_finish_minute',
                    'event_on_map',         'event_place_name',     'event_members_limit',  'post_id','post_tags', 'post_warnings'
                ),
                'exclusion_esc_sql' => array('post_content'),
                'exclusion_exist'   => array('post_miniature', 'post_sandbox', 'event_place_name', 'post_association_object', 'post_warnings'),
                'module'            => 'request.events.editor.php',
                'class'             => 'Pure\Requests\Events\Editor\Create',
                'method'            => 'create'
            ),
            //Events: UPDATE EVENT
            'templates_of_events_update_event'   => array(
                'parameters'        => array(
                    'author_id',            'action',               'post_title',                   'post_content',
                    'post_excerpt',         'post_visibility',      'post_association',             'post_association_object',
                    'post_category',        'post_sandbox',         'post_allow_comments',          'post_id',
                    'event_start_day',      'event_start_month',    'event_start_year',             'event_start_hour',
                    'event_start_minute',   'event_finish_day',     'event_finish_month',           'event_finish_year',
                    'event_finish_hour',    'event_finish_minute',  'event_registration_start_day', 'event_registration_start_month',
                    'event_registration_start_year',    'event_registration_start_hour',    'event_registration_start_minute',  'event_registration_finish_day',
                    'event_registration_finish_month',  'event_registration_finish_year',   'event_registration_finish_hour',   'event_registration_finish_minute',
                    'event_on_map',         'event_place_name',     'post_miniature',               'event_members_limit', 'post_tags', 'post_warnings'
                ),
                'exclusion_esc_sql' => array('post_content'),
                'exclusion_exist'   => array('post_miniature', 'post_sandbox', 'event_place_name', 'post_association_object', 'post_warnings'),
                'module'            => 'request.events.editor.php',
                'class'             => 'Pure\Requests\Events\Editor\Create',
                'method'            => 'update'
            ),
            //Events: ACTION MEMBER EVENT
            'templates_of_events_action_do'   => array(
                'parameters'        => array( 'user_id', 'action', 'event_id'),
                'module'            => 'request.events.actions.php',
                'class'             => 'Pure\Requests\Events\Actions\Core',
                'method'            => 'action'
            ),
            //Reports: CREATE REPORT
            'templates_of_reports_create_report'   => array(
                'parameters'        => array(
                    'author_id',            'action',               'post_title',                   'post_content',
                    'post_excerpt',         'post_visibility',      'post_association',             'post_association_object',
                    'post_category',        'post_sandbox',         'post_allow_comments',          'post_miniature',
                    'report_on_map',        'report_place_name',    'report_collection',            'post_id',
                    'post_tags',            'post_warnings'
                ),
                'exclusion_esc_sql' => array('post_content'),
                'exclusion_exist'   => array('post_miniature', 'post_sandbox', 'report_on_map', 'report_place_name', 'post_association_object', 'post_warnings'),
                'module'            => 'request.reports.editor.php',
                'class'             => 'Pure\Requests\Reports\Editor\Create',
                'method'            => 'create'
            ),
            //Reports: UPDATE REPORT
            'templates_of_reports_update_report'   => array(
                'parameters'        => array(
                    'author_id',            'action',               'post_title',                   'post_content',
                    'post_excerpt',         'post_visibility',      'post_association',             'post_association_object',
                    'post_category',        'post_sandbox',         'post_allow_comments',          'post_miniature',
                    'report_on_map',        'report_place_name',    'report_collection',            'post_id',
                    'post_tags',            'post_warnings'
                ),
                'exclusion_esc_sql' => array('post_content'),
                'exclusion_exist'   => array('post_miniature', 'post_sandbox', 'report_on_map', 'report_place_name', 'post_association_object', 'post_warnings'),
                'module'            => 'request.reports.editor.php',
                'class'             => 'Pure\Requests\Reports\Editor\Create',
                'method'            => 'update'
            ),
            //Reports: VOTE
            'templates_of_reports_vote'   => array(
                'parameters'        => array( 'post_id', 'user_id', 'index', 'value'),
                'module'            => 'request.reports.requests.php',
                'class'             => 'Pure\Requests\Reports\Requests\Provider',
                'method'            => 'vote'
            ),
            //Questions: CREATE QUESTION
            'templates_of_questions_create_question'   => array(
                'parameters'        => array(
                    'author_id',            'action',               'post_title',                   'post_content',
                    'post_visibility',      'post_association',     'post_association_object',      'post_keywords',
                    'post_category',        'post_sandbox',         'post_allow_comments',          'post_id'
                ),
                'exclusion_esc_sql' => array('post_content'),
                'exclusion_exist'   => array('post_sandbox', 'post_association_object'),
                'module'            => 'request.questions.editor.php',
                'class'             => 'Pure\Requests\Questions\Editor\Create',
                'method'            => 'create'
            ),
            //Questions: UPDATE QUESTION
            'templates_of_questions_update_question'   => array(
                'parameters'        => array(
                    'author_id',            'action',               'post_title',                   'post_content',
                    'post_visibility',      'post_association',     'post_association_object',      'post_keywords',
                    'post_category',        'post_sandbox',         'post_allow_comments',          'post_id'
                ),
                'exclusion_esc_sql' => array('post_content'),
                'exclusion_exist'   => array('post_sandbox', 'post_association_object'),
                'module'            => 'request.questions.editor.php',
                'class'             => 'Pure\Requests\Questions\Editor\Create',
                'method'            => 'update'
            ),
            //Questions: ADDITION UPDATE
            'templates_of_questions_update_addition'   => array(
                'parameters'        => array( 'post_id', 'addition_id', 'author_id', 'content'),
                'module'            => 'request.questions.additions.php',
                'class'             => 'Pure\Requests\Questions\Additions\Create',
                'method'            => 'update'
            ),
            //Questions: ADDITION REMOVE
            'templates_of_questions_remove_addition'   => array(
                'parameters'        => array('addition_id'),
                'module'            => 'request.questions.additions.php',
                'class'             => 'Pure\Requests\Questions\Additions\Create',
                'method'            => 'remove'
            ),
            //Questions: RELATED POST ADD
            'templates_of_questions_add_related_post'   => array(
                'parameters'        => array('question_id', 'post_url'),
                'module'            => 'request.questions.relatedposts.php',
                'class'             => 'Pure\Requests\Questions\RelatedPosts\Core',
                'method'            => 'add'
            ),
            //Questions: RELATED POST REMOVE
            'templates_of_questions_remove_related_post'   => array(
                'parameters'        => array('question_id', 'post_id'),
                'module'            => 'request.questions.relatedposts.php',
                'class'             => 'Pure\Requests\Questions\RelatedPosts\Core',
                'method'            => 'remove'
            ),
            //Questions: RELATED QUESTION ADD
            'templates_of_questions_add_related_question'   => array(
                'parameters'        => array('question_id', 'post_url'),
                'module'            => 'request.questions.relatedquestions.php',
                'class'             => 'Pure\Requests\Questions\RelatedQuestions\Core',
                'method'            => 'add'
            ),
            //Questions: RELATED QUESTION REMOVE
            'templates_of_questions_remove_related_question'   => array(
                'parameters'        => array('question_id', 'post_id'),
                'module'            => 'request.questions.relatedquestions.php',
                'class'             => 'Pure\Requests\Questions\RelatedQuestions\Core',
                'method'            => 'remove'
            ),
            //Questions: SOLUTION SET
            'templates_of_questions_solution_set'   => array(
                'parameters'        => array('question_id', 'object_id', 'object_type'),
                'module'            => 'request.questions.solutions.php',
                'class'             => 'Pure\Requests\Questions\Solutions\Core',
                'method'            => 'set'
            ),
            //Questions: ANSWER (COMMENT) UPDATE
            'templates_of_questions_update_answer'   => array(
                'parameters'        => array( 'post_id', 'comment_id', 'parent_id', 'author_id', 'content'),
                'module'            => 'request.questions.answers.php',
                'class'             => 'Pure\Requests\Questions\Answers\Create',
                'method'            => 'update'
            ),
            //Questions: ANSWER (COMMENT) MORE
            'templates_of_questions_get_more_answer'   => array(
                'parameters'        => array( 'post_id', 'shown', 'all'),
                'module'            => 'request.questions.answers.php',
                'class'             => 'Pure\Requests\Questions\Answers\More',
                'method'            => 'get'
            ),
            //Post attachments: ADD
            'templates_of_post_attachments_add'   => array(
                'parameters'        => array('object_id', 'object_type'),
                'module'            => 'request.postattachments.requests.php',
                'class'             => 'Pure\Requests\PostAttachments\Core',
                'method'            => 'add'
            ),
            //Post attachments: REMOVE
            'templates_of_post_attachments_remove'   => array(
                'parameters'        => array( 'object_id', 'object_type', 'user_id', 'url'),
                'module'            => 'request.postattachments.requests.php',
                'class'             => 'Pure\Requests\PostAttachments\Core',
                'method'            => 'remove'
            ),
            //Post attachments: REQUEST
            'templates_of_post_attachments_request'   => array(
                'parameters'        => array( 'object_ids', 'object_types'),
                'module'            => 'request.postattachments.requests.php',
                'class'             => 'Pure\Requests\PostAttachments\Core',
                'method'            => 'request'
            ),
            //Authorization: LOGIN
            'templates_of_authorization_login'   => array(
                'parameters'        => array( 'login', 'password', 'remember'),
                'module'            => 'request.authorization.requests.php',
                'class'             => 'Pure\Requests\Authorization\Requests\Core',
                'method'            => 'login'
            ),
            //Authorization: TRY (REGISTRATION)
            'templates_of_authorization_registration'   => array(
                'parameters'        => array( 'login', 'password', 'email'),
                'module'            => 'request.authorization.requests.php',
                'class'             => 'Pure\Requests\Authorization\Requests\Core',
                'method'            => 'registration'
            ),
            //Authorization: CONFIRM (REGISTRATION)
            'templates_of_registration_email_confirm'   => array(
                'parameters'        => array( 'code'),
                'module'            => 'request.authorization.requests.php',
                'class'             => 'Pure\Requests\Authorization\Requests\Core',
                'method'            => 'confirm'
            ),
            //Authorization: RESEND ACTIVATION LINK
            'templates_of_authorization_resendactivation'   => array(
                'parameters'        => array( 'email'),
                'module'            => 'request.authorization.requests.php',
                'class'             => 'Pure\Requests\Authorization\Requests\Core',
                'method'            => 'resend'
            ),
            //Authorization: CHECK ACTUALITY OF CURRENT SESSION
            'templates_of_authorization_actual'   => array(
                'parameters'        => array(),
                'module'            => 'request.authorization.requests.php',
                'class'             => 'Pure\Requests\Authorization\Requests\Core',
                'method'            => 'actual'
            ),
            //Authorization: RESET PASSWORD
            'templates_of_authorization_resetpassword'   => array(
                'parameters'        => array( 'login', 'email'),
                'module'            => 'request.authorization.requests.php',
                'class'             => 'Pure\Requests\Authorization\Requests\Core',
                'method'            => 'reset'
            ),
            //STREAM: add
            'stream_add'   => array(
                'parameters'        => array( 'owner_id', 'target_id'),
                'module'            => 'request.templates.stream.php',
                'class'             => 'Pure\Requests\Templates\Stream\Stream',
                'method'            => 'add'
            ),
            'stream_remove'   => array(
                'parameters'        => array( 'owner_id', 'target_id'),
                'module'            => 'request.templates.stream.php',
                'class'             => 'Pure\Requests\Templates\Stream\Stream',
                'method'            => 'remove'
            ),
            'stream_toggle'   => array(
                'parameters'        => array( 'owner_id', 'target_id'),
                'module'            => 'request.templates.stream.php',
                'class'             => 'Pure\Requests\Templates\Stream\Stream',
                'method'            => 'toggle'
            ),
            //Settings:: IMPORTER
            'demo_importer_progress'     => array(
                'parameters'    => array('index'),
                'module'        => 'request.importer.php',
                'class'         => 'Pure\Requests\Importer\Core',
                'method'        => 'progress'
            ),
        );
        function __construct(){
            $this->url = get_site_url().'/'.$this->root;
        }

    }
}
?>