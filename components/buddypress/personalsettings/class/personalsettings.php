<?php
namespace Pure\Components\BuddyPress\PersonalSettings{
    class User{
        private $meta_key = 'pure_user_personal_page_settings';
        private function validate($parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $result = ($result === false ? $result : (isset($parameters->user_id) === true ? (gettype($parameters->user_id) == 'integer' ? true : false) : false));
                    break;
                case 'set':
                    $result = ($result === false ? $result : (isset($parameters->user_id) === true ? (gettype($parameters->user_id  ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->field  ) === true ? (gettype($parameters->field    ) == 'string'   ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->value  ) === true ? true : false));
                    break;
                case 'setTitleImage':
                    $result = ($result === false ? $result : (isset($parameters->user_id) === true ? (gettype($parameters->user_id  ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->path   ) === true ? (gettype($parameters->path     ) == 'string'   ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->x      ) === true ? (gettype($parameters->x        ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->y      ) === true ? (gettype($parameters->y        ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->height ) === true ? (gettype($parameters->height   ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->width  ) === true ? (gettype($parameters->width    ) == 'integer'  ? true : false) : false));
                    break;
                case 'deleteTitleImage':
                    $result = ($result === false ? $result : (isset($parameters->user_id) === true ? (gettype($parameters->user_id  ) == 'integer'  ? true : false) : false));
                    break;
            }
            return $result;
        }
        private function defaults(){
            return array(
                'header_background' => (object)array(
                    'attachment_id' =>false,
                    'settings'      =>false,
                    'url'           =>false
                ),
                'background'        => (object)array(
                    'attachment_id' =>false,
                    'settings'      =>false,
                ),
                'privacy'        => (object)array(
                    'mode'          =>'all',//all || registered || friends
                ),
                'quotes'            => (object)array(
                    'template'  =>false,
                    'settings'  =>false
                ),
            );
        }
        public function get($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $settings   = get_user_meta($parameters->user_id, $this->meta_key, true);
                $defaults   = $this->defaults();
                if ($settings !== ''){
                    foreach($defaults as $key=>$value){
                        if (isset($settings[$key]) === false){
                            $settings[$key] = $defaults[$key];
                        }
                    }
                }else{
                    $settings = $defaults;
                }
                $settings["header_background"]->url = \Pure\Resources\Names::instance()->repairURL($settings["header_background"]->url);
                if ($settings["header_background"]->url === site_url() || $settings["header_background"]->url === site_url().'/'){
                    $settings["header_background"]->url = '';
                }
                return $settings;
            }
            return false;
        }
        private function save($user_id, $settings){
            $settings["header_background"]->url = \Pure\Resources\Names::instance()->clearURL($settings["header_background"]->url);
            return update_user_meta( $user_id, $this->meta_key, $settings );
        }
        public function set($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false){
                    if ((int)$current->ID === (int)$parameters->user_id){
                        $settings = $this->get($parameters);
                        if ($settings !== false){
                            $settings[$parameters->field] = $parameters->value;
                            return $this->save($parameters->user_id, $settings);
                        }
                    }
                }
            }
            return false;
        }
        public function setTitleImage($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false){
                    if ((int)$current->ID === (int)$parameters->user_id){
                        $settings = $this->get($parameters);
                        if ($settings !== false){
                            if ((int)$parameters->x >= 0 && (int)$parameters->y >= 0 && (int)$parameters->height > 0 && (int)$parameters->width > 0){
                                if (file_exists(\Pure\Configuration::instance()->dir($parameters->path)) !== false){
                                    if ( !function_exists( 'wp_crop_image' ) ) { require_once( \Pure\Configuration::instance()->dir(ABSPATH . 'wp-admin/includes/image.php') ); }
                                    $destination_file = ABSPATH.'wp-content/uploads/personalpages/'.$parameters->user_id;
                                    if (file_exists(\Pure\Configuration::instance()->dir($destination_file)) === false){
                                        mkdir($destination_file, 0777, true);
                                    }
                                    $destination_file   = $destination_file.'/title_background';
                                    $image_editor       = wp_get_image_editor($parameters->path);
                                    if (!is_wp_error($image_editor)){
                                        $result = $image_editor->crop(
                                            (int) $parameters->x,
                                            (int) $parameters->y,
                                            (int) $parameters->width,
                                            (int) $parameters->height,
                                            (int) $parameters->width,
                                            (int) $parameters->height,
                                            false
                                        );
                                        if (!is_wp_error($result)){
                                            $result = $image_editor->save($destination_file);
                                            if (!is_wp_error($result)){
                                                $url = str_replace($_SERVER['DOCUMENT_ROOT'], get_site_url(), $result['path']);
                                                $url = ($url == $result['path'] ? str_replace(stripcslashes($_SERVER['DOCUMENT_ROOT']), get_site_url(), $result['path']) : $url);
                                                $this->set(
                                                    (object)array(
                                                        'user_id'   =>$parameters->user_id,
                                                        'field'     =>'header_background',
                                                        'value'     =>(object)array(
                                                            'attachment_id' =>false,
                                                            'settings'      =>false,
                                                            'url'           =>$url
                                                        ),
                                                    )
                                                );
                                                return $url;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return false;
        }
        public function deleteTitleImage($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false) {
                    if ((int)$current->ID === (int)$parameters->user_id) {
                        $settings = $this->get($parameters);
                        if ($settings !== false) {
                            return $this->set(
                                (object)array(
                                    'user_id'   =>$parameters->user_id,
                                    'field'     =>'header_background',
                                    'value'     =>(object)array(
                                        'attachment_id' =>false,
                                        'settings'      =>false,
                                        'url'           =>false
                                    ),
                                )
                            );
                        }
                    }
                }
            }
            return false;
        }
        public function availableForCurrentUser($target_user_id){
            if ((int)$target_user_id > 0){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $settings   = $this->get(
                    (object)array(
                        'user_id'=>(int)$target_user_id
                    )
                );
                $allow      = false;
                if ($settings !== false){
                    switch($settings['privacy']->mode){
                        case 'all':
                            $allow = true;
                            break;
                        case 'registered':
                            $allow = ($current !== false ? true : false);
                            break;
                        case 'friends':
                            if ($current !== false){
                                if ((int)$current->ID !== (int)$target_user_id){
                                    $current    = $WordPress->get_current_user(false, true);
                                    $allow      = (in_array($target_user_id, $current->friends) !== false ? true : false);
                                }else{
                                    $allow      = true;
                                }
                            }
                            break;
                    }
                }
            }
            $WordPress  = NULL;
            return $allow;
        }
    }
    class Group{
        private $meta_key = 'pure_group_personal_page_settings';
        private function validate($parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $result = ($result === false ? $result : (isset($parameters->group_id) === true ? (gettype($parameters->group_id) == 'integer' ? true : false) : false));
                    break;
                case 'set':
                    $result = ($result === false ? $result : (isset($parameters->group_id   ) === true ? (gettype($parameters->group_id     ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->field      ) === true ? (gettype($parameters->field        ) == 'string'   ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->value      ) === true ? true : false));
                    break;
                case 'setTitleImage':
                    $result = ($result === false ? $result : (isset($parameters->user_id) === true ? (gettype($parameters->user_id  ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->group_id) === true ? (gettype($parameters->group_id     ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->path   ) === true ? (gettype($parameters->path     ) == 'string'   ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->x      ) === true ? (gettype($parameters->x        ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->y      ) === true ? (gettype($parameters->y        ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->height ) === true ? (gettype($parameters->height   ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->width  ) === true ? (gettype($parameters->width    ) == 'integer'  ? true : false) : false));
                    break;
                case 'deleteTitleImage':
                    $result = ($result === false ? $result : (isset($parameters->user_id) === true ? (gettype($parameters->user_id  ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->group_id   ) === true ? (gettype($parameters->group_id     ) == 'integer'  ? true : false) : false));
                    break;
            }
            return $result;
        }
        private function defaults(){
            return array(
                'header_background' => (object)array(
                    'attachment_id' =>false,
                    'settings'      =>false,
                    'url'           =>false
                ),
                'background'        => (object)array(
                    'attachment_id' =>false,
                    'settings'      =>false
                ),
            );
        }
        public function get($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $settings   = groups_get_groupmeta($parameters->group_id, $this->meta_key, true);
                $defaults   = $this->defaults();
                if ($settings !== ''){
                    foreach($defaults as $key=>$value){
                        if (isset($settings[$key]) === false){
                            $settings[$key] = $defaults[$key];
                        }
                    }
                }else{
                    $settings = $defaults;
                }
                $settings["header_background"]->url = \Pure\Resources\Names::instance()->repairURL($settings["header_background"]->url);
                if ($settings["header_background"]->url === site_url() || $settings["header_background"]->url === site_url().'/'){
                    $settings["header_background"]->url = '';
                }
                return $settings;
            }
            return false;
        }
        private function save($group_id, $settings){
            $settings["header_background"]->url = \Pure\Resources\Names::instance()->clearURL($settings["header_background"]->url);
            return groups_update_groupmeta( $group_id, $this->meta_key, $settings );
        }
        public function set($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                //Have to add access checking
                $settings = $this->get($parameters);
                if ($settings !== false){
                    $settings[$parameters->field] = $parameters->value;
                    return $this->save($parameters->group_id, $settings);
                }
            }
            return false;
        }
        public function setTitleImage($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false){
                    if ((int)$current->ID === (int)$parameters->user_id){
                        \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                        $GroupData      = new \Pure\Components\BuddyPress\Groups\Core();
                        $permissions    = $GroupData->getUserPermissions(
                            (object)array(
                                'group' =>$parameters->group_id,
                                'user'  =>$current
                            )
                        );
                        if ($permissions->details !== false && $permissions->visibility !== false) {
                            $settings = $this->get($parameters);
                            if ($settings !== false){
                                if ((int)$parameters->x >= 0 && (int)$parameters->y >= 0 && (int)$parameters->height > 0 && (int)$parameters->width > 0){
                                    if (file_exists(\Pure\Configuration::instance()->dir($parameters->path)) !== false){
                                        if ( !function_exists( 'wp_crop_image' ) ) { require_once( \Pure\Configuration::instance()->dir(ABSPATH . 'wp-admin/includes/image.php') ); }
                                        $destination_file = ABSPATH.'wp-content/uploads/grouppages/'.$parameters->group_id;
                                        if (file_exists(\Pure\Configuration::instance()->dir($destination_file)) === false){
                                            mkdir($destination_file, 0777, true);
                                        }
                                        $destination_file   = $destination_file.'/title_background';
                                        $image_editor       = wp_get_image_editor($parameters->path);
                                        if (!is_wp_error($image_editor)){
                                            $result = $image_editor->crop(
                                                (int) $parameters->x,
                                                (int) $parameters->y,
                                                (int) $parameters->width,
                                                (int) $parameters->height,
                                                (int) $parameters->width,
                                                (int) $parameters->height,
                                                false
                                            );
                                            if (!is_wp_error($result)){
                                                $result = $image_editor->save($destination_file);
                                                if (!is_wp_error($result)){
                                                    $url = str_replace($_SERVER['DOCUMENT_ROOT'], get_site_url(), $result['path']);
                                                    $url = ($url == $result['path'] ? str_replace(stripcslashes($_SERVER['DOCUMENT_ROOT']), get_site_url(), $result['path']) : $url);
                                                    $this->set(
                                                        (object)array(
                                                            'user_id'   =>$parameters->user_id,
                                                            'group_id'  =>$parameters->group_id,
                                                            'field'     =>'header_background',
                                                            'value'     =>(object)array(
                                                                'attachment_id' =>false,
                                                                'settings'      =>false,
                                                                'url'           =>$url
                                                            ),
                                                        )
                                                    );
                                                    return $url;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return false;
        }
        public function deleteTitleImage($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false) {
                    if ((int)$current->ID === (int)$parameters->user_id) {
                        \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                        $GroupData      = new \Pure\Components\BuddyPress\Groups\Core();
                        $permissions    = $GroupData->getUserPermissions(
                            (object)array(
                                'group' =>$parameters->group_id,
                                'user'  =>$current
                            )
                        );
                        if ($permissions->details !== false && $permissions->visibility !== false) {
                            $settings = $this->get($parameters);
                            if ($settings !== false) {
                                return $this->set(
                                    (object)array(
                                        'group_id'  =>$parameters->group_id,
                                        'field'     =>'header_background',
                                        'value'     =>(object)array(
                                            'attachment_id' =>false,
                                            'settings'      =>false,
                                            'url'           =>false
                                        ),
                                    )
                                );
                            }
                        }
                    }
                }
            }
            return false;
        }



        function __construct(){
        }
    }
}
?>