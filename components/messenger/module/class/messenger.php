<?php
namespace Pure\Components\Messenger\Module{
    class Messenger{
        private function attachTinyMCE(){
            /*
             * It doesn't necessary, because \_WP_Editors::editor_settings attach it. But let it be here
             * just to remember
            \Pure\Components\Attacher\Module\Attacher::instance()->addJS(
                get_site_url().'/wp-includes/js/tinymce/tinymce.min.js',
                false,
                true
            );*/
            \Pure\Components\Attacher\Module\Attacher::instance()->addCSS(
                get_site_url().'/wp-includes/css/editor.min.css',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addCSS(
                get_site_url().'/wp-includes/css/dashicons.min.css',
                false,
                true
            );
            if ( ! class_exists( '_WP_Editors' ) ) {
                require( ABSPATH . WPINC . '/class-wp-editor.php' );
            }
            \_WP_Editors::editor_settings(
                'nulleditorjustforconfiguration',
                \_WP_Editors::parse_settings( 'nulleditorjustforconfiguration', array() )
            );
        }
        private function attachResources(){
            \Pure\Components\Dialogs\B\Initialization::instance()->attach();
        }
        public function attach($echo = true){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $innerHTML  = '';
            if ($current !== false){
                \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->settings->messenger->properties;
                $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->less($parameters);
                \Pure\Templates\Messenger\Manager\Initialization::instance()->attach_resources_of($parameters->template, false, true);
                require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->requests.'/settings/request.templates.messenger.php'));
                $Settings = new \Pure\Requests\Templates\Messenger\Settings\Initialization();
                $Settings->init((object)array(
                    'template'                  =>$parameters->template,
                    'user_id'                   =>$current->ID,
                    'user_avatar'               =>$WordPress->user_avatar_url($current->ID),
                    'user_name'                 =>$current->name,
                    'mails_max_count'           =>$parameters->mails_max_count,
                    'mail_max_size'             =>$parameters->mail_max_size,
                    'mail_subject_max_size'     =>$parameters->mail_subject_max_size,
                    'allow_attachment_in_mail'  =>$parameters->allow_attachment_in_mail,
                    'attachment_max_size'       =>$parameters->attachment_max_size,
                    'attachment_max_count'      =>$parameters->attachment_max_count,
                    'chats_max_count_messages'  =>$parameters->chats_max_count_messages,
                    'chat_message_max_size'     =>$parameters->chat_message_max_size,
                    'chat_allow_memes'          =>$parameters->chat_allow_memes,
                    'chat_attachment_max_size'  =>$parameters->chat_attachment_max_size,
                    'notifications_max_count'   =>$parameters->notifications_max_count,
                ));
                $Settings = NULL;
                $this->attachTinyMCE();
                $this->attachResources();
            }
            $WordPress  = NULL;
            return $innerHTML;
        }
    }
    $Messenger = new Messenger();
    $Messenger->attach(true);
    $Messenger = NULL;
}
?>