<?php
namespace Pure\Templates\BuddyPress\HeadersBackgrounds{
    class B{
        public $name = 'Slide show on background image';
        private $minimal = 8;
        private $maximum = 40;
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
                    if ($settings['background']->template === 'B') {
                        \Pure\Components\GlobalSettings\MIMETypes\Initialization::instance()->attach(true);
                        $limit = (is_numeric($settings['background']->settings) === true ? $settings['background']->settings : $this->maximum);
                        $limit = ($limit > $this->maximum ? $this->maximum : ($limit < $this->minimal ? $this->minimal : $limit));
                        global $wpdb;
                        $result     = $wpdb->get_results(   'SELECT '.
                                                                'ID '.
                                                            'FROM '.
                                                                'wp_posts '.
                                                            'WHERE '.
                                                                'post_author='.$parameters->user_id.' AND '.
                                                                'post_type="attachment" AND '.
                                                                'post_mime_type IN ('.\Pure\Components\GlobalSettings\MIMETypes\Types::$images_sql.') '.
                                                            'ORDER BY post_date_gmt DESC '.
                                                            'LIMIT '.$limit);

                        $backgrounds        = array();
                        $full_background    = false;
                        if (is_array($result) === true){
                            foreach($result as $background_image){
                                $background = wp_get_attachment_image_src($background_image->ID, 'medium', false);
                                if ($background !== false){
                                    $backgrounds[] = $background[0];
                                }
                            }
                            $full_background = rand(0, count($result) - 1);
                            $full_background = $result[$full_background]->ID;
                            $full_background = wp_get_attachment_image_src($full_background, 'large', false);
                            $full_background = ($full_background !== false ? $full_background[0] : false);
                        }
                        if (count($backgrounds) > 0 && $full_background !== false){
                            $count = count($backgrounds);
                            $innerHTML =            '<div data-element-type="Pure.Social.Header.Background.B.Container">'.
                                                        '<div data-element-type="Pure.Social.Header.Background.B.BackgroundImage" style="background-image:url('.$full_background.')">'.
                                                        '</div>';
                            if ($count < $this->minimal){
                            }else{
                                $index = 0;
                                for($column = 0; $column <= 3; $column ++){
                                    $innerHTML .=       '<div data-element-type="Pure.Social.Header.Background.B.Images.Column" data-addition-type="'.$column.'">';
                                    for($row = 0, $max_rows = floor($count / 4); $row < $max_rows; $row ++){
                                        $innerHTML .=       '<div data-element-type="Pure.Social.Header.Background.B.Image" style="background-image:url('.$backgrounds[$index].')">'.
                                                            '</div>';
                                        $index ++;
                                        if (rand(0, 100) < 30){
                                            $innerHTML .=   '<div data-element-type="Pure.Social.Header.Background.B.Image" data-addition-type="empty">'.
                                                            '</div>';
                                        }
                                    }
                                    $innerHTML .=       '</div>';
                                }
                            }
                            $innerHTML .=           '</div>';
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
                    if ($settings['background']->template === 'B'){
                        $count = (is_numeric($settings['background']->settings) === true ? (int)$settings['background']->settings : $this->minimal);
                    }else{
                        $count = $this->minimal;
                    }
                    $count = ($count > $this->maximum ? $this->maximum : ($count < $this->minimal ? $this->minimal : $count));
                    $innerHTML =    '<div data-element-type="Pure.Social.Header.Background.B.Setting">'.
                                        '<div data-element-type="Pure.Social.Header.Background.B.Setting.Background">'.
                                            '<input data-element-type="Pure.Social.Header.Background.B.Setting.Input" value="'.$count.'" min="'.$this->minimal.'" max="'.$this->maximum.'" type="number" data-engine-background-config-id="'.$parameters->storage_id.'" data-engine-background-template="B"/>'.
                                        '</div>'.
                                        '<div data-element-type="Pure.Social.Header.Background.B.Setting.Description">'.
                                            '<p>'.__( 'As background you will see slide show with yours images (from your galleries). To activate slide show you should have at least 8 images in you library. Here you can defined how much images should be in slide show.', 'pure' ).'</p>'.
                                        '</div>'.
                                    '</div>';
                }
            }
            return $innerHTML;
        }
    }
}
?>