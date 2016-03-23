<?php
namespace Pure\Templates\Messenger\Manager{
    class A{
        private $parameters;
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            /*
            $parameters->templates = (isset     ($parameters->templates) === true  ? $parameters->templates : new \stdClass());
            $parameters->templates = (is_object ($parameters->templates) === true  ? $parameters->templates : new \stdClass());
            $parameters->templates->mails           = (isset($parameters->templates->mails          ) === true  ? $parameters->templates->mails         : 'A');
            $parameters->templates->chat            = (isset($parameters->templates->chat           ) === true  ? $parameters->templates->chat          : 'A');
            $parameters->templates->notifications   = (isset($parameters->templates->notifications  ) === true  ? $parameters->templates->notifications : 'A');
            */
            return $parameters;
        }
        private function innerHTML(){
            $Templates = (object)array(
                'mails'         => \Pure\Templates\Messenger\Mails\         Initialization::instance()->get('A', 'immediately'),
                'chat'          => \Pure\Templates\Messenger\Chat\          Initialization::instance()->get('A', 'immediately'),
                'notifications' => \Pure\Templates\Messenger\Notifications\ Initialization::instance()->get('A', 'immediately'),
                'users'         => \Pure\Templates\Messenger\Users\         Initialization::instance()->get('A', 'immediately')
            );
            $innerHTML = '';
            if ($Templates->mails !== false && $Templates->chat !== false && $Templates->notifications !== false){
                $innerHTML =    '<!--BEGIN: Messenger -->'.
                                '<div data-element-type="Pure.Messenger.Container" data-messenger-engine-element="container">'.
                                    '<!--BEGIN: MAILS -->'.
                                    '<input data-element-type="Pure.Messenger.Content" type="radio" id="Messenger.Mails" name="Messenger.Parts" data-messenger-engine-switcher="mails" checked/>'.
                                    '<div data-element-type="Pure.Messenger.Content" data-addition-type="mails">';
                $innerHTML .=       $Templates->mails->get();
                $innerHTML .=       '</div>'.
                                    '<!--END: MAILS -->'.
                                    '<!--BEGIN: CHAT -->'.
                                    '<input data-element-type="Pure.Messenger.Content" type="radio" id="Messenger.Chat" name="Messenger.Parts" data-messenger-engine-switcher="chat"/>'.
                                    '<div data-element-type="Pure.Messenger.Content" data-addition-type="chat">';
                $innerHTML .=       $Templates->chat->get();
                $innerHTML .=       '</div>'.
                                    '<!--END: CHAT -->'.
                                    '<!--BEGIN: NOTIFICATIONS -->'.
                                    '<input data-element-type="Pure.Messenger.Content" type="radio" id="Messenger.Notifications" name="Messenger.Parts" data-messenger-engine-switcher="notifications"/>'.
                                    '<div data-element-type="Pure.Messenger.Content" data-addition-type="notifications">';
                $innerHTML .=       $Templates->notifications->get();
                $innerHTML .=       '</div>'.
                                    '<!--END: NOTIFICATIONS -->'.
                                    '<!--BEGIN: MENU -->'.
                                    '<div data-element-type="Pure.Messenger.Menu">'.
                                        '<div data-element-type="Pure.Messenger.Menu.Content">'.
                                            '<label for="Messenger.Mails">'.
                                                '<div data-element-type="Pure.Messenger.Menu.Item" data-addition-type="left">'.
                                                    '<div data-element-type="Pure.Messenger.Menu.Item.Icon" data-addition-type="mail">'.
                                                    '</div>'.
                                                    '<p data-element-type="Pure.Messenger.Menu.Item.Title">'.__( 'mail', 'pure' ).'</p>'.
                                                    '<p data-element-type="Pure.Messenger.Menu.Item.Count" data-messenger-engine-counter="mails"></p>'.
                                                '</div>'.
                                            '</label>'.
                                            '<label for="Messenger.Chat">'.
                                                '<div data-element-type="Pure.Messenger.Menu.Item" data-addition-type="left">'.
                                                    '<div data-element-type="Pure.Messenger.Menu.Item.Icon" data-addition-type="chat">'.
                                                    '</div>'.
                                                    '<p data-element-type="Pure.Messenger.Menu.Item.Title">'.__( 'chat', 'pure' ).'</p>'.
                                                    '<p data-element-type="Pure.Messenger.Menu.Item.Count" data-messenger-engine-counter="chat"></p>'.
                                                '</div>'.
                                            '</label>'.
                                            '<label for="Messenger.Notifications">'.
                                                '<div data-element-type="Pure.Messenger.Menu.Item" data-addition-type="left">'.
                                                    '<div data-element-type="Pure.Messenger.Menu.Item.Icon" data-addition-type="notification">'.
                                                    '</div>'.
                                                    '<p data-element-type="Pure.Messenger.Menu.Item.Title">'.__( 'notification', 'pure' ).'</p>'.
                                                    '<p data-element-type="Pure.Messenger.Menu.Item.Count" data-messenger-engine-counter="notifications"></p>'.
                                                '</div>'.
                                            '</label>'.
                                            '<div data-element-type="Pure.Messenger.Menu.Item" data-addition-type="right" data-messenger-engine-button="close">'.
                                                '<div data-element-type="Pure.Messenger.Menu.Item.Icon" data-addition-type="exit">'.
                                                '</div>'.
                                                '<p data-element-type="Pure.Messenger.Menu.Item.Title">'.__( 'close', 'pure' ).'</p>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<!--END: MENU -->';
                $innerHTML .=       $Templates->users->get();
                $innerHTML .=   '</div>'.
                                '<!--END: Messenger -->';
            }
            return $innerHTML;
        }
        private function resources(){
            \Pure\Templates\ProgressBar\Initialization::instance()->attach_resources_of('A', false, 'immediately');
            \Pure\Components\Dialogs\B\Initialization::instance()->attach(false, 'immediately');
        }
        public function get($parameters = NULL){
            $this->parameters = $this->validate($parameters);
            $this->resources();
            return $this->innerHTML();
        }
    }
}
?>