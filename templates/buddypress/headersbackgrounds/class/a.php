<?php
namespace Pure\Templates\BuddyPress\HeadersBackgrounds{
    class A{
        public $name = 'Simple background image';
        private function validate(&$parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $result = ($result === false ? $result : (isset($parameters->user_id) === true ? (gettype($parameters->user_id) == 'integer'  ? true : false) : false));
                    break;
                case 'settings':
                    $result = ($result === false ? $result : (isset($parameters->user_id    ) === true ? (gettype($parameters->user_id      ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->storage_id ) === true ? (gettype($parameters->storage_id   ) == 'string'   ? true : false) : false));
                    break;
            }
            return $result;
        }
        public function get($parameters){
            $innerHTML = '';
            if ($this->validate($parameters, __METHOD__) === true){
                \Pure\Components\BuddyPress\PersonalSettings\Initialization::instance()->attach();
                $Settings   = new \Pure\Components\BuddyPress\PersonalSettings\User();
                $settings   = $Settings->get((object)array('user_id'=>(int)$parameters->user_id));
                $Settings   = NULL;
                if ($settings !== false) {
                    if ($settings['background']->template === 'A') {
                        if (is_numeric($settings['background']->settings) === true){
                            $background_image = wp_get_attachment_image_src($settings['background']->settings, 'full', false);
                            if ($background_image !== false){
                                $innerHTML = '<div data-element-type="Pure.Social.Header.Background.A.Container" style="background-image:url('.$background_image[0].')"></div>';
                            }
                        }
                    }
                }
            }
            return $innerHTML;
        }
        public function settings($parameters){
            $innerHTML  = '';
            if ($this->validate($parameters, __METHOD__) === true){
                \Pure\Components\BuddyPress\PersonalSettings\Initialization::instance()->attach();
                $Settings   = new \Pure\Components\BuddyPress\PersonalSettings\User();
                $settings   = $Settings->get((object)array('user_id'=>(int)$parameters->user_id));
                $Settings   = NULL;
                if ($settings !== false){
                    $default_image      = \Pure\Templates\BuddyPress\HeadersBackgrounds\Initialization::instance()->configuration->urls->images.'/A/no_image.png';
                    if ($settings['background']->template === 'A'){
                        $background_image   = (is_numeric($settings['background']->settings) === true ? wp_get_attachment_image_src( $settings['background']->settings, 'thumbnail', false ) : false);
                        $background_image   = ($background_image !== false ? (isset($background_image[0]) === true ? $background_image[0] : $default_image) : $default_image);
                    }else{
                        $background_image = $default_image;
                    }
                    $innerHTML =    '<div data-element-type="Pure.Social.Header.Background.A.Setting">'.
                                        '<div data-element-type="Pure.Social.Header.Background.A.Setting.Background">'.
                                            '<img alt="" data-element-type="Pure.Social.Header.Background.A.Setting" src="'.$background_image.'" data-storage-id="'.$parameters->storage_id.'" pure-wordpress-media-images-default-src="'.$default_image.'"/>'.
                                            '<div data-element-type="Pure.Social.Header.Background.A.Setting.Controls.Container">'.
                                                '<div data-element-type="Pure.Social.Header.Background.A.Setting.Controls">'.
                                                    '<div data-element-type="Pure.Social.Header.Background.A.Setting.Button" data-addition-type="Load" pure-wordpress-media-images-add-selector="*[data-storage-id=|'.$parameters->storage_id.'|]">'.
                                                        '<p>'.__( 'load', 'pure' ).'</p>'.
                                                    '</div>'.
                                                    '<div data-element-type="Pure.Social.Header.Background.A.Setting.Button" data-addition-type="Remove" pure-wordpress-media-images-remove-selector="*[data-storage-id=|'.$parameters->storage_id.'|]">'.
                                                        '<p>'.__( 'remove', 'pure' ).'</p>'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                            '<input data-element-type="Pure.Social.Header.Background.A.Setting.Input" '.
                                                    'data-engine-background-config-id="'.$parameters->storage_id.'" '.
                                                    'data-engine-background-template="A" '.
                                                    'data-storage-id="'.$parameters->storage_id.'" '.
                                                    'value="'.(is_numeric($settings['background']->settings) === true ? $settings['background']->settings : '').'" '.
                                                    'hidden/>'.
                                        '</div>'.
                                        '<div data-element-type="Pure.Social.Header.Background.A.Setting.Description">'.
                                            '<p>'.__( 'You can upload image to make as background of the title of your personal page. You can upload next formats: .jpg, .jpeg, .bmp, .png and .gif. Or you can choose image from you collection.', 'pure' ).'</p>'.
                                        '</div>'.
                                    '</div>';
                }
            }
            return $innerHTML;
        }
    }
}
?>