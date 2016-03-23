<?php
namespace Pure\Requests\Templates\Profile{
    class Profile{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'avatar':
                    $parameters->user       = (integer  )($parameters->user     );
                    $parameters->path       = (string   )($parameters->path     );
                    $parameters->x          = (integer  )($parameters->x        );
                    $parameters->y          = (integer  )($parameters->y        );
                    $parameters->height     = (integer  )($parameters->height   );
                    $parameters->width      = (integer  )($parameters->width    );
                    return true;
                case 'update':
                    $parameters->user       = (integer  )($parameters->user     );
                    $parameters->fields     = (string   )($parameters->fields   );
                    return true;
                case 'deleteAvatar':
                    $parameters->user       = (integer  )($parameters->user     );
                    return true;
                case 'email':
                    $parameters->user       = (integer  )($parameters->user     );
                    $parameters->email      = (string   )($parameters->email    );
                    return true;
                case 'password':
                    $parameters->user       = (integer  )($parameters->user     );
                    $parameters->old        = (string   )($parameters->old      );
                    $parameters->new        = (string   )($parameters->new      );
                    return true;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'update':
                    $parameters->fields = sanitize_text_field($parameters->fields);
                    break;
                case 'email':
                    $parameters->email  = sanitize_text_field($parameters->email);
                    break;
            }
        }
        public function avatar($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $response   = (object)array(
                    'url'       =>'',
                    'message'   =>''
                );
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->user) {
                    \Pure\Components\BuddyPress\Profile\Initialization::instance()->attach();
                    $BuddyPressProfile = new \Pure\Components\BuddyPress\Profile\Core();
                    if (isset($_FILES['file']) === true){
                        if ($_FILES['file']['size'] < bp_core_avatar_original_max_filesize()){
                            if ($parameters->x      !== -1 && $parameters->y        !== -1 &&
                                $parameters->height !== -1 && $parameters->width    !== -1){
                                $crop = (object)array(
                                    'x'=>$parameters->x,
                                    'y'=>$parameters->y,
                                    'h'=>$parameters->height,
                                    'w'=>$parameters->width
                                );
                            }else{
                                $crop = false;
                            }
                            if ($crop === false){
                                if ( !function_exists( 'wp_handle_upload' ) ) { require_once( \Pure\Configuration::instance()->dir(ABSPATH . 'wp-admin/includes/file.php') ); }
                                $file               = wp_handle_upload($_FILES['file'], array( 'test_form' => false ));
                                $response->url      = $file['url'];
                                $response->path     = $file['file'];
                                $response->message  = 'ready_for_crop';
                            }else{
                                if ($parameters->path !== ''){
                                    @unlink($parameters->path);
                                }
                                if ($BuddyPressProfile->setAvatar((object)array(
                                        'id'    =>(int)$parameters->user,
                                        'files' =>$_FILES,
                                        'field' =>'file',
                                        'crop'  =>$crop)) === true){
                                    $response->url      = $BuddyPressProfile->getAvatar((object)array('id'=>(int)$parameters->user));
                                    $response->message  = 'success';
                                }else{
                                    $response->message = 'error_during_saving';
                                }
                            }
                        }else{
                            $response->message = 'too_large_filesize';
                        }
                    }else{
                        $response->message = 'wrong_file';
                    }
                    $BuddyPressProfile = NULL;
                }else{
                    $response->message = 'wrong_user';
                }
                echo json_encode($response);
            }
        }
        public function update($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $this->sanitize($parameters, __METHOD__);
                try{
                    $parameters->fields = stripslashes($parameters->fields);
                    $fields             = json_decode(stripslashes($parameters->fields));
                    if (is_null($fields) === false){
                        \Pure\Components\BuddyPress\Profile\Initialization::instance()->attach();
                        $BuddyPressProfile = new \Pure\Components\BuddyPress\Profile\Core();
                        if ($BuddyPressProfile->set((object)array(
                            'id'    =>$parameters->user,
                            'fields'=>$fields
                        )) === true){
                            echo "updated";
                        }else{
                            echo "error_during_saving";
                        }
                        $BuddyPressProfile = NULL;
                        return true;
                    }
                    //echo var_dump($parameters->fields);
                }catch (\Exception $e){
                    echo 'incorrect_data';
                    return false;
                }
                echo 'incorrect_data';
                return false;
            }
            echo 'fail';
            return false;
        }
        public function deleteAvatar($parameters){
            $response   = (object)array(
                'url'       =>'',
                'message'   =>'error validation'
            );
            if ($this->validate($parameters, __METHOD__) === true) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->user) {
                    \Pure\Components\BuddyPress\Profile\Initialization::instance()->attach();
                    $BuddyPressProfile = new \Pure\Components\BuddyPress\Profile\Core();
                    $avatar = $BuddyPressProfile->delAvatar((object)array('id'=>(int)$parameters->user));
                    if ($avatar !== false){
                        $response->url      = $avatar;
                        $response->message  = 'success';
                    }else{
                        $response->message  = 'fail';
                    }
                }else{
                    $response->message  = 'no access';
                }
            }
            echo json_encode($response);
        }
        public function email($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->user) {
                    if (filter_var($parameters->email, FILTER_VALIDATE_EMAIL) !== false){
                        $result = wp_update_user(
                            array(
                                'ID'            => (int)$parameters->user,
                                'user_email'    => filter_var($parameters->email, FILTER_VALIDATE_EMAIL)
                            )
                        );
                        if ( is_wp_error($result)) {
                            echo 'fail';return false;
                        } else {
                            echo 'success';return true;
                        }
                    }
                    echo 'bad email';return false;
                }
                echo 'no access';return false;
            }
            echo 'validation error';return false;
        }
        public function password($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->user) {
                    if (wp_check_password( $parameters->old, $current->data->user_pass ) !== false ){
                        wp_set_password( $parameters->new, (int)$parameters->user );
                        echo 'success';return true;
                    }else{
                        echo 'wrong password';return false;
                    }
                }
                echo 'no access';return false;
            }
            echo 'validation error';return false;
        }
    }
}
?>