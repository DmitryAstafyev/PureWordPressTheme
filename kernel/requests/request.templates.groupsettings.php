<?php
namespace Pure\Requests\Templates\GroupSettings{
    class Settings{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'update':
                    $parameters->user       = (integer  )($parameters->user     );
                    $parameters->group      = (integer  )($parameters->group    );
                    $parameters->settings   = (string   )($parameters->settings );
                    return true;
                case 'setTitleImage':
                    $parameters->user       = (integer  )($parameters->user     );
                    $parameters->group      = (integer  )($parameters->group    );
                    $parameters->path       = (string   )($parameters->path     );
                    $parameters->x          = (integer  )($parameters->x        );
                    $parameters->y          = (integer  )($parameters->y        );
                    $parameters->height     = (integer  )($parameters->height   );
                    $parameters->width      = (integer  )($parameters->width    );
                    return true;
                case 'delTitleImage':
                    $parameters->user       = (integer  )($parameters->user     );
                    $parameters->group      = (integer  )($parameters->group    );
                    return true;
            }
            return false;
        }
        public function update($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->user) {
                    \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                    $GroupData      = new \Pure\Components\BuddyPress\Groups\Core();
                    $permissions    = $GroupData->getUserPermissions(
                        (object)array(
                            'group' =>$parameters->group,
                            'user'  =>$current
                        )
                    );
                    if ($permissions->details !== false && $permissions->visibility !== false){
                        try{
                            $parameters->settings   = stripslashes($parameters->settings);
                            $settings               = json_decode(stripslashes($parameters->settings));
                            //echo var_dump($settings);
                            if (is_null($settings) === false){
                                \Pure\Components\BuddyPress\PersonalSettings\Initialization::instance()->attach();
                                $BuddyPressGroupSettings = new \Pure\Components\BuddyPress\PersonalSettings\Group();
                                foreach($settings as $field_name=>$field_value){
                                    if ($BuddyPressGroupSettings->set((object)array(
                                            'group_id'  =>(int)$parameters->group,
                                            'field'     =>(string)$field_name,
                                            'value'     =>$field_value)) === false){
                                        $BuddyPressGroupSettings = NULL;
                                        echo "error_during_saving";
                                        return false;
                                    }
                                }
                                $BuddyPressGroupSettings = NULL;
                                echo "updated";
                                return true;
                            }
                        }catch (\Exception $e){
                            echo 'incorrect_data';
                            return false;
                        }
                        echo 'incorrect_data';
                        return false;
                    }
                }
            }
            echo 'no_access';
            return false;
        }
        public function setTitleImage($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $response   = (object)array(
                    'url'       =>'',
                    'message'   =>''
                );
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->user) {
                    \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                    $GroupData      = new \Pure\Components\BuddyPress\Groups\Core();
                    $permissions    = $GroupData->getUserPermissions(
                        (object)array(
                            'group' =>$parameters->group,
                            'user'  =>$current
                        )
                    );
                    if ($permissions->details !== false && $permissions->visibility !== false) {
                        \Pure\Components\BuddyPress\PersonalSettings\Initialization::instance()->attach();
                        $BuddyPressGroupSettings = new \Pure\Components\BuddyPress\PersonalSettings\Group();
                        if (isset($_FILES['file']) === true){
                            if ($_FILES['file']['size'] < wp_max_upload_size()){
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
                                if ($parameters->path === ''){
                                    if ( !function_exists( 'wp_handle_upload' ) ) { require_once( \Pure\Configuration::instance()->dir(ABSPATH . 'wp-admin/includes/file.php') ); }
                                    $file               = wp_handle_upload($_FILES['file'], array( 'test_form' => false ));
                                    $response->url      = $file['url'];
                                    $response->path     = $file['file'];
                                    $parameters->path   = $response->path;
                                }
                                if ($crop === false){
                                    $response->message  = 'ready_for_crop';
                                }else{
                                    $result = $BuddyPressGroupSettings->setTitleImage(
                                        (object)array(
                                            'user_id'   =>$parameters->user,
                                            'group_id'  =>$parameters->group,
                                            'path'      =>$parameters->path,
                                            'x'         =>$parameters->x,
                                            'y'         =>$parameters->y,
                                            'height'    =>$parameters->height,
                                            'width'     =>$parameters->width
                                        )
                                    );
                                    if ($parameters->path !== ''){
                                        @unlink($parameters->path);
                                    }
                                    if ($result !== false){
                                        $response->url      = $result;
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
                        $BuddyPressGroupSettings = NULL;
                    }
                }else{
                    $response->message = 'wrong_user';
                }
                echo json_encode($response);
            }
        }
        public function delTitleImage($parameters){
            $response   = 'error validation';
            if ($this->validate($parameters, __METHOD__) === true) {
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->user) {
                    \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                    $GroupData      = new \Pure\Components\BuddyPress\Groups\Core();
                    $permissions    = $GroupData->getUserPermissions(
                        (object)array(
                            'group' =>$parameters->group,
                            'user'  =>$current
                        )
                    );
                    if ($permissions->details !== false && $permissions->visibility !== false) {
                        \Pure\Components\BuddyPress\PersonalSettings\Initialization::instance()->attach();
                        $BuddyPressGroupSettings = new \Pure\Components\BuddyPress\PersonalSettings\Group();
                        if ($BuddyPressGroupSettings->deleteTitleImage(
                                (object)array(
                                    'user_id'   =>$parameters->user,
                                    'group_id'  =>$parameters->group,
                                )
                            ) !== false){
                            $response = 'success';
                        }else{
                            $response = 'fail';
                        }
                    }
                }else{
                    $response = 'no access';
                }
            }
            echo $response;
        }
    }
}
?>