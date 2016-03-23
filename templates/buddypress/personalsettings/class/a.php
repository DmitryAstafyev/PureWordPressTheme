<?php
namespace Pure\Templates\BuddyPress\PersonalSettings{
    class A{
        private function validate(&$parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $result = ($result === false ? $result : (isset($parameters->user_id) === true ? (gettype($parameters->user_id  ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->id     ) === true ? (gettype($parameters->id       ) == 'string'   ? true : false) : false));
                    break;
            }
            return $result;
        }
        private function resources($parameters){
            //Attach styles
            \Pure\Components\Styles\CheckBoxes\B\Initialization         ::instance()->attach();
            \Pure\Components\Styles\RadioBoxes\A\Initialization         ::instance()->attach();
            \Pure\Components\Dialogs\A\Initialization                   ::instance()->attach();
            \Pure\Templates\ProgressBar\Initialization                  ::instance()->get('A');
            \Pure\Templates\ProgressBar\Initialization                  ::instance()->get('B');
            //Attach tools
            \Pure\Components\Crop\Module\Initialization                 ::instance()->attach();
            \Pure\Components\Uploader\Module\Initialization             ::instance()->attach();
            \Pure\Components\WordPress\Media\Resources\Initialization   ::instance()->attach();
            //Define variables
            \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
            \Pure\Components\Attacher\Module\Initialization             ::instance()->attach();
            $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.personalsettings.configuration.user_id',
                $parameters->user_id,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.personalsettings.configuration.destination',
                $Requests->url,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.personalsettings.configuration.commands.avatar',
                'templates_of_profile_set_user_avatar',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.personalsettings.configuration.request.avatar.send',
                'command'.      '=templates_of_profile_set_user_avatar'.    '&'.
                'user'.         '='.$parameters->user_id.                   '&'.
                'path'.         '='.'[path]'.                               '&'.
                'x'.            '='.'[x]'.                                  '&'.
                'y'.            '='.'[y]'.                                  '&'.
                'height'.       '='.'[height]'.                             '&'.
                'width'.        '='.'[width]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.personalsettings.configuration.request.avatar.remove',
                'command'.      '=templates_of_profile_del_user_avatar'.    '&'.
                'user'.         '='.$parameters->user_id,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.personalsettings.configuration.commands.title',
                'templates_of_personalsettings_set_title_image',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.personalsettings.configuration.request.title.send',
                'command'.      '=templates_of_personalsettings_set_title_image'.   '&'.
                'user'.         '='.$parameters->user_id.                           '&'.
                'path'.         '='.'[path]'.                                       '&'.
                'x'.            '='.'[x]'.                                          '&'.
                'y'.            '='.'[y]'.                                          '&'.
                'height'.       '='.'[height]'.                                     '&'.
                'width'.        '='.'[width]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.personalsettings.configuration.request.title.remove',
                'command'.      '=templates_of_personalsettings_del_title_image'.   '&'.
                'user'.         '='.$parameters->user_id,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.personalsettings.configuration.request.settings',
                'command'.      '=templates_of_personalsettings_update'.    '&'.
                'user'.         '='.$parameters->user_id.                   '&'.
                'settings'.     '='.'[settings]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.personalsettings.configuration.request.email',
                'command'.      '=templates_of_email_update'.   '&'.
                'user'.         '='.$parameters->user_id.       '&'.
                'email'.        '='.'[email]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.personalsettings.configuration.request.password',
                'command'.      '=templates_of_password_update'.    '&'.
                'user'.         '='.$parameters->user_id.           '&'.
                'old'.          '='.'[old]'.                        '&'.
                'new'.          '='.'[new]',
                false,
                true
            );
            $Requests = NULL;
        }
        public function get($parameters){
            $innerHTML  = '';
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user(false, false, true);
                $WordPress  = NULL;
                if ((int)$parameters->user_id === (int)$current->ID){
                    \Pure\Components\GlobalSettings\MIMETypes\Initialization::instance()->attach();
                    $Settings       = new \Pure\Components\BuddyPress\PersonalSettings\User();
                    $settings       = $Settings->get((object)array('user_id'=>(int)$parameters->user_id));
                    $Settings       = NULL;
                    $no_image       = Initialization::instance()->configuration->urls->images.'/A/no_image.png';
                    $images         = (object)array(
                        'header'        =>'',
                        'background'    =>($settings["background"       ]->attachment_id !== false ? wp_get_attachment_url($settings["background"       ]->attachment_id) : $no_image),
                    );
                    if ((int)$settings['header_background']->attachment_id > 0){
                        $images->header = wp_get_attachment_image_src( (int)$settings['header_background']->attachment_id, 'full', false );
                        $images->header = (is_array($images->header) !== false ? $images->header[0] : '');
                    }
                    if ($images->header === '' && (string)$settings['header_background']->url !== ''){
                        $images->header = $settings['header_background']->url;
                    }else{
                        $images->header = false;
                    }
                    $images->header     = ($images->header      !== false ? $images->header     : $no_image);
                    $images->background = ($images->background  !== false ? $images->background : $no_image);
                    $innerHTML      = Initialization::instance()->html(
                        'A/wrapper',
                        array(
                            array('instance_id',            $parameters->id                 ),
                            array('group_id',               uniqid()                        ),
                            array('id',                     uniqid()                        ),
                            array('no_image',               $no_image                       ),
                            array('image_avatar',           $current->avatar                ),
                            array('image_title',            $images->header                 ),
                            array('image_background',       $images->background             ),
                            array('image_types',            \Pure\Components\GlobalSettings\MIMETypes\Types::$images),
                            //Tabs
                            array('tab_0',           __('Avatar & view','pure') ),
                            array('tab_1',           __('Privacy','pure') ),
                            array('tab_2',           __('Email & password','pure') ),
                            //Avatar
                            array('avatar_0',       __('Your avatar','pure') ),
                            array('avatar_1',       __('Define here your avatar','pure') ),
                            array('header_0',       __('Title image','pure') ),
                            array('header_1',       __('This image will be shown on header of your personal page.','pure') ),
                            array('background_0',   __('Background','pure') ),
                            array('background_1',   __('Background image for your personal page','pure') ),
                            array('color_0',        __('Color scheme','pure') ),
                            array('color_1',        __('You can choose the most suitable color scheme','pure') ),
                            //Privacy
                            array('privacy_0',      __('Access to your personal page','pure') ),
                            array('privacy_1',      __('Here you can define who can see your page. Please pay your attention, this setting manages access only to your personal page. Access to your posts can be configured in post editor.','pure') ),
                            array('privacy_2',      __('Everybody can see my page','pure') ),
                            array('privacy_3',      __('Only registered members can see my page','pure') ),
                            array('privacy_4',      __('Only my friends can see my page','pure') ),
                            array('privacy_checked_all',        $settings['privacy']->mode === 'all'        ? 'checked' : '' ),
                            array('privacy_checked_registered', $settings['privacy']->mode === 'registered' ? 'checked' : '' ),
                            array('privacy_checked_friends',    $settings['privacy']->mode === 'friends'    ? 'checked' : '' ),
                            //Email & password
                            array('security_0',     __('Email','pure') ),
                            array('security_1',     __('Email for access to your account.','pure') ),
                            array('security_2',     __('Update email','pure') ),
                            array('security_3',     __('Password','pure') ),
                            array('security_4',     __('You can change here you password.','pure') ),
                            array('security_5',     __('Update password','pure') ),
                            array('security_6',     __('new password','pure') ),
                            array('security_7',     __('new password again','pure') ),
                            array('current_email',  $current->user_email ),
                            //Buttons
                            array('save_image',     __('Save image','pure') ),
                            array('load_image',     __('Load image','pure') ),
                            array('remove',         __('Remove','pure') ),
                            array('select_image',   __('Select image','pure') ),
                            array('save',           __('Save','pure') ),
                            array('cancel',         __('close','pure') ),
                            //array('',            __('','pure') ),
                        )
                    );
                    $this->resources($parameters);
                }
            }
            return $innerHTML;
        }
    }
}
?>