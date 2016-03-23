<?php
namespace Pure\Components\BuddyPress\Profile{
    class Core{
        public $locked = array(
            'user_login',   //from wp_users
            'user_email',   //from wp_users
            'user_nicename' //from wp_users
        );
        public $forcibly_visibility = array(
            'user_login'    => 'public',
            'user_email'    => 'adminsonly',
            'user_nicename' => 'public'
        );
        public $invisible = array(
            'user_nicename' //from wp_users
        );
        public $visibility = array(
            'public',
            'adminsonly',
            'friends',
            'loggedin'
        );
        private function validate(&$parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $result = ($result === false ? $result : (isset($parameters->id) === true ? (gettype($parameters->id) == 'integer'  ? true : false) : false));
                    $parameters->full = (isset($parameters->full) === true ? (gettype($parameters->full) === 'boolean' ? $parameters->full : false) : false);
                    break;
                case 'set':
                    $result = ($result === false ? $result : (isset($parameters->id     ) === true ? (gettype($parameters->id       ) == 'integer' ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->fields ) === true ? (gettype($parameters->fields   ) == 'array'   ? true : false) : false));
                    break;
                case 'getWPProfileFieldSettings':
                    $result = ($result === false ? $result : (isset($parameters->id     ) === true ? (gettype($parameters->id   ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->field  ) === true ? (gettype($parameters->field) == 'string'   ? true : false) : false));
                    break;
                case 'setWPProfileFieldSettings':
                    $result = ($result === false ? $result : (isset($parameters->id         ) === true ? (gettype($parameters->id           ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->field      ) === true ? (gettype($parameters->field        ) == 'string'   ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->allow      ) === true ? (gettype($parameters->allow        ) == 'boolean'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->visibility ) === true ? (gettype($parameters->visibility   ) == 'string'   ? true : false) : false));
                    break;
                case 'getBuddyPressVisibility':
                    $result = ($result === false ? $result : (isset($parameters->id     ) === true ? (gettype($parameters->id   ) == 'integer'  ? true : false) : false));
                    break;
                case 'setBuddyPressVisibility':
                    $result = ($result === false ? $result : (isset($parameters->id         ) === true ? (gettype($parameters->id       ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->fields     ) === true ? (gettype($parameters->fields   ) == 'array'    ? true : false) : false));
                    break;
                case 'getAvatar':
                    $result             = ($result === false ? $result : (isset($parameters->id) === true ? (gettype($parameters->id) == 'integer'  ? true : false) : false));
                    $parameters->width  = (isset($parameters->width ) === true ? (gettype($parameters->width    ) == 'integer'  ? $parameters->width    : false) : false);
                    $parameters->height = (isset($parameters->height) === true ? (gettype($parameters->height   ) == 'integer'  ? $parameters->height   : false) : false);
                    break;
                case 'setAvatar':
                    $result             = ($result === false ? $result : (isset($parameters->id     ) === true ? (gettype($parameters->id   ) == 'integer'  ? true : false) : false));
                    $result             = ($result === false ? $result : (isset($parameters->files  ) === true ? (gettype($parameters->files) == 'array'    ? true : false) : false));
                    $result             = ($result === false ? $result : (isset($parameters->field  ) === true ? (gettype($parameters->field) == 'string'   ? true : false) : false));
                    $parameters->crop   = (isset($parameters->crop) === true ? (gettype($parameters->crop) == 'object'  ? $parameters->crop : false) : false);
                    break;
                case 'delAvatar':
                    $result             = ($result === false ? $result : (isset($parameters->id     ) === true ? (gettype($parameters->id   ) == 'integer'  ? true : false) : false));
                    break;
            }
            return $result;
        }
        public function getCollections($add_childs = false){
            global $wpdb;
            $fields = $wpdb->get_results('SELECT wp_bp_xprofile_fields.*, '.
                                            '(SELECT wp_bp_xprofile_meta.meta_value '.
                                                'FROM wp_bp_xprofile_meta '.
                                                    'WHERE '.
                                                        'wp_bp_xprofile_fields.id = wp_bp_xprofile_meta.object_id AND '.
                                                        'wp_bp_xprofile_meta.object_type = "field" AND '.
                                                        'wp_bp_xprofile_meta.meta_key="default_visibility") '.
                                            'AS visibility, '.
                                            '(SELECT wp_bp_xprofile_meta.meta_value '.
                                                'FROM wp_bp_xprofile_meta '.
                                                    'WHERE '.
                                                        'wp_bp_xprofile_fields.id = wp_bp_xprofile_meta.object_id AND '.
                                                        'wp_bp_xprofile_meta.object_type = "field" AND '.
                                                        'wp_bp_xprofile_meta.meta_key="allow_custom_visibility" ) '.
                                            'AS allow_change_visibility, '.
                                            'wp_bp_xprofile_groups.name AS group_name, '.
                                            'wp_bp_xprofile_groups.description AS group_description '.
                                            'FROM '.
                                                'wp_bp_xprofile_fields, '.
                                                'wp_bp_xprofile_groups '.
                                                'WHERE '.
                                                    'parent_id = 0 AND '.
                                                    'wp_bp_xprofile_fields.group_id = wp_bp_xprofile_groups.id');
            if (is_array($fields) === true){
                $collections = (object)array(
                    'list'      =>array(),
                    'structure' =>array()
                );
                foreach($fields as $field){
                    if (array_key_exists($field->group_name, $collections->structure) === false){
                        $collections->structure[$field->group_name] = (object)array(
                            'id'            =>$field->group_id,
                            'name'          =>$field->group_name,
                            'description'   =>$field->group_description,
                            'fields'        =>array()
                        );
                    }
                    $field->visibility              = (is_null($field->visibility) === true ? 'public' : $field->visibility);
                    $field->allow_change_visibility = (is_null($field->allow_change_visibility) === true ? true : ($field->allow_change_visibility == 'allowed' ? true : false));
                    $collections->structure[$field->group_name]->fields[$field->name] = $field;
                    $collections->list[$field->id]  = (object)array(
                        'field_id'          =>$field->id,
                        'field_name'        =>$field->name,
                        'collection_id'     =>$field->group_id,
                        'collection_name'   =>$field->group_name
                    );
                }
                if ($add_childs === true){
                    $fields = $wpdb->get_results('SELECT * FROM wp_bp_xprofile_fields WHERE parent_id <> 0');
                    foreach($fields as $field){
                        $group_name = $collections->list[$field->parent_id]->collection_name;
                        $field_name = $collections->list[$field->parent_id]->field_name;
                        if(isset($collections->structure[$group_name]->fields[$field_name]->childs) === false){
                            $collections->structure[$group_name]->fields[$field_name]->childs = array();
                        }
                        $collections->structure[$group_name]->fields[$field_name]->childs[] = $field;
                    }
                }
                return $collections;
            }
            return false;
        }
        private function getWPProfileFieldSettings($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                global $wpdb;
                $result = $wpdb->get_results(   'SELECT '.
                                                    'user_control, visibility '.
                                                'FROM wp_pure_users_profile_config '.
                                                    'WHERE '.
                                                        'object_id='.$parameters->id.' AND '.
                                                        'field_name="'.$parameters->field.'"');
                if (is_array($result) === true){
                    if (count($result) === 1){
                        return $result[0];
                    }
                }
                return false;
            }
            return NULL;
        }
        private function setWPProfileFieldSettings($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                if (in_array($parameters->visibility, $this->visibility) === true){
                global $wpdb;
                $result = $wpdb->query( 'SELECT '.
                                            'user_control, visibility '.
                                        'FROM wp_pure_users_profile_config '.
                                            'WHERE '.
                                                'object_id='.$parameters->id.' AND '.
                                                'field_name="'.$parameters->field.'"');
                if (gettype($result) === 'integer'){
                    if ($result === 1){
                        $wpdb->query(   'UPDATE wp_pure_users_profile_config '.
                                            'SET '.
                                                'user_control="'.($parameters->allow === true ? 1 : 0).'", '.
                                                'visibility="'.$parameters->visibility.'" '.
                                            'WHERE '.
                                                'object_id='.$parameters->id.' AND '.
                                                'field_name="'.$parameters->field.'" '.
                                            'LIMIT 1');
                        return true;
                    }else{
                        $wpdb->query(   'INSERT wp_pure_users_profile_config '.
                                            'SET '.
                                                'object_id = '.$parameters->id.', '.
                                                'field_name = "'.$parameters->field.'", '.
                                                'user_control = "'.($parameters->allow === true ? 1 : 0).'", '.
                                                'visibility = "'.$parameters->visibility.'"');
                        return true;
                    }
                }
                return false;
                }
            }
            return NULL;
        }
        private function getBuddyPressVisibility($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                global $wpdb;
                $result = $wpdb->get_results(   'SELECT '.
                                                    'meta_value '.
                                                    'FROM '.
                                                        'wp_usermeta '.
                                                    'WHERE '.
                                                        'user_id='.$parameters->id.' AND '.
                                                        'meta_key="bp_xprofile_visibility_levels"');
                if (is_array($result) === true){
                    if (count($result) === 1){
                        return unserialize($result[0]->meta_value);
                    }
                }
            }
            return false;
        }
        private function setBuddyPressVisibility($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                global $wpdb;
                $current_fields = $this->getBuddyPressVisibility((object)array('id'=>(int)$parameters->id));
                $collection     = $this->getCollections();
                foreach($parameters->fields as $key=>$field){
                    if (isset($collection->list[$key]) === false || in_array($field, $this->visibility) === false){
                        return false;
                    }
                }
                if (is_array($current_fields) === true){
                    foreach($current_fields as $key=>$field){
                        $parameters->fields[$key] = (isset($parameters->fields[$key]) === false ? $field : $parameters->fields[$key]);
                    }
                    $fields = serialize($parameters->fields);
                    $wpdb->query('UPDATE wp_usermeta '.
                                    'SET '.
                                        'meta_value=\''.$fields.'\' '.
                                    'WHERE '.
                                        'user_id='.$parameters->id.' AND '.
                                        'meta_key="bp_xprofile_visibility_levels" '.
                                    'LIMIT 1');
                    return true;
                }else{
                    $fields = serialize($parameters->fields);
                    $wpdb->query('INSERT '.
                                    'wp_usermeta '.
                                        'SET '.
                                            'user_id = '.$parameters->id.', '.
                                            'meta_key = "bp_xprofile_visibility_levels", '.
                                            'meta_value = \''.$fields.'\'');
                    return true;
                }
            }
            return false;
        }
        private function getDefaultBuddyPressVisibility(){
            $fields = array();
            global $wpdb;
            $result = $wpdb->get_results(   'SELECT '.
                                                'meta_value AS visibility, '.
                                                'object_id AS field_id '.
                                            'FROM '.
                                                'wp_bp_xprofile_meta '.
                                            'WHERE '.
                                                'object_type="field" AND '.
                                                'meta_key="default_visibility"');
            if (is_array($result) === true){
                foreach($result as $field){
                    $fields[$field->field_id] = $field->visibility;
                }
            }
            return $fields;
        }
        private function checkVisibility($inFields, $friendship, $current, $owner, $is_admin){
            //echo '<p>'.var_dump($friendship).'</p>';
            $outFields = array();
            foreach($inFields as $field_name=>$field){
                if ($owner !== false || $is_admin !== false){
                    $outFields[$field_name] = $field;
                }else{
                    switch($field->visibility){
                        case 'public':
                            $outFields[$field_name] = $field;
                            break;
                        case 'loggedin':
                            if ($current !== false){
                                $outFields[$field_name] = $field;
                            }
                            break;
                        case 'friends':
                            if ($friendship !== false){
                                $outFields[$field_name] = $field;
                            }
                            break;
                        case 'adminsonly':
                            //Do nothing
                            break;
                    }
                }
            }
            //echo '<p>'.var_dump($outFields).'</p>';
            return $outFields;
        }
        private function rename($field_name){
            switch($field_name){
                case 'user_email'   : return __('Email',         'pure');
                case 'user_login'   : return __('Login',         'pure');
                case 'user_url'     : return __('Site',          'pure');
                case 'first_name'   : return __('First name',    'pure');
                case 'last_name'    : return __('Last name',     'pure');
                case 'nickname'     : return __('Nick',          'pure');
                case 'description'  : return __('About',         'pure');
            }
            return $field_name;
        }
        public function get($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                //$current->role->is_admin
                $profile    = new \stdClass();
                global $wpdb;
                $result = $wpdb->get_results(   'SELECT user_email, user_login, user_nicename, user_url '.
                                                    'FROM wp_users '.
                                                        'WHERE ID='.$parameters->id);
                if (is_array($result) === true){
                    if (count($result) === 1){
                        $result = $result[0];
                        $profile->WordPressBasic = array();
                        foreach($result as $field_name=>$field_value){
                            $settings = $this->getWPProfileFieldSettings((object)array(
                                'id'    =>(int)$parameters->id,
                                'field' =>$field_name
                            ));
                            $settings = (is_null($settings) === true ? false : $settings);
                            if (in_array($field_name, $this->invisible) === false){
                                $profile->WordPressBasic[$field_name] = (object)array(
                                    'id'                        =>$field_name,
                                    'name'                      =>$this->rename($field_name),
                                    'value'                     =>$field_value,
                                    'locked'                    =>in_array($field_name, $this->locked),
                                    'visibility'                =>($settings === false ? 'public' : $settings->visibility),
                                    'allow_change_visibility'   =>($settings === false ? true : (boolean)$settings->user_control),
                                );
                                $profile->WordPressBasic[$field_name]->type = ($field_name === 'description' ? 'textarea' : 'textbox');
                                if (isset($this->forcibly_visibility[$field_name]) === true){
                                    $profile->WordPressBasic[$field_name]->visibility               = $this->forcibly_visibility[$field_name];
                                    $profile->WordPressBasic[$field_name]->allow_change_visibility  = false;
                                }
                            }
                        }
                        $fields = $wpdb->get_results(   'SELECT '.
                                                            'umeta_id, '.
                                                            'meta_key, '.
                                                            'meta_value, '.
                                                            '(SELECT user_control FROM wp_pure_users_profile_config WHERE '.
                                                            'wp_pure_users_profile_config.object_id = '.$parameters->id.' AND wp_pure_users_profile_config.field_name = meta_key) AS allow_change_visibility, '.
                                                            '(SELECT visibility FROM wp_pure_users_profile_config WHERE '.
                                                            'wp_pure_users_profile_config.object_id = '.$parameters->id.' AND wp_pure_users_profile_config.field_name = meta_key) AS visibility '.
                                                        'FROM '.
                                                            'wp_usermeta '.
                                                        'WHERE '.
                                                            'user_id = '.$parameters->id.' '.
                                                        'AND ( '.
                                                            'meta_key = "first_name" '.
                                                            'OR meta_key = "last_name" '.
                                                            'OR meta_key = "nickname" '.
                                                            'OR meta_key = "description" '.
                                                        ')');
                        if (is_array($fields) === true) {
                            foreach($fields as $field){
                                $profile->WordPressBasic[$field->meta_key] = (object)array(
                                    'id'                        =>$field->meta_key,
                                    'name'                      =>$this->rename($field->meta_key),
                                    'value'                     =>$field->meta_value,
                                    'locked'                    =>in_array($field->meta_key, $this->locked),
                                    'visibility'                =>(is_null($field->visibility) === true ? 'public' : $field->visibility),
                                    'allow_change_visibility'   =>(is_null($field->allow_change_visibility) === true ? true : (boolean)$field->allow_change_visibility)
                                );
                                $profile->WordPressBasic[$field->meta_key]->type = ($field->meta_key === 'description' ? 'textarea' : 'textbox');
                            }
                            $fields = $wpdb->get_results(   'SELECT wp_bp_xprofile_data.field_id, wp_bp_xprofile_data.value '.
                                                                'FROM wp_bp_xprofile_data '.
                                                                    'WHERE '.
                                                                        'user_id='.$parameters->id);
                            if (is_array($fields) === true){
                                $collections = $this->getCollections($parameters->full);
                                if ($collections !== false){
                                    $_fields    = array();
                                    foreach($fields as $field) {
                                        $_fields[$field->field_id] = $field;
                                    }
                                    $fields     = $_fields;
                                    //================================
                                    //echo var_dump($fields);
                                    //echo var_dump($collections->structure);
                                    //================================
                                    $profile->BuddyPressProfile = array();
                                    $visibility = $this->getBuddyPressVisibility((object)array('id'=>$parameters->id));
                                    foreach($collections->list as $key=>$collection_field){
                                        $collection_name = $collection_field->collection_name;
                                        if (isset($profile->BuddyPressProfile[$collection_name]) === false){
                                            $profile->BuddyPressProfile[$collection_name]           =  (object)array(
                                                'id'            =>$collections->structure[$collection_name]->id,
                                                'name'          =>$collections->structure[$collection_name]->name,
                                                'description'   =>$collections->structure[$collection_name]->description,
                                                'fields'        =>array()
                                            );
                                            $profile->BuddyPressProfile[$collection_name]->fields   = array();
                                        }
                                        $field_name = $collection_field->field_name;
                                        $profile->BuddyPressProfile[$collection_name]->fields[$field_name]          = $collections->structure[$collection_name]->fields[$field_name];
                                        $profile->BuddyPressProfile[$collection_name]->fields[$field_name]->value   = (isset($fields[$key]) !== false ? $fields[$key]->value : '');
                                        if ($visibility !== false){
                                            if (isset($visibility[(int)$key]) === true){
                                                $profile->BuddyPressProfile[$collection_name]->fields[$field_name]->visibility = $visibility[(int)$key];
                                            }
                                        }

                                    }
                                }
                            }
                            //echo var_dump($profile);
                            //At this point we have full(!) profile of user structured by groups. But we have to hide something for current user.
                            //So next step - check configuration of visibility each field
                            //Sure, if current user settings or current user is owner of profile - show full
                            $friendship = false;
                            if ($current !== false){
                                if ($current->role->is_admin !== true && (int)$current->ID !== (int)$parameters->id){
                                    \Pure\Components\BuddyPress\Friendship\Initialization::instance()->attach();
                                    $Friendship = new \Pure\Components\BuddyPress\Friendship\Core();
                                    $friendship = $Friendship->isFriends((object)array(
                                        'memberIDA'=>(int)$current->ID,
                                        'memberIDB'=>(int)$parameters->id
                                    ));
                                    $Friendship = NULL;
                                    $friendship = ($friendship !== false ? (boolean)$friendship->accepted : false);
                                    $profile->WordPressBasic = $this->checkVisibility(
                                        $profile->WordPressBasic,
                                        $friendship,
                                        $current,
                                        ($current !== false ? ($current->ID == $parameters->id ? true : false) : false),
                                        ($current !== false ? $current->role->is_admin : false)
                                    );
                                    foreach($profile->BuddyPressProfile as $collection_name=>$collection){
                                        $profile->BuddyPressProfile[$collection_name]->fields = $this->checkVisibility(
                                            $collection->fields,
                                            $friendship,
                                            $current,
                                            ($current !== false ? ($current->ID == $parameters->id ? true : false) : false),
                                            ($current !== false ? $current->role->is_admin : false)
                                        );
                                    }
                                }
                            }
                            $profile->WordPressBasic = $this->checkVisibility(
                                $profile->WordPressBasic,
                                $friendship,
                                $current,
                                ($current !== false ? ($current->ID == $parameters->id ? true : false) : false),
                                ($current !== false ? $current->role->is_admin : false)
                            );
                            foreach($profile->BuddyPressProfile as $collection_name=>$collection){
                                $profile->BuddyPressProfile[$collection_name]->fields = $this->checkVisibility(
                                    $collection->fields,
                                    $friendship,
                                    $current,
                                    ($current !== false ? ($current->ID == $parameters->id ? true : false) : false),
                                    ($current !== false ? $current->role->is_admin : false)
                                );
                            }
                            //echo var_dump($profile);
                            return $profile;
                        }
                    }
                }
            }
            return false;
        }
        private function updateField($user_id, $field_id, $field_value, $collection = false){
            if (gettype($user_id)                       === 'integer' &&
                in_array($field_id, $this->locked)      === false &&
                in_array($field_id, $this->invisible)   === false){
                $field_obj = false;
                if (gettype($field_value) === 'array'){
                    $field_obj      = $field_value;
                    $field_value    = serialize($field_value);
                }
                if (gettype($field_value) === 'string'){
                    global $wpdb;
                    $field_id           = esc_sql($field_id     );
                    $field_value        = esc_sql($field_value  );
                    if (in_array($field_id, array(
                            'user_nicename',    'user_url',
                            'first_name',       'last_name',
                            'nickname',         'description')) === true){
                        if ($field_id === 'user_url' || $field_id === 'user_nicename'){
                            $wpdb->query(   'UPDATE wp_users '.
                                                'SET '.
                                                    $field_id.' = "'.$field_value.'" '.
                                                'WHERE '.
                                                    'ID = '.$user_id.' '.
                                                'LIMIT 1');
                            return true;
                        }else{
                            $wpdb->query(   'UPDATE wp_usermeta '.
                                                'SET '.
                                                    'meta_value="'.$field_value.'" '.
                                                'WHERE '.
                                                    'user_id = '.$user_id.' AND '.
                                                    'meta_key = "'.$field_id.'" '.
                                                'LIMIT 1');
                            return true;
                        }
                    }else{
                        if (is_numeric($field_id) === true){
                            $collection = ($collection === false ? $this->getCollections(true) : $collection);
                            if (isset($collection->list[$field_id]) === true){
                                if ((int)$collection->list[$field_id]->field_id === (int)$field_id){
                                    $group_name     = $collection->list[$field_id]->collection_name;
                                    $field_name     = $collection->list[$field_id]->field_name;
                                    $valid_value    = true;
                                    if (isset($collection->structure[$group_name]->fields[$field_name]->childs) === true){
                                        if ($field_obj !== false){
                                            $available      = array();
                                            foreach($collection->structure[$group_name]->fields[$field_name]->childs as $child){
                                                $available[] = stripcslashes($child->name);
                                            }
                                            foreach($field_obj as $value){
                                                $valid_value = ($valid_value === false ? false : in_array($value, $available));
                                            }
                                        }
                                    }
                                    if ($valid_value === true){
                                        //echo var_dump($field_value);
                                        $result = $wpdb->query( 'SELECT * '.
                                                                    'FROM '.
                                                                        'wp_bp_xprofile_data '.
                                                                    'WHERE '.
                                                                        'user_id = '.$user_id.' AND '.
                                                                        'field_id = '.$field_id.' '.
                                                                    'LIMIT 1');
                                        if (gettype($result) === 'integer'){
                                            if ($result === 1){
                                                $wpdb->query( 'UPDATE wp_bp_xprofile_data '.
                                                                'SET '.
                                                                    'value="'.$field_value.'" '.
                                                                'WHERE '.
                                                                    'user_id = '.$user_id.' AND '.
                                                                    'field_id = '.$field_id.' '.
                                                                'LIMIT 1');
                                                return true;
                                            }else{
                                                $wpdb->query(   'INSERT '.
                                                                    'wp_bp_xprofile_data '.
                                                                    'SET '.
                                                                        'field_id = '.$field_id.', '.
                                                                        'user_id = '.$user_id.', '.
                                                                        'value = "'.$field_value.'", '.
                                                                        'last_updated = "'.date("Y-m-d H:i:s").'"');
                                                return true;
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
        public function updateFieldVisibility($user_id, $field_id, $field_visibility, $collection = false){
            if (gettype($user_id) === 'integer' && in_array($field_id, $this->locked) === false && in_array($field_id, $this->invisible) === false) {
                if (in_array($field_visibility, $this->visibility) === true){
                    global $wpdb;
                    $field_id = esc_sql($field_id);
                    if (in_array($field_id, array(
                            'user_nicename',    'user_url',
                            'first_name',       'last_name',
                            'nickname',         'description')) === true
                    ) {
                        $settings = $this->getWPProfileFieldSettings((object)array(
                            'id'    =>(int)$user_id,
                            'field' =>$field_id
                        ));
                        $settings = (is_null($settings) === true ? false : $settings);
                        $settings = (object)array(
                            'visibility'                =>($settings === false ? 'public' : $settings->visibility),
                            'allow_change_visibility'   =>($settings === false ? true : (boolean)$settings->user_control)
                        );
                        if ($settings->allow_change_visibility === true){
                            return $this->setWPProfileFieldSettings((object)array(
                                'id'        =>(int)$user_id,
                                'field'     =>$field_id,
                                'allow'     =>true,
                                'visibility'=>$field_visibility,
                            ));
                        }
                        return false;
                    }else{
                        if (isset($collection->list[$field_id]) === true) {
                            if ((int)$collection->list[$field_id]->field_id === (int)$field_id) {
                                $group_name = $collection->list[$field_id]->collection_name;
                                $field_name = $collection->list[$field_id]->field_name;
                                if ($collection->structure[$group_name]->fields[$field_name]->allow_change_visibility === true) {
                                    return $this->setBuddyPressVisibility((object)array(
                                        'id'    =>(int)$user_id,
                                        'fields'=>array($field_id=>$field_visibility)
                                    ));
                                }
                            }
                            return true;
                        }
                        return false;
                    }
                }
            }
            return NULL;
        }
        /*
         $parameters->fields ::
            array() {
                  [0]=>
                      object(stdClass)#3411 (5) {
                        ["id"]          => string   // field id
                        ["group"]       => string   // name of group (collection)
                        ["value"]       => string   // field value
                        ["visibility"]  => string   // visibility for field
                        ["json"]        => boolean  // true if value is json
                      }
                ...
            }
         */
        public function set($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->id){
                    $collection     = $this->getCollections(true);
                    $successful     = true;
                    foreach($parameters->fields as $field){
                        if (in_array($field->id, $this->locked) === false && in_array($field->id, $this->invisible) === false){
                            if ($this->updateField(
                                    (int)$parameters->id,
                                    $field->id,
                                    $field->value,
                                    $collection) === false){
                                $successful = false;
                            }
                            if ($this->updateFieldVisibility((int)$parameters->id, $field->id, $field->visibility, $collection) === false){
                                $successful = false;
                            }
                        }
                    }
                    return $successful;
                }
            }
            return false;
        }
        public function getAvatar($parameters){
            $result = '';
            if ($this->validate($parameters, __METHOD__) === true){
                if (function_exists('bp_core_fetch_avatar') === true){
                    $result = bp_core_fetch_avatar(array (
                        'item_id'       => $parameters->id,
                        'object'        => 'user',
                        'type'          => 'full',
                        'avatar_dir'    => 'avatars',
                        'email'         => bp_core_get_user_email($parameters->id),
                        'width'         => ($parameters->width  !== false ? $parameters->width  : ''),
                        'height'        => ($parameters->height !== false ? $parameters->height : ''),
                        'html'          => false ));
                }
            }
            return $result;
        }
        public function setAvatar($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                if (count($parameters->files) === 1){
                    if (isset($parameters->files[$parameters->field]['type']) === true){
                        \Pure\Components\GlobalSettings\MIMETypes\Initialization::instance()->attach(true);
                        if (stripos(\Pure\Components\GlobalSettings\MIMETypes\Types::$images, $parameters->files[$parameters->field]['type']) !== false){
                            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                            $current    = $WordPress->get_current_user();
                            $WordPress  = NULL;
                            if ((int)$current->ID === (int)$parameters->id){
                                return $this->doSetAvatar($parameters);
                            }
                        }
                    }
                }
            }
            return false;
        }
        //This is not secured method. It much better to use setAvatar. This method used directly by DEMO builder.
        public function doSetAvatar($parameters){
            global $bp;
            $_POST['action']            = 'bp_avatar_upload';
            $bp->members->current_id    = (int)$parameters->id;
            $bp->avatar_admin           = new \stdClass();
            $size                       = @getimagesize( $parameters->files[$parameters->field]['tmp_name'] );
            if (is_array($size) === true){
                $max_width      = bp_core_avatar_original_max_width();
                $ratio          = ($size[0] > $max_width ? $max_width / $size[0] : 1);
                $avatar_root    = bp_core_get_upload_dir().'/'.'avatars';
                $avatar_dir     = $avatar_root.'/'.(int)$parameters->id;
                if (file_exists(\Pure\Configuration::instance()->dir($avatar_root)) === false){
                    mkdir($avatar_root);
                }
                if (file_exists(\Pure\Configuration::instance()->dir($avatar_dir)) === false){
                    mkdir($avatar_dir);
                }
                if (bp_core_avatar_handle_upload($parameters->files, 'xprofile_avatar_upload_dir') === true) {
                    $bp->avatar_admin->step = 'crop-image';
                    if ($parameters->crop === false){
                        do_action( 'xprofile_avatar_uploaded' );
                        bp_core_add_message( __( 'Your new profile photo was uploaded successfully.', 'buddypress' ) );
                        return bp_core_avatar_handle_crop( array(
                                'object'        => 'user',
                                'avatar_dir'    => 'avatars',
                                'item_id'       => $parameters->id,
                                'original_file' => $bp->avatar_admin->image->dir)
                        );
                    }else{
                        do_action( 'xprofile_avatar_uploaded' );
                        bp_core_add_message( __( 'Your new profile photo was uploaded successfully.', 'buddypress' ) );
                        return bp_core_avatar_handle_crop( array(
                                'object'        => 'user',
                                'avatar_dir'    => 'avatars',
                                'item_id'       => $parameters->id,
                                'original_file' => $bp->avatar_admin->image->dir,
                                'crop_w'        => (int)($parameters->crop->w * $ratio),
                                'crop_h'        => (int)($parameters->crop->h * $ratio),
                                'crop_x'        => (int)($parameters->crop->x * $ratio),
                                'crop_y'        => (int)($parameters->crop->y * $ratio))

                        );
                    }
                }
            }
        }
        public function delAvatar($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                if ((int)$current->ID === (int)$parameters->id){
                    if (bp_core_delete_existing_avatar(array('item_id'=>(int)$parameters->id, 'object'=>'user')) !== false){
                        return $WordPress->user_avatar_url((int)$parameters->id);
                    }
                }
                $WordPress  = NULL;
            }
            return false;
        }
        function __construct(){
        }
    }
}
?>